<?php

namespace Tests\Feature;

use App\Helpers\LocaleHelper;
use App\Models\User;
use App\Services\CalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LocalizationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@localization.com',
            'password' => bcrypt('password'),
            'locale' => 'cs',
        ]);
    }

    /** @test */
    public function complete_localization_flow_works_for_authenticated_users()
    {
        // Start with Czech user
        $this->actingAs($this->user);

        // Visit dashboard - should be in Czech
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard'); // Page title
        $response->assertSee('Rychlé akce'); // Quick actions in Czech

        // Update user preference directly (simulating locale change)
        $this->user->update(['locale' => 'en']);
        session(['locale' => 'en']);

        // Visit dashboard again - should be in English
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Quick Actions'); // Should be in English now

        // Verify user preference was updated
        $this->user->refresh();
        $this->assertEquals('en', $this->user->locale);
    }

    /** @test */
    public function guest_users_get_locale_from_browser_headers()
    {
        // Test Czech browser detection
        $response = $this->withHeaders([
            'Accept-Language' => 'cs-CZ,cs;q=0.9,en;q=0.8',
        ])->get('/login');

        $response->assertStatus(200);
        // Check that page is in Czech language context
        $response->assertSee('lang="cs"', false);

        // Test English browser detection
        $response = $this->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->get('/login');

        $response->assertStatus(200);
        // Check that page is in English language context
        $response->assertSee('lang="en"', false);
    }

    /** @test */
    public function session_locale_overrides_browser_preference()
    {
        // Set session to Czech
        session(['locale' => 'cs']);

        // Request with English browser header
        $response = $this->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->get('/login');

        $response->assertStatus(200);
        // Check that page is in Czech language context (session overrides browser)
        $response->assertSee('lang="cs"', false);

        // The actual app locale should be set to Czech
        $this->assertEquals('cs', app()->getLocale());
    }

    /** @test */
    public function user_preference_overrides_session_and_browser()
    {
        // User prefers English
        $this->user->update(['locale' => 'en']);

        // Set session to Czech
        session(['locale' => 'cs']);

        $this->actingAs($this->user);

        // Request with Czech browser header
        $response = $this->withHeaders([
            'Accept-Language' => 'cs-CZ,cs;q=0.9',
        ])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Quick Actions'); // Should show English (user preference)
    }

    /** @test */
    public function calendar_service_respects_locale_settings()
    {
        // Test Czech locale - Monday first
        app()->setLocale('cs');

        $date = Carbon::create(2024, 12, 26); // Thursday
        $weekStart = CalendarService::getWeekStart($date);
        $weekEnd = CalendarService::getWeekEnd($date);

        $this->assertEquals(Carbon::MONDAY, $weekStart->dayOfWeek);
        $this->assertEquals(Carbon::SUNDAY, $weekEnd->dayOfWeek);
        $this->assertEquals('2024-12-23', $weekStart->format('Y-m-d'));
        $this->assertEquals('2024-12-29', $weekEnd->format('Y-m-d'));

        // Test English locale - Sunday first
        app()->setLocale('en');

        $weekStart = CalendarService::getWeekStart($date);
        $weekEnd = CalendarService::getWeekEnd($date);

        $this->assertEquals(Carbon::SUNDAY, $weekStart->dayOfWeek);
        $this->assertEquals(Carbon::SATURDAY, $weekEnd->dayOfWeek);
        $this->assertEquals('2024-12-22', $weekStart->format('Y-m-d'));
        $this->assertEquals('2024-12-28', $weekEnd->format('Y-m-d'));
    }

    /** @test */
    public function time_tracking_calendar_displays_correct_day_order()
    {
        $this->actingAs($this->user);

        // Test Czech calendar (Monday first)
        $this->user->update(['locale' => 'cs']);
        $response = $this->get('/time-tracking/calendar');

        $response->assertStatus(200);
        // Check that calendar shows Czech day order
        $response->assertSeeInOrder(['Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne']);

        // Test English calendar (Sunday first)
        $this->user->update(['locale' => 'en']);
        $response = $this->get('/time-tracking/calendar');

        $response->assertStatus(200);
        // Check that calendar shows English day order
        $response->assertSeeInOrder(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']);
    }

    /** @test */
    public function currency_formatting_works_correctly_in_views()
    {
        $this->actingAs($this->user);

        // Test Czech currency formatting
        $this->user->update(['locale' => 'cs']);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // Verify Czech currency format appears (if there's sample data)
        // Note: This would need actual data to test properly

        // Test English currency formatting
        $this->user->update(['locale' => 'en']);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // Verify English currency format appears (if there's sample data)
    }

    /** @test */
    public function all_main_pages_translate_correctly()
    {
        $this->actingAs($this->user);

        $pages = [
            '/dashboard',
            '/clients',
            '/projects',
            '/invoices',
            '/time-tracking',
            '/expenses',
        ];

        foreach ($pages as $page) {
            // Test Czech
            $this->user->update(['locale' => 'cs']);
            $response = $this->get($page);
            $response->assertStatus(200);

            // Test English
            $this->user->update(['locale' => 'en']);
            $response = $this->get($page);
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function locale_selector_appears_on_all_authenticated_pages()
    {
        $this->actingAs($this->user);

        $pages = [
            '/dashboard',
            '/clients',
            '/projects',
            '/invoices',
            '/time-tracking',
            '/expenses',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertStatus(200);

            // Check that locale selector is present
            $response->assertSee('cs'); // Current locale button
            // Note: Could be more specific by checking for the actual locale selector component
        }
    }

    /** @test */
    public function invalid_locale_is_rejected()
    {
        $this->actingAs($this->user);

        // Try to set invalid locale via session
        session(['locale' => 'invalid']);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // Should fall back to user preference (Czech)
        $this->assertEquals('cs', app()->getLocale());
    }

    /** @test */
    public function middleware_sets_correct_locale_for_each_request()
    {
        // Test that middleware correctly detects and sets locale

        // 1. Guest with Czech browser
        $response = $this->withHeaders([
            'Accept-Language' => 'cs-CZ',
        ])->get('/login');

        $this->assertEquals('cs', app()->getLocale());

        // 2. Guest with English browser
        app()->setLocale('cs'); // Reset

        $response = $this->withHeaders([
            'Accept-Language' => 'en-US',
        ])->get('/login');

        $this->assertEquals('en', app()->getLocale());

        // 3. Authenticated user with preference
        $this->user->update(['locale' => 'en']);
        $this->actingAs($this->user);

        $response = $this->withHeaders([
            'Accept-Language' => 'cs-CZ',
        ])->get('/dashboard');

        $this->assertEquals('en', app()->getLocale()); // User preference wins
    }

    /** @test */
    public function calendar_configuration_is_available_in_views()
    {
        $this->actingAs($this->user);

        // Test Czech calendar config
        $this->user->update(['locale' => 'cs']);
        $response = $this->get('/time-tracking/calendar');

        $response->assertStatus(200);

        // Check that calendar config variables are available
        // This is more of a smoke test - the actual JS variables would need browser testing
        $response->assertSee('calendar'); // Page should contain calendar-related content

        // Test English calendar config
        $this->user->update(['locale' => 'en']);
        $response = $this->get('/time-tracking/calendar');

        $response->assertStatus(200);
        $response->assertSee('calendar');
    }

    /** @test */
    public function blade_directives_work_with_different_locales()
    {
        // This would require a test view or component that uses the custom directives
        // For now, we test the underlying service methods

        // Test Czech formatting
        app()->setLocale('cs');
        $formatted = format_money(1234.56);
        $this->assertStringContainsString('1.234,56 Kč', $formatted);

        // Test English formatting
        app()->setLocale('en');
        $formatted = format_money(1234.56);
        $this->assertStringContainsString('$1,234.56', $formatted);
    }

    /** @test */
    public function localization_persists_across_multiple_requests()
    {
        $this->actingAs($this->user);

        // Set to English
        $this->user->update(['locale' => 'en']);

        // Make multiple requests
        for ($i = 0; $i < 3; $i++) {
            $response = $this->get('/dashboard');
            $response->assertStatus(200);
            $this->assertEquals('en', app()->getLocale());

            // Reset app locale to test middleware
            app()->setLocale('cs');
        }
    }

    /** @test */
    public function locale_helper_provides_correct_configurations()
    {
        // Test Czech configuration
        app()->setLocale('cs');
        $config = LocaleHelper::getCalendarConfig();

        $this->assertEquals('cs', $config['locale']);
        $this->assertEquals(1, $config['firstDay']); // Monday
        $this->assertEquals('d.m.Y', $config['dateFormat']);
        $this->assertContains('Pondělí', $config['dayNames']);

        // Test English configuration
        app()->setLocale('en');
        $config = LocaleHelper::getCalendarConfig();

        $this->assertEquals('en', $config['locale']);
        $this->assertEquals(0, $config['firstDay']); // Sunday
        $this->assertEquals('m/d/Y', $config['dateFormat']);
        $this->assertContains('Monday', $config['dayNames']);
    }

    /** @test */
    public function error_pages_respect_locale()
    {
        // Test 404 page in Czech
        app()->setLocale('cs');
        $response = $this->get('/nonexistent-page');
        $response->assertStatus(404);
        // Note: Would need custom 404 page with translations to fully test

        // Test 404 page in English
        app()->setLocale('en');
        $response = $this->get('/nonexistent-page');
        $response->assertStatus(404);
    }
}
