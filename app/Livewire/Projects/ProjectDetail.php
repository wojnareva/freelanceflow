<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;

class ProjectDetail extends Component
{
    public Project $project;

    public $showTaskModal = false;

    public $editingTask = null;

    // Task form properties
    public $taskTitle = '';

    public $taskDescription = '';

    public $taskStatus = 'todo';

    public $taskPriority = 'medium';

    public $taskDueDate = '';

    public $taskEstimatedHours = '';

    protected $rules = [
        'taskTitle' => 'required|min:3|max:255',
        'taskDescription' => 'nullable|max:1000',
        'taskStatus' => 'required|in:todo,in_progress,completed,blocked',
        'taskPriority' => 'required|in:low,medium,high,urgent',
        'taskDueDate' => 'nullable|date',
        'taskEstimatedHours' => 'nullable|numeric|min:0|max:999.99',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function createTask()
    {
        $this->resetTaskForm();
        $this->showTaskModal = true;
    }

    public function editTask($taskId)
    {
        $this->editingTask = Task::findOrFail($taskId);
        $this->taskTitle = $this->editingTask->title;
        $this->taskDescription = $this->editingTask->description;
        $this->taskStatus = $this->editingTask->status->value;
        $this->taskPriority = $this->editingTask->priority;
        $this->taskDueDate = $this->editingTask->due_date?->format('Y-m-d');
        $this->taskEstimatedHours = $this->editingTask->estimated_hours;
        $this->showTaskModal = true;
    }

    public function saveTask()
    {
        $this->validate();

        $data = [
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?: null,
            'status' => $this->taskStatus,
            'priority' => $this->taskPriority,
            'due_date' => $this->taskDueDate ?: null,
            'estimated_hours' => $this->taskEstimatedHours,
        ];

        if ($this->editingTask) {
            $this->editingTask->update($data);
            session()->flash('success', 'Task updated successfully!');
        } else {
            $data['project_id'] = $this->project->id;
            $data['user_id'] = auth()->id();
            Task::create($data);
            session()->flash('success', 'Task created successfully!');
        }

        $this->closeTaskModal();
        $this->project->refresh();
    }

    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->delete();
        session()->flash('success', 'Task deleted successfully!');
        $this->project->refresh();
    }

    public function updateTaskStatus($taskId, $status)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['status' => $status]);
        $this->project->refresh();
    }

    public function closeTaskModal()
    {
        $this->showTaskModal = false;
        $this->editingTask = null;
        $this->resetTaskForm();
    }

    private function resetTaskForm()
    {
        $this->reset(['taskTitle', 'taskDescription', 'taskStatus', 'taskPriority', 'taskDueDate', 'taskEstimatedHours']);
        $this->taskStatus = 'todo';
        $this->taskPriority = 'medium';
    }

    public function getProjectStatsProperty()
    {
        return [
            'total_tasks' => $this->project->tasks()->count(),
            'completed_tasks' => $this->project->tasks()->where('status', 'completed')->count(),
            'total_time' => $this->project->timeEntries()->sum('duration'),
            'total_earnings' => $this->project->timeEntries()->selectRaw('SUM(duration * hourly_rate / 60) as total')->value('total') ?? 0,
            'progress' => $this->project->tasks()->count() > 0
                ? round(($this->project->tasks()->where('status', 'completed')->count() / $this->project->tasks()->count()) * 100)
                : 0,
        ];
    }

    public function getRecentTimeEntriesProperty()
    {
        return $this->project->timeEntries()
            ->with(['task'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getTasksByStatusProperty()
    {
        return $this->project->tasks()
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');
    }

    private function formatDuration($minutes)
    {
        if (! $minutes) {
            return '0h';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        return "{$mins}m";
    }

    public function render()
    {
        return view('livewire.projects.project-detail', [
            'projectStats' => $this->projectStats,
            'recentTimeEntries' => $this->recentTimeEntries,
            'tasksByStatus' => $this->tasksByStatus,
        ]);
    }
}
