<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $amount = '';
    public $currency = 'USD';
    public $category = '';
    public $project_id = '';
    public $billable = false;
    public $status = 'pending';
    public $expense_date;
    public $receipt;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'amount' => 'required|numeric|min:0.01',
        'currency' => 'required|string|size:3',
        'category' => 'required|string',
        'project_id' => 'nullable|exists:projects,id',
        'billable' => 'boolean',
        'status' => 'required|string|in:pending,approved,rejected,reimbursed',
        'expense_date' => 'required|date|before_or_equal:today',
        'receipt' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:10240',
    ];

    protected $messages = [
        'title.required' => 'The expense title is required.',
        'amount.required' => 'The amount is required.',
        'amount.min' => 'The amount must be greater than 0.',
        'currency.size' => 'Currency must be a 3-letter code.',
        'category.required' => 'Please select a category.',
        'expense_date.required' => 'The expense date is required.',
        'receipt.mimes' => 'Receipt must be a JPEG, PNG, or PDF file.',
        'receipt.max' => 'Receipt file size cannot exceed 10MB.',
    ];

    public function mount()
    {
        $this->expense_date = now()->format('Y-m-d');
        $this->currency = auth()->user()->default_currency ?? (app()->getLocale() === 'cs' ? 'CZK' : 'USD');
    }

    public function save()
    {
        $this->validate();

        $receiptPath = null;
        if ($this->receipt) {
            $receiptPath = $this->receipt->store('receipts', 'public');
        }

        $expense = Expense::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'category' => $this->category,
            'project_id' => $this->project_id ?: null,
            'billable' => $this->billable,
            'billed' => false,
            'status' => $this->status,
            'expense_date' => $this->expense_date,
            'receipt_path' => $receiptPath,
        ]);

        $this->dispatch('expense-created', [
            'expense' => $expense->title,
            'amount' => $expense->formatted_amount
        ]);

        return redirect()->route('expenses.index');
    }

    public function getProjectsProperty()
    {
        return Project::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.expenses.create', [
            'projects' => $this->projects,
            'categories' => Expense::getCategories(),
        ]);
    }
}