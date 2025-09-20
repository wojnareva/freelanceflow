<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Carbon\Carbon;

class ProjectTimeline extends Component
{
    public ?Project $project = null;
    public $viewMode = 'month'; // week, month, quarter
    public $currentDate;
    public $showAllProjects = false;

    public function mount(?Project $project = null)
    {
        $this->project = $project;
        $this->showAllProjects = $project === null;
        $this->currentDate = Carbon::now();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function previousPeriod()
    {
        switch ($this->viewMode) {
            case 'week':
                $this->currentDate = $this->currentDate->subWeek();
                break;
            case 'month':
                $this->currentDate = $this->currentDate->subMonth();
                break;
            case 'quarter':
                $this->currentDate = $this->currentDate->subQuarter();
                break;
        }
    }

    public function nextPeriod()
    {
        switch ($this->viewMode) {
            case 'week':
                $this->currentDate = $this->currentDate->addWeek();
                break;
            case 'month':
                $this->currentDate = $this->currentDate->addMonth();
                break;
            case 'quarter':
                $this->currentDate = $this->currentDate->addQuarter();
                break;
        }
    }

    public function today()
    {
        $this->currentDate = Carbon::now();
    }

    public function getTimelineDataProperty()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();

        if ($this->showAllProjects) {
            // Get tasks from all active projects
            $tasks = Task::whereHas('project', function ($query) {
                $query->where('status', '!=', 'archived');
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('due_date', [$startDate, $endDate])
                      ->orWhereBetween('created_at', [$startDate, $endDate]);
            })
            ->with(['project.client'])
            ->orderBy('due_date')
            ->orderBy('created_at')
            ->get();

            // Get time entries from all projects
            $timeEntries = TimeEntry::whereHas('project', function ($query) {
                $query->where('status', '!=', 'archived');
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['task', 'project.client'])
            ->orderBy('date')
            ->get()
            ->groupBy('date');
        } else {
            // Get tasks for specific project
            $tasks = $this->project->tasks()
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('due_date', [$startDate, $endDate])
                          ->orWhereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('due_date')
                ->orderBy('created_at')
                ->get();

            // Get time entries for specific project
            $timeEntries = $this->project->timeEntries()
                ->whereBetween('date', [$startDate, $endDate])
                ->with(['task'])
                ->orderBy('date')
                ->get()
                ->groupBy('date');
        }

        // Combine and organize data by date
        $timeline = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            
            $timeline[$dateKey] = [
                'date' => $current->copy(),
                'tasks' => $tasks->filter(function ($task) use ($current) {
                    return $task->due_date && $task->due_date->format('Y-m-d') === $current->format('Y-m-d');
                }),
                'time_entries' => $timeEntries->get($dateKey, collect()),
                'total_hours' => $timeEntries->get($dateKey, collect())->sum('duration') / 60,
                'projects' => $this->showAllProjects ? 
                    $timeEntries->get($dateKey, collect())->unique('project_id')->pluck('project') : 
                    ($this->project ? collect([$this->project]) : collect()),
            ];
            
            $current->addDay();
        }

        return collect($timeline);
    }

    public function getProjectMilestonesProperty()
    {
        $milestones = [];
        
        if ($this->showAllProjects) {
            // Get milestones from all active projects
            $projects = Project::where('status', '!=', 'archived')->get();
            
            foreach ($projects as $project) {
                if ($project->start_date) {
                    $milestones[] = [
                        'date' => $project->start_date,
                        'title' => $project->name . ' - Start',
                        'type' => 'start',
                        'icon' => 'play',
                        'project' => $project->name,
                        'client' => $project->client->name
                    ];
                }
                
                if ($project->end_date) {
                    $milestones[] = [
                        'date' => $project->end_date,
                        'title' => $project->name . ' - Due',
                        'type' => 'due',
                        'icon' => 'flag',
                        'project' => $project->name,
                        'client' => $project->client->name
                    ];
                }
            }
            
            // Add task milestones from all projects
            $importantTasks = Task::whereHas('project', function ($query) {
                $query->where('status', '!=', 'archived');
            })
            ->whereIn('priority', ['high', 'urgent'])
            ->whereNotNull('due_date')
            ->with(['project.client'])
            ->get();
        } else {
            // Single project milestones
            if ($this->project->start_date) {
                $milestones[] = [
                    'date' => $this->project->start_date,
                    'title' => 'Project Start',
                    'type' => 'start',
                    'icon' => 'play'
                ];
            }
            
            if ($this->project->end_date) {
                $milestones[] = [
                    'date' => $this->project->end_date,
                    'title' => 'Project Due',
                    'type' => 'due',
                    'icon' => 'flag'
                ];
            }

            // Add task milestones (high priority or urgent tasks)
            $importantTasks = $this->project->tasks()
                ->whereIn('priority', ['high', 'urgent'])
                ->whereNotNull('due_date')
                ->get();
        }

        foreach ($importantTasks as $task) {
            $milestones[] = [
                'date' => $task->due_date,
                'title' => $task->title,
                'type' => 'task',
                'icon' => 'exclamation',
                'priority' => $task->priority,
                'status' => $task->status,
                'project' => $this->showAllProjects ? $task->project->name : null,
                'client' => $this->showAllProjects ? $task->project->client->name : null
            ];
        }

        return collect($milestones)->sortBy('date');
    }

    private function getStartDate()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->currentDate->copy()->startOfWeek();
            case 'month':
                return $this->currentDate->copy()->startOfMonth();
            case 'quarter':
                return $this->currentDate->copy()->startOfQuarter();
            default:
                return $this->currentDate->copy()->startOfMonth();
        }
    }

    private function getEndDate()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->currentDate->copy()->endOfWeek();
            case 'month':
                return $this->currentDate->copy()->endOfMonth();
            case 'quarter':
                return $this->currentDate->copy()->endOfQuarter();
            default:
                return $this->currentDate->copy()->endOfMonth();
        }
    }

    public function getPeriodTitleProperty()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->currentDate->format('M j') . ' - ' . $this->currentDate->copy()->endOfWeek()->format('M j, Y');
            case 'month':
                return $this->currentDate->format('F Y');
            case 'quarter':
                return 'Q' . $this->currentDate->quarter . ' ' . $this->currentDate->format('Y');
            default:
                return $this->currentDate->format('F Y');
        }
    }

    public function render()
    {
        return view('livewire.projects.project-timeline', [
            'timelineData' => $this->timelineData,
            'projectMilestones' => $this->projectMilestones,
            'periodTitle' => $this->periodTitle,
        ]);
    }
}