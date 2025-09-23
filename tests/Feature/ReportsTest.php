<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\TimeEntry;
use App\Models\Expense;
use App\Enums\InvoiceStatus;
use App\Enums\ExpenseStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsTest extends TestCase
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

    public function test_user_can_view_reports_index(): void
    {
        $response = $this->actingAs($this->user)->get('/reports');

        $response->assertStatus(200);
        $response->assertSee('Financial Reports');
    }

    public function test_reports_index_shows_revenue_overview(): void
    {
        // Create paid invoices
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 10000,
            'paid_at' => now(),
        ]);
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 15000,
            'paid_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('25,000'); // Total revenue
    }

    public function test_reports_show_time_tracking_summary(): void
    {
        // Create time entries
        TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'start_time' => now()->subHours(2),
            'end_time' => now(),
            'billable' => true,
        ]);
        TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'start_time' => now()->subHours(3),
            'end_time' => now()->subHours(1),
            'billable' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('4.0') // Total hours
            ->assertSee('2.0'); // Billable hours
    }

    public function test_reports_show_expense_summary(): void
    {
        // Create expenses
        Expense::factory()->for($this->user)->create([
            'amount' => 1000,
            'status' => ExpenseStatus::Approved,
            'billable' => true,
        ]);
        Expense::factory()->for($this->user)->create([
            'amount' => 500,
            'status' => ExpenseStatus::Approved,
            'billable' => false,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('1,500') // Total expenses
            ->assertSee('1,000'); // Billable expenses
    }

    public function test_reports_can_be_filtered_by_date_range(): void
    {
        // Create data from different periods
        $oldInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 5000,
            'paid_at' => now()->subMonths(3),
            'issue_date' => now()->subMonths(3),
        ]);
        
        $recentInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 10000,
            'paid_at' => now()->subDays(5),
            'issue_date' => now()->subDays(5),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->set('dateFrom', now()->subMonth()->format('Y-m-d'))
            ->set('dateTo', now()->format('Y-m-d'))
            ->call('updateDateRange')
            ->assertSee('10,000') // Recent invoice
            ->assertDontSee('5,000'); // Old invoice should be filtered out
    }

    public function test_reports_show_client_breakdown(): void
    {
        $client2 = Client::factory()->for($this->user)->create(['name' => 'Client Two']);
        
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 8000,
            'paid_at' => now(),
        ]);
        
        Invoice::factory()->for($this->user)->for($client2)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 12000,
            'paid_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee($this->client->name)
            ->assertSee('Client Two')
            ->assertSee('8,000')
            ->assertSee('12,000');
    }

    public function test_reports_show_project_profitability(): void
    {
        // Create billable time entries
        TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'start_time' => now()->subHours(4),
            'end_time' => now(),
            'billable' => true,
        ]);

        // Create project expenses
        Expense::factory()->for($this->user)->create([
            'project_id' => $this->project->id,
            'amount' => 2000,
            'status' => ExpenseStatus::Approved,
        ]);

        // Create invoice for the project
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 10000,
            'paid_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee($this->project->name)
            ->assertSee('8,000'); // Profit (10000 - 2000)
    }

    public function test_reports_show_monthly_revenue_trend(): void
    {
        // Create invoices for different months
        for ($i = 0; $i < 6; $i++) {
            Invoice::factory()->for($this->user)->for($this->client)->create([
                'status' => InvoiceStatus::Paid,
                'total' => ($i + 1) * 1000,
                'paid_at' => now()->subMonths($i),
                'issue_date' => now()->subMonths($i),
            ]);
        }

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('1,000')
            ->assertSee('2,000')
            ->assertSee('3,000');
    }

    public function test_reports_show_outstanding_invoices(): void
    {
        $draftInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Draft,
            'total' => 5000,
        ]);
        
        $sentInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Sent,
            'total' => 7500,
        ]);

        $overdueInvoice = Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Sent,
            'total' => 3000,
            'due_date' => now()->subDays(10),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('15,500') // Total outstanding (5000 + 7500 + 3000)
            ->assertSee('3,000'); // Overdue amount
    }

    public function test_reports_can_be_exported_to_csv(): void
    {
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 10000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/reports/export?format=csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="financial-report.csv"');
    }

    public function test_reports_can_be_exported_to_pdf(): void
    {
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 10000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/reports/export?format=pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_reports_show_hourly_rate_analysis(): void
    {
        $project2 = Project::factory()->for($this->user)->for($this->client)->create([
            'hourly_rate' => 100
        ]);

        TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'start_time' => now()->subHours(2),
            'end_time' => now(),
            'billable' => true,
        ]);

        TimeEntry::factory()->for($this->user)->for($project2)->create([
            'start_time' => now()->subHours(1),
            'end_time' => now(),
            'billable' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee($this->project->hourly_rate)
            ->assertSee($project2->hourly_rate);
    }

    public function test_user_cannot_access_other_users_reports(): void
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->for($otherUser)->create();
        
        Invoice::factory()->for($otherUser)->for($otherClient)->create([
            'status' => InvoiceStatus::Paid,
            'total' => 10000,
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertDontSee($otherClient->name)
            ->assertSee('0'); // Should show 0 revenue for current user
    }

    public function test_reports_handle_empty_data_gracefully(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('0') // Should show zeros for empty data
            ->assertSee('No data available');
    }

    public function test_reports_show_tax_summary(): void
    {
        Invoice::factory()->for($this->user)->for($this->client)->create([
            'status' => InvoiceStatus::Paid,
            'subtotal' => 10000,
            'tax_rate' => 21,
            'tax_amount' => 2100,
            'total' => 12100,
            'paid_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Reports\Index::class)
            ->assertSee('2,100') // Tax collected
            ->assertSee('21%'); // Tax rate
    }
}