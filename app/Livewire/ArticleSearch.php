<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KnowledgeBaseArticle;

class ArticleSearch extends Component
{
    public $search = '';
    public $class="";



    public function updatedSearch(){
        $this->class = !empty($this->search) ? 'outer_google_search' : '';
    }

    public function render()
    {

        $articles = KnowledgeBaseArticle::query();

    $searchWords = array_filter(explode(' ', trim($this->search))); // Clean and split search

    foreach ($searchWords as $word) {
        $articles->where('title', 'like', '%' . $word . '%');
    }

    $articles = $articles->get();


        return view('livewire.article-search',['articles'=>$articles]);
    }


}
