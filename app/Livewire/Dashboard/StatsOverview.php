<?php

namespace App\Livewire\Dashboard;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Livewire\Component;

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
        $currencyService = app(CurrencyService::class);
        $userCurrency = $currencyService->getUserCurrency();

        // Calculate monthly revenue with currency conversion
        $monthlyRevenue = 0;
        $monthlyInvoices = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->get();

        foreach ($monthlyInvoices as $invoice) {
            $monthlyRevenue += $currencyService->convert($invoice->total, $invoice->currency, $userCurrency);
        }
        $this->monthlyRevenue = $monthlyRevenue;

        // Calculate unpaid invoices with currency conversion
        $unpaidTotal = 0;
        $unpaidInvoices = Invoice::whereIn('status', ['sent', 'overdue'])->get();
        foreach ($unpaidInvoices as $invoice) {
            $unpaidTotal += $currencyService->convert($invoice->total, $invoice->currency, $userCurrency);
        }
        $this->unpaidInvoices = $unpaidTotal;

        // Calculate overdue invoices with currency conversion
        $overdueTotal = 0;
        $overdueInvoices = Invoice::where('status', 'overdue')->get();
        foreach ($overdueInvoices as $invoice) {
            $overdueTotal += $currencyService->convert($invoice->total, $invoice->currency, $userCurrency);
        }
        $this->overdueInvoices = $overdueTotal;

        $this->activeProjects = Project::where('status', 'active')->count();
        $this->totalClients = Client::count();
        $this->hoursThisWeek = TimeEntry::whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->sum('duration') / 60;
    }

    public function refreshStats()
    {
        $this->calculateStats();
    }

    public function getFormattedMonthlyRevenueProperty()
    {
        $currencyService = app(CurrencyService::class);

        return $currencyService->format($this->monthlyRevenue, $currencyService->getUserCurrency());
    }

    public function getFormattedUnpaidInvoicesProperty()
    {
        $currencyService = app(CurrencyService::class);

        return $currencyService->format($this->unpaidInvoices, $currencyService->getUserCurrency());
    }

    public function getFormattedOverdueInvoicesProperty()
    {
        $currencyService = app(CurrencyService::class);

        return $currencyService->format($this->overdueInvoices, $currencyService->getUserCurrency());
    }

    public function render()
    {
        $currencyService = app(CurrencyService::class);

        return view('livewire.dashboard.stats-overview', [
            'userCurrency' => $currencyService->getUserCurrency(),
        ]);
    }
}
