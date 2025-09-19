<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billable' => 'boolean',
        'billed' => 'boolean',
        'expense_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
