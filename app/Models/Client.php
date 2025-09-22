<?php

namespace App\Models;

use App\Enums\Currency;
use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'email',
        'phone',
        'vat_number',
        'address',
        'currency',
        'hourly_rate',
        'notes',
        'settings',
        'ico',
        'dic',
        'company_registry_data',
        'registry_updated_at',
    ];

    protected $casts = [
        'currency' => Currency::class,
        'settings' => 'array',
        'hourly_rate' => 'decimal:2',
        'company_registry_data' => 'array',
        'registry_updated_at' => 'datetime',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasManyThrough(TimeEntry::class, Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedHourlyRateAttribute(): string
    {
        if (! $this->hourly_rate) {
            return 'Not set';
        }

        return $this->currency->formatAmount($this->hourly_rate);
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->invoices()
            ->where('status', 'paid')
            ->sum('total');
    }

    public function getFormattedTotalRevenueAttribute(): string
    {
        return $this->currency->formatAmount($this->total_revenue);
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->timeEntries()->sum('duration') / 60;
    }

    public function convertAmountToUserCurrency(float $amount): string
    {
        $currencyService = app(CurrencyService::class);

        return $currencyService->convertAndFormat($amount, $this->currency);
    }
}
