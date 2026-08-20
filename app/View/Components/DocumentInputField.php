<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DocumentInputField extends Component
{
    /**
     * Create a new component instance.
     */
    public $type;
    public $label;
    public $name;
    public $id;
    public $class;
    public $value;
    public $options;
    public $alwaysActive;
    public $error;

    public function __construct(
        $type = '',
        $label = '',
        $name,
        $id = null,
        $class = '',
        $value = null,
        $options = [],
        $alwaysActive = false,
        $error = false
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->class = $class;
        $this->value = $value;
        $this->options = $options;
        $this->alwaysActive = $alwaysActive;
        $this->error = $error;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.document-input-field');
    }
}
