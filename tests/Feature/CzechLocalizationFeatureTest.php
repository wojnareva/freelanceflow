<?php

namespace Tests\Feature;

use App\Livewire\LocaleSelector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CzechLocalizationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_czech_locale()
    {
        $response = $this->post('/register', [
            'name' => 'Jan Novák',
            'email' => 'jan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'locale' => 'cs',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'jan@example.com')->first();
        $this->assertEquals('cs', $user->locale);
        $this->assertEquals('CZK', $user->currency);
        $this->assertEquals('Europe/Prague', $user->timezone);
    }

    /** @test */
    public function czech_user_sees_czech_interface()
    {
        $user = User::factory()->create(['locale' => 'cs']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Přehled'); // Dashboard in Czech
        $response->assertSee('Rychlé akce'); // Quick actions in Czech
    }

    /** @test */
    public function user_can_change_locale_via_selector()
    {
        $user = User::factory()->create(['locale' => 'en']);

        Livewire::actingAs($user)
            ->test(LocaleSelector::class)
            ->call('changeLocale', 'cs')
            ->assertRedirect();

        $user->refresh();
        $this->assertEquals('cs', $user->locale);
    }

    /** @test */
    public function user_can_update_locale_in_profile()
    {
        $user = User::factory()->create(['locale' => 'en']);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'locale' => 'cs',
            'currency' => 'CZK',
            'timezone' => 'Europe/Prague',
            'number_format' => 'czech',
        ]);

        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertEquals('cs', $user->locale);
        $this->assertEquals('CZK', $user->currency);
        $this->assertEquals('Europe/Prague', $user->timezone);
    }

    /** @test */
    public function middleware_sets_correct_locale_for_czech_user()
    {
        $user = User::factory()->create(['locale' => 'cs']);

        $this->actingAs($user)->get('/dashboard');

        $this->assertEquals('cs', app()->getLocale());
    }

    /** @test */
    public function guest_gets_czech_locale_from_browser_headers()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'cs,en;q=0.9',
        ])->get('/register');

        $response->assertStatus(200);
        $this->assertEquals('cs', app()->getLocale());
    }

    /** @test */
    public function session_locale_overrides_browser_preference()
    {
        $this->withSession(['locale' => 'en'])
            ->withHeaders(['Accept-Language' => 'cs'])
            ->get('/register');

        $this->assertEquals('en', app()->getLocale());
    }

    /** @test */
    public function user_preference_overrides_session_locale()
    {
        $user = User::factory()->create(['locale' => 'cs']);

        $this->withSession(['locale' => 'en'])
            ->actingAs($user)
            ->get('/dashboard');

        $this->assertEquals('cs', app()->getLocale());
    }

    /** @test */
    public function registration_form_shows_czech_as_default_option()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('🇨🇿 Čeština');
        $response->assertSee('selected', false); // Check if Czech is pre-selected
    }

    /** @test */
    public function profile_form_shows_current_locale_settings()
    {
        $user = User::factory()->create([
            'locale' => 'cs',
            'currency' => 'CZK',
            'timezone' => 'Europe/Prague',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('🇨🇿 Čeština');
        $response->assertSee('🇨🇿 Czech Koruna (CZK)');
        $response->assertSee('Europe/Prague');
    }

    /** @test */
    public function navigation_includes_locale_selector()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeLivewire(LocaleSelector::class);
    }

    /** @test */
    public function invalid_locale_is_rejected_in_registration()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'locale' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['locale']);
    }

    /** @test */
    public function invalid_locale_is_rejected_in_profile_update()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'locale' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['locale']);
    }
}
