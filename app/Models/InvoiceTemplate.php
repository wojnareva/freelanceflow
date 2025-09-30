<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'project_id',
        'name',
        'description',
        'frequency',
        'start_date',
        'end_date',
        'next_generation_date',
        'days_until_due',
        'amount',
        'currency',
        'tax_rate',
        'line_items',
        'notes',
        'is_active',
        'invoices_generated',
        'last_generated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_generation_date' => 'date',
        'last_generated_at' => 'date',
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'line_items' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function generateInvoice(): Invoice
    {
        $dueDate = $this->next_generation_date->addDays($this->days_until_due);

        $invoice = Invoice::create([
            'user_id' => $this->user_id,
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'invoice_template_id' => $this->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'status' => 'draft',
            'issue_date' => $this->next_generation_date,
            'due_date' => $dueDate,
            'subtotal' => $this->amount,
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->amount * ($this->tax_rate / 100),
            'total' => $this->amount + ($this->amount * ($this->tax_rate / 100)),
            'currency' => $this->currency,
            'notes' => $this->notes,
            'client_details' => $this->client->toArray(),
        ]);

        // Create invoice items from template
        foreach ($this->line_items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'] ?? 1,
                'rate' => $item['rate'],
                'amount' => ($item['quantity'] ?? 1) * $item['rate'],
                'type' => $item['type'] ?? 'fixed',
            ]);
        }

        // Update template tracking
        $this->increment('invoices_generated');
        $this->update([
            'last_generated_at' => now(),
            'next_generation_date' => $this->calculateNextGenerationDate(),
        ]);

        return $invoice;
    }

    public function calculateNextGenerationDate(): Carbon
    {
        $current = $this->next_generation_date;

        return match ($this->frequency) {
            'weekly' => $current->addWeek(),
            'monthly' => $current->addMonth(),
            'quarterly' => $current->addMonths(3),
            'yearly' => $current->addYear(),
        };
    }

    public function getTotalWithTaxAttribute(): float
    {
        return $this->amount + ($this->amount * ($this->tax_rate / 100));
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForGeneration($query)
    {
        return $query->active()
            ->where('next_generation_date', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            });
    }
}
