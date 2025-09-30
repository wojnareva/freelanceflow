<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Expense $expense;

    public $title;

    public $description;

    public $amount;

    public $currency;

    public $category;

    public $project_id;

    public $billable;

    public $status;

    public $expense_date;

    public $receipt;

    public $removeReceipt = false;

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
        $this->title = $this->expense->title;
        $this->description = $this->expense->description;
        $this->amount = $this->expense->amount;
        $this->currency = $this->expense->currency;
        $this->category = $this->expense->category;
        $this->project_id = $this->expense->project_id;
        $this->billable = $this->expense->billable;
        $this->status = $this->expense->status;
        $this->expense_date = $this->expense->expense_date->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $updateData = [
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'category' => $this->category,
            'project_id' => $this->project_id ?: null,
            'billable' => $this->billable,
            'status' => $this->status,
            'expense_date' => $this->expense_date,
        ];

        // Handle receipt upload/removal
        if ($this->receipt) {
            // Delete old receipt if exists
            if ($this->expense->receipt_path) {
                Storage::disk('public')->delete($this->expense->receipt_path);
            }
            // Store new receipt
            $updateData['receipt_path'] = $this->receipt->store('receipts', 'public');
        } elseif ($this->removeReceipt && $this->expense->receipt_path) {
            // Remove existing receipt
            Storage::disk('public')->delete($this->expense->receipt_path);
            $updateData['receipt_path'] = null;
        }

        $this->expense->update($updateData);

        $this->dispatch('expense-updated', [
            'expense' => $this->expense->title,
            'amount' => $this->expense->formatted_amount,
        ]);

        return redirect()->route('expenses.index');
    }

    public function markRemoveReceipt()
    {
        $this->removeReceipt = true;
    }

    public function cancelRemoveReceipt()
    {
        $this->removeReceipt = false;
    }

    public function getProjectsProperty()
    {
        return Project::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.expenses.edit', [
            'projects' => $this->projects,
            'categories' => Expense::getCategories(),
        ]);
    }
}
