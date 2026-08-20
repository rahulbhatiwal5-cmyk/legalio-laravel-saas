<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use App\Models\PromptAttach;
use App\Models\Setting;
use App\Models\PromptVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Services\AIService;

class AiPromptController extends Controller
{


    protected $AI;

    public function __construct(AIService $service)
    {
        $this->AI = $service;
    }

    public function allPrompt(){
        $prompt=Prompt::all();
        return view ('admin.ai_prompt.all_prompt',compact('prompt'));
    }

    public function addPrompt(){
        $aiModelRefs = Setting::where('type', 'ai')
                        ->whereNotNull('model_ref')
                        ->distinct()
                        ->pluck('model_ref');

        return view ('admin.ai_prompt.add_prompt',compact('aiModelRefs'));
    }

    public function documentPrompts( Request $request ){


        $front_page_prompts=PromptAttach::all();
        $prompt=Prompt::all();
        return view ('admin.ai_prompt.document_prompt',compact('front_page_prompts','prompt'));
    }



    public function storePrompt(Request $request){
        

        $request->validate([
            // 'type' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'prompt' => 'required|string',
            'location' => 'required|string',
        ]);

        $prompt = new Prompt;
        $key = strtolower($request->name);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        $prompt->key = $key;
        $prompt->name = $request->name;
        $prompt->type = $request->type;
        $prompt->location = $request->location;
        $prompt->description = $request->description;
        $prompt->original_prompt = $request->prompt;
        $prompt->updated_prompt = $request->prompt;
        $prompt->prompt_ai_model = $request->prompt_ai_model;
        $prompt->ai_verification_model = $request->ai_verification_model;

        if($request->ai_verification_model != 'disabled'){
            $prompt->is_verified = 1;
        }else{
            $prompt->is_verified = 0;
        }
    
        $prompt->save();

        return redirect()->route('all.prompt')->with('success',"Prompt stored successfully");
    }

    public function editPrompt($id){
        $prompt=Prompt::findOrFail($id);
        $prompt->first();
        $aiModelRefs = Setting::where('type', 'ai')
                        ->whereNotNull('model_ref')
                        ->distinct()
                        ->pluck('model_ref');
        return view('admin.ai_prompt.edit_prompt',compact('prompt','aiModelRefs'));
    }

    public function updatePrompt(Request $request){
        
        $prompt =Prompt::Where('id',$request->id)->first();

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'prompt' => 'required|string',
            'location' => 'required|string',
        ]);
        $key = strtolower($request->name);
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        $prompt->key = $key;
        $prompt->name = $request->name;
        $prompt->type = $request->type;
        $prompt->location = $request->location;
        $prompt->description = $request->description;
        $prompt->original_prompt = $request->prompt;
        $prompt->updated_prompt = $request->prompt;
        $prompt->prompt_ai_model = $request->prompt_ai_model;
        $prompt->ai_verification_model = $request->ai_verification_model;

        if($request->ai_verification_model != 'disabled'){
            $prompt->is_verified = 1;
        }else{
            $prompt->is_verified = 0;
        }
  
        $prompt->save();

        return redirect()->route('all.prompt')->with('success',"Prompt stored successfully");

    }
    public function deletePrompt($id){
        $prompt =Prompt::Where('id',$id)->first();
        $prompt->delete();
        return back()->with('success',"Prompt Delete successfully");

    }

    public function generateResponse(Request $request)
    {
        $response = $this->AI->generateText("Hello");
        return response()->json($response);
    }


    public function config(Request $request){

        // dd($request->input('models'));

        if($request->post()){
            unset($request['_token']);
            foreach ($request->all() as $key => $value){
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

        }
     
        $settings = Setting::where('type', 'ai')->get();
        // dd($settings);
        return view('admin.ai_prompt.config',compact('settings'));
    }


    public function configupdate(Request $request)
    {
        $models = $request->input('models'); // Get models data from the request
    
        if ($models) {
            // Loop through each model
            foreach ($models as $model) {
                $modelRef = $model['model_ref']; // Get the model reference for this model
    
                // Define the fields that should be updated or inserted, adding the name corresponding to each key
                $fields = [
                    'server_account_file_path' => [
                        'name' => 'Service Account File Path',
                        'value' => $model['service_account_file_path'],
                    ],
                    'project_id' => [
                        'name' => 'Project ID',
                        'value' => $model['project_id'],
                    ],
                    'api_endpoint' => [
                        'name' => 'API Endpoint',
                        'value' => $model['api_endpoint'],
                    ],
                    'model_id' => [
                        'name' => 'Model ID',
                        'value' => $model['model_id'],
                    ],
                    'generate_content_api' => [
                        'name' => 'Generate Content API',
                        'value' => $model['generate_content_api'],
                    ],
                    'location_id' => [
                        'name' => 'Location ID',
                        'value' => $model['location_id'],
                    ],
                ];
    
                // Loop through each field and either update or insert the value for this model's reference
                foreach ($fields as $key => $field) {
                    // Use updateOrCreate to update existing records or create new ones if not found
                    Setting::updateOrCreate(
                        ['model_ref' => $modelRef, 'key' => $key], // Check for model_ref and key
                        [
                            'name' => $field['name'],     // Name corresponding to the key
                            'value' => $field['value'],   // The value for the field
                            'type' => 'ai',               // Set type as 'ai'
                        ]
                    );
                }
            }
    
            // Return a success response
            return redirect()->back()->with('success', 'Models have been saved successfully.');
        }
    
        // If no models data is provided, return with an error message
        return redirect()->back()->with('error', 'No models data received.');
    }
    
    
    public function deleteConfigByModelRef($modelRef)
    {
        // dd($modelRef);
        // Delete all the settings for the given model_ref
        Setting::where('model_ref', $modelRef)->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Settings for model_ref ' . $modelRef . ' have been deleted.');
    }
    
    
    


    public function getInfo(Request $request ){
        $resource_id = $request->resource_id;
        $prompt = PromptAttach::where('resource_id',$resource_id)->first();
        return response()->json($prompt);
    }


    public function aiVerification(){
        $prompt_verification = PromptVerification::first();
        return view('admin.ai_prompt.ai_verification_prompt',compact('prompt_verification'));
    }

    public function verificationAddProcess(Request $request){
        
        if(!empty($request->id)){
            $prompt_verification = PromptVerification::find($request->id);
            $prompt_verification->ai_prompt = $request->ai_prompt;
            $prompt_verification->conflict_prompt = $request->conflict_prompt;
            $prompt_verification->update();
        }else{
            $prompt_verification = new PromptVerification;
            $prompt_verification->ai_prompt = $request->ai_prompt;
            $prompt_verification->conflict_prompt = $request->conflict_prompt;
            $prompt_verification->save();
        }
       
        return redirect()->back()->with('success','Saved Successfully');
    }
}
