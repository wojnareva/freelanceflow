<?php

return [
    'title' => 'Výdaje',
    'expense' => 'Výdaj',
    'expenses' => 'Výdaje',
    'all_expenses' => 'Všechny výdaje',
    'new_expense' => 'Nový výdaj',
    'add_expense' => 'Přidat výdaj',
    'create_expense' => 'Vytvořit výdaj',
    'edit_expense' => 'Upravit výdaj',
    'delete_expense' => 'Smazat výdaj',
    'expense_details' => 'Detaily výdaje',
    'no_expenses' => 'Žádné výdaje',
    'expense_created' => 'Výdaj byl vytvořen',
    'expense_updated' => 'Výdaj byl aktualizován',
    'expense_deleted' => 'Výdaj byl smazán',

    // Expense Information
    'amount' => 'Částka',
    'description' => 'Popis',
    'category' => 'Kategorie',
    'date' => 'Datum',
    'receipt' => 'Účtenka',
    'receipts' => 'Účtenky',
    'attachment' => 'Příloha',
    'attachments' => 'Přílohy',
    'vendor' => 'Dodavatel',
    'supplier' => 'Dodavatel',
    'merchant' => 'Obchodník',
    'location' => 'Místo',
    'payment_method' => 'Způsob platby',
    'currency' => 'Měna',
    'tax_rate' => 'Sazba DPH',
    'tax_amount' => 'Částka DPH',
    'total_amount' => 'Celková částka',
    'net_amount' => 'Čistá částka',
    'gross_amount' => 'Hrubá částka',

    // Expense Categories
    'categories' => [
        'office_supplies' => 'Kancelářské potřeby',
        'travel' => 'Cestování',
        'accommodation' => 'Ubytování',
        'meals' => 'Stravování',
        'transportation' => 'Doprava',
        'software' => 'Software',
        'hardware' => 'Hardware',
        'training' => 'Školení',
        'marketing' => 'Marketing',
        'advertising' => 'Reklama',
        'consulting' => 'Poradenství',
        'legal' => 'Právní služby',
        'insurance' => 'Pojištění',
        'utilities' => 'Služby',
        'rent' => 'Nájem',
        'maintenance' => 'Údržba',
        'equipment' => 'Vybavení',
        'communication' => 'Komunikace',
        'shipping' => 'Doprava',
        'miscellaneous' => 'Různé',
        'other' => 'Ostatní',
    ],

    // Payment Methods
    'payment_methods' => [
        'cash' => 'Hotově',
        'credit_card' => 'Kreditní karta',
        'debit_card' => 'Debetní karta',
        'bank_transfer' => 'Bankovní převod',
        'check' => 'Šek',
        'paypal' => 'PayPal',
        'other' => 'Jinak',
    ],

    // Expense Status
    'status' => 'Stav',
    'statuses' => [
        'pending' => 'Čeká na schválení',
        'approved' => 'Schváleno',
        'rejected' => 'Zamítnuto',
        'reimbursed' => 'Proplaceno',
        'paid' => 'Zaplaceno',
    ],

    // Project & Client Association
    'project' => 'Projekt',
    'client' => 'Klient',
    'billable' => 'Fakturovatelné',
    'non_billable' => 'Nefakturovatelné',
    'mark_as_billable' => 'Označit jako fakturovatelné',
    'reimbursable' => 'Proplatitelné',
    'non_reimbursable' => 'Neproplatitelné',

    // Receipt Management
    'upload_receipt' => 'Nahrát účtenku',
    'scan_receipt' => 'Skenovat účtenku',
    'receipt_required' => 'Účtenka je povinná',
    'receipt_optional' => 'Účtenka je volitelná',
    'no_receipt' => 'Žádná účtenka',
    'receipt_attached' => 'Účtenka přiložena',
    'view_receipt' => 'Zobrazit účtenku',
    'download_receipt' => 'Stáhnout účtenku',

    // Approval Workflow
    'approval' => 'Schválení',
    'pending_approval' => 'Čeká na schválení',
    'approved' => 'Schváleno',
    'rejected' => 'Zamítnuto',
    'approve' => 'Schválit',
    'reject' => 'Zamítnout',
    'approval_required' => 'Vyžaduje schválení',
    'auto_approval' => 'Automatické schválení',
    'approval_limit' => 'Limit schválení',
    'approver' => 'Schvalovatel',

    // Reporting & Analytics
    'reports' => 'Sestavy',
    'expense_report' => 'Sestava výdajů',
    'monthly_expenses' => 'Měsíční výdaje',
    'total_expenses' => 'Celkové výdaje',
    'average_expense' => 'Průměrný výdaj',
    'expense_trends' => 'Trendy výdajů',
    'top_categories' => 'Nejčastější kategorie',
    'expense_by_category' => 'Výdaje podle kategorie',
    'expense_by_month' => 'Výdaje podle měsíce',

    // Filters & Search
    'search_expenses' => 'Hledat výdaje',
    'filter_by_category' => 'Filtrovat podle kategorie',
    'filter_by_status' => 'Filtrovat podle stavu',
    'filter_by_date' => 'Filtrovat podle data',
    'filter_by_amount' => 'Filtrovat podle částky',
    'filter_by_project' => 'Filtrovat podle projektu',
    'filter_by_client' => 'Filtrovat podle klienta',
    'date_range' => 'Časové období',
    'amount_range' => 'Rozsah částky',
    'show_approved_only' => 'Zobrazit pouze schválené',
    'show_pending_only' => 'Zobrazit pouze čekající',
    'show_reimbursed_only' => 'Zobrazit pouze proplacené',

    // Actions
    'submit_expense' => 'Odeslat výdaj',
    'save_draft' => 'Uložit koncept',
    'mark_reimbursed' => 'Označit jako proplacené',
    'duplicate_expense' => 'Duplikovat výdaj',
    'export_expenses' => 'Exportovat výdaje',
    'import_expenses' => 'Importovat výdaje',
    'bulk_approve' => 'Hromadné schválení',
    'bulk_reject' => 'Hromadné zamítnutí',

    // Validation Messages
    'validation' => [
        'amount_required' => 'Částka je povinná',
        'amount_positive' => 'Částka musí být kladná',
        'description_required' => 'Popis je povinný',
        'category_required' => 'Kategorie je povinná',
        'date_required' => 'Datum je povinné',
        'date_valid' => 'Datum musí být platné',
        'receipt_required' => 'Účtenka je povinná',
        'amount_limit_exceeded' => 'Částka překračuje povolený limit',
        'duplicate_expense' => 'Duplicitní výdaj',
    ],

    // Confirmation Messages
    'confirmations' => [
        'delete_expense' => 'Opravdu chcete smazat tento výdaj?',
        'approve_expense' => 'Opravdu chcete schválit tento výdaj?',
        'reject_expense' => 'Opravdu chcete zamítnout tento výdaj?',
        'mark_reimbursed' => 'Opravdu chcete označit tento výdaj jako proplacený?',
        'this_action_cannot_be_undone' => 'Tuto akci nelze vrátit zpět.',
    ],

    // Success Messages
    'success' => [
        'expense_created' => 'Výdaj byl úspěšně vytvořen',
        'expense_updated' => 'Výdaj byl úspěšně aktualizován',
        'expense_deleted' => 'Výdaj byl úspěšně smazán',
        'expense_approved' => 'Výdaj byl schválen',
        'expense_rejected' => 'Výdaj byl zamítnut',
        'expense_reimbursed' => 'Výdaj byl proplacen',
        'receipt_uploaded' => 'Účtenka byla nahrána',
    ],

    // Navigation & UI
    'back_to_expenses' => 'Zpět na výdaje',

    // Missing translations for Expenses Index
    'billable_expenses' => 'Fakturovatelné výdaje',
    'unbilled_expenses' => 'Nefakturované výdaje',
    'track_project_expenses' => 'Sledujte projektové výdaje, účtenky a fakturovatelné položky',

    // Placeholders
    'placeholders' => [
        'enter_title' => 'Zadejte název výdaje (např. Oběd s klientem, Předplatné software)',
        'enter_amount' => '0,00',
        'enter_description' => 'Volitelný podrobný popis výdaje...',
        'search_expenses' => 'Hledat výdaje...',
    ],
];