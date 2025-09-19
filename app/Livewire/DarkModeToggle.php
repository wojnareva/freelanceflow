<?php

namespace App\Livewire;

use Livewire\Component;

class DarkModeToggle extends Component
{
    public $darkMode = false;

    public function mount()
    {
        // Get dark mode preference from browser localStorage or default to false
        $this->darkMode = request()->cookie('dark-mode', false);
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        
        // Set cookie to remember preference
        cookie()->queue('dark-mode', $this->darkMode ? 'true' : 'false', 60 * 24 * 365); // 1 year
        
        $this->dispatch('dark-mode-toggled', darkMode: $this->darkMode);
    }

    public function render()
    {
        return view('livewire.dark-mode-toggle');
    }
}
