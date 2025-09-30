<?php

namespace App\Livewire\InvoiceTemplates;

use App\Models\Client;
use App\Models\InvoiceTemplate;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $client_id = '';
    public $frequency = 'monthly';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $amount = '';
    public $days_until_due = 30;

    protected $rules = [
        'name' => 'required|string|max:255',
        'client_id' => 'required|exists:clients,id',
        'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after:start_date',
        'amount' => 'required|numeric|min:0',
        'days_until_due' => 'required|integer|min:0|max:365',
    ];

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        // Calculate next generation date based on frequency and start date
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $nextGenDate = match($this->frequency) {
            'weekly' => $startDate->copy()->addWeek(),
            'monthly' => $startDate->copy()->addMonth(),
            'quarterly' => $startDate->copy()->addQuarter(),
            'yearly' => $startDate->copy()->addYear(),
            default => $startDate->copy()->addMonth(),
        };

        InvoiceTemplate::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'client_id' => $this->client_id,
            'frequency' => $this->frequency,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'next_generation_date' => $nextGenDate,
            'amount' => $this->amount,
            'currency' => 'CZK', // Default currency
            'days_until_due' => $this->days_until_due,
            'line_items' => json_encode([]), // Empty line items initially
        ]);

        session()->flash('success', __('invoices.template_created_successfully'));

        return redirect()->route('invoice-templates.index');
    }

    public function render()
    {
        return view('livewire.invoice-templates.create', [
            'clients' => Client::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }
}