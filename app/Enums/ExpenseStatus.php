<?php

namespace App\Enums;

enum ExpenseStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Reimbursed = 'reimbursed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('expense_status.pending'),
            self::Approved => __('expense_status.approved'),
            self::Rejected => __('expense_status.rejected'),
            self::Reimbursed => __('expense_status.reimbursed'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Reimbursed => 'blue',
        };
    }
}
