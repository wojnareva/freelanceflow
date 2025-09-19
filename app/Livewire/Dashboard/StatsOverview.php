<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\Client;
use Carbon\Carbon;

class StatsOverview extends Component
{
    public $monthlyRevenue;
    public $unpaidInvoices;
    public $activeProjects;
    public $hoursThisWeek;
    public $totalClients;
    public $overdueInvoices;

    public function mount()
    {
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $this->monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('total');

        $this->unpaidInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->sum('total');

        $this->overdueInvoices = Invoice::where('status', 'overdue')
            ->sum('total');

        $this->activeProjects = Project::where('status', 'active')->count();

        $this->totalClients = Client::count();

        $this->hoursThisWeek = TimeEntry::whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('duration') / 60;
    }

    public function refreshStats()
    {
        $this->calculateStats();
    }

    public function render()
    {
        return view('livewire.dashboard.stats-overview');
    }
}
