<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
        $months = collect();
        $revenueData = collect();
        
        // Get last 6 months of revenue data
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');
            
            $revenue = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('total');
            
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
            $previousPeriodRevenue += Invoice::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('total');
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
        return view('livewire.dashboard.revenue-chart');
    }
}
