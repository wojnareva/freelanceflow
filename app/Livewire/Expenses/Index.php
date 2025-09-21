<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = 'all';
    public $projectFilter = 'all';
    public $billableFilter = 'all';
    public $dateRange = '30days';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => 'all'],
        'projectFilter' => ['except' => 'all'],
        'billableFilter' => ['except' => 'all'],
        'dateRange' => ['except' => '30days'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
    }

    public function updatingBillableFilter()
    {
        $this->resetPage();
    }

    public function updatingDateRange()
    {
        $this->resetPage();
    }

    public function markAsBilled($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        
        if (!$expense->billable) {
            $this->dispatch('error', 'Only billable expenses can be marked as billed');
            return;
        }

        $expense->update(['billed' => true]);
        
        $this->dispatch('expense-billed', [
            'expense' => $expense->title,
            'amount' => $expense->formatted_amount
        ]);
    }

    public function toggleBillable($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        $expense->update(['billable' => !$expense->billable]);
        
        $this->dispatch('expense-updated', [
            'expense' => $expense->title,
            'status' => $expense->billable ? 'marked as billable' : 'marked as non-billable'
        ]);
    }

    public function deleteExpense($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        $expenseName = $expense->title;
        $expense->delete();
        
        $this->dispatch('expense-deleted', ['expense' => $expenseName]);
    }

    public function getExpensesProperty()
    {
        $query = Expense::with(['project'])
            ->where('user_id', auth()->id());

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('project', function ($projectQuery) {
                      $projectQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Category filter
        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }

        // Project filter
        if ($this->projectFilter !== 'all') {
            $query->where('project_id', $this->projectFilter);
        }

        // Billable filter
        if ($this->billableFilter !== 'all') {
            if ($this->billableFilter === 'billable') {
                $query->where('billable', true);
            } elseif ($this->billableFilter === 'non-billable') {
                $query->where('billable', false);
            } elseif ($this->billableFilter === 'unbilled') {
                $query->where('billable', true)->where('billed', false);
            } elseif ($this->billableFilter === 'billed') {
                $query->where('billed', true);
            }
        }

        // Date range filter
        if ($this->dateRange !== 'all') {
            $now = now();
            switch ($this->dateRange) {
                case '7days':
                    $query->where('expense_date', '>=', $now->subDays(7));
                    break;
                case '30days':
                    $query->where('expense_date', '>=', $now->subDays(30));
                    break;
                case '90days':
                    $query->where('expense_date', '>=', $now->subDays(90));
                    break;
                case 'thisyear':
                    $query->whereYear('expense_date', $now->year);
                    break;
                case 'lastyear':
                    $query->whereYear('expense_date', $now->year - 1);
                    break;
            }
        }

        return $query->latest('expense_date')->paginate(10);
    }

    public function getProjectsProperty()
    {
        return Project::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function getTotalStatsProperty()
    {
        $baseQuery = Expense::where('user_id', auth()->id());
        
        // Apply same filters as main query for consistency
        if ($this->search) {
            $baseQuery->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('project', function ($projectQuery) {
                      $projectQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->categoryFilter !== 'all') {
            $baseQuery->where('category', $this->categoryFilter);
        }

        if ($this->projectFilter !== 'all') {
            $baseQuery->where('project_id', $this->projectFilter);
        }

        if ($this->billableFilter !== 'all') {
            if ($this->billableFilter === 'billable') {
                $baseQuery->where('billable', true);
            } elseif ($this->billableFilter === 'non-billable') {
                $baseQuery->where('billable', false);
            } elseif ($this->billableFilter === 'unbilled') {
                $baseQuery->where('billable', true)->where('billed', false);
            } elseif ($this->billableFilter === 'billed') {
                $baseQuery->where('billed', true);
            }
        }

        if ($this->dateRange !== 'all') {
            $now = now();
            switch ($this->dateRange) {
                case '7days':
                    $baseQuery->where('expense_date', '>=', $now->subDays(7));
                    break;
                case '30days':
                    $baseQuery->where('expense_date', '>=', $now->subDays(30));
                    break;
                case '90days':
                    $baseQuery->where('expense_date', '>=', $now->subDays(90));
                    break;
                case 'thisyear':
                    $baseQuery->whereYear('expense_date', $now->year);
                    break;
                case 'lastyear':
                    $baseQuery->whereYear('expense_date', $now->year - 1);
                    break;
            }
        }

        return [
            'total' => (clone $baseQuery)->sum('amount'),
            'billable' => (clone $baseQuery)->where('billable', true)->sum('amount'),
            'unbilled' => (clone $baseQuery)->where('billable', true)->where('billed', false)->sum('amount'),
            'count' => (clone $baseQuery)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.expenses.index', [
            'expenses' => $this->expenses,
            'projects' => $this->projects,
            'categories' => Expense::getCategories(),
            'stats' => $this->totalStats,
        ])->layout('layouts.app');
    }
}
