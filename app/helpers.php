<?php

use App\Services\LocalizationService;

if (!function_exists('format_money')) {
    /**
     * Format money according to user's locale and currency.
     */
    function format_money($amount, $currency = null): string
    {
        return LocalizationService::formatMoney($amount, $currency);
    }
}

if (!function_exists('format_number')) {
    /**
     * Format number according to user's locale.
     */
    function format_number($number, $decimals = 2): string
    {
        return LocalizationService::formatNumber($number, $decimals);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date according to user's locale.
     */
    function format_date($date, $format = null): string
    {
        return LocalizationService::formatDate($date, $format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime according to user's locale.
     */
    function format_datetime($datetime, $format = null): string
    {
        return LocalizationService::formatDateTime($datetime, $format);
    }
}

if (!function_exists('format_time')) {
    /**
     * Format time according to user's locale.
     */
    function format_time($time, $format = null): string
    {
        return LocalizationService::formatTime($time, $format);
    }
}

if (!function_exists('is_czech_locale')) {
    /**
     * Check if current locale is Czech.
     */
    function is_czech_locale(): bool
    {
        return app()->getLocale() === 'cs';
    }
}

if (!function_exists('get_locale_info')) {
    /**
     * Get current locale information.
     */
    function get_locale_info(): array
    {
        return LocalizationService::getCurrentLocaleInfo();
    }
}