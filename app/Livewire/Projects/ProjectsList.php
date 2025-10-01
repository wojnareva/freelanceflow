<?php

namespace App\Livewire\Projects;

use App\Models\Client;
use App\Models\Project;
use App\Services\PerformanceService;
use App\Traits\HandlesErrors;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsList extends Component
{
    use HandlesErrors, WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $clientFilter = '';

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    // Create/Edit Project Modal
    public $showModal = false;

    public $editingProject = null;

    public $name = '';

    public $description = '';

    public $clientId = '';

    public $status = 'active';

    public $startDate = '';

    public $endDate = '';

    public $budget = '';

    public $hourlyRate = '';

    public $estimatedHours = '';

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'clientId' => 'required|exists:clients,id',
        'status' => 'required|in:draft,active,on_hold,completed,archived',
        'startDate' => 'nullable|date',
        'endDate' => 'nullable|date|after_or_equal:startDate',
        'budget' => 'nullable|numeric|min:0',
        'hourlyRate' => 'nullable|numeric|min:0',
        'estimatedHours' => 'nullable|numeric|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingClientFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function createProject()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editProject($projectId)
    {
        $this->editingProject = Project::findOrFail($projectId);
        $this->name = $this->editingProject->name;
        $this->description = $this->editingProject->description;
        $this->clientId = $this->editingProject->client_id;
        $this->status = $this->editingProject->status;
        $this->startDate = $this->editingProject->start_date?->format('Y-m-d');
        $this->endDate = $this->editingProject->end_date?->format('Y-m-d');
        $this->budget = $this->editingProject->budget;
        $this->hourlyRate = $this->editingProject->hourly_rate;
        $this->estimatedHours = $this->editingProject->estimated_hours;
        $this->showModal = true;
    }

    public function saveProject()
    {
        $this->tryOperation(function () {
            // Standard Livewire validation - will show errors in UI
            $this->validate($this->rules);

            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'client_id' => $this->clientId,
                'status' => $this->status,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'budget' => $this->budget,
                'hourly_rate' => $this->hourlyRate,
                'estimated_hours' => $this->estimatedHours,
            ];

            if ($this->editingProject) {
                $this->editingProject->update($data);
                $this->showSuccess('Project updated successfully!');
            } else {
                $data['user_id'] = auth()->id();
                Project::create($data);
                $this->showSuccess('Project created successfully!');
            }

            // Clear performance caches after project changes
            $performanceService = app(PerformanceService::class);
            $performanceService->clearProjectsListCache(auth()->id());
            $performanceService->clearDashboardStatsCache(auth()->id());

            $this->closeModal();
        }, 'save project');
    }

    public function deleteProject($projectId)
    {
        $this->tryOperation(function () use ($projectId) {
            $project = Project::findOrFail($projectId);

            // Check if project belongs to current user
            if ($project->user_id !== auth()->id()) {
                throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to delete this project.');
            }

            $project->delete();

            // Clear performance caches after project deletion
            $performanceService = app(PerformanceService::class);
            $performanceService->clearProjectsListCache(auth()->id());
            $performanceService->clearDashboardStatsCache(auth()->id());

            $this->showSuccess('Project deleted successfully!');
        }, 'delete project');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingProject = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['name', 'description', 'clientId', 'status', 'startDate', 'endDate', 'budget', 'hourlyRate', 'estimatedHours']);
        $this->status = 'active';
    }

    public function getProjectsProperty()
    {
        $performanceService = app(PerformanceService::class);
        $userId = Auth::id();

        // Create filters array for cache key
        $filters = [
            'search' => $this->search,
            'status_filter' => $this->statusFilter,
            'client_filter' => $this->clientFilter,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
            'page' => $this->getPage(),
        ];

        return $performanceService->getProjectsList($userId, $filters, function () {
            return Project::with(['client', 'tasks'])
                ->withCount(['tasks', 'timeEntries'])
                ->selectRaw('projects.*, COALESCE((SELECT SUM(duration) FROM time_entries WHERE time_entries.project_id = projects.id), 0) as time_entries_sum_duration')
                ->where('user_id', Auth::id())
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('description', 'like', '%'.$this->search.'%')
                            ->orWhereHas('client', function ($clientQuery) {
                                $clientQuery->where('name', 'like', '%'.$this->search.'%');
                            });
                    });
                })
                ->when($this->statusFilter, function ($query) {
                    $query->where('status', $this->statusFilter);
                })
                ->when($this->clientFilter !== '', function ($query) {
                    $query->where('client_id', $this->clientFilter);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(12);
        });
    }

    public function getClientsProperty()
    {
        // Use get() with select for Livewire compatibility
        return Client::where('user_id', Auth::id())
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getStatsProperty()
    {
        $baseQuery = Project::where('user_id', Auth::id());

        if ($this->search) {
            $baseQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('client', function ($clientQuery) {
                        $clientQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->clientFilter) {
            $baseQuery->where('client_id', $this->clientFilter);
        }

        // Use a single query with DB aggregation for better performance
        $stats = $baseQuery->selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "active" THEN 1 END) as active,
            COUNT(CASE WHEN status = "completed" THEN 1 END) as completed,
            COUNT(CASE WHEN status = "on_hold" THEN 1 END) as on_hold,
            COUNT(CASE WHEN status = "draft" THEN 1 END) as draft,
            COUNT(CASE WHEN status = "archived" THEN 1 END) as archived
        ')->first();

        return [
            'total' => $stats->total,
            'active' => $stats->active,
            'completed' => $stats->completed,
            'on_hold' => $stats->on_hold,
            'draft' => $stats->draft,
            'archived' => $stats->archived,
        ];
    }

    private function formatDuration($minutes)
    {
        // Handle null or empty values
        if ($minutes === null || $minutes === '' || $minutes === 0) {
            return '0h';
        }

        // Cast to numeric to avoid decimal casting issues
        $minutes = (float) $minutes;

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        return "{$mins}m";
    }

    public function render()
    {
        return view('livewire.projects.projects-list', [
            'projects' => $this->projects,
            'clients' => $this->clients,
            'stats' => $this->stats,
        ]);
    }
}
