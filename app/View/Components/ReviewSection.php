<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReviewSection extends Component
{
    /**
     * Create a new component instance.
     */
    public $reviews;
    public $data;


    public function __construct($reviews = [], $data = [])
    {

        $this->reviews = $reviews ?? [];
        $this->data = $data ?? [];
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view('components.review-section', [
       
            'reviews' => $this->reviews,
            'data' => $this->data
        ]);
    }
}
