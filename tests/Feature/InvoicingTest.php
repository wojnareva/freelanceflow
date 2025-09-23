<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\TimeEntry;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->client = Client::factory()->for($this->user)->create();
        $this->project = Project::factory()->for($this->user)->for($this->client)->create();
    }

    public function test_user_can_view_invoices_index(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create();

        $response = $this->actingAs($this->user)->get('/invoices');

        $response->assertStatus(200);
        $response->assertSee($invoice->number);
    }

    public function test_user_can_create_invoice(): void
    {
        $invoiceData = [
            'client_id' => $this->client->id,
            'number' => 'INV-001',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => InvoiceStatus::Draft,
            'currency' => 'CZK',
            'subtotal' => 10000,
            'tax_rate' => 21,
            'tax_amount' => 2100,
            'total' => 12100,
            'notes' => 'Test invoice',
        ];

        $response = $this->actingAs($this->user)->post('/invoices', $invoiceData);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-001',
        ]);
    }

    public function test_user_can_view_invoice_detail(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create();

        $response = $this->actingAs($this->user)->get("/invoices/{$invoice->id}");

        $response->assertStatus(200);
        $response->assertSee($invoice->number);
    }

    public function test_user_can_update_invoice(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create();

        $updateData = [
            'client_id' => $this->client->id,
            'number' => 'INV-002',
            'issue_date' => $invoice->issue_date->format('Y-m-d'),
            'due_date' => $invoice->due_date->format('Y-m-d'),
            'status' => InvoiceStatus::Sent,
            'currency' => $invoice->currency,
            'subtotal' => $invoice->subtotal,
            'tax_rate' => $invoice->tax_rate,
            'tax_amount' => $invoice->tax_amount,
            'total' => $invoice->total,
            'notes' => 'Updated notes',
        ];

        $response = $this->actingAs($this->user)->put("/invoices/{$invoice->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'number' => 'INV-002',
            'status' => InvoiceStatus::Sent,
        ]);
    }

    public function test_user_can_delete_invoice(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create();

        $response = $this->actingAs($this->user)->delete("/invoices/{$invoice->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_invoices_list_component_displays_invoices(): void
    {
        $invoices = Invoice::factory(3)->for($this->user)->for($this->client)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoicesList::class)
            ->assertSee($invoices[0]->number)
            ->assertSee($invoices[1]->number)
            ->assertSee($invoices[2]->number);
    }

    public function test_invoice_builder_can_create_from_time_entries(): void
    {
        $timeEntries = TimeEntry::factory(3)->for($this->user)->for($this->project)->create([
            'billable' => true,
            'invoiced' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoiceBuilder::class)
            ->set('clientId', $this->client->id)
            ->set('selectedTimeEntries', $timeEntries->pluck('id')->toArray())
            ->call('generateInvoice')
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
        ]);

        foreach ($timeEntries as $entry) {
            $this->assertDatabaseHas('time_entries', [
                'id' => $entry->id,
                'invoiced' => true,
            ]);
        }
    }

    public function test_invoice_builder_can_filter_time_entries_by_client(): void
    {
        $client2 = Client::factory()->for($this->user)->create();
        $project2 = Project::factory()->for($this->user)->for($client2)->create();
        
        $timeEntry1 = TimeEntry::factory()->for($this->user)->for($this->project)->create(['billable' => true]);
        $timeEntry2 = TimeEntry::factory()->for($this->user)->for($project2)->create(['billable' => true]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoiceBuilder::class)
            ->set('clientId', $this->client->id)
            ->assertSee($timeEntry1->description)
            ->assertDontSee($timeEntry2->description);
    }

    public function test_invoice_status_can_be_updated(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Draft
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoicesList::class)
            ->call('updateInvoiceStatus', $invoice->id, InvoiceStatus::Sent->value);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => InvoiceStatus::Sent,
        ]);
    }

    public function test_invoice_can_be_marked_as_paid(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Sent
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoicesList::class)
            ->call('markAsPaid', $invoice->id);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => InvoiceStatus::Paid,
        ]);

        $invoice->refresh();
        $this->assertNotNull($invoice->paid_at);
    }

    public function test_invoices_can_be_filtered_by_status(): void
    {
        $draftInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Draft
        ]);
        $sentInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Sent
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoicesList::class)
            ->set('statusFilter', InvoiceStatus::Draft->value)
            ->assertSee($draftInvoice->number)
            ->assertDontSee($sentInvoice->number);
    }

    public function test_invoices_can_be_filtered_by_client(): void
    {
        $client2 = Client::factory()->for($this->user)->create();
        $invoice1 = Invoice::factory()->for($this->user)->for($this->client)->create();
        $invoice2 = Invoice::factory()->for($this->user)->for($client2)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoicesList::class)
            ->set('clientFilter', $this->client->id)
            ->assertSee($invoice1->number)
            ->assertDontSee($invoice2->number);
    }

    public function test_user_cannot_access_other_users_invoices(): void
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->for($otherUser)->create();
        $otherInvoice = Invoice::factory()->for($otherUser)->for($otherClient)->create();

        $response = $this->actingAs($this->user)->get("/invoices/{$otherInvoice->id}");

        $response->assertStatus(404);
    }

    public function test_invoice_pdf_can_be_generated(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create();
        InvoiceItem::factory()->for($invoice)->create();

        $response = $this->actingAs($this->user)->get("/invoices/{$invoice->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_invoice_totals_are_calculated_correctly(): void
    {
        $invoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'subtotal' => 10000,
            'tax_rate' => 21,
        ]);

        $this->assertEquals(2100, $invoice->tax_amount);
        $this->assertEquals(12100, $invoice->total);
    }

    public function test_invoice_number_is_unique_per_user(): void
    {
        Invoice::factory()->for($this->user)->for($this->client)->create(['number' => 'INV-001']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Invoice::factory()->for($this->user)->for($this->client)->create(['number' => 'INV-001']);
    }

    public function test_only_billable_uninvoiced_time_entries_can_be_selected(): void
    {
        $billableEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'billable' => true,
            'invoiced' => false,
        ]);
        $nonBillableEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'billable' => false,
            'invoiced' => false,
        ]);
        $invoicedEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'billable' => true,
            'invoiced' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Invoicing\InvoiceBuilder::class)
            ->set('clientId', $this->client->id)
            ->assertSee($billableEntry->description)
            ->assertDontSee($nonBillableEntry->description)
            ->assertDontSee($invoicedEntry->description);
    }
}