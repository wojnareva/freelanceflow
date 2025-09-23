<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TimeTrackingTest extends TestCase
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

    public function test_user_can_view_time_entries_index(): void
    {
        $response = $this->actingAs($this->user)->get('/time-tracking');

        $response->assertStatus(200);
        $response->assertSee('Time Tracking');
    }

    public function test_time_entries_list_can_create_time_entry(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\TimeEntriesList::class)
            ->set('project_id', $this->project->id)
            ->set('description', 'Working on feature')
            ->set('start_time', now()->subHours(2)->format('H:i'))
            ->set('end_time', now()->format('H:i'))
            ->set('date', now()->format('Y-m-d'))
            ->set('billable', true)
            ->call('save');

        $this->assertDatabaseHas('time_entries', [
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'description' => 'Working on feature',
        ]);
    }

    public function test_user_can_update_time_entry(): void
    {
        $timeEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create();

        $updateData = [
            'project_id' => $this->project->id,
            'description' => 'Updated description',
            'start_time' => $timeEntry->start_time->format('Y-m-d H:i:s'),
            'end_time' => $timeEntry->end_time->format('Y-m-d H:i:s'),
            'billable' => false,
        ];

        $response = $this->actingAs($this->user)->put("/time-tracking/{$timeEntry->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('time_entries', [
            'id' => $timeEntry->id,
            'description' => 'Updated description',
            'billable' => false,
        ]);
    }

    public function test_user_can_delete_time_entry(): void
    {
        $timeEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create();

        $response = $this->actingAs($this->user)->delete("/time-tracking/{$timeEntry->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('time_entries', ['id' => $timeEntry->id]);
    }

    public function test_floating_timer_component_can_start_timer(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\FloatingTimer::class)
            ->set('selectedProjectId', $this->project->id)
            ->set('description', 'Working on timer')
            ->call('startTimer')
            ->assertSet('isRunning', true)
            ->assertNotNull('startTime');
    }

    public function test_floating_timer_component_can_stop_timer(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\FloatingTimer::class)
            ->set('selectedProjectId', $this->project->id)
            ->set('description', 'Working on timer')
            ->call('startTimer')
            ->call('stopTimer')
            ->assertSet('isRunning', false);

        $this->assertDatabaseHas('time_entries', [
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'description' => 'Working on timer',
        ]);
    }

    public function test_time_entries_list_displays_entries(): void
    {
        $timeEntries = TimeEntry::factory(3)->for($this->user)->for($this->project)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\TimeEntriesList::class)
            ->assertSee($timeEntries[0]->description)
            ->assertSee($timeEntries[1]->description)
            ->assertSee($timeEntries[2]->description);
    }

    public function test_time_entries_calendar_shows_entries_by_date(): void
    {
        $timeEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'start_time' => now()->startOfDay(),
            'end_time' => now()->startOfDay()->addHours(2),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\TimeEntriesCalendar::class)
            ->assertSee($timeEntry->description);
    }

    public function test_bulk_time_entry_editor_can_update_multiple_entries(): void
    {
        $timeEntries = TimeEntry::factory(3)->for($this->user)->for($this->project)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\BulkTimeEntryEditor::class)
            ->set('selectedEntries', $timeEntries->pluck('id')->toArray())
            ->set('bulkBillable', false)
            ->call('updateSelectedEntries');

        foreach ($timeEntries as $entry) {
            $this->assertDatabaseHas('time_entries', [
                'id' => $entry->id,
                'billable' => false,
            ]);
        }
    }

    public function test_time_entries_can_be_filtered_by_project(): void
    {
        $project2 = Project::factory()->for($this->user)->for($this->client)->create();
        $timeEntry1 = TimeEntry::factory()->for($this->user)->for($this->project)->create();
        $timeEntry2 = TimeEntry::factory()->for($this->user)->for($project2)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\TimeEntriesList::class)
            ->set('projectFilter', $this->project->id)
            ->assertSee($timeEntry1->description)
            ->assertDontSee($timeEntry2->description);
    }

    public function test_time_entries_can_be_filtered_by_billable_status(): void
    {
        $billableEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create(['billable' => true]);
        $nonBillableEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create(['billable' => false]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\TimeEntriesList::class)
            ->set('billableFilter', 'billable')
            ->assertSee($billableEntry->description)
            ->assertDontSee($nonBillableEntry->description);
    }

    public function test_user_cannot_access_other_users_time_entries(): void
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->for($otherUser)->create();
        $otherProject = Project::factory()->for($otherUser)->for($otherClient)->create();
        $otherTimeEntry = TimeEntry::factory()->for($otherUser)->for($otherProject)->create();

        $response = $this->actingAs($this->user)->get("/time-tracking/{$otherTimeEntry->id}");

        $response->assertStatus(404);
    }

    public function test_time_entry_duration_is_calculated_correctly(): void
    {
        $startTime = now()->startOfDay();
        $endTime = $startTime->copy()->addHours(2)->addMinutes(30);

        $timeEntry = TimeEntry::factory()->for($this->user)->for($this->project)->create([
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $this->assertEquals(2.5, $timeEntry->duration_hours);
    }

    public function test_timer_cannot_start_without_project(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\FloatingTimer::class)
            ->set('description', 'Working on timer')
            ->call('startTimer')
            ->assertHasErrors(['selectedProjectId']);
    }

    public function test_timer_cannot_start_without_description(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\TimeTracking\FloatingTimer::class)
            ->set('selectedProjectId', $this->project->id)
            ->call('startTimer')
            ->assertHasErrors(['description']);
    }
}