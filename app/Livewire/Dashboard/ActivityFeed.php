<?php

namespace App\Livewire\Dashboard;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ActivityFeed extends Component
{
    public $activities;

    public $limit = 10;

    public function mount()
    {
        $this->loadActivities();
    }

    public function loadActivities()
    {
        $activities = collect();

        // Recent time entries
        $timeEntries = TimeEntry::with(['project', 'task'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($entry) {
                return [
                    'type' => 'time_entry',
                    'icon' => 'clock',
                    'color' => 'purple',
                    'title' => 'Time logged',
                    'description' => $entry->description,
                    'details' => [
                        'project' => $entry->project?->name ?? 'N/A',
                        'duration' => $this->formatDuration($entry->duration),
                        'amount' => '$'.number_format($entry->amount, 2),
                    ],
                    'created_at' => $entry->created_at,
                ];
            });

        // Recent invoices
        $invoices = Invoice::with('client')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($invoice) {
                return [
                    'type' => 'invoice',
                    'icon' => 'document-text',
                    'color' => $invoice->status->value === 'paid' ? 'green' : 'yellow',
                    'title' => 'Invoice '.strtolower($invoice->status->value),
                    'description' => 'Invoice #'.$invoice->invoice_number,
                    'details' => [
                        'client' => $invoice->client?->name ?? 'N/A',
                        'amount' => $invoice->formatted_total,
                        'status' => $invoice->status->label(),
                    ],
                    'created_at' => $invoice->created_at,
                ];
            });

        // Recent projects
        $projects = Project::with('client')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'icon' => 'folder',
                    'color' => 'blue',
                    'title' => 'Project created',
                    'description' => $project->name,
                    'details' => [
                        'client' => $project->client?->name ?? 'N/A',
                        'status' => $project->status->label(),
                        'budget' => $project->budget && is_numeric($project->budget) ? '$'.number_format($project->budget, 2) : 'No budget set',
                    ],
                    'created_at' => $project->created_at,
                ];
            });

        // Recent tasks
        $tasks = Task::with('project')
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task',
                    'icon' => 'check-circle',
                    'color' => $task->status->value === 'completed' ? 'green' : 'gray',
                    'title' => 'Task '.strtolower($task->status->value),
                    'description' => $task->title,
                    'details' => [
                        'project' => $task->project?->name ?? 'N/A',
                        'priority' => ucfirst($task->priority),
                        'status' => $task->status->label(),
                    ],
                    'created_at' => $task->updated_at,
                ];
            });

        // Combine and sort all activities
        $this->activities = $activities
            ->concat($timeEntries)
            ->concat($invoices)
            ->concat($projects)
            ->concat($tasks)
            ->sortByDesc('created_at')
            ->take($this->limit)
            ->values();
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

    public function refreshFeed()
    {
        $this->loadActivities();
    }

    public function render()
    {
        return view('livewire.dashboard.activity-feed');
    }
}
