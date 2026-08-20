<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PromptAttach;
use App\Models\Document;
use App\Models\PromptVerification;
use App\Services\AIService;
use App\Models\Media;
use Illuminate\Support\Facades\Log;

class FrontAiImageModel extends Component
{

    public $show = false;
    public $modalTitle = 'Default Title';
    public $pre_configured_prompt = '';
    public $prompt_name;
    public $recordId;
    public $docID;
    public $loading= false;
    public $ai_output;
    protected $AI;
    public $selectedModel ;
    public $is_verified;
    public $ai_prompt;
    public $ai_model;
    public $conflict_prompt;
    public $verified_output;
    public $final_output;

    protected $listeners = ['openImageModel' => 'open', 'closeImageModal' => 'close'];

    public function mount()
    {
        $this->selectedModel = 'chatgpt';
        Log::error('model : ' . $this->selectedModel);
        $this->AI = new \App\Services\AIService($this->selectedModel);
    }

    // public function updatedSelectedModel()
    // {
    //     if ($this->AI) {
    //         $this->AI->setModelRef($this->selectedModel);
    //     } else {
    //         $this->AI = new \App\Services\AIService($this->selectedModel);
    //     }
    // }

    public function open($title = "Default Title", $recordId = null, $document_id)
    {
        $this->show = true;
        $this->recordId = $recordId;
        $this->docID = $document_id;

        $document = Document::find($document_id);
        Log::error('document : ' . $document);
        $attach_prompt = PromptAttach::where('resource_id', $recordId)->first();
        Log::error('attach_prompt : ' . $attach_prompt);
        $this->modalTitle = $document?->title ?? 'AI Integrations';
        Log::error('modal_title : ' . $this->modalTitle);

        $this->is_verified = $attach_prompt->prompt?->is_verified;
        Log::error('is_verified : ' . $this->is_verified);

        $this->ai_model = $attach_prompt->prompt?->ai_verification_model;
        Log::error('ai_model : ' . $this->ai_model);

        $this->pre_configured_prompt = str_replace(
            ['{document_name}', '{document_image}', '{primary_keyword}', '{short_description}'],
            [
                $document?->title ?? '',
                'https://google.com',
                $document?->primary_keywords ?? $document?->title,
                $document?->short_description ?? ''
            ],
            $attach_prompt->prompt?->updated_prompt ?? 'No AI Integration Found.'
        );

        Log::error('pre_configured_prompt : ' . $this->pre_configured_prompt);

        $this->prompt_name = $attach_prompt->prompt?->name ?? 'Not found';
        
        Log::error('prompt_name : ' . $this->prompt_name);

        $this->dispatch('refreshComponent');
        $this->startAiProcessing();
        
    }
    
    public function startAiProcessing()
    {
        $this->loading = true;
        $this->dispatch('runAiImageGenerator');
        // $this->sendImagePrompt();
        Log::info('start runAiImageGenerator');
    }

    public function regenerateResponse(){
        $this->loading = true;
        $this->dispatch('runAiImageGenerator');
        // $this->sendImagePrompt();
        Log::info('start runAiImageGenerator');
    }

    public function sendImagePrompt()
    {
        Log::info('sendPrompt called with pre_configured_prompt');
        $this->loading = true;

        if (!$this->AI) {
            $this->AI = new \App\Services\AIService('chatgpt');
        }

        $promptText = $this->pre_configured_prompt;
        $media = $this->AI->generateAndStoreImageWithOpenAI($promptText);
        Log::info('media: ' . $media);

        if ($media && isset($media['file_path'])) {
            $publicUrl = asset('storage/' . $media['file_path']);
        
            $this->ai_output = '<img src="' . $publicUrl . '" alt="Generated Image" style="max-width:100%;" id="'.$media['id'].'">';
            Log::info('Image URL shown in frontend: ' . $publicUrl);
        } else {
            $this->ai_output = 'Failed to generate image.';
        }
        
        $this->loading = false;
        Log::info('sendPrompt completed with ai_output: ' . $this->ai_output);
    }
 
    
    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.front-ai-image-model');
    }

}

