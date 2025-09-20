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
        'user_id',
        'client_id',
        'name',
        'description',
        'status',
        'budget',
        'hourly_rate',
        'start_date',
        'end_date',
        'estimated_hours',
        'deadline',
        'started_at',
        'completed_at',
        'color',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
        'started_at' => 'date',
        'completed_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
        return $this->timeEntries->sum(fn ($entry) => $entry->duration / 60);
    }

    public function getProgressAttribute(): float
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();

        return ($completedTasks / $totalTasks) * 100;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {
            // Delete related records before deleting the project
            $project->timeEntries()->delete();
            $project->tasks()->delete();
            $project->expenses()->delete();
            // Note: We don't delete invoices as they may need to be preserved for accounting
        });
    }
}
