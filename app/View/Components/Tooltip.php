<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tooltip extends Component
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
        string $size = 'medium'
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
        return view('components.tooltip');
    }

    /**
     * Get tooltip classes based on position
     */
    public function getPositionClasses(): string
    {
        return match ($this->position) {
            'top' => 'bottom-full left-1/2 transform -translate-x-1/2 mb-2',
            'bottom' => 'top-full left-1/2 transform -translate-x-1/2 mt-2',
            'left' => 'right-full top-1/2 transform -translate-y-1/2 mr-2',
            'right' => 'left-full top-1/2 transform -translate-y-1/2 ml-2',
            default => 'bottom-full left-1/2 transform -translate-x-1/2 mb-2',
        };
    }

    /**
     * Get arrow classes based on position
     */
    public function getArrowClasses(): string
    {
        return match ($this->position) {
            'top' => 'top-full left-1/2 transform -translate-x-1/2 border-l-transparent border-r-transparent border-t-gray-800 border-b-transparent',
            'bottom' => 'bottom-full left-1/2 transform -translate-x-1/2 border-l-transparent border-r-transparent border-b-gray-800 border-t-transparent',
            'left' => 'left-full top-1/2 transform -translate-y-1/2 border-t-transparent border-b-transparent border-l-gray-800 border-r-transparent',
            'right' => 'right-full top-1/2 transform -translate-y-1/2 border-t-transparent border-b-transparent border-r-gray-800 border-l-transparent',
            default => 'top-full left-1/2 transform -translate-x-1/2 border-l-transparent border-r-transparent border-t-gray-800 border-b-transparent',
        };
    }

    /**
     * Get size classes
     */
    public function getSizeClasses(): string
    {
        return match ($this->size) {
            'small' => 'px-2 py-1 text-xs max-w-xs',
            'medium' => 'px-3 py-2 text-sm max-w-sm',
            'large' => 'px-4 py-3 text-base max-w-md',
            default => 'px-3 py-2 text-sm max-w-sm',
        };
    }
}
