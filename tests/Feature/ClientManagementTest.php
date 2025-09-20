<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_client_index_requires_authentication(): void
    {
        $response = $this->get('/clients');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_clients_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clients');

        $response->assertStatus(200);
        $response->assertSee('Clients');
    }

    public function test_user_can_view_client_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clients/create');

        $response->assertStatus(200);
        $response->assertSee('Add New Client');
    }

    public function test_user_can_create_client(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('clients.client-form')
            ->set('name', 'Test Client')
            ->set('email', 'test@example.com')
            ->set('phone', '123-456-7890')
            ->set('company', 'Test Company')
            ->call('save')
            ->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_view_client_detail_page(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/clients/{$client->id}");

        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee($client->email);
    }

    public function test_clients_list_component_displays_clients(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test('clients.clients-list')
            ->assertSee($client->name)
            ->assertSee($client->email);
    }

    public function test_user_can_search_clients(): void
    {
        $user = User::factory()->create();
        $client1 = Client::factory()->create(['user_id' => $user->id, 'name' => 'John Doe']);
        $client2 = Client::factory()->create(['user_id' => $user->id, 'name' => 'Jane Smith']);

        Livewire::actingAs($user)
            ->test('clients.clients-list')
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_user_can_delete_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test('clients.clients-list')
            ->call('deleteClient', $client->id);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_client_detail_shows_stats(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test('clients.client-detail', ['client' => $client])
            ->assertSee('Total Projects')
            ->assertSee('Active Projects')
            ->assertSee('Total Hours')
            ->assertSee('Total Revenue');
    }
}
