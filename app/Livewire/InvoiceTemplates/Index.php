<?php

namespace App\Livewire\InvoiceTemplates;

use App\Models\InvoiceTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $filter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function toggleTemplate($templateId)
    {
        $template = InvoiceTemplate::findOrFail($templateId);
        $template->update(['is_active' => ! $template->is_active]);

        $this->dispatch('template-toggled', [
            'template' => $template->name,
            'status' => $template->is_active ? 'activated' : 'deactivated',
        ]);
    }

    public function generateInvoice($templateId)
    {
        $template = InvoiceTemplate::findOrFail($templateId);

        if (! $template->is_active) {
            $this->dispatch('error', 'Cannot generate invoice from inactive template');

            return;
        }

        try {
            $invoice = $template->generateInvoice();
            $this->dispatch('invoice-generated', [
                'invoice' => $invoice->invoice_number,
                'client' => $invoice->client->name,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('error', 'Failed to generate invoice: '.$e->getMessage());
        }
    }

    public function getTemplatesProperty()
    {
        $query = InvoiceTemplate::with(['client', 'project'])
            ->where('user_id', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('client', function ($clientQuery) {
                        $clientQuery->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        $query = match ($this->filter) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            'due' => $query->dueForGeneration(),
            default => $query,
        };

        return $query->latest()->paginate(10);
    }

    public function render()
    {
        return view('livewire.invoice-templates.index', [
            'templates' => $this->templates,
        ])->layout('layouts.app');
    }
}
