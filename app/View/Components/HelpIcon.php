<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HelpIcon extends Component
{
    public string $text;
    public string $position;
    public string $size;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $text,
        string $position = 'top',
        string $size = 'small'
    ) {
        $this->text = $text;
        $this->position = $position;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.help-icon');
    }
}
