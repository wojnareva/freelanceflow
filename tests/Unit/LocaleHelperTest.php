<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\LocaleHelper;
use Illuminate\Support\Carbon;

class LocaleHelperTest extends TestCase
{
    /** @test */
    public function it_returns_monday_as_first_day_for_czech_locale()
    {
        app()->setLocale('cs');
        
        $this->assertEquals(1, LocaleHelper::getFirstDayOfWeek());
    }
    
    /** @test */
    public function it_returns_sunday_as_first_day_for_english_locale()
    {
        app()->setLocale('en');
        
        $this->assertEquals(0, LocaleHelper::getFirstDayOfWeek());
    }
    
    /** @test */
    public function it_provides_correct_calendar_config_for_czech()
    {
        app()->setLocale('cs');
        $config = LocaleHelper::getCalendarConfig();
        
        $this->assertEquals('cs', $config['locale']);
        $this->assertEquals(1, $config['firstDay']);
        $this->assertEquals('d.m.Y', $config['dateFormat']);
        $this->assertEquals('H:i', $config['timeFormat']);
        $this->assertContains('Pondělí', $config['dayNames']);
        $this->assertContains('Po', $config['dayNamesShort']);
    }
    
    /** @test */
    public function it_provides_correct_calendar_config_for_english()
    {
        app()->setLocale('en');
        $config = LocaleHelper::getCalendarConfig();
        
        $this->assertEquals('en', $config['locale']);
        $this->assertEquals(0, $config['firstDay']);
        $this->assertEquals('m/d/Y', $config['dateFormat']);
        $this->assertEquals('g:i A', $config['timeFormat']);
        $this->assertContains('Monday', $config['dayNames']);
        $this->assertContains('Mon', $config['dayNamesShort']);
    }
    
    /** @test */
    public function it_orders_day_names_correctly_for_czech_monday_start()
    {
        app()->setLocale('cs');
        $dayNames = LocaleHelper::getDayNamesOrdered();
        
        // Should start with Monday (Pondělí)
        $this->assertEquals('Pondělí', $dayNames[0]);
        $this->assertEquals('Neděle', $dayNames[6]);
    }
    
    /** @test */
    public function it_orders_day_names_correctly_for_english_sunday_start()
    {
        app()->setLocale('en');
        $dayNames = LocaleHelper::getDayNamesOrdered();
        
        // Should start with Sunday
        $this->assertEquals('Sunday', $dayNames[0]);
        $this->assertEquals('Saturday', $dayNames[6]);
    }
    
    /** @test */
    public function it_formats_dates_according_to_locale()
    {
        $date = Carbon::create(2024, 12, 25, 14, 30, 0);
        
        app()->setLocale('cs');
        $this->assertEquals('25.12.2024', LocaleHelper::formatDate($date));
        $this->assertEquals('14:30', LocaleHelper::formatTime($date));
        $this->assertEquals('25.12.2024 14:30', LocaleHelper::formatDateTime($date));
        
        app()->setLocale('en');
        $this->assertEquals('12/25/2024', LocaleHelper::formatDate($date));
        $this->assertEquals('2:30 PM', LocaleHelper::formatTime($date));
        $this->assertEquals('12/25/2024 2:30 PM', LocaleHelper::formatDateTime($date));
    }
    
    /** @test */
    public function it_handles_null_dates_gracefully()
    {
        $this->assertNull(LocaleHelper::formatDate(null));
        $this->assertNull(LocaleHelper::formatTime(null));
        $this->assertNull(LocaleHelper::formatDateTime(null));
    }
}