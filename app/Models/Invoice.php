<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\InvoiceStatus;
use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'project_id',
        'invoice_template_id',
        'status',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'currency',
        'notes',
        'client_details',
        'paid_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'currency' => Currency::class,
        'status' => InvoiceStatus::class,
        'client_details' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoiceTemplate(): BelongsTo
    {
        return $this->belongsTo(InvoiceTemplate::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'paid';
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total - $this->payments->sum('amount');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return $this->currency->formatAmount($this->subtotal);
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return $this->currency->formatAmount($this->tax_amount);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->currency->formatAmount($this->total);
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return $this->currency->formatAmount($this->remaining_amount);
    }

    public function convertToUserCurrency(): array
    {
        $currencyService = app(CurrencyService::class);
        $userCurrency = $currencyService->getUserCurrency();

        if ($this->currency === $userCurrency) {
            return [
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'total' => $this->total,
                'currency' => $userCurrency,
                'exchange_rate' => 1.0,
            ];
        }

        $exchangeRate = $currencyService->getExchangeRate($this->currency, $userCurrency);

        return [
            'subtotal' => $currencyService->convert($this->subtotal, $this->currency, $userCurrency),
            'tax_amount' => $currencyService->convert($this->tax_amount, $this->currency, $userCurrency),
            'total' => $currencyService->convert($this->total, $this->currency, $userCurrency),
            'currency' => $userCurrency,
            'exchange_rate' => $exchangeRate,
        ];
    }

    public static function generateInvoiceNumber(): string
    {
        return 'INV-'.date('Y').'-'.str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (! $invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }
}
