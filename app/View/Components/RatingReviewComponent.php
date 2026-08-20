<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RatingReviewComponent extends Component
{
    /**
     * Create a new component instance.
     */
 
    public $ratingText;
    public $reviewHaceText;
    
    public function __construct($ratingText = '', $reviewHaceText = '')
    {
        $this->ratingText = $ratingText;
        $this->reviewHaceText = $reviewHaceText;
        
    }
     
    
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.rating-review-component');
    }
}
