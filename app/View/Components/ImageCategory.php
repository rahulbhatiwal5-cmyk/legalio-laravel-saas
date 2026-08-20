<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageCategory extends Component
{
    /**
     * Create a new component instance.
     */
    public $category;
    public $path;

    public function __construct($category = null, $path = null)
    {
        $this->category = $category;
        $this->path = $path;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.image-category');
    }
}
