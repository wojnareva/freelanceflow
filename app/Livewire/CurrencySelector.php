<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Services\CurrencyService;
use Livewire\Component;

class CurrencySelector extends Component
{
    public $selectedCurrency = '';

    public $showExchangeRate = false;

    public $baseCurrency = '';

    public $showAllCurrencies = false;

    public $onlyPopular = true;

    protected $listeners = ['currencyChanged' => 'handleCurrencyChange'];

    public function mount($value = null, $showExchangeRate = false, $baseCurrency = null, $showAllCurrencies = false)
    {
        $this->selectedCurrency = $value ?? Currency::USD->value;
        $this->showExchangeRate = $showExchangeRate;
        $this->baseCurrency = $baseCurrency ?? Currency::USD->value;
        $this->showAllCurrencies = $showAllCurrencies;
        $this->onlyPopular = ! $showAllCurrencies;
    }

    public function updatedSelectedCurrency($value)
    {
        $this->dispatch('currencySelected', currency: $value);
    }

    public function toggleCurrencyList()
    {
        $this->showAllCurrencies = ! $this->showAllCurrencies;
        $this->onlyPopular = ! $this->showAllCurrencies;
    }

    public function getExchangeRate()
    {
        if (! $this->showExchangeRate || $this->selectedCurrency === $this->baseCurrency) {
            return null;
        }

        $currencyService = app(CurrencyService::class);
        $from = Currency::from($this->baseCurrency);
        $to = Currency::from($this->selectedCurrency);

        return $currencyService->getExchangeRate($from, $to);
    }

    public function getCurrencies()
    {
        $currencyService = app(CurrencyService::class);

        return $this->onlyPopular
            ? $currencyService->getPopularCurrencies()
            : $currencyService->getAllCurrencies();
    }

    public function render()
    {
        return view('livewire.currency-selector', [
            'currencies' => $this->getCurrencies(),
            'exchangeRate' => $this->getExchangeRate(),
        ]);
    }
}
