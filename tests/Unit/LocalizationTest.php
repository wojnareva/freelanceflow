<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LocalizationService;
use App\Services\AresService;
use App\Rules\ValidIco;
use Carbon\Carbon;

class LocalizationTest extends TestCase
{
    /** @test */
    public function it_formats_czech_currency_correctly()
    {
        app()->setLocale('cs');
        
        $formatted = LocalizationService::formatMoney(2700.50, 'CZK');
        
        $this->assertEquals('2 700,50 Kč', $formatted);
    }
    
    /** @test */
    public function it_formats_czech_numbers_correctly()
    {
        app()->setLocale('cs');
        
        $formatted = LocalizationService::formatNumber(2700.50);
        
        $this->assertEquals('2 700,50', $formatted);
    }
    
    /** @test */
    public function it_formats_czech_dates_correctly()
    {
        app()->setLocale('cs');
        Carbon::setLocale('cs');
        
        $date = Carbon::parse('2025-09-21');
        $formatted = LocalizationService::formatDate($date);
        
        $this->assertEquals('21. 9. 2025', $formatted);
    }
    
    /** @test */
    public function it_validates_ico_correctly()
    {
        $validIcoRule = new ValidIco();
        
        // Valid IČO: 25063677 (known valid example)
        $this->assertTrue($validIcoRule->passes('25063677'));
        
        // Invalid IČO: wrong check digit
        $this->assertFalse($validIcoRule->passes('25063678'));
        
        // Invalid format: too short
        $this->assertFalse($validIcoRule->passes('1234567'));
        
        // Invalid format: contains letters
        $this->assertFalse($validIcoRule->passes('2708244A'));
    }
    
    /** @test */
    public function ares_service_validates_ico_format()
    {
        $aresService = new AresService();
        
        $this->assertTrue($aresService->isValidIcoFormat('25596641'));
        $this->assertFalse($aresService->isValidIcoFormat('123'));
        $this->assertFalse($aresService->isValidIcoFormat('123456789'));
        $this->assertFalse($aresService->isValidIcoFormat('abcd1234'));
    }
    
    /** @test */
    public function ares_service_validates_ico_check_digit()
    {
        $aresService = new AresService();
        
        // Valid IČO with correct check digit
        $this->assertTrue($aresService->isValidIco('25063677'));
        
        // Invalid IČO with wrong check digit
        $this->assertFalse($aresService->isValidIco('25063678'));
    }
    
    /** @test */
    public function it_gets_available_locales()
    {
        $locales = LocalizationService::getAvailableLocales();
        
        $this->assertArrayHasKey('cs', $locales);
        $this->assertArrayHasKey('en', $locales);
        $this->assertArrayHasKey('sk', $locales);
        
        $this->assertEquals('Čeština', $locales['cs']['name']);
        $this->assertEquals('🇨🇿', $locales['cs']['flag']);
    }
    
    /** @test */
    public function it_validates_locale_correctly()
    {
        $this->assertTrue(LocalizationService::isValidLocale('cs'));
        $this->assertTrue(LocalizationService::isValidLocale('en'));
        $this->assertTrue(LocalizationService::isValidLocale('sk'));
        
        $this->assertFalse(LocalizationService::isValidLocale('de'));
        $this->assertFalse(LocalizationService::isValidLocale('invalid'));
    }
    
    /** @test */
    public function helper_functions_work_correctly()
    {
        app()->setLocale('cs');
        
        $this->assertEquals('2 700,50 Kč', format_money(2700.50, 'CZK'));
        $this->assertEquals('2 700,50', format_number(2700.50));
        $this->assertTrue(is_czech_locale());
        
        $localeInfo = get_locale_info();
        $this->assertEquals('Čeština', $localeInfo['name']);
    }
}
