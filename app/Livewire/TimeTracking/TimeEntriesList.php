<?php

namespace App\Livewire\TimeTracking;

use App\Models\Project;
use App\Models\TimeEntry;
use Livewire\Component;
use Livewire\WithPagination;

class TimeEntriesList extends Component
{
    use WithPagination;

    public $search = '';

    public $projectFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $showOnlyBillable = false;

    public $showEditModal = false;

    public $editingEntry = null;

    // Edit form properties
    public $editDescription = '';

    public $editDuration = '';

    public $editDate = '';

    public $editBillable = true;

    protected $rules = [
        'editDescription' => 'required|min:3|max:255',
        'editDuration' => 'required|numeric|min:1',
        'editDate' => 'required|date',
        'editBillable' => 'boolean',
    ];

    public function mount()
    {
        $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = now()->endOfWeek()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
    }

    public function editEntry($entryId)
    {
        $this->editingEntry = TimeEntry::findOrFail($entryId);
        $this->editDescription = $this->editingEntry->description;
        $this->editDuration = $this->editingEntry->duration;
        $this->editDate = $this->editingEntry->date->format('Y-m-d');
        $this->editBillable = $this->editingEntry->billable;
        $this->showEditModal = true;
    }

    public function updateEntry()
    {
        $this->validate();

        $this->editingEntry->update([
            'description' => $this->editDescription,
            'duration' => $this->editDuration,
            'date' => $this->editDate,
            'billable' => $this->editBillable,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Time entry updated successfully!');
    }

    public function deleteEntry($entryId)
    {
        TimeEntry::findOrFail($entryId)->delete();
        session()->flash('success', 'Time entry deleted successfully!');
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingEntry = null;
        $this->reset(['editDescription', 'editDuration', 'editDate', 'editBillable']);
    }

    public function getTimeEntriesProperty()
    {
        return TimeEntry::with(['project.client', 'task'])
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->projectFilter, function ($query) {
                $query->where('project_id', $this->projectFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->where('date', '<=', $this->dateTo);
            })
            ->when($this->showOnlyBillable, function ($query) {
                $query->where('billable', true);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getProjectsProperty()
    {
        return Project::where('status', 'active')
            ->with('client')
            ->orderBy('name')
            ->get();
    }

    public function getTotalHoursProperty()
    {
        return TimeEntry::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->projectFilter, function ($query) {
                $query->where('project_id', $this->projectFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->where('date', '<=', $this->dateTo);
            })
            ->when($this->showOnlyBillable, function ($query) {
                $query->where('billable', true);
            })
            ->sum('duration') / 60; // Convert minutes to hours
    }

    public function getTotalEarningsProperty()
    {
        return TimeEntry::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%'.$this->search.'%');
            })
            ->when($this->projectFilter, function ($query) {
                $query->where('project_id', $this->projectFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->where('date', '<=', $this->dateTo);
            })
            ->when($this->showOnlyBillable, function ($query) {
                $query->where('billable', true);
            })
            ->selectRaw('SUM(duration * hourly_rate / 60) as total')
            ->value('total') ?? 0;
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
        return view('livewire.time-tracking.time-entries-list', [
            'timeEntries' => $this->timeEntries,
            'projects' => $this->projects,
            'totalHours' => $this->totalHours,
            'totalEarnings' => $this->totalEarnings,
        ]);
    }
}
