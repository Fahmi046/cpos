<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProgressBar extends Component
{
    public $color;

    public function __construct($color = 'teal')
    {
        $this->color = $color;
    }

    public function render()
    {
        return view('components.progress-bar');
    }
}
