<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TextFormComponent extends Component
{
    public $label;

    public $varname;

    public $placeholder;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $varname, $placeholder)
    {
        $this->label = $label;
        $this->varname = $varname;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.text-form-component');
    }
}
