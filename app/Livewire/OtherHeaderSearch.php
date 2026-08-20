<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\MetaData;

class OtherHeaderSearch extends Component
{
    public $search = '';
    public $class="";

    public function updatedSearch(){
        $this->class = !empty($this->search) ? 'outer_google_search' : '';
    }

    public function render()
    {
        $documents = Document::query()
        ->whereNotNull('slug'); // Ensure slug exists

        $searchWords = array_filter(explode(' ', trim($this->search))); // Clean and split search

        foreach ($searchWords as $word) {
            $documents->where('title', 'like', '%' . $word . '%');
        }

        $documents = $documents->where('published','1')->get();

        $keys = [
            'header_document_search_placeholder',
            'header_document_search_message'
            ];

            $results = MetaData::whereIn('key', $keys)->get()->keyBy('key');
            $data = [
            'header_document_search_placeholder' => $results['header_document_search_placeholder']->value ?? null,
            'header_document_search_message' => $results['header_document_search_message']->value ?? null,
            ];

        return view('livewire.other-header-search',['documents'=>$documents]);
    }
}
