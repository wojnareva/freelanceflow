<?php

namespace App\Livewire\Reports;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Client;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $dateRange = 'this_year';
    public $currency = 'USD';
    public $reportType = 'overview';
    public $selectedClient = '';
    public $selectedProject = '';

    protected $queryString = [
        'dateRange' => ['except' => 'this_year'],
        'currency' => ['except' => 'USD'],
        'reportType' => ['except' => 'overview'],
        'selectedClient' => ['except' => ''],
        'selectedProject' => ['except' => ''],
    ];

    public function updatingDateRange()
    {
        $this->dispatch('report-updated');
    }

    public function updatingCurrency()
    {
        $this->dispatch('report-updated');
    }

    public function updatingReportType()
    {
        $this->dispatch('report-updated');
    }

    public function updatingSelectedClient()
    {
        $this->selectedProject = '';
        $this->dispatch('report-updated');
    }

    public function updatingSelectedProject()
    {
        $this->dispatch('report-updated');
    }

    public function exportReport($format)
    {
        $this->dispatch('export-report', ['format' => $format]);
    }

    public function getDateRangeQuery()
    {
        $now = now();
        
        return match ($this->dateRange) {
            'today' => [$now->startOfDay(), $now->endOfDay()],
            'yesterday' => [$now->subDay()->startOfDay(), $now->subDay()->endOfDay()],
            'this_week' => [$now->startOfWeek(), $now->endOfWeek()],
            'last_week' => [$now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()],
            'this_month' => [$now->startOfMonth(), $now->endOfMonth()],
            'last_month' => [$now->subMonth()->startOfMonth(), $now->subMonth()->endOfMonth()],
            'this_quarter' => [$now->startOfQuarter(), $now->endOfQuarter()],
            'last_quarter' => [$now->subQuarter()->startOfQuarter(), $now->subQuarter()->endOfQuarter()],
            'this_year' => [$now->startOfYear(), $now->endOfYear()],
            'last_year' => [$now->subYear()->startOfYear(), $now->subYear()->endOfYear()],
            default => [$now->startOfYear(), $now->endOfYear()],
        };
    }

    public function getOverviewStatsProperty()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        $userId = auth()->id();

        $baseInvoiceQuery = Invoice::where('user_id', $userId)
            ->where('currency', $this->currency)
            ->whereBetween('invoice_date', [$startDate, $endDate]);

        $baseExpenseQuery = Expense::where('user_id', $userId)
            ->where('currency', $this->currency)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        $baseTimeQuery = TimeEntry::where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate]);

        if ($this->selectedClient) {
            $baseInvoiceQuery->where('client_id', $this->selectedClient);
            $baseExpenseQuery->whereHas('project', function ($q) {
                $q->where('client_id', $this->selectedClient);
            });
            $baseTimeQuery->whereHas('project', function ($q) {
                $q->where('client_id', $this->selectedClient);
            });
        }

        if ($this->selectedProject) {
            $baseInvoiceQuery->whereHas('items', function ($q) {
                $q->where('project_id', $this->selectedProject);
            });
            $baseExpenseQuery->where('project_id', $this->selectedProject);
            $baseTimeQuery->where('project_id', $this->selectedProject);
        }

        return [
            'total_revenue' => (clone $baseInvoiceQuery)->where('status', 'paid')->sum('total_amount'),
            'pending_revenue' => (clone $baseInvoiceQuery)->whereIn('status', ['sent', 'viewed'])->sum('total_amount'),
            'overdue_revenue' => (clone $baseInvoiceQuery)->where('status', 'overdue')->sum('total_amount'),
            'total_expenses' => (clone $baseExpenseQuery)->sum('amount'),
            'billable_expenses' => (clone $baseExpenseQuery)->where('billable', true)->sum('amount'),
            'unbilled_expenses' => (clone $baseExpenseQuery)->where('billable', true)->where('billed', false)->sum('amount'),
            'total_hours' => (clone $baseTimeQuery)->sum(DB::raw('TIMESTAMPDIFF(SECOND, start_time, end_time) / 3600')),
            'billable_hours' => (clone $baseTimeQuery)->where('billable', true)->sum(DB::raw('TIMESTAMPDIFF(SECOND, start_time, end_time) / 3600')),
            'invoice_count' => (clone $baseInvoiceQuery)->count(),
            'expense_count' => (clone $baseExpenseQuery)->count(),
        ];
    }

    public function getRevenueByMonthProperty()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        $userId = auth()->id();

        $query = Invoice::where('user_id', $userId)
            ->where('currency', $this->currency)
            ->where('status', 'paid')
            ->whereBetween('invoice_date', [$startDate, $endDate]);

        if ($this->selectedClient) {
            $query->where('client_id', $this->selectedClient);
        }

        return $query->select(
                DB::raw('YEAR(invoice_date) as year'),
                DB::raw('MONTH(invoice_date) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                    'total' => $item->total,
                ];
            });
    }

    public function getTopClientsProperty()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        $userId = auth()->id();

        return Invoice::where('user_id', $userId)
            ->where('currency', $this->currency)
            ->where('status', 'paid')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->with('client')
            ->select('client_id', DB::raw('SUM(total_amount) as total_revenue'), DB::raw('COUNT(*) as invoice_count'))
            ->groupBy('client_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    public function getTopProjectsProperty()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        $userId = auth()->id();

        return TimeEntry::where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->with('project')
            ->select(
                'project_id',
                DB::raw('SUM(TIMESTAMPDIFF(SECOND, start_time, end_time) / 3600) as total_hours'),
                DB::raw('COUNT(*) as entry_count')
            )
            ->groupBy('project_id')
            ->orderByDesc('total_hours')
            ->limit(10)
            ->get();
    }

    public function getExpensesByCategoryProperty()
    {
        [$startDate, $endDate] = $this->getDateRangeQuery();
        $userId = auth()->id();

        $query = Expense::where('user_id', $userId)
            ->where('currency', $this->currency)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($this->selectedProject) {
            $query->where('project_id', $this->selectedProject);
        }

        return $query->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => Expense::getCategories()[$item->category] ?? ucfirst($item->category),
                    'total' => $item->total,
                ];
            });
    }

    public function getProfitabilityProperty()
    {
        $stats = $this->overviewStats;
        
        $profit = $stats['total_revenue'] - $stats['total_expenses'];
        $margin = $stats['total_revenue'] > 0 ? ($profit / $stats['total_revenue']) * 100 : 0;

        return [
            'profit' => $profit,
            'margin' => $margin,
            'revenue' => $stats['total_revenue'],
            'expenses' => $stats['total_expenses'],
        ];
    }

    public function getClientsProperty()
    {
        return Client::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function getProjectsProperty()
    {
        $query = Project::where('user_id', auth()->id());
        
        if ($this->selectedClient) {
            $query->where('client_id', $this->selectedClient);
        }

        return $query->orderBy('name')->get();
    }

    public function getCurrenciesProperty()
    {
        return [
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'CAD' => 'CAD - Canadian Dollar',
            'AUD' => 'AUD - Australian Dollar',
        ];
    }

    public function render()
    {
        return view('livewire.reports.index', [
            'overviewStats' => $this->overviewStats,
            'revenueByMonth' => $this->revenueByMonth,
            'topClients' => $this->topClients,
            'topProjects' => $this->topProjects,
            'expensesByCategory' => $this->expensesByCategory,
            'profitability' => $this->profitability,
            'clients' => $this->clients,
            'projects' => $this->projects,
            'currencies' => $this->currencies,
        ])->layout('layouts.app');
    }
}