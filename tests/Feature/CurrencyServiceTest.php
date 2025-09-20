<?php

namespace Tests\Feature;

use App\Enums\Currency;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CurrencyService $currencyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyService = app(CurrencyService::class);
    }

    public function test_currency_conversion_same_currency_returns_same_amount()
    {
        $amount = 100.0;
        $result = $this->currencyService->convert($amount, Currency::USD, Currency::USD);

        $this->assertEquals($amount, $result);
    }

    public function test_currency_conversion_usd_to_eur()
    {
        $amount = 100.0;
        $result = $this->currencyService->convert($amount, Currency::USD, Currency::EUR);

        $this->assertEquals(85.0, $result);
    }

    public function test_currency_formatting_usd()
    {
        $amount = 100.50;
        $result = $this->currencyService->format($amount, Currency::USD);

        $this->assertEquals('$100.50', $result);
    }

    public function test_currency_formatting_eur()
    {
        $amount = 85.75;
        $result = $this->currencyService->format($amount, Currency::EUR);

        $this->assertEquals('85.75 €', $result);
    }

    public function test_get_popular_currencies_returns_expected_count()
    {
        $currencies = $this->currencyService->getPopularCurrencies();

        $this->assertIsArray($currencies);
        $this->assertCount(5, $currencies);
        $this->assertEquals('USD', $currencies[0]['value']);
    }

    public function test_convert_and_format_with_specified_currency()
    {
        $result = $this->currencyService->convertAndFormat(100.0, Currency::USD, Currency::EUR);

        $this->assertEquals('85.00 €', $result);
    }
}
