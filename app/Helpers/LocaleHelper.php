<?php

namespace App\Helpers;

use App\Services\LocalizationService;
use Illuminate\Support\Carbon;

class LocaleHelper
{
    /**
     * Configure Carbon locale settings based on the current app locale
     */
    public static function configureCarbon(): void
    {
        $locale = app()->getLocale();

        // Set Carbon locale
        Carbon::setLocale($locale);

        // Note: Carbon doesn't have global setWeekStartsAt method
        // Instead, we handle week start/end in individual methods
        // This method mainly sets up the locale for Carbon
    }

    /**
     * Get the first day of the week for the current locale (0 = Sunday, 1 = Monday)
     */
    public static function getFirstDayOfWeek(): int
    {
        return LocalizationService::getWeekStartsOn();
    }

    /**
     * Get calendar configuration for JavaScript components
     */
    public static function getCalendarConfig(): array
    {
        $locale = app()->getLocale();

        $config = [
            'locale' => $locale,
            'firstDay' => static::getFirstDayOfWeek(),
            'weekStartsAt' => static::getFirstDayOfWeek(),
        ];

        // Add locale-specific settings
        match ($locale) {
            'cs' => $config = array_merge($config, [
                'dateFormat' => 'd.m.Y',
                'timeFormat' => 'H:i',
                'dateTimeFormat' => 'd.m.Y H:i',
                'monthNames' => [
                    'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen',
                    'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec',
                ],
                'monthNamesShort' => [
                    'Led', 'Úno', 'Bře', 'Dub', 'Kvě', 'Čer',
                    'Čec', 'Srp', 'Zář', 'Říj', 'Lis', 'Pro',
                ],
                'dayNames' => [
                    'Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota',
                ],
                'dayNamesShort' => ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
                'dayNamesMin' => ['N', 'P', 'Ú', 'S', 'Č', 'P', 'S'],
            ]),
            'en' => $config = array_merge($config, [
                'dateFormat' => 'm/d/Y',
                'timeFormat' => 'g:i A',
                'dateTimeFormat' => 'm/d/Y g:i A',
                'monthNames' => [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December',
                ],
                'monthNamesShort' => [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
                ],
                'dayNames' => [
                    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
                ],
                'dayNamesShort' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                'dayNamesMin' => ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            ])
        };

        return $config;
    }

    /**
     * Get translated day names starting from the first day of the week
     */
    public static function getDayNamesOrdered(): array
    {
        $config = static::getCalendarConfig();
        $dayNames = $config['dayNames'];
        $firstDay = $config['firstDay'];

        // Reorder days starting from the first day of the week
        if ($firstDay === 1) { // Monday first
            return array_merge(array_slice($dayNames, 1), [$dayNames[0]]);
        }

        return $dayNames; // Sunday first
    }

    /**
     * Get short day names starting from the first day of the week
     */
    public static function getDayNamesShortOrdered(): array
    {
        $config = static::getCalendarConfig();
        $dayNames = $config['dayNamesShort'];
        $firstDay = $config['firstDay'];

        // Reorder days starting from the first day of the week
        if ($firstDay === 1) { // Monday first
            return array_merge(array_slice($dayNames, 1), [$dayNames[0]]);
        }

        return $dayNames; // Sunday first
    }

    /**
     * Format date according to current locale
     */
    public static function formatDate(?Carbon $date = null): ?string
    {
        if (! $date) {
            return null;
        }

        $config = static::getCalendarConfig();

        return $date->format($config['dateFormat']);
    }

    /**
     * Format time according to current locale
     */
    public static function formatTime(?Carbon $date = null): ?string
    {
        if (! $date) {
            return null;
        }

        $config = static::getCalendarConfig();

        return $date->format($config['timeFormat']);
    }

    /**
     * Format datetime according to current locale
     */
    public static function formatDateTime(?Carbon $date = null): ?string
    {
        if (! $date) {
            return null;
        }

        $config = static::getCalendarConfig();

        return $date->format($config['dateTimeFormat']);
    }
}
