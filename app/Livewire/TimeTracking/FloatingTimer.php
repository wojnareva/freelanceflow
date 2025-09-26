<?php

namespace App\Livewire\TimeTracking;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Livewire\Component;

class FloatingTimer extends Component
{
    public $isRunning = false;

    public $startTime = null;

    public $elapsedTime = 0;

    public $selectedProjectId = null;

    public $selectedTaskId = null;

    public $description = '';

    public $projects = [];

    public $tasks = [];

    public $isMinimized = false;

    protected $rules = [
        'selectedProjectId' => 'required|exists:projects,id',
        'description' => 'required|min:3|max:255',
    ];

    public function mount()
    {
        $this->loadProjects();
        $this->checkForRunningTimer();
    }

    public function loadProjects()
    {
        $this->projects = Project::whereIn('status', ['active', 'on_hold', 'draft'])
            ->with('client')
            ->orderBy('status') // Show active first, then on_hold, then draft
            ->orderBy('name')
            ->get();
    }

    public function updatedSelectedProjectId()
    {
        if ($this->selectedProjectId) {
            $this->tasks = Task::where('project_id', $this->selectedProjectId)
                ->where('status', '!=', 'completed')
                ->orderBy('title')
                ->get();
        } else {
            $this->tasks = [];
        }
        $this->selectedTaskId = null;
    }

    public function checkForRunningTimer()
    {
        // Check if there's a timer running in localStorage/session
        // For now, we'll just check if there's an incomplete time entry
        $runningEntry = TimeEntry::where('user_id', auth()->id())
            ->whereNull('ended_at')
            ->first();

        if ($runningEntry) {
            $this->isRunning = true;
            $this->startTime = $runningEntry->started_at;
            $this->selectedProjectId = $runningEntry->project_id;
            $this->selectedTaskId = $runningEntry->task_id;
            $this->description = $runningEntry->description;
            $this->updatedSelectedProjectId();
        }
    }

    public function startTimer()
    {
        $this->validate([
            'selectedProjectId' => 'required|exists:projects,id',
            'description' => 'required|min:3|max:255',
        ]);

        if ($this->isRunning) {
            return;
        }

        $project = Project::find($this->selectedProjectId);

        $this->startTime = now();
        $this->isRunning = true;
        $this->elapsedTime = 0;

        // Create a time entry record (we'll update it when stopped)
        TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $this->selectedProjectId,
            'task_id' => $this->selectedTaskId,
            'description' => $this->description,
            'date' => today(),
            'started_at' => $this->startTime,
            'duration' => 0, // Will be updated when timer stops
            'hourly_rate' => $project->hourly_rate ?? $project->client->hourly_rate ?? 0,
            'billable' => true,
            'billed' => false,
        ]);

        $this->dispatch('timer-started');
        session()->flash('success', __('time.timer_started_for', ['project' => $project->name]));
    }

    public function stopTimer()
    {
        if (! $this->isRunning) {
            return;
        }

        $endTime = now();
        $duration = Carbon::parse($this->startTime)->diffInMinutes($endTime);

        // Find and update the running time entry
        $timeEntry = TimeEntry::where('user_id', auth()->id())
            ->where('project_id', $this->selectedProjectId)
            ->whereNull('ended_at')
            ->first();

        if ($timeEntry) {
            $timeEntry->update([
                'ended_at' => $endTime,
                'duration' => $duration,
            ]);
        }

        $this->isRunning = false;
        $this->startTime = null;
        $this->elapsedTime = 0;
        $this->description = '';
        $this->selectedTaskId = null;

        $this->dispatch('timer-stopped');
        session()->flash('success', __('time.time_entry_saved', ['duration' => $this->formatDuration($duration)]));
    }

    public function toggleMinimize()
    {
        $this->isMinimized = ! $this->isMinimized;
    }

    public function updateElapsedTime()
    {
        if ($this->isRunning && $this->startTime) {
            $this->elapsedTime = Carbon::parse($this->startTime)->diffInSeconds(now());
        }
    }

    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        return "{$mins}m";
    }

    public function render()
    {
        $this->updateElapsedTime();

        return view('livewire.time-tracking.floating-timer');
    }
}
