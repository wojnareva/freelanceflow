<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\TimeEntry;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
                        'project' => $entry->project->name,
                        'duration' => $this->formatDuration($entry->duration),
                        'amount' => '$' . number_format($entry->amount, 2)
                    ],
                    'created_at' => $entry->created_at,
                ];
            });

        // Recent invoices
        $invoices = Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($invoice) {
                return [
                    'type' => 'invoice',
                    'icon' => 'document-text',
                    'color' => $invoice->status === 'paid' ? 'green' : 'yellow',
                    'title' => 'Invoice ' . strtolower($invoice->status),
                    'description' => 'Invoice #' . $invoice->invoice_number,
                    'details' => [
                        'client' => $invoice->client->name,
                        'amount' => '$' . number_format($invoice->total, 2),
                        'status' => ucfirst($invoice->status)
                    ],
                    'created_at' => $invoice->created_at,
                ];
            });

        // Recent projects
        $projects = Project::with('client')
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
                        'client' => $project->client->name,
                        'status' => ucfirst($project->status),
                        'budget' => $project->budget && is_numeric($project->budget) ? '$' . number_format($project->budget, 2) : 'No budget set'
                    ],
                    'created_at' => $project->created_at,
                ];
            });

        // Recent tasks
        $tasks = Task::with('project')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task',
                    'icon' => 'check-circle',
                    'color' => $task->status === 'completed' ? 'green' : 'gray',
                    'title' => 'Task ' . strtolower($task->status),
                    'description' => $task->title,
                    'details' => [
                        'project' => $task->project->name,
                        'priority' => ucfirst($task->priority),
                        'status' => ucfirst($task->status)
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
