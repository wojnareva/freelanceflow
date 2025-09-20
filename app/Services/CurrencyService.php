<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    private const CACHE_TTL = 3600; // 1 hour

    private const BASE_CURRENCY = 'USD';

    /**
     * Get exchange rates for all supported currencies
     */
    public function getExchangeRates(): array
    {
        return Cache::remember('currency_exchange_rates', self::CACHE_TTL, function () {
            try {
                // In a real application, you would use a service like:
                // - ExchangeRate-API
                // - Fixer.io
                // - CurrencyLayer
                // For demo purposes, we'll use static rates

                return $this->getStaticExchangeRates();
            } catch (\Exception $e) {
                Log::warning('Failed to fetch exchange rates: '.$e->getMessage());

                return $this->getStaticExchangeRates();
            }
        });
    }

    /**
     * Get static exchange rates for demo purposes
     */
    private function getStaticExchangeRates(): array
    {
        return [
            'USD' => 1.0,
            'EUR' => 0.85,
            'GBP' => 0.73,
            'CAD' => 1.35,
            'AUD' => 1.52,
            'JPY' => 110.0,
            'CHF' => 0.92,
            'NOK' => 8.5,
            'SEK' => 8.8,
            'DKK' => 6.3,
            'PLN' => 3.9,
            'CZK' => 22.0,
        ];
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, Currency $from, Currency $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->getExchangeRates();

        // Convert to USD first if not already
        $usdAmount = $from->value === self::BASE_CURRENCY
            ? $amount
            : $amount / $rates[$from->value];

        // Convert from USD to target currency
        $convertedAmount = $to->value === self::BASE_CURRENCY
            ? $usdAmount
            : $usdAmount * $rates[$to->value];

        return round($convertedAmount, $to->getDecimalPlaces());
    }

    /**
     * Format amount in the specified currency
     */
    public function format(float $amount, Currency $currency): string
    {
        return $currency->formatAmount($amount);
    }

    /**
     * Get the user's preferred currency (from user settings or default)
     */
    public function getUserCurrency(): Currency
    {
        $user = auth()->user();

        if ($user && isset($user->settings['preferred_currency'])) {
            return Currency::from($user->settings['preferred_currency']);
        }

        return Currency::USD; // Default currency
    }

    /**
     * Convert and format amount to user's preferred currency
     */
    public function convertAndFormat(float $amount, Currency $from, ?Currency $to = null): string
    {
        $targetCurrency = $to ?? $this->getUserCurrency();
        $convertedAmount = $this->convert($amount, $from, $targetCurrency);

        return $this->format($convertedAmount, $targetCurrency);
    }

    /**
     * Get popular currencies for dropdown selection
     */
    public function getPopularCurrencies(): array
    {
        return collect(Currency::getPopular())
            ->map(function (Currency $currency) {
                return [
                    'value' => $currency->value,
                    'label' => $currency->getSymbol().' '.$currency->getName(),
                    'symbol' => $currency->getSymbol(),
                ];
            })
            ->toArray();
    }

    /**
     * Get all currencies for dropdown selection
     */
    public function getAllCurrencies(): array
    {
        return collect(Currency::getAll())
            ->map(function (Currency $currency) {
                return [
                    'value' => $currency->value,
                    'label' => $currency->getSymbol().' '.$currency->getName(),
                    'symbol' => $currency->getSymbol(),
                ];
            })
            ->toArray();
    }

    /**
     * Get current exchange rate between two currencies
     */
    public function getExchangeRate(Currency $from, Currency $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        $rates = $this->getExchangeRates();

        // Get rate via USD
        $fromToUsd = 1 / $rates[$from->value];
        $usdToTarget = $rates[$to->value];

        return round($fromToUsd * $usdToTarget, 6);
    }

    /**
     * Get revenue data converted to user's preferred currency
     */
    public function getRevenueInUserCurrency(array $revenueData): array
    {
        $userCurrency = $this->getUserCurrency();

        return collect($revenueData)->map(function ($item) use ($userCurrency) {
            if (isset($item['amount']) && isset($item['currency'])) {
                $originalCurrency = Currency::from($item['currency']);
                $item['amount'] = $this->convert($item['amount'], $originalCurrency, $userCurrency);
                $item['currency'] = $userCurrency->value;
                $item['formatted_amount'] = $this->format($item['amount'], $userCurrency);
            }

            return $item;
        })->toArray();
    }
}
