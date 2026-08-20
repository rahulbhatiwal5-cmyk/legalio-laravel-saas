<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchableDropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $id;
    public $options;
    public $selected;
    public $selectedText;

    public function __construct($name, $id, $options = [], $selected = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->options = $options;
        $this->selected = $selected;
        $this->selectedText = $options[$selected] ?? 'Select an Option';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.searchable-dropdown');
    }
}
