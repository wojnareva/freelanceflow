<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'amount',
        'currency',
        'category',
        'billable',
        'billed',
        'receipt_path',
        'expense_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billable' => 'boolean',
        'billed' => 'boolean',
        'expense_date' => 'date',
        'status' => \App\Enums\ExpenseStatus::class,
    ];

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = ($value === '' || $value === null) ? null : $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2).' '.$this->currency;
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'travel' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'meals' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'office' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'software' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            'marketing' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
            'equipment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'other' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'travel' => __('expenses.category_travel'),
            'meals' => __('expenses.category_meals'),
            'office' => __('expenses.category_office'),
            'software' => __('expenses.category_software'),
            'marketing' => __('expenses.category_marketing'),
            'equipment' => __('expenses.category_equipment'),
            'other' => __('expenses.category_other'),
            default => ucfirst($this->category ?? __('expenses.category_other')),
        };
    }

    public function hasReceipt(): bool
    {
        return ! empty($this->receipt_path);
    }

    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    public function scopeBilled($query)
    {
        return $query->where('billed', true);
    }

    public function scopeUnbilled($query)
    {
        return $query->where('billable', true)->where('billed', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('expense_date', now()->year);
    }

    public static function getCategories(): array
    {
        return [
            'travel' => __('expenses.category_travel'),
            'meals' => __('expenses.category_meals'),
            'office' => __('expenses.category_office'),
            'software' => __('expenses.category_software'),
            'marketing' => __('expenses.category_marketing'),
            'equipment' => __('expenses.category_equipment'),
            'other' => __('expenses.category_other'),
        ];
    }
}
