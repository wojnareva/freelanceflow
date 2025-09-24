<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\LocalizationService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LocaleSelector extends Component  
{
    public $currentLocale;
    public $showDropdown = false;
    public $availableLocales = [];

    public function mount()
    {
        $this->currentLocale = app()->getLocale();
        $this->availableLocales = LocalizationService::getAvailableLocales();
    }

    public function changeLocale($locale)
    {
        // Validate locale
        if (!LocalizationService::isValidLocale($locale)) {
            session()->flash('error', 'Invalid locale selected.');
            return;
        }

        // Set session locale
        session(['locale' => $locale]);
        
        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        // Set application locale immediately (triggers middleware logic)
        app()->setLocale($locale);
        
        // Update component state for UI
        $this->currentLocale = $locale;
        $this->showDropdown = false;
        
        // Force full navigation to current URL (re-triggers middleware and full reload)
        return $this->redirect(request()->url());
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function render()
    {
        return view('livewire.locale-selector');
    }
}