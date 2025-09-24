<?php

namespace App\Livewire\Dashboard;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Services\CurrencyService;
use App\Services\PerformanceService;
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
        $performanceService = app(PerformanceService::class);
        $userId = auth()->id();

        // Cache the entire dashboard stats calculation
        $stats = $performanceService->getDashboardStats($userId, function () use ($userId) {
            $currencyService = app(CurrencyService::class);
            $userCurrency = $currencyService->getUserCurrency();

            // Use DB aggregation instead of loading models and looping
            // For same currency, we can aggregate directly. For multi-currency, we need to handle differently
            $monthlyRevenueQuery = Invoice::where('user_id', $userId)
                ->where('status', 'paid')
                ->whereMonth('paid_at', Carbon::now()->month)
                ->whereYear('paid_at', Carbon::now()->year);

            // Always convert to user's currency if there are mixed currencies
            // Get all invoices with their currency
            $monthlyInvoices = $monthlyRevenueQuery->select('total', 'currency')->get();
            
            $monthlyRevenue = 0;
            foreach ($monthlyInvoices as $invoice) {
                // Get invoice currency or default to user's currency if not set
                $invoiceCurrency = \App\Enums\Currency::tryFrom($invoice->currency) ?? $userCurrency;
                if ($invoiceCurrency === $userCurrency) {
                    // Same currency, no conversion needed
                    $monthlyRevenue += $invoice->total;
                } else {
                    // Convert to user's currency
                    $monthlyRevenue += $currencyService->convert($invoice->total, $invoiceCurrency, $userCurrency);
                }
            }

            // Optimize unpaid and overdue calculations
            $unpaidQuery = Invoice::where('user_id', $userId)->whereIn('status', ['sent', 'overdue']);
            $overdueQuery = Invoice::where('user_id', $userId)->where('status', 'overdue');

            // Handle currency conversion for unpaid invoices
            $unpaidTotal = 0;
            $unpaidInvoiceList = $unpaidQuery->select('total', 'currency')->get();
            foreach ($unpaidInvoiceList as $invoice) {
                $invoiceCurrency = \App\Enums\Currency::tryFrom($invoice->currency) ?? $userCurrency;
                if ($invoiceCurrency === $userCurrency) {
                    $unpaidTotal += $invoice->total;
                } else {
                    $unpaidTotal += $currencyService->convert($invoice->total, $invoiceCurrency, $userCurrency);
                }
            }
            $unpaidInvoices = $unpaidTotal;

            // Handle currency conversion for overdue invoices
            $overdueTotal = 0;
            $overdueInvoiceList = $overdueQuery->select('total', 'currency')->get();
            foreach ($overdueInvoiceList as $invoice) {
                $invoiceCurrency = \App\Enums\Currency::tryFrom($invoice->currency) ?? $userCurrency;
                if ($invoiceCurrency === $userCurrency) {
                    $overdueTotal += $invoice->total;
                } else {
                    $overdueTotal += $currencyService->convert($invoice->total, $invoiceCurrency, $userCurrency);
                }
            }
            $overdueInvoices = $overdueTotal;

            // Optimize other stats with user scoping
            $activeProjects = Project::where('user_id', $userId)->where('status', 'active')->count();
            $totalClients = Client::where('user_id', $userId)->count();
            $hoursThisWeek = TimeEntry::where('user_id', $userId)
                ->whereBetween('date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])->sum('duration') / 60;

            return [
                'monthlyRevenue' => $monthlyRevenue,
                'unpaidInvoices' => $unpaidInvoices,
                'overdueInvoices' => $overdueInvoices,
                'activeProjects' => $activeProjects,
                'totalClients' => $totalClients,
                'hoursThisWeek' => $hoursThisWeek,
            ];
        });

        // Assign cached values to component properties
        $this->monthlyRevenue = $stats['monthlyRevenue'];
        $this->unpaidInvoices = $stats['unpaidInvoices'];
        $this->overdueInvoices = $stats['overdueInvoices'];
        $this->activeProjects = $stats['activeProjects'];
        $this->totalClients = $stats['totalClients'];
        $this->hoursThisWeek = $stats['hoursThisWeek'];
    }

    public function refreshStats()
    {
        // Clear the cache before recalculating
        $performanceService = app(PerformanceService::class);
        $performanceService->clearDashboardStatsCache(auth()->id());
        
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
