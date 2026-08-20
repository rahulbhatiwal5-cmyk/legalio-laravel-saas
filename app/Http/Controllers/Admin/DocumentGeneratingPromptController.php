<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentGeneratingPrompts;
use Illuminate\Support\Facades\Log;

class DocumentGeneratingPromptController extends Controller
{
    public function documentGeneratingPrompt()
    {
        return view('admin.ai_prompt.document_generating_prompt');
    }

    public function getPrompts(Request $request)
    {

        try {
            $step = $request->input('step');
            $contractType = $request->input('contract_type');

            if ($step) {
                $query = DocumentGeneratingPrompts::where('steps_no', $step);

                if ($contractType) {
                    $query->where('contract_type', $contractType);
                }
                $prompt = $query->first();

                if ($prompt) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'id' => $prompt->id,
                            'steps_no' => $prompt->steps_no,
                            'prompts' => $prompt->prompts,
                            'contract_type' => $prompt->contract_type 
                        ]
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'No prompts found for this step'
                ]);
            } else {
                $prompts = DocumentGeneratingPrompts::orderBy('steps_no', 'asc')->get();

                return response()->json([
                    'success' => true,
                    'data' => $prompts->map(function ($prompt) {
                        return [
                            'id' => $prompt->id,
                            'steps_no' => $prompt->steps_no,
                            'prompts' => $prompt->prompts,
                            'contract_type' => $prompt->contract_type
                        ];
                    })
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Log::info('Store Request Data:', $request->all());
            $validated = $request->validate([
                'steps_no' => 'required|integer|min:1|max:5',
                'contract_type' => 'required|in:question,contract',
                'prompts' => 'required|string|min:1'
            ]);
  
            $prompt = DocumentGeneratingPrompts::create([
                'steps_no' => $validated['steps_no'],
                'contract_type' => $validated['contract_type'],
                'prompts' => $validated['prompts']

            ]);

            Log::info('Prompt created: ' . json_encode($prompt));

            return response()->json([
                'success' => true,
                'message' => 'Prompts saved successfully!',
                'data' => $prompt
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request)
    {
        try {
            $request->validate([
                'prompt_id' => 'required|exists:document_generating_prompts,id',
                'steps_no' => 'required|integer|min:1|max:5',
                'contract_type' => 'required|in:question,contract',
                'prompts.*' => 'required|string|min:1'
            ]);

            $prompt = DocumentGeneratingPrompts::findOrFail($request->prompt_id);
            $prompt->update([
                'steps_no' => $request->steps_no,
                'contract_type' => $request->contract_type,
                'prompts' => $request->prompts
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prompts updated successfully!',
                'data' => $prompt
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:document_generating_prompts,id'
            ]);

            $prompt = DocumentGeneratingPrompts::findOrFail($request->id);
            $stepNo = $prompt->steps_no;
            $contract_type = $prompt->contract_type;
            $prompt->delete();

            return response()->json([
                'success' => true,
                'message' => "Prompt for Step {$stepNo} ({$contract_type}) deleted successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
