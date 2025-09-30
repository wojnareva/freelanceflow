<?php

namespace App\Livewire\Invoicing;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Livewire\Component;

class InvoiceBuilder extends Component
{
    public $step = 1; // 1: Select Time Entries, 2: Invoice Details, 3: Review

    // Step 1: Time Entry Selection
    public $selectedClient = '';

    public $selectedProject = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $selectedTimeEntries = [];

    public $availableTimeEntries = [];

    // Step 2: Invoice Details
    public $invoiceNumber = '';

    public $issueDate = '';

    public $dueDate = '';

    public $taxRate = '0';

    public $currency = 'USD';

    public $notes = '';

    public $clientDetails = '';

    // Calculated totals
    public $subtotal = 0;

    public $taxAmount = 0;

    public $total = 0;

    protected $rules = [
        'selectedTimeEntries' => 'array',
        'invoiceNumber' => 'required|string|unique:invoices,invoice_number',
        'issueDate' => 'required|date',
        'dueDate' => 'required|date|after_or_equal:issueDate',
        'taxRate' => 'required|numeric|min:0|max:100',
        'currency' => 'required|string|max:3',
        'notes' => 'nullable|string|max:1000',
        'clientDetails' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->issueDate = Carbon::now()->format('Y-m-d');
        $this->dueDate = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->invoiceNumber = $this->generateInvoiceNumber();

        if (app()->getLocale() === 'cs') {
            $this->currency = 'CZK';
        }

        $this->loadTimeEntries();
    }

    public function updatedSelectedClient()
    {
        $this->selectedProject = '';
        $this->loadTimeEntries();
    }

    public function updatedSelectedProject()
    {
        $this->loadTimeEntries();
    }

    public function updatedDateFrom()
    {
        $this->loadTimeEntries();
    }

    public function updatedDateTo()
    {
        $this->loadTimeEntries();
    }

    public function loadTimeEntries()
    {
        $query = TimeEntry::with(['task', 'project', 'project.client'])
            ->where('billable', true)
            ->whereNull('invoice_item_id')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo]);

        if ($this->selectedClient && $this->selectedClient !== '') {
            $query->whereHas('project.client', function ($q) {
                $q->where('id', (int)$this->selectedClient);
            });
        }

        if ($this->selectedProject && $this->selectedProject !== '') {
            $query->where('project_id', (int)$this->selectedProject);
        }

        $this->availableTimeEntries = $query->orderBy('date', 'desc')->get();

        // Clear selected entries if they're no longer available
        $availableIds = $this->availableTimeEntries->pluck('id')->toArray();
        $this->selectedTimeEntries = array_intersect($this->selectedTimeEntries, $availableIds);

        $this->calculateTotals();
    }

    public function toggleTimeEntry($timeEntryId)
    {
        if (in_array($timeEntryId, $this->selectedTimeEntries)) {
            $this->selectedTimeEntries = array_values(array_diff($this->selectedTimeEntries, [$timeEntryId]));
        } else {
            $this->selectedTimeEntries[] = $timeEntryId;
        }

        $this->calculateTotals();
    }

    public function selectAllTimeEntries()
    {
        $this->selectedTimeEntries = $this->availableTimeEntries->pluck('id')->toArray();
        $this->calculateTotals();
    }

    public function clearSelectedTimeEntries()
    {
        $this->selectedTimeEntries = [];
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $selectedEntries = $this->availableTimeEntries->whereIn('id', $this->selectedTimeEntries);

        $this->subtotal = $selectedEntries->sum(function ($entry) {
            return ($entry->duration / 60) * $entry->hourly_rate;
        });

        $this->taxAmount = $this->subtotal * ($this->taxRate / 100);
        $this->total = $this->subtotal + $this->taxAmount;
    }

    public function updatedTaxRate()
    {
        $this->calculateTotals();
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            // Allow proceeding even without time entries for manual invoices
            // Auto-fill client details if we have selected entries
            if (! empty($this->selectedTimeEntries)) {
                $firstEntry = $this->availableTimeEntries->firstWhere('id', $this->selectedTimeEntries[0]);
                if ($firstEntry && $firstEntry->project && $firstEntry->project->client) {
                    $client = $firstEntry->project->client;
                    $this->clientDetails = $client->name."\n".
                                         ($client->email ? $client->email."\n" : '').
                                         ($client->phone ? $client->phone."\n" : '').
                                         ($client->address ? $client->address : '');
                }
            }
        } elseif ($this->step === 2) {
            $this->validate();
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function createInvoice()
    {
        $this->validate();

        // Allow creating invoices without time entries for manual invoices

        $selectedEntries = TimeEntry::whereIn('id', $this->selectedTimeEntries)->get();

        // Determine client and project
        $client = null;
        $primaryProject = null;

        if ($selectedEntries->isNotEmpty()) {
            // Use data from selected time entries
            $primaryProject = $selectedEntries->first()->project;
            $client = $primaryProject->client;
        } else {
            // For manual invoices without time entries, we need a client
            // Since we don't have client selection for manual invoices yet,
            // we'll create with null client_id and rely on clientDetails
            $client = null;
            $primaryProject = null;
        }

        // Create the invoice
        $invoice = Invoice::create([
            'invoice_number' => $this->invoiceNumber,
            'client_id' => $client ? $client->id : null,
            'project_id' => $primaryProject ? $primaryProject->id : null,
            'status' => 'draft',
            'issue_date' => $this->issueDate,
            'due_date' => $this->dueDate,
            'subtotal' => $this->subtotal,
            'tax_rate' => $this->taxRate,
            'tax_amount' => $this->taxAmount,
            'total' => $this->total,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'client_details' => $this->clientDetails,
        ]);

        // Create invoice items from time entries
        foreach ($selectedEntries as $entry) {
            $invoiceItem = InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type' => 'time',
                'description' => $entry->description,
                'quantity' => $entry->duration / 60, // Convert minutes to hours
                'rate' => $entry->hourly_rate,
                'amount' => ($entry->duration / 60) * $entry->hourly_rate,
            ]);

            // Link the time entry to this invoice item
            $entry->update(['invoice_item_id' => $invoiceItem->id]);
        }

        session()->flash('success', 'Invoice created successfully!');

        return redirect()->route('invoices.show', $invoice);
    }

    public function getClientsProperty()
    {
        return Client::orderBy('name')->get();
    }

    public function getProjectsProperty()
    {
        if ($this->selectedClient && $this->selectedClient !== '') {
            return Project::where('client_id', (int)$this->selectedClient)->orderBy('name')->get();
        }

        return collect();
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'INV-'.Carbon::now()->format('Y').'-';
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.invoicing.invoice-builder', [
            'clients' => $this->clients,
            'projects' => $this->projects,
        ]);
    }
}
