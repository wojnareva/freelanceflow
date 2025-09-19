<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'description',
        'duration',
        'hourly_rate',
        'billable',
        'billed',
        'invoice_item_id',
        'date',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'billable' => 'boolean',
        'billed' => 'boolean',
        'date' => 'date',
        'started_at' => 'datetime:H:i',
        'ended_at' => 'datetime:H:i',
        'duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function getHoursAttribute(): float
    {
        return $this->duration / 60;
    }

    public function getAmountAttribute(): float
    {
        return ($this->duration / 60) * $this->hourly_rate;
    }

    public function getDurationFormattedAttribute(): string
    {
        $hours = intval($this->duration / 60);
        $minutes = $this->duration % 60;
        return sprintf('%dh %02dm', $hours, $minutes);
    }
}
