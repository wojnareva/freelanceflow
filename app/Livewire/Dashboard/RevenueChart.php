<?php

namespace App\Livewire\Dashboard;

use App\Models\Invoice;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Livewire\Component;

class RevenueChart extends Component
{
    public $chartData;

    public $totalRevenue;

    public $previousPeriodRevenue;

    public $growthPercentage;

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        $currencyService = app(CurrencyService::class);
        $userCurrency = $currencyService->getUserCurrency();
        
        $months = collect();
        $revenueData = collect();

        // Get last 6 months of revenue data
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');

            // Get invoices for the month and convert to user currency
            $monthInvoices = Invoice::where('user_id', auth()->id())
                ->where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->get();
            
            $revenue = 0;
            foreach ($monthInvoices as $invoice) {
                $invoiceCurrency = \App\Enums\Currency::tryFrom($invoice->currency) ?? \App\Enums\Currency::USD;
                $revenue += $currencyService->convert($invoice->total, $invoiceCurrency, $userCurrency);
            }

            $months->push($monthName);
            $revenueData->push($revenue);
        }

        $this->chartData = [
            'labels' => $months->toArray(),
            'data' => $revenueData->toArray(),
        ];

        // Calculate metrics
        $this->totalRevenue = $revenueData->sum();

        // Previous 6 months for comparison
        $previousPeriodRevenue = 0;
        for ($i = 11; $i >= 6; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthInvoices = Invoice::where('user_id', auth()->id())
                ->where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->get();
            
            foreach ($monthInvoices as $invoice) {
                $invoiceCurrency = \App\Enums\Currency::tryFrom($invoice->currency) ?? \App\Enums\Currency::USD;
                $previousPeriodRevenue += $currencyService->convert($invoice->total, $invoiceCurrency, $userCurrency);
            }
        }

        $this->previousPeriodRevenue = $previousPeriodRevenue;

        if ($previousPeriodRevenue > 0) {
            $this->growthPercentage = (($this->totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
        } else {
            $this->growthPercentage = $this->totalRevenue > 0 ? 100 : 0;
        }
    }

    public function refreshChart()
    {
        $this->loadChartData();
    }

    public function render()
    {
        $currencyService = app(CurrencyService::class);
        $userCurrency = $currencyService->getUserCurrency();
        
        return view('livewire.dashboard.revenue-chart', [
            'userCurrency' => $userCurrency,
            'formattedTotal' => $currencyService->format($this->totalRevenue, $userCurrency),
            'formattedAverage' => $currencyService->format(
                count($this->chartData['data']) > 0 ? $this->totalRevenue / count($this->chartData['data']) : 0,
                $userCurrency
            ),
        ]);
    }
}
