<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;

class ProjectKanban extends Component
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
        'taskEstimatedHours' => 'nullable|numeric|min:0',
    ];

    protected $listeners = ['taskMoved' => 'moveTask'];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function moveTask($taskId, $newStatus)
    {
        $task = Task::findOrFail($taskId);
        $task->update(['status' => $newStatus]);
        $this->project->refresh();
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
        $this->taskStatus = $this->editingTask->status;
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
            'description' => $this->taskDescription,
            'status' => $this->taskStatus,
            'priority' => $this->taskPriority,
            'due_date' => $this->taskDueDate,
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

    public function getTasksByStatusProperty()
    {
        return $this->project->tasks()
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status');
    }

    public function render()
    {
        return view('livewire.projects.project-kanban', [
            'tasksByStatus' => $this->tasksByStatus,
        ]);
    }
}