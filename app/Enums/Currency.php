<?php

namespace App\Enums;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case JPY = 'JPY';
    case CHF = 'CHF';
    case NOK = 'NOK';
    case SEK = 'SEK';
    case DKK = 'DKK';
    case PLN = 'PLN';
    case CZK = 'CZK';

    public function getSymbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::GBP => '£',
            self::CAD => 'C$',
            self::AUD => 'A$',
            self::JPY => '¥',
            self::CHF => 'CHF',
            self::NOK => 'kr',
            self::SEK => 'kr',
            self::DKK => 'kr',
            self::PLN => 'zł',
            self::CZK => 'Kč',
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::GBP => 'British Pound',
            self::CAD => 'Canadian Dollar',
            self::AUD => 'Australian Dollar',
            self::JPY => 'Japanese Yen',
            self::CHF => 'Swiss Franc',
            self::NOK => 'Norwegian Krone',
            self::SEK => 'Swedish Krona',
            self::DKK => 'Danish Krone',
            self::PLN => 'Polish Złoty',
            self::CZK => 'Czech Koruna',
        };
    }

    public function getDecimalPlaces(): int
    {
        return match ($this) {
            self::JPY => 0, // Yen doesn't use decimal places
            default => 2,
        };
    }

    public static function getPopular(): array
    {
        return [
            self::USD,
            self::EUR,
            self::GBP,
            self::CAD,
            self::AUD,
        ];
    }

    public static function getAll(): array
    {
        return self::cases();
    }

    public function formatAmount(float $amount): string
    {
        // Use LocalizationService for proper formatting based on locale
        $localizationService = app(\App\Services\LocalizationService::class);
        return $localizationService->formatMoney($amount, $this->value);
    }
}
