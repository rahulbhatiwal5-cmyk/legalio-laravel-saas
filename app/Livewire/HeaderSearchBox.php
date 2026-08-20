<?php

namespace App\Livewire;
use App\Models\Document;
use App\Models\MetaData;

use Livewire\Component;

class HeaderSearchBox extends Component
{

    public $search = '';
    public $class="";

    public function goToResults()
    {
        $term = trim($this->search);
    
        if ($term === '') {
            return redirect()->route('user.search');
        }
    
        return redirect()->route('user.search', ['q' => $term]);
    }
    
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
            'header_search_placeholder'
            ];

            $results = MetaData::whereIn('key', $keys)->get()->keyBy('key');
            $data = [
            'header_search_placeholder' => $results['header_search_placeholder']->value ?? null,
            ];
        return view('livewire.header-search-box',['documents'=>$documents,'data'=> $data]);
    }
}
