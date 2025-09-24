<?php

namespace App\Services;

use App\Helpers\LocaleHelper;
use Illuminate\Support\Carbon;

class CalendarService
{
    /**
     * Get calendar configuration for frontend components
     */
    public static function getConfig(): array
    {
        return LocaleHelper::getCalendarConfig();
    }
    
    /**
     * Get week start day for the current locale
     */
    public static function getWeekStartDay(): int
    {
        return LocaleHelper::getFirstDayOfWeek();
    }
    
    /**
     * Get localized day names in the correct order (starting with the first day of the week)
     */
    public static function getDayNames(): array
    {
        return LocaleHelper::getDayNamesOrdered();
    }
    
    /**
     * Get localized short day names in the correct order
     */
    public static function getDayNamesShort(): array
    {
        return LocaleHelper::getDayNamesShortOrdered();
    }
    
    /**
     * Create a Carbon instance with the correct week start day for the current locale
     */
    public static function createDate($date = null): Carbon
    {
        LocaleHelper::configureCarbon();
        
        if ($date) {
            return Carbon::parse($date);
        }
        
        return Carbon::now();
    }
    
    /**
     * Get the current week start date
     */
    public static function getWeekStart(?Carbon $date = null): Carbon
    {
        $date = $date ?? static::createDate();
        $firstDayOfWeek = LocaleHelper::getFirstDayOfWeek();
        
        // If locale wants Monday first (Czech)
        if ($firstDayOfWeek === 1) {
            return $date->copy()->startOfWeek(Carbon::MONDAY);
        }
        
        // Default Sunday first (English)
        return $date->copy()->startOfWeek(Carbon::SUNDAY);
    }
    
    /**
     * Get the current week end date
     */
    public static function getWeekEnd(?Carbon $date = null): Carbon
    {
        $weekStart = static::getWeekStart($date);
        return $weekStart->copy()->addDays(6);
    }
    
    /**
     * Get the current month start date
     */
    public static function getMonthStart(?Carbon $date = null): Carbon
    {
        $date = $date ?? static::createDate();
        
        return $date->copy()->startOfMonth();
    }
    
    /**
     * Get the current month end date
     */
    public static function getMonthEnd(?Carbon $date = null): Carbon
    {
        $date = $date ?? static::createDate();
        
        return $date->copy()->endOfMonth();
    }
    
    /**
     * Get calendar data for a specific month
     */
    public static function getMonthCalendar(?Carbon $date = null): array
    {
        $date = $date ?? static::createDate();
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();
        
        // Calculate calendar start (first day of the week containing the first day of the month)
        $calendarStart = $monthStart->copy()->startOfWeek();
        
        // Calculate calendar end (last day of the week containing the last day of the month)
        $calendarEnd = $monthEnd->copy()->endOfWeek();
        
        $weeks = [];
        $currentDate = $calendarStart->copy();
        
        while ($currentDate->lte($calendarEnd)) {
            $week = [];
            
            for ($i = 0; $i < 7; $i++) {
                $week[] = [
                    'date' => $currentDate->copy(),
                    'day' => $currentDate->day,
                    'isCurrentMonth' => $currentDate->month === $date->month,
                    'isToday' => $currentDate->isToday(),
                    'isWeekend' => $currentDate->isWeekend(),
                    'formatted' => LocaleHelper::formatDate($currentDate),
                ];
                
                $currentDate->addDay();
            }
            
            $weeks[] = $week;
        }
        
        return [
            'month' => $date->month,
            'year' => $date->year,
            'monthName' => $date->translatedFormat('F'),
            'weeks' => $weeks,
            'firstDayOfWeek' => static::getWeekStartDay(),
            'dayNames' => static::getDayNames(),
            'dayNamesShort' => static::getDayNamesShort(),
        ];
    }
    
    /**
     * Get dates for a specific week
     */
    public static function getWeekDates(?Carbon $date = null): array
    {
        $date = $date ?? static::createDate();
        $weekStart = static::getWeekStart($date);
        
        $dates = [];
        
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $weekStart->copy()->addDays($i);
            
            $dates[] = [
                'date' => $currentDate->copy(),
                'day' => $currentDate->day,
                'isToday' => $currentDate->isToday(),
                'isWeekend' => $currentDate->isWeekend(),
                'formatted' => LocaleHelper::formatDate($currentDate),
                'dayName' => $currentDate->translatedFormat('l'),
                'dayNameShort' => $currentDate->translatedFormat('D'),
            ];
        }
        
        return $dates;
    }
}