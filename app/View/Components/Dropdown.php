<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $id;
    public $option;
    public $selected;
    public function __construct($name,$id,$option=[],$selected=null)
    {
        $this->name=$name;
        $this->id=$id;
        $this->option=$option;
        $this->selected=$selected;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown');
    }
}
