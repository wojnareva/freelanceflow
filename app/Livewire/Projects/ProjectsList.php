<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;

class ProjectsList extends Component
{
    use WithPagination;

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
        $this->validate();

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
            session()->flash('success', 'Project updated successfully!');
        } else {
            $data['user_id'] = auth()->id();
            Project::create($data);
            session()->flash('success', 'Project created successfully!');
        }

        $this->closeModal();
    }

    public function deleteProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        $project->delete();
        session()->flash('success', 'Project deleted successfully!');
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
        return Project::with(['client', 'tasks'])
            ->withCount(['tasks', 'timeEntries'])
            ->withSum('timeEntries', 'duration')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('client', function ($clientQuery) {
                          $clientQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->clientFilter, function ($query) {
                $query->where('client_id', $this->clientFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    public function getClientsProperty()
    {
        return Client::orderBy('name')->get();
    }

    public function getStatsProperty()
    {
        $baseQuery = Project::query();
        
        if ($this->search) {
            $baseQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function ($clientQuery) {
                      $clientQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        if ($this->clientFilter) {
            $baseQuery->where('client_id', $this->clientFilter);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'on_hold' => (clone $baseQuery)->where('status', 'on_hold')->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'archived' => (clone $baseQuery)->where('status', 'archived')->count(),
        ];
    }

    private function formatDuration($minutes)
    {
        if (!$minutes) return '0h';
        
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
