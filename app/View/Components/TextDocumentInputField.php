<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


class TextDocumentInputField extends Component
{
    public $type;
    public $name;
    public $id;
    public $label;
    public $value;
    public $class;
    public $questions;
    public $options;
    public $qu_conditions;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $type = 'text',
        $name,
        $id,
        $label = '',
        $value = '',
        $class = '',
        $questions = [],
        $options = [],
        $quConditions = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->class = $class;
        $this->questions = $questions;
        $this->options = $options;
        $this->qu_conditions = $quConditions;
    }

    /**
     * Get the view / contents.
     */
    public function render()
    {
        return view('components.text-document-input-field');
    }
}
