<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PromptAttach;
use App\Models\Document;
use App\Models\PromptVerification;
use App\Services\AIService;

class FrontDocumentAiModal extends Component
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

    protected $listeners = ['openDocumentModal' => 'open', 'closeDocumentModal' => 'close'];

    // public function __construct()
    // {
    //     $this->AI = app(AIService::class);
    // }
    public function mount()
    {
        $this->selectedModel = 'chatgpt';
        // $this->selectedModel = 'Gemini 2.0';  // default value to match your first option in $aiModelRefs
        $this->AI = new \App\Services\AIService($this->selectedModel);
    }
    
    public function updatedSelectedModel()
    {
        if ($this->AI) {
            $this->AI->setModelRef($this->selectedModel);
        } else {
            $this->AI = new \App\Services\AIService($this->selectedModel);
        }
    }

    public function open($title = "Default Title", $recordId = null, $document_id)
    {
        $this->show = true;
        $this->recordId =  $recordId;
        $this->docID = $document_id;
        $available_documents = Document::where('published', '1')->get();
        $allDocumentsList = "";

        foreach ($available_documents as $avail_doc) {
            $allDocumentsList .= "- ID: {$avail_doc->id}, Title: {$avail_doc->title}, Description: {$avail_doc->short_description}\n";
        }

        $document = Document::find($document_id);
        $attach_prompt = PromptAttach::where('resource_id', $recordId)->first();
    
        $this->modalTitle = $document?->title ?? 'AI Integrations';
    
        $this->is_verified = $attach_prompt->prompt?->is_verified;
        $this->ai_model = $attach_prompt->prompt?->ai_verification_model;

        $this->pre_configured_prompt = str_replace(
            ['{document_name}', '{document_image}', '{primary_keyword}', '{short_description}', '{available_documents}'],
            [$document?->title ?? '', 'https://google.com', $document?->primary_keywords ?? $document?->title, $document?->short_description ?? '', $allDocumentsList ?? '' ],
            $attach_prompt->prompt->updated_prompt ?? 'No AI Integration Found.'
        );
        
        $this->prompt_name = $attach_prompt->prompt->name ?? 'Not found';
        $this->dispatch('refreshComponent');
        $this->startAiProcessing();
    }
    

    public function startAiProcessing()
    {
        $this->loading = true;
        $this->dispatch('processAiPrompt');
    }

    public function regenerateResponse(){
        $this->loading = true;
        $this->dispatch('processAiPrompt');
    }

    public function sendPrompt()
    {
        $this->loading = true;

        if(!$this->AI){
            $this->AI = new \App\Services\AIService($this->selectedModel); // fallback
        }
    
        if($this->is_verified === 1){
            $document= Document::find($this->docID);

            $promptVerification = PromptVerification::first();
            $this->ai_prompt = str_replace(
                ['{document_name}'],[$document?->title ?? ''], 
                $promptVerification->ai_prompt ?? 'No AI Integration Found.'
            );
            $this->conflict_prompt = str_replace(
                ['{document_name}'],[$document?->title ?? ''],
                $promptVerification->conflict_prompt ?? 'No AI Integration Found.'
            );

            $output = $this->AI->generateText($this->pre_configured_prompt);
            $this->ai_output=$output;

            $this->verified_output = $this->AI->aiVerificationPrompt(
                $this->ai_prompt,
                $this->ai_output,
                $this->ai_model
            );

            $this->final_output = $this->AI->aiVerificationPrompt(
                $this->conflict_prompt,
                $this->verified_output,
                $this->selectedModel
            );

            $this->ai_output=$this->final_output;
        }else{

            $output = $this->AI->generateText($this->pre_configured_prompt);
            $this->ai_output=$output;
        }

        
        $this->loading=false;
    }


    public function close()
    {
        $this->show = false;
    }


    
    public function render()
    {
        return view('livewire.front-document-ai-modal');
    }
}

