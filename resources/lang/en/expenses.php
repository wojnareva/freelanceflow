<?php

return [
    'title' => 'Expenses',
    'expense' => 'Expense',
    'expenses' => 'Expenses',
    'all_expenses' => 'All Expenses',
    'new_expense' => 'New Expense',
    'add_expense' => 'Add Expense',
    'create_expense' => 'Create Expense',
    'edit_expense' => 'Edit Expense',
    'delete_expense' => 'Delete Expense',
    'expense_details' => 'Expense Details',
    'no_expenses' => 'No expenses',
    'expense_created' => 'Expense created',
    'expense_updated' => 'Expense updated',
    'expense_deleted' => 'Expense deleted',

    // Expense Information
    'amount' => 'Amount',
    'description' => 'Description',
    'category' => 'Category',
    'date' => 'Date',
    'receipt' => 'Receipt',
    'receipts' => 'Receipts',
    'attachment' => 'Attachment',
    'attachments' => 'Attachments',
    'vendor' => 'Vendor',
    'supplier' => 'Supplier',
    'merchant' => 'Merchant',
    'location' => 'Location',
    'payment_method' => 'Payment Method',
    'currency' => 'Currency',
    'tax_rate' => 'Tax Rate',
    'tax_amount' => 'Tax Amount',
    'total_amount' => 'Total Amount',
    'net_amount' => 'Net Amount',
    'gross_amount' => 'Gross Amount',

    // Expense Categories
    'categories' => [
        'office_supplies' => 'Office Supplies',
        'travel' => 'Travel',
        'accommodation' => 'Accommodation',
        'meals' => 'Meals',
        'transportation' => 'Transportation',
        'software' => 'Software',
        'hardware' => 'Hardware',
        'training' => 'Training',
        'marketing' => 'Marketing',
        'advertising' => 'Advertising',
        'consulting' => 'Consulting',
        'legal' => 'Legal Services',
        'insurance' => 'Insurance',
        'utilities' => 'Utilities',
        'rent' => 'Rent',
        'maintenance' => 'Maintenance',
        'equipment' => 'Equipment',
        'communication' => 'Communication',
        'shipping' => 'Shipping',
        'miscellaneous' => 'Miscellaneous',
        'other' => 'Other',
    ],

    // Payment Methods
    'payment_methods' => [
        'cash' => 'Cash',
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
        'bank_transfer' => 'Bank Transfer',
        'check' => 'Check',
        'paypal' => 'PayPal',
        'other' => 'Other',
    ],

    // Expense Status
    'status' => 'Status',
    'statuses' => [
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'reimbursed' => 'Reimbursed',
        'paid' => 'Paid',
    ],

    // Project & Client Association
    'project' => 'Project',
    'client' => 'Client',
    'billable' => 'Billable',
    'non_billable' => 'Non-billable',
    'reimbursable' => 'Reimbursable',
    'non_reimbursable' => 'Non-reimbursable',

    // Receipt Management
    'upload_receipt' => 'Upload Receipt',
    'scan_receipt' => 'Scan Receipt',
    'receipt_required' => 'Receipt required',
    'receipt_optional' => 'Receipt optional',
    'no_receipt' => 'No receipt',
    'receipt_attached' => 'Receipt attached',
    'view_receipt' => 'View Receipt',
    'download_receipt' => 'Download Receipt',

    // Approval Workflow
    'approval' => 'Approval',
    'pending_approval' => 'Pending Approval',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'approve' => 'Approve',
    'reject' => 'Reject',
    'approval_required' => 'Approval Required',
    'auto_approval' => 'Auto Approval',
    'approval_limit' => 'Approval Limit',
    'approver' => 'Approver',

    // Reporting & Analytics
    'reports' => 'Reports',
    'expense_report' => 'Expense Report',
    'monthly_expenses' => 'Monthly Expenses',
    'total_expenses' => 'Total Expenses',
    'average_expense' => 'Average Expense',
    'expense_trends' => 'Expense Trends',
    'top_categories' => 'Top Categories',
    'expense_by_category' => 'Expenses by Category',
    'expense_by_month' => 'Expenses by Month',

    // Filters & Search
    'search_expenses' => 'Search Expenses',
    'filter_by_category' => 'Filter by Category',
    'filter_by_status' => 'Filter by Status',
    'filter_by_date' => 'Filter by Date',
    'filter_by_amount' => 'Filter by Amount',
    'filter_by_project' => 'Filter by Project',
    'filter_by_client' => 'Filter by Client',
    'date_range' => 'Date Range',
    'amount_range' => 'Amount Range',
    'show_approved_only' => 'Show Approved Only',
    'show_pending_only' => 'Show Pending Only',
    'show_reimbursed_only' => 'Show Reimbursed Only',

    // Actions
    'submit_expense' => 'Submit Expense',
    'save_draft' => 'Save Draft',
    'mark_reimbursed' => 'Mark as Reimbursed',
    'duplicate_expense' => 'Duplicate Expense',
    'export_expenses' => 'Export Expenses',
    'import_expenses' => 'Import Expenses',
    'bulk_approve' => 'Bulk Approve',
    'bulk_reject' => 'Bulk Reject',
    'mark_as_billable' => 'Mark as Billable',

    // Validation Messages
    'validation' => [
        'amount_required' => 'Amount is required',
        'amount_positive' => 'Amount must be positive',
        'description_required' => 'Description is required',
        'category_required' => 'Category is required',
        'date_required' => 'Date is required',
        'date_valid' => 'Date must be valid',
        'receipt_required' => 'Receipt is required',
        'amount_limit_exceeded' => 'Amount exceeds allowed limit',
        'duplicate_expense' => 'Duplicate expense',
    ],

    // Confirmation Messages
    'confirmations' => [
        'delete_expense' => 'Are you sure you want to delete this expense?',
        'approve_expense' => 'Are you sure you want to approve this expense?',
        'reject_expense' => 'Are you sure you want to reject this expense?',
        'mark_reimbursed' => 'Are you sure you want to mark this expense as reimbursed?',
        'this_action_cannot_be_undone' => 'This action cannot be undone.',
    ],

    // Success Messages
    'success' => [
        'expense_created' => 'Expense created successfully',
        'expense_updated' => 'Expense updated successfully',
        'expense_deleted' => 'Expense deleted successfully',
        'expense_approved' => 'Expense approved',
        'expense_rejected' => 'Expense rejected',
        'expense_reimbursed' => 'Expense reimbursed',
        'receipt_uploaded' => 'Receipt uploaded',
    ],

    // Navigation & UI
    'back_to_expenses' => 'Back to Expenses',
];