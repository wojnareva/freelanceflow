<?php

namespace App\Livewire\TimeTracking;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Services\CalendarService;
use Carbon\Carbon;
use Livewire\Component;

class TimeEntriesCalendar extends Component
{
    public $currentDate;

    public $viewType = 'month'; // month, week

    public $selectedProjectId = '';

    public $showOnlyBillable = false;

    public function mount()
    {
        $this->currentDate = now()->startOfMonth();
    }

    public function previousPeriod()
    {
        if ($this->viewType === 'month') {
            $this->currentDate = $this->currentDate->copy()->subMonth();
        } else {
            $this->currentDate = $this->currentDate->copy()->subWeek();
        }
    }

    public function nextPeriod()
    {
        if ($this->viewType === 'month') {
            $this->currentDate = $this->currentDate->copy()->addMonth();
        } else {
            $this->currentDate = $this->currentDate->copy()->addWeek();
        }
    }

    public function today()
    {
        $this->currentDate = now()->startOfMonth();
    }

    public function setViewType($type)
    {
        $this->viewType = $type;
        if ($type === 'week') {
            $this->currentDate = CalendarService::getWeekStart($this->currentDate);
        } else {
            $this->currentDate = $this->currentDate->copy()->startOfMonth();
        }
    }

    public function getCalendarDaysProperty()
    {
        if ($this->viewType === 'month') {
            return $this->getMonthDays();
        } else {
            return $this->getWeekDays();
        }
    }

    private function getMonthDays()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        // Start from the first day of week for current locale containing the first day
        $startDate = CalendarService::getWeekStart($startOfMonth);
        // End on last day of week for current locale containing the last day  
        $endDate = CalendarService::getWeekEnd($endOfMonth);

        $days = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $days[] = $currentDate->copy();
            $currentDate->addDay();
        }

        return collect($days)->chunk(7);
    }

    private function getWeekDays()
    {
        $startOfWeek = CalendarService::getWeekStart($this->currentDate);
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfWeek->copy()->addDays($i);
        }

        return collect([$days]);
    }

    public function getTimeEntriesProperty()
    {
        $startDate = $this->viewType === 'month'
            ? CalendarService::getWeekStart($this->currentDate->copy()->startOfMonth())
            : CalendarService::getWeekStart($this->currentDate);

        $endDate = $this->viewType === 'month'
            ? CalendarService::getWeekEnd($this->currentDate->copy()->endOfMonth())
            : CalendarService::getWeekEnd($this->currentDate);

        return TimeEntry::with(['project.client', 'task'])
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->when($this->selectedProjectId, function ($query) {
                $query->where('project_id', $this->selectedProjectId);
            })
            ->when($this->showOnlyBillable, function ($query) {
                $query->where('billable', true);
            })
            ->orderBy('date')
            ->orderBy('started_at')
            ->get()
            ->groupBy(function ($entry) {
                return $entry->date->format('Y-m-d');
            });
    }

    public function getProjectsProperty()
    {
        return Project::where('status', 'active')
            ->with('client')
            ->orderBy('name')
            ->get();
    }

    public function getDailyTotal($date)
    {
        $dateStr = $date->format('Y-m-d');
        $entries = $this->timeEntries->get($dateStr, collect());

        return $entries->sum('duration');
    }

    public function getDailyEarnings($date)
    {
        $dateStr = $date->format('Y-m-d');
        $entries = $this->timeEntries->get($dateStr, collect());

        return $entries->sum(function ($entry) {
            return ($entry->duration * $entry->hourly_rate) / 60;
        });
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
        $this->currentDate->locale(app()->getLocale());

        return view('livewire.time-tracking.time-entries-calendar', [
            'calendarDays' => $this->calendarDays,
            'timeEntries' => $this->timeEntries,
            'projects' => $this->projects,
            'dayNamesShort' => CalendarService::getDayNamesShort(),
            'monthName' => __('time-tracking.' . strtolower($this->currentDate->format('F'))) . ' ' . $this->currentDate->format('Y'),
        ]);
    }
}
