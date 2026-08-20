<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RatingComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public $class;
    public $rating;
    public $ratingClass;
    public $ratingText;

    public function __construct($class ='',$rating='',$ratingClass='',$ratingText='')
    {
        $this->class = $class;
        $this->rating = $rating;
        $this->ratingClass = $ratingClass;
        $this->ratingText = $ratingText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.rating-component');
    }
}
