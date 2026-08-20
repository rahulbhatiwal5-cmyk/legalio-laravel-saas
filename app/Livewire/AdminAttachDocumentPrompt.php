<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Prompt;
use App\Models\PromptAttach;

class AdminAttachDocumentPrompt extends Component
{
    public $prompts;
    public $attachedPrompts = [];

    public function mount()
    {
        $this->attachedPrompts =PromptAttach::all();
        $this->prompts=Prompt::all();
    }


    public function savePromptSelection(  $resourceId,$promptId)
    {
        // dd($resourceId,$promptId);
        $attached_dox = PromptAttach::where('resource_id', $resourceId)->first();

        // dd($attached_dox);

        if ($attached_dox) {
            $attached_dox->update(['prompt_id' => $promptId]);
        }


    }

    public function render()
    {

        return view('livewire.admin-attach-document-prompt');
    }
}
