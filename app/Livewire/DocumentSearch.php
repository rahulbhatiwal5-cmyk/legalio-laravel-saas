<?php

// namespace App\Livewire;

// use Livewire\Component;
// use App\Models\Document;

// class DocumentSearch extends Component
// {
//     public $search = '';
//     public $class="";
//     public $categoryId = null;



//     public function updatedSearch(){
//         $this->class = !empty($this->search) ? 'outer_google_search' : '';
//     }

//     public function render()
//     {

//         $documents = Document::query()
//         ->whereNotNull('slug'); // Ensure slug exists

//         if ($this->categoryId) {
//             $documents->whereHas('categories', function ($q) {
//                 $q->where('categories.id', $this->categoryId);
//             });
//         }
    
//     $searchWords = array_filter(explode(' ', trim($this->search))); // Clean and split search
    
//     foreach ($searchWords as $word) {
//         $documents->where('title', 'like', '%' . $word . '%');
//     }
    
//     $documents = $documents->where('published','1')->get();

//         return view('livewire.document-search',['documents'=>$documents]);
//     }

//     public function search() 
//     {
//         return redirect()->route('user.search', [
//             'q' => $this->searchQuery
//         ]);
//     }
// }


namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\MetaData;

class DocumentSearch extends Component
{
    public $search = '';
    public $class  = '';

    public function updatedSearch()
    {
        $this->class = !empty(trim($this->search)) ? 'outer_google_search' : '';
    }

 
    public function goToResults()
    {
        $term = trim($this->search);

        if ($term === '') {
            return redirect()->route('user.search');
        }

        return redirect()->route('user.search', ['q' => $term]);
    }

    public function render()
    {
        $documents = Document::query()
            ->whereNotNull('slug')
            ->where('published', '1');

        $searchWords = array_filter(explode(' ', trim($this->search)));

        foreach ($searchWords as $word) {
            $documents->where('title', 'like', '%' . $word . '%');
        }

        $documents = $documents->get();

        $keys = [
            'header_document_search_placeholder',
            'header_document_search_message',
        ];

        $results = MetaData::whereIn('key', $keys)->get()->keyBy('key');

        $data = [
            'header_document_search_placeholder' => $results['header_document_search_placeholder']->value ?? null,
            'header_document_search_message'     => $results['header_document_search_message']->value ?? null,
        ];

        return view('livewire.document-search', [
            'documents' => $documents,
            'data'      => $data,
        ]);
    }
}