<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'name',
        'description',
        'status',
        'budget',
        'hourly_rate',
        'deadline',
        'started_at',
        'completed_at',
        'color',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'deadline' => 'date',
        'started_at' => 'date',
        'completed_at' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->timeEntries->sum(fn($entry) => $entry->duration / 60);
    }

    public function getProgressAttribute(): float
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return 0;
        
        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return ($completedTasks / $totalTasks) * 100;
    }
}
