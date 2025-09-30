<?php

namespace App\Livewire\Invoicing;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Validation\Rule;
use Livewire\Component;

class InvoiceEditor extends Component
{
    public Invoice $invoice;

    public string $invoiceNumber = '';

    public string $issueDate = '';

    public string $dueDate = '';

    public string $taxRate = '0';

    public string $currency = 'USD';

    public string $notes = '';

    public string $clientDetails = '';

    /**
     * Array of items being edited.
     * Each item: ['id' => int|null, 'type' => string, 'description' => string, 'quantity' => float, 'rate' => float]
     */
    public array $items = [];

    public float $subtotal = 0.0;

    public float $taxAmount = 0.0;

    public float $total = 0.0;

    protected function rules(): array
    {
        return [
            'invoiceNumber' => [
                'required',
                'string',
                Rule::unique('invoices', 'invoice_number')->ignore($this->invoice->id),
            ],
            'issueDate' => ['required', 'date'],
            'dueDate' => ['required', 'date', 'after_or_equal:issueDate'],
            'taxRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency' => ['required', 'string', 'max:3'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'clientDetails' => ['nullable', 'string', 'max:500'],
            'items' => ['array'],
            'items.*.type' => ['required', 'string', Rule::in(['time', 'fixed', 'expense'])],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice->load('items');

        if ($this->invoice->status !== InvoiceStatus::Draft) {
            session()->flash('error', __('invoices.only_draft_editable'));
            redirect()->route('invoices.show', $this->invoice)->send();

            return;
        }

        $this->invoiceNumber = $this->invoice->invoice_number;
        $this->issueDate = optional($this->invoice->issue_date)->format('Y-m-d') ?? '';
        $this->dueDate = optional($this->invoice->due_date)->format('Y-m-d') ?? '';
        $this->taxRate = (string) $this->invoice->tax_rate;
        $this->currency = (string) $this->invoice->currency->value;
        $this->notes = (string) $this->invoice->notes;
        $this->clientDetails = is_array($this->invoice->client_details) ? implode("\n", array_filter($this->invoice->client_details)) : (string) $this->invoice->client_details;

        $this->items = $this->invoice->items->map(function (InvoiceItem $item) {
            return [
                'id' => $item->id,
                'type' => $item->type,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'rate' => (float) $item->rate,
            ];
        })->toArray();

        $this->recalculateTotals();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => null,
            'type' => 'fixed',
            'description' => '',
            'quantity' => 1,
            'rate' => 0,
        ];

        $this->recalculateTotals();
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateTotals();
    }

    public function updatedItems(): void
    {
        $this->recalculateTotals();
    }

    public function updatedTaxRate(): void
    {
        $this->recalculateTotals();
    }

    private function recalculateTotals(): void
    {
        $this->subtotal = 0.0;

        foreach ($this->items as $item) {
            $line = ((float) ($item['quantity'] ?? 0)) * ((float) ($item['rate'] ?? 0));
            $this->subtotal += $line;
        }

        $taxRate = (float) $this->taxRate;
        $this->taxAmount = $this->subtotal * ($taxRate / 100);
        $this->total = $this->subtotal + $this->taxAmount;
    }

    public function save(): void
    {
        $this->validate();

        // Update invoice core fields
        $this->invoice->update([
            'invoice_number' => $this->invoiceNumber,
            'issue_date' => $this->issueDate,
            'due_date' => $this->dueDate,
            'tax_rate' => $this->taxRate,
            'tax_amount' => $this->taxAmount,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'client_details' => $this->clientDetails,
        ]);

        // Sync items
        $existingIds = $this->invoice->items()->pluck('id')->all();
        $keptIds = [];

        foreach ($this->items as $data) {
            $payload = [
                'type' => $data['type'],
                'description' => $data['description'],
                'quantity' => (float) $data['quantity'],
                'rate' => (float) $data['rate'],
                'amount' => (float) $data['quantity'] * (float) $data['rate'],
            ];

            if (! empty($data['id'])) {
                $item = $this->invoice->items()->whereKey($data['id'])->first();
                if ($item) {
                    $item->update($payload);
                    $keptIds[] = $item->id;
                }
            } else {
                $item = $this->invoice->items()->create($payload);
                $keptIds[] = $item->id;
            }
        }

        // Delete removed items
        $toDelete = array_diff($existingIds, $keptIds);
        if (! empty($toDelete)) {
            $this->invoice->items()->whereIn('id', $toDelete)->delete();
        }

        session()->flash('success', __('invoices.updated_successfully'));
        $this->dispatch('invoice-updated');
    }

    public function render()
    {
        return view('livewire.invoicing.invoice-editor', [
            'invoice' => $this->invoice,
        ]);
    }
}
