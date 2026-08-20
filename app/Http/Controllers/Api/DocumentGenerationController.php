<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentGenerator;
use App\Models\Prompt;
use App\Services\AIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Jobs\GenerateDocumentJob;
use App\Events\DocumentCreated;

class DocumentGenerationController extends Controller
{
    // public function startDocumentGeneration(Request $request){
    //     // return $request->all();
    //     try{    
    //         $document = Document::find($request->document_id);
    //         if(!$document){
    //             $document = new Document();
    //             $document->title = $request->document_name;
    //             $document->slug = Str::slug($request->document_name); 
    //             $document->save();
    //         }
    
    //         $document_generator = DocumentGenerator::where('document_id', $request->document_id)->first() ?? new DocumentGenerator;
    //         $document_generator->document_id = $document->id;
    //         $document_generator->ai_status = 1; 
    //         $document_generator->save();

    //         $prompt = Prompt::where([['key','document_generator'],['location','document']])->first();
    //         $ai_model = $prompt?->prompt_ai_model ?? '';
    
    //         GenerateDocumentJob::dispatch(
    //             $document->id,
    //             $document->title,
    //             $document_generator->id,
    //             $request->additional_information,
    //             $request->fileInput,
    //             $request->is_verified,
    //             $ai_model
    //         );
    
    //         return response()->json([
    //             'status' => true,
    //             'document_id' => $document->id,
    //             'id' => $document_generator->id,
    //             'ai_status' => $document_generator->ai_status,
    //             'ai_model' => $ai_model,
    //             'message' => 'Generation started. Please check status shortly.'
    //         ]);
    //     }catch(\Exception $e){
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
        
    // }

    public function startDocumentGeneration(Request $request)
    {
        try {   
            $document = Document::find($request->document_id);
            if(!$document){
                $document = new Document();
                $document->title = $request->document_name;
                $document->slug = Str::slug($request->document_name, '-');
                $document->name_on_image = implode('@', explode(' ', $request->document_name));
                $document->save();
            }

            $document_generator = DocumentGenerator::where('document_id', $document->id)->first() ?? new DocumentGenerator;
            $document_generator->document_id = $document->id;
            $document_generator->ai_status = 1; 
            $document_generator->save();

            $prompt = Prompt::where([['key','document_generator'],['location','document']])->first();
            $ai_model = $prompt?->prompt_ai_model ?? '';

            event(new DocumentCreated(
                $document,
                $document->id,
                $request->document_name,
                $request->additional_information,
                // $request->fileInput,
                $request->file('fileInput'),
                $request->is_verified,
                $document_generator->id
            ));

            return response()->json([
                'status' => true,
                'document_id' => $document->id,
                'id' => $document_generator->id,
                'ai_status' => $document_generator->ai_status,
                'ai_model' => $ai_model,
                'message' => 'Generation started. Please check status shortly.'
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


}
