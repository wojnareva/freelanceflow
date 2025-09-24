<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CalendarService;
use Illuminate\Support\Carbon;

class CalendarServiceTest extends TestCase
{
    /** @test */
    public function it_returns_correct_week_start_for_czech_locale()
    {
        app()->setLocale('cs');
        
        // Test with a Thursday (2024-12-26)
        $date = Carbon::create(2024, 12, 26);
        $weekStart = CalendarService::getWeekStart($date);
        
        // Should be Monday (2024-12-23)
        $this->assertEquals(Carbon::MONDAY, $weekStart->dayOfWeek);
        $this->assertEquals('2024-12-23', $weekStart->format('Y-m-d'));
    }
    
    /** @test */
    public function it_returns_correct_week_start_for_english_locale()
    {
        app()->setLocale('en');
        
        // Test with a Thursday (2024-12-26)
        $date = Carbon::create(2024, 12, 26);
        $weekStart = CalendarService::getWeekStart($date);
        
        // Should be Sunday (2024-12-22)
        $this->assertEquals(Carbon::SUNDAY, $weekStart->dayOfWeek);
        $this->assertEquals('2024-12-22', $weekStart->format('Y-m-d'));
    }
    
    /** @test */
    public function it_returns_correct_week_end_for_czech_locale()
    {
        app()->setLocale('cs');
        
        // Test with a Thursday (2024-12-26)
        $date = Carbon::create(2024, 12, 26);
        $weekEnd = CalendarService::getWeekEnd($date);
        
        // Should be Sunday (2024-12-29)
        $this->assertEquals(Carbon::SUNDAY, $weekEnd->dayOfWeek);
        $this->assertEquals('2024-12-29', $weekEnd->format('Y-m-d'));
    }
    
    /** @test */
    public function it_returns_correct_week_end_for_english_locale()
    {
        app()->setLocale('en');
        
        // Test with a Thursday (2024-12-26)
        $date = Carbon::create(2024, 12, 26);
        $weekEnd = CalendarService::getWeekEnd($date);
        
        // Should be Saturday (2024-12-28)
        $this->assertEquals(Carbon::SATURDAY, $weekEnd->dayOfWeek);
        $this->assertEquals('2024-12-28', $weekEnd->format('Y-m-d'));
    }
    
    /** @test */
    public function it_generates_correct_week_dates_for_czech_locale()
    {
        app()->setLocale('cs');
        
        // Test with a Thursday (2024-12-26)
        $date = Carbon::create(2024, 12, 26);
        $weekDates = CalendarService::getWeekDates($date);
        
        $this->assertCount(7, $weekDates);
        
        // First day should be Monday
        $this->assertEquals(Carbon::MONDAY, $weekDates[0]['date']->dayOfWeek);
        $this->assertEquals('2024-12-23', $weekDates[0]['date']->format('Y-m-d'));
        
        // Last day should be Sunday  
        $this->assertEquals(Carbon::SUNDAY, $weekDates[6]['date']->dayOfWeek);
        $this->assertEquals('2024-12-29', $weekDates[6]['date']->format('Y-m-d'));
    }
    
    /** @test */
    public function it_generates_correct_week_dates_for_english_locale()
    {
        app()->setLocale('en');
        
        // Test with a Thursday (2024-12-26)
        $date = Carbon::create(2024, 12, 26);
        $weekDates = CalendarService::getWeekDates($date);
        
        $this->assertCount(7, $weekDates);
        
        // First day should be Sunday
        $this->assertEquals(Carbon::SUNDAY, $weekDates[0]['date']->dayOfWeek);
        $this->assertEquals('2024-12-22', $weekDates[0]['date']->format('Y-m-d'));
        
        // Last day should be Saturday
        $this->assertEquals(Carbon::SATURDAY, $weekDates[6]['date']->dayOfWeek);
        $this->assertEquals('2024-12-28', $weekDates[6]['date']->format('Y-m-d'));
    }
    
    /** @test */
    public function it_generates_month_calendar_with_correct_structure()
    {
        app()->setLocale('cs');
        
        $date = Carbon::create(2024, 12, 15);
        $calendar = CalendarService::getMonthCalendar($date);
        
        $this->assertEquals(12, $calendar['month']);
        $this->assertEquals(2024, $calendar['year']);
        $this->assertArrayHasKey('weeks', $calendar);
        $this->assertArrayHasKey('dayNames', $calendar);
        $this->assertArrayHasKey('firstDayOfWeek', $calendar);
        
        $this->assertEquals(1, $calendar['firstDayOfWeek']); // Monday for Czech
        
        // Check that we have the correct number of weeks
        $this->assertGreaterThanOrEqual(4, count($calendar['weeks']));
        $this->assertLessThanOrEqual(6, count($calendar['weeks']));
        
        // Each week should have 7 days
        foreach ($calendar['weeks'] as $week) {
            $this->assertCount(7, $week);
        }
        
        // First day name should be Monday for Czech
        $this->assertEquals('Pondělí', $calendar['dayNames'][0]);
    }
    
    /** @test */
    public function it_creates_carbon_dates_with_correct_locale_settings()
    {
        app()->setLocale('cs');
        
        $date = CalendarService::createDate('2024-12-26');
        
        $this->assertEquals('2024-12-26', $date->format('Y-m-d'));
    }
    
    /** @test */
    public function it_returns_correct_calendar_config()
    {
        app()->setLocale('cs');
        $config = CalendarService::getConfig();
        
        $this->assertEquals('cs', $config['locale']);
        $this->assertEquals(1, $config['firstDay']);
        
        app()->setLocale('en');
        $config = CalendarService::getConfig();
        
        $this->assertEquals('en', $config['locale']);
        $this->assertEquals(0, $config['firstDay']);
    }
}