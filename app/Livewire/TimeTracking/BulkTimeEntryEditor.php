<?php

namespace App\Livewire\TimeTracking;

use App\Models\Project;
use App\Models\TimeEntry;
use Livewire\Component;
use Livewire\WithPagination;

class BulkTimeEntryEditor extends Component
{
    use WithPagination;

    public $selectedEntries = [];

    public $selectAll = false;

    public $search = '';

    public $projectFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $showOnlyBillable = false;

    // Bulk edit properties
    public $bulkAction = '';

    public $bulkProjectId = '';

    public $bulkBillable = '';

    public $bulkHourlyRate = '';

    public $bulkDescription = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedEntries = $this->timeEntries->pluck('id')->toArray();
        } else {
            $this->selectedEntries = [];
        }
    }

    public function updatedSelectedEntries()
    {
        $this->selectAll = count($this->selectedEntries) === $this->timeEntries->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedEntries = [];
        $this->selectAll = false;
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
        $this->selectedEntries = [];
        $this->selectAll = false;
    }

    public function applyBulkAction()
    {
        if (empty($this->selectedEntries)) {
            session()->flash('error', 'Please select at least one time entry.');

            return;
        }

        $count = 0;

        switch ($this->bulkAction) {
            case 'delete':
                $count = TimeEntry::whereIn('id', $this->selectedEntries)
                    ->where('user_id', auth()->id())
                    ->count();

                TimeEntry::whereIn('id', $this->selectedEntries)
                    ->where('user_id', auth()->id())
                    ->delete();

                session()->flash('success', "Deleted {$count} time entries.");
                break;

            case 'update_project':
                if (! $this->bulkProjectId) {
                    session()->flash('error', 'Please select a project.');

                    return;
                }

                $project = Project::find($this->bulkProjectId);
                $count = TimeEntry::whereIn('id', $this->selectedEntries)
                    ->where('user_id', auth()->id())
                    ->update([
                        'project_id' => $this->bulkProjectId,
                        'hourly_rate' => $project->hourly_rate ?? $project->client->hourly_rate ?? 0,
                    ]);

                session()->flash('success', "Updated project for {$count} time entries.");
                break;

            case 'update_billable':
                if ($this->bulkBillable === '') {
                    session()->flash('error', 'Please select billable status.');

                    return;
                }

                $count = TimeEntry::whereIn('id', $this->selectedEntries)
                    ->where('user_id', auth()->id())
                    ->update(['billable' => (bool) $this->bulkBillable]);

                $status = $this->bulkBillable ? 'billable' : 'non-billable';
                session()->flash('success', "Marked {$count} time entries as {$status}.");
                break;

            case 'update_rate':
                if (! $this->bulkHourlyRate || $this->bulkHourlyRate <= 0) {
                    session()->flash('error', 'Please enter a valid hourly rate.');

                    return;
                }

                $count = TimeEntry::whereIn('id', $this->selectedEntries)
                    ->where('user_id', auth()->id())
                    ->update(['hourly_rate' => $this->bulkHourlyRate]);

                session()->flash('success', "Updated hourly rate for {$count} time entries.");
                break;

            case 'append_description':
                if (! $this->bulkDescription) {
                    session()->flash('error', 'Please enter description to append.');

                    return;
                }

                $entries = TimeEntry::whereIn('id', $this->selectedEntries)
                    ->where('user_id', auth()->id())
                    ->get();

                foreach ($entries as $entry) {
                    $entry->update([
                        'description' => $entry->description.' '.$this->bulkDescription,
                    ]);
                }

                $count = $entries->count();
                session()->flash('success', "Appended description to {$count} time entries.");
                break;

            default:
                session()->flash('error', 'Please select an action.');

                return;
        }

        $this->selectedEntries = [];
        $this->selectAll = false;
        $this->resetBulkForm();
        $this->resetPage();
    }

    public function resetBulkForm()
    {
        $this->bulkAction = '';
        $this->bulkProjectId = '';
        $this->bulkBillable = '';
        $this->bulkHourlyRate = '';
        $this->bulkDescription = '';
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
            ->paginate(50);
    }

    public function getProjectsProperty()
    {
        return Project::where('status', 'active')
            ->with('client')
            ->orderBy('name')
            ->get();
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
        return view('livewire.time-tracking.bulk-time-entry-editor', [
            'timeEntries' => $this->timeEntries,
            'projects' => $this->projects,
        ]);
    }
}
