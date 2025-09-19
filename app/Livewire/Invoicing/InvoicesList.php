<?php

namespace App\Livewire\Invoicing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Client;

class InvoicesList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $clientFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'clientFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingClientFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->clientFilter = '';
        $this->resetPage();
    }

    public function updateInvoiceStatus($invoiceId, $status)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->update(['status' => $status]);
        
        if ($status === 'paid') {
            $invoice->update(['paid_at' => now()]);
        } else {
            $invoice->update(['paid_at' => null]);
        }

        session()->flash('success', 'Invoice status updated successfully!');
    }

    public function deleteInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->delete();
        
        session()->flash('success', 'Invoice deleted successfully!');
    }

    public function getInvoicesProperty()
    {
        $query = Invoice::with(['client', 'project'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('client', function ($clientQuery) {
                          $clientQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->clientFilter, function ($query) {
                $query->where('client_id', $this->clientFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(10);
    }

    public function getClientsProperty()
    {
        return Client::orderBy('name')->get();
    }

    public function getStatsProperty()
    {
        $totalInvoices = Invoice::count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $pendingInvoices = Invoice::where('status', 'sent')->count();
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
                                 ->where('due_date', '<', now())
                                 ->count();

        return [
            'total_invoices' => $totalInvoices,
            'total_revenue' => $totalRevenue,
            'pending_invoices' => $pendingInvoices,
            'overdue_invoices' => $overdueInvoices,
        ];
    }

    public function render()
    {
        return view('livewire.invoicing.invoices-list', [
            'invoices' => $this->invoices,
            'clients' => $this->clients,
            'stats' => $this->stats,
        ]);
    }
}