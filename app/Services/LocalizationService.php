<?php

namespace App\Services;

use NumberFormatter;
use Carbon\Carbon;

class LocalizationService
{
    /**
     * Format money according to user's locale and currency.
     */
    public static function formatMoney($amount, $currency = null): string
    {
        $locale = app()->getLocale();
        $user = auth()->user();
        $currency = $currency ?? $user?->currency ?? ($locale === 'cs' ? 'CZK' : 'USD');
        $numberFormat = $user?->number_format ?? 'czech_space';
        
        if ($locale === 'cs') {
            // Czech formatting with two options
            if ($currency === 'CZK') {
                if ($numberFormat === 'czech_space') {
                    // Format: 1 234,50 Kč (with thin space)
                    return number_format($amount, 2, ',', ' ') . ' Kč';
                } else {
                    // Format: 1.234,50 Kč (with dot)
                    return number_format($amount, 2, ',', '.') . ' Kč';
                }
            }
            
            // For other currencies, use NumberFormatter with custom pattern
            $formatter = new NumberFormatter('cs_CZ', NumberFormatter::CURRENCY);
            if ($numberFormat === 'czech_space') {
                $formatter->setPattern('#,##0.00 ¤');
                $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ' ');
            }
            return $formatter->formatCurrency($amount, $currency);
        }
        
        // English formatting
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }
    
    /**
     * Format number according to user's locale.
     */
    public static function formatNumber($number, $decimals = 2): string 
    {
        $locale = app()->getLocale();
        $user = auth()->user();
        $numberFormat = $user?->number_format ?? 'czech_space';
        
        if ($locale === 'cs') {
            if ($numberFormat === 'czech_space') {
                // Czech with space: 1 234,50
                return number_format($number, $decimals, ',', ' ');
            } else {
                // Czech with dot: 1.234,50
                return number_format($number, $decimals, ',', '.');
            }
        }
        
        // English: 2,700.50  
        return number_format($number, $decimals, '.', ',');
    }
    
    /**
     * Format date according to user's locale.
     */
    public static function formatDate($date, $format = null): string
    {
        $locale = app()->getLocale();
        $carbon = Carbon::parse($date);
        
        if ($locale === 'cs') {
            $carbon->locale('cs');
            return $format ? $carbon->translatedFormat($format) : $carbon->translatedFormat('d. m. Y');
        }
        
        return $carbon->format($format ?? 'Y-m-d');
    }
    
    /**
     * Format datetime according to user's locale.
     */
    public static function formatDateTime($datetime, $format = null): string
    {
        $locale = app()->getLocale();
        $carbon = Carbon::parse($datetime);
        
        if ($locale === 'cs') {
            $carbon->locale('cs');
            $defaultFormat = 'j. n. Y v H:i';
            return $carbon->translatedFormat($format ?? $defaultFormat);
        }
        
        return $carbon->format($format ?? 'Y-m-d H:i');
    }
    
    /**
     * Get available locales configuration.
     */
    public static function getAvailableLocales(): array
    {
        return config('app.available_locales', [
            'cs' => ['name' => 'Čeština', 'flag' => '🇨🇿', 'code' => 'cs'],
            'en' => ['name' => 'English', 'flag' => '🇺🇸', 'code' => 'en'],
        ]);
    }
    
    /**
     * Check if locale is supported.
     */
    public static function isValidLocale(string $locale): bool
    {
        return array_key_exists($locale, self::getAvailableLocales());
    }
    
    /**
     * Get current locale information.
     */
    public static function getCurrentLocaleInfo(): array
    {
        $locale = app()->getLocale();
        $locales = self::getAvailableLocales();
        
        return $locales[$locale] ?? $locales['cs'];
    }
    
    /**
     * Format time according to user's locale.
     */
    public static function formatTime($time, $format = null): string
    {
        $locale = app()->getLocale();
        $carbon = Carbon::parse($time);
        
        if ($locale === 'cs') {
            $carbon->locale('cs');
            return $carbon->translatedFormat($format ?? 'H:i');
        }
        
        return $carbon->format($format ?? 'H:i');
    }
    
    /**
     * Get user's timezone or default.
     */
    public static function getUserTimezone(): string
    {
        return auth()->user()?->timezone ?? config('app.timezone', 'Europe/Prague');
    }
    
    /**
     * Convert to user's timezone and format.
     */
    public static function formatDateTimeInUserTimezone($datetime, $format = null): string
    {
        $carbon = Carbon::parse($datetime)->setTimezone(self::getUserTimezone());
        return self::formatDateTime($carbon, $format);
    }

    /**
     * Get the first day of the week for the current locale.
     * (0 for Sunday, 1 for Monday)
     */
    public static function getWeekStartsOn(): int
    {
        $locale = app()->getLocale();

        return $locale === 'cs' ? 1 : 0;
    }
}