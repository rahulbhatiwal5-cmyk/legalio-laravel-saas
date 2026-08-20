<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GoogleInput extends Component
{
    /**
     * Create a new component instance.
     */

        public $type, $name, $id, $label, $value, $error, $alwaysActive, $options;

        public function __construct(
            $type = 'text',
            $name,
            $id = null,
            $label = '',
            $value = '',
            $error = false,
            $alwaysActive = false,
            $options = []
        ) {
            $this->type = $type;
            $this->name = $name;
            $this->id = $id ?? $name;
            $this->label = $label;
            $this->value = $value;
            $this->error = $error;
            $this->options = $options;
        
            // Automatically set active if value is present
            $this->alwaysActive = $alwaysActive || !empty($value);
        }
        
    

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.google-input');
    }
}
