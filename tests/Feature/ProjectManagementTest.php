<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->client = Client::factory()->for($this->user)->create();
    }

    public function test_user_can_view_projects_index(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();

        $response = $this->actingAs($this->user)->get('/projects');

        $response->assertStatus(200);
        $response->assertSee($project->name);
    }

    public function test_projects_list_livewire_component_can_create_project(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectsList::class)
            ->set('name', 'Test Project')
            ->set('description', 'Test Description')
            ->set('clientId', $this->client->id)
            ->set('status', ProjectStatus::Draft->value)
            ->set('startDate', now()->format('Y-m-d'))
            ->set('endDate', now()->addDays(30)->format('Y-m-d'))
            ->set('budget', 5000)
            ->set('hourlyRate', 75)
            ->call('saveProject');

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
        ]);
    }

    public function test_user_can_view_project_detail(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();

        $response = $this->actingAs($this->user)->get("/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertSee($project->name);
        $response->assertSee($project->description);
    }

    public function test_project_detail_component_can_update_project(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectDetail::class, ['project' => $project])
            ->set('name', 'Updated Project')
            ->set('description', 'Updated Description')
            ->set('status', ProjectStatus::Active)
            ->call('save');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'status' => ProjectStatus::Active,
        ]);
    }

    public function test_projects_list_component_can_delete_project(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectsList::class)
            ->call('delete', $project->id);

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_projects_list_component_displays_projects(): void
    {
        $projects = Project::factory(3)->for($this->user)->for($this->client)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectsList::class)
            ->assertSee($projects[0]->name)
            ->assertSee($projects[1]->name)
            ->assertSee($projects[2]->name);
    }

    public function test_projects_list_can_filter_by_status(): void
    {
        $activeProject = Project::factory()->for($this->user)->for($this->client)->create([
            'status' => ProjectStatus::Active
        ]);
        $completedProject = Project::factory()->for($this->user)->for($this->client)->create([
            'status' => ProjectStatus::Completed
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectsList::class)
            ->set('statusFilter', ProjectStatus::Active->value)
            ->assertSee($activeProject->name)
            ->assertDontSee($completedProject->name);
    }

    public function test_projects_list_can_search_projects(): void
    {
        $project1 = Project::factory()->for($this->user)->for($this->client)->create(['name' => 'Website Development']);
        $project2 = Project::factory()->for($this->user)->for($this->client)->create(['name' => 'Mobile App']);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectsList::class)
            ->set('search', 'Website')
            ->assertSee($project1->name)
            ->assertDontSee($project2->name);
    }

    public function test_project_detail_shows_tasks(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();
        $task = Task::factory()->for($project)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectDetail::class, ['project' => $project])
            ->assertSee($task->title);
    }

    public function test_project_kanban_displays_tasks_by_status(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();
        $todoTask = Task::factory()->for($project)->create(['status' => TaskStatus::Todo]);
        $inProgressTask = Task::factory()->for($project)->create(['status' => TaskStatus::InProgress]);
        $completedTask = Task::factory()->for($project)->create(['status' => TaskStatus::Completed]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectKanban::class, ['project' => $project])
            ->assertSee($todoTask->title)
            ->assertSee($inProgressTask->title)
            ->assertSee($completedTask->title);
    }

    public function test_project_timeline_shows_project_dates(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create([
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectTimeline::class, ['project' => $project])
            ->assertSee($project->name);
    }

    public function test_projects_list_only_shows_user_projects(): void
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->for($otherUser)->create();
        $otherProject = Project::factory()->for($otherUser)->for($otherClient)->create();
        $userProject = Project::factory()->for($this->user)->for($this->client)->create();

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\Projects\ProjectsList::class)
            ->assertSee($userProject->name)
            ->assertDontSee($otherProject->name);
    }

    public function test_project_deletion_cascades_to_tasks(): void
    {
        $project = Project::factory()->for($this->user)->for($this->client)->create();
        $task = Task::factory()->for($project)->create();

        $this->actingAs($this->user)->delete("/projects/{$project->id}");

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        $this->assertSoftDeleted('project_tasks', ['id' => $task->id]);
    }
}