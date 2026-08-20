<?php

namespace App\Livewire;
use App\Models\Document;
use Livewire\Component;

class UserDocumentSearch extends Component
{
    public $search= '';
    public function render()
    {
        $documents = Document::where('title', 'like', "{$this->search}%")->where('published','1')->get();
        return view('livewire.user-document-search',['documents'=>$documents]);
    }
}
