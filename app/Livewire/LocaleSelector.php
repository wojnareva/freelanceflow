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
            return;
        }

        // Set session locale
        session(['locale' => $locale]);
        
        // Update user preference if authenticated
        if (Auth::check()) {
            /** @var User|null $user */
            $user = Auth::user();
            if ($user) {
                $user->update(['locale' => $locale]);
            }
        }
        
        // Apply locale immediately within this request
        app()->setLocale($locale);
        
        // Update component state for immediate UI feedback
        $this->currentLocale = $locale;
        $this->showDropdown = false;
        
        // Force a full navigation so middleware re-runs with updated session
        return $this->redirect(request()->url(), navigate: true);
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