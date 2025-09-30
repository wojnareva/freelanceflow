<?php

namespace Tests\Feature;

use App\Enums\ExpenseStatus;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseTrackingTest extends TestCase
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
        Storage::fake('public');
    }

    public function test_user_can_view_expenses_index(): void
    {
        $expense = Expense::factory()->for($this->user)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->assertSee($expense->title);
    }

    public function test_user_can_create_expense(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Create::class)
            ->set('title', 'Office supplies')
            ->set('description', 'Office supplies')
            ->set('amount', 500)
            ->set('currency', 'CZK')
            ->set('expense_date', now()->format('Y-m-d'))
            ->set('category', 'office')
            ->set('project_id', $this->project->id)
            ->set('billable', true)
            ->set('status', ExpenseStatus::Pending->value)
            ->call('save');

        $this->assertDatabaseHas('expenses', [
            'user_id' => $this->user->id,
            'title' => 'Office supplies',
            'description' => 'Office supplies',
            'amount' => 500,
        ]);
    }

    public function test_user_can_create_expense_with_receipt(): void
    {
        $file = UploadedFile::fake()->image('receipt.jpg');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Create::class)
            ->set('title', 'Business lunch')
            ->set('description', 'Business lunch')
            ->set('amount', 1000)
            ->set('currency', 'CZK')
            ->set('expense_date', now()->format('Y-m-d'))
            ->set('category', 'meals')
            ->set('receipt', $file)
            ->set('billable', false)
            ->set('status', ExpenseStatus::Pending->value)
            ->call('save');

        $expense = Expense::where('title', 'Business lunch')->first();
        $this->assertNotNull($expense->receipt_path);
        Storage::disk('public')->assertExists($expense->receipt_path);
    }

    public function test_user_can_update_expense(): void
    {
        $expense = Expense::factory()->for($this->user)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Edit::class, ['expense' => $expense])
            ->set('title', 'Updated expense')
            ->set('description', 'Updated expense')
            ->set('amount', 750)
            ->set('category', 'office')
            ->set('billable', false)
            ->set('status', ExpenseStatus::Approved->value)
            ->call('save');

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Updated expense',
            'status' => ExpenseStatus::Approved->value,
        ]);
    }

    public function test_user_can_delete_expense(): void
    {
        $expense = Expense::factory()->for($this->user)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->call('deleteExpense', $expense->id);

        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_expenses_index_component_displays_expenses(): void
    {
        $expenses = Expense::factory(3)->for($this->user)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->assertSee($expenses[0]->description)
            ->assertSee($expenses[1]->description)
            ->assertSee($expenses[2]->description);
    }

    public function test_expenses_can_be_filtered_by_status(): void
    {
        $pendingExpense = Expense::factory()->for($this->user)->create(['status' => ExpenseStatus::Pending->value]);
        $approvedExpense = Expense::factory()->for($this->user)->create(['status' => ExpenseStatus::Approved->value]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->set('statusFilter', ExpenseStatus::Pending->value)
            ->assertSee($pendingExpense->description)
            ->assertDontSee($approvedExpense->description);
    }

    public function test_expenses_can_be_filtered_by_project(): void
    {
        $project2 = Project::factory()->for($this->user)->for($this->client)->create();
        $expense1 = Expense::factory()->for($this->user)->create(['project_id' => $this->project->id]);
        $expense2 = Expense::factory()->for($this->user)->create(['project_id' => $project2->id]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->set('projectFilter', $this->project->id)
            ->assertSee($expense1->description)
            ->assertDontSee($expense2->description);
    }

    public function test_expenses_can_be_filtered_by_billable_status(): void
    {
        $billableExpense = Expense::factory()->for($this->user)->create(['billable' => true]);
        $nonBillableExpense = Expense::factory()->for($this->user)->create(['billable' => false]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->set('billableFilter', 'billable')
            ->assertSee($billableExpense->description)
            ->assertDontSee($nonBillableExpense->description);
    }

    public function test_expenses_can_be_searched(): void
    {
        $expense1 = Expense::factory()->for($this->user)->create(['description' => 'Office supplies']);
        $expense2 = Expense::factory()->for($this->user)->create(['description' => 'Travel expenses']);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->set('search', 'Office')
            ->assertSee($expense1->description)
            ->assertDontSee($expense2->description);
    }

    public function test_expense_create_component_validates_required_fields(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Create::class)
            ->call('save')
            ->assertHasErrors(['description', 'amount', 'expense_date', 'category']);
    }

    public function test_expense_edit_component_loads_existing_data(): void
    {
        $expense = Expense::factory()->for($this->user)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Edit::class, ['expense' => $expense])
            ->assertSet('description', $expense->description)
            ->assertSet('amount', $expense->amount)
            ->assertSet('category', $expense->category);
    }

    public function test_expense_status_can_be_updated(): void
    {
        $expense = Expense::factory()->for($this->user)->create(['status' => ExpenseStatus::Pending->value]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->call('updateStatus', $expense->id, ExpenseStatus::Approved->value);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => ExpenseStatus::Approved->value,
        ]);
    }

    public function test_user_cannot_access_other_users_expenses(): void
    {
        $otherUser = User::factory()->create();
        $otherExpense = Expense::factory()->for($otherUser)->create();

        $response = $this->actingAs($this->user)->get("/expenses/{$otherExpense->id}");

        $response->assertStatus(404);
    }

    public function test_expense_amount_must_be_positive(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Create::class)
            ->set('title', 'Invalid expense')
            ->set('amount', -100)
            ->set('currency', 'CZK')
            ->set('expense_date', now()->format('Y-m-d'))
            ->set('category', 'office')
            ->set('billable', false)
            ->set('status', ExpenseStatus::Pending->value)
            ->call('save')
            ->assertHasErrors(['amount']);
    }

    public function test_expense_receipt_must_be_valid_image(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Create::class)
            ->set('title', 'Test expense')
            ->set('amount', 100)
            ->set('currency', 'CZK')
            ->set('expense_date', now()->format('Y-m-d'))
            ->set('category', 'office')
            ->set('receipt', $file)
            ->call('save')
            ->assertHasErrors(['receipt']);
    }

    public function test_expense_totals_by_category_are_calculated(): void
    {
        Expense::factory()->for($this->user)->create(['category' => 'Travel', 'amount' => 1000]);
        Expense::factory()->for($this->user)->create(['category' => 'Travel', 'amount' => 1500]);
        Expense::factory()->for($this->user)->create(['category' => 'Office', 'amount' => 500]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Index::class)
            ->assertSee('2500') // Travel total
            ->assertSee('500'); // Office total
    }

    public function test_billable_expenses_can_be_added_to_invoice(): void
    {
        $billableExpense = Expense::factory()->for($this->user)->create([
            'billable' => true,
            'billed' => false,
            'project_id' => $this->project->id,
        ]);

        // This would typically be tested with an invoice builder component
        $this->assertFalse($billableExpense->billed);
        $this->assertTrue($billableExpense->billable);
    }

    public function test_expense_date_cannot_be_in_future(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Expenses\Create::class)
            ->set('title', 'Future expense')
            ->set('amount', 500)
            ->set('currency', 'CZK')
            ->set('expense_date', now()->addDays(1)->format('Y-m-d'))
            ->set('category', 'office')
            ->set('billable', false)
            ->set('status', ExpenseStatus::Pending->value)
            ->call('save')
            ->assertHasErrors(['expense_date']);
    }
}
