<?php

namespace Tests\Feature;

use App\Livewire\Clients\ClientForm;
use App\Models\Client;
use App\Models\User;
use App\Services\AresService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class AresIntegrationFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function client_form_validates_ico_correctly()
    {
        $user = User::factory()->create();

        // Test invalid IČO
        $response = $this->actingAs($user)->post('/clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'ico' => '12345678', // Invalid check digit
        ]);

        $response->assertSessionHasErrors(['ico']);
    }

    /** @test */
    public function client_form_accepts_valid_ico()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'ico' => '25063677', // Valid IČO
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'ico' => '25063677',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function livewire_client_form_can_fetch_company_data()
    {
        $user = User::factory()->create();

        // Mock ARES API response
        Http::fake([
            'ares.gov.cz/*' => Http::response([
                'ekonomickySubjekt' => [
                    'ico' => '25063677',
                    'dic' => 'CZ25063677',
                    'obchodniJmeno' => 'Test Company s.r.o.',
                    'sidlo' => [
                        'nazevUlice' => 'Testovací',
                        'cisloDomovni' => '123',
                        'nazevObce' => 'Praha',
                        'psc' => '11000',
                    ],
                ],
            ], 200),
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('ico', '25063677')
            ->call('fetchCompanyData')
            ->assertSet('companyDataFound', true)
            ->assertSet('company', 'Test Company s.r.o.')
            ->assertSee('Údaje firmy byly načteny z registru');
    }

    /** @test */
    public function livewire_client_form_handles_ico_not_found()
    {
        $user = User::factory()->create();

        // Mock ARES API response for not found
        Http::fake([
            'ares.gov.cz/*' => Http::response([], 404),
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('ico', '99999999')
            ->call('fetchCompanyData')
            ->assertSet('companyDataFound', false)
            ->assertHasErrors(['ico']);
    }

    /** @test */
    public function livewire_client_form_auto_fills_on_ico_change()
    {
        $user = User::factory()->create();

        // Mock ARES API response
        Http::fake([
            'ares.gov.cz/*' => Http::response([
                'ekonomickySubjekt' => [
                    'ico' => '25063677',
                    'dic' => 'CZ25063677',
                    'obchodniJmeno' => 'Auto Fill Company',
                    'sidlo' => [
                        'nazevUlice' => 'Auto Street',
                        'cisloDomovni' => '456',
                        'nazevObce' => 'Brno',
                        'psc' => '60200',
                    ],
                ],
            ], 200),
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('autoFillEnabled', true)
            ->set('ico', '25063677')
            ->assertSet('companyDataFound', true)
            ->assertSet('company', 'Auto Fill Company');
    }

    /** @test */
    public function user_can_toggle_auto_fill_functionality()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->assertSet('autoFillEnabled', true)
            ->call('toggleAutoFill')
            ->assertSet('autoFillEnabled', false)
            ->call('toggleAutoFill')
            ->assertSet('autoFillEnabled', true);
    }

    /** @test */
    public function user_can_clear_company_data()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'company_registry_data' => ['test' => 'data'],
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class, ['client' => $client])
            ->set('companyDataFound', true)
            ->call('clearCompanyData')
            ->assertSet('companyDataFound', false)
            ->assertSet('company', '')
            ->assertSet('address', '');

        $client->refresh();
        $this->assertNull($client->company_registry_data);
    }

    /** @test */
    public function ares_service_caches_company_data()
    {
        $aresService = new AresService;

        // Mock first API call
        Http::fake([
            'ares.gov.cz/*' => Http::response([
                'ekonomickySubjekt' => [
                    'ico' => '25063677',
                    'obchodniJmeno' => 'Cached Company',
                ],
            ], 200),
        ]);

        // First call should hit the API
        $data1 = $aresService->getCompanyData('25063677');
        $this->assertEquals('Cached Company', $data1['company_name']);

        // Clear HTTP fake to ensure no second API call
        Http::fake([]);

        // Second call should use cache
        $data2 = $aresService->getCompanyData('25063677');
        $this->assertEquals('Cached Company', $data2['company_name']);
    }

    /** @test */
    public function client_with_ico_shows_registry_data_in_edit_form()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'ico' => '25063677',
            'company_registry_data' => [
                'company_name' => 'Registry Company',
                'address' => 'Registry Address',
            ],
            'registry_updated_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class, ['client' => $client])
            ->assertSet('ico', '25063677')
            ->assertSet('companyDataFound', true);
    }

    /** @test */
    public function ico_field_shows_validation_errors()
    {
        $user = User::factory()->create();

        // Test too short IČO
        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('ico', '123')
            ->call('fetchCompanyData')
            ->assertHasErrors(['ico']);

        // Test non-numeric IČO
        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('ico', 'abcd1234')
            ->call('fetchCompanyData')
            ->assertHasErrors(['ico']);
    }

    /** @test */
    public function duplicate_ico_is_prevented()
    {
        $user = User::factory()->create();

        // Create first client with IČO
        Client::factory()->create([
            'user_id' => $user->id,
            'ico' => '25063677',
        ]);

        // Try to create second client with same IČO
        $response = $this->actingAs($user)->post('/clients', [
            'name' => 'Duplicate Client',
            'email' => 'duplicate@example.com',
            'ico' => '25063677',
        ]);

        $response->assertSessionHasErrors(['ico']);
    }

    /** @test */
    public function client_form_preserves_user_data_when_auto_filling()
    {
        $user = User::factory()->create();

        // Mock ARES API response
        Http::fake([
            'ares.gov.cz/*' => Http::response([
                'ekonomickySubjekt' => [
                    'ico' => '25063677',
                    'obchodniJmeno' => 'ARES Company',
                    'sidlo' => [
                        'nazevUlice' => 'ARES Street',
                        'cisloDomovni' => '123',
                        'nazevObce' => 'Praha',
                        'psc' => '11000',
                    ],
                ],
            ], 200),
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('company', 'User Company') // User has already entered company name
            ->set('ico', '25063677')
            ->call('fetchCompanyData')
            // Should not overwrite user's company name
            ->assertSet('company', 'User Company');
    }

    /** @test */
    public function ares_api_error_is_handled_gracefully()
    {
        $user = User::factory()->create();

        // Mock ARES API timeout/error
        Http::fake([
            'ares.gov.cz/*' => Http::response([], 500),
        ]);

        Livewire::actingAs($user)
            ->test(ClientForm::class)
            ->set('ico', '25063677')
            ->call('fetchCompanyData')
            ->assertSet('companyDataFound', false)
            ->assertHasErrors(['ico']);
    }
}
