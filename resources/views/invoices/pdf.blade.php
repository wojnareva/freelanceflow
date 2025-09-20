<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header-row {
            display: table;
            width: 100%;
        }
        
        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
        }
        
        .header-right {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #3B82F6;
            margin: 0;
        }
        
        .invoice-number {
            font-size: 18px;
            color: #666;
            margin: 5px 0 0 0;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        
        .company-details {
            color: #666;
        }
        
        .details-section {
            margin: 30px 0;
        }
        
        .details-row {
            display: table;
            width: 100%;
        }
        
        .bill-to, .invoice-details {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        
        .section-title {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .client-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .client-details {
            color: #666;
            margin-bottom: 3px;
        }
        
        .invoice-detail {
            margin-bottom: 8px;
        }
        
        .invoice-detail-label {
            color: #666;
            display: inline-block;
            width: 80px;
        }
        
        .invoice-detail-value {
            font-weight: bold;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th {
            background-color: #F8F9FA;
            border: 1px solid #DEE2E6;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            border: 1px solid #DEE2E6;
            padding: 12px;
            font-size: 14px;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .item-description {
            font-weight: 500;
        }
        
        .item-type {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        
        .totals-table {
            margin-left: auto;
            width: 250px;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .totals-label, .totals-value {
            display: table-cell;
            padding: 5px 0;
        }
        
        .totals-label {
            text-align: left;
            color: #666;
        }
        
        .totals-value {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            border-top: 2px solid #3B82F6;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .total-row .totals-label, .total-row .totals-value {
            font-size: 18px;
            font-weight: bold;
            color: #3B82F6;
        }
        
        .notes {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #DEE2E6;
        }
        
        .notes-title {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .notes-content {
            color: #666;
            line-height: 1.6;
            white-space: pre-line;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #DEE2E6;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-row">
            <div class="header-left">
                <h1 class="invoice-title">INVOICE</h1>
                <p class="invoice-number">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="header-right">
                <div class="company-name">FreelanceFlow</div>
                <div class="company-details">Your Business Name</div>
                <div class="company-details">Your Business Address</div>
                <div class="company-details">Your Business Email</div>
                <div class="company-details">Your Business Phone</div>
            </div>
        </div>
    </div>

    <!-- Client and Invoice Details -->
    <div class="details-section">
        <div class="details-row">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="client-name">{{ $invoice->client->name }}</div>
                @if($invoice->client->company)
                    <div class="client-details">{{ $invoice->client->company }}</div>
                @endif
                <div class="client-details">{{ $invoice->client->email }}</div>
                @if($invoice->client->address)
                    <div class="client-details">{{ $invoice->client->address }}</div>
                @endif
            </div>
            
            <div class="invoice-details">
                <div class="invoice-detail">
                    <span class="invoice-detail-label">Issue Date:</span>
                    <span class="invoice-detail-value">{{ $invoice->issue_date->format('M j, Y') }}</span>
                </div>
                <div class="invoice-detail">
                    <span class="invoice-detail-label">Due Date:</span>
                    <span class="invoice-detail-value">{{ $invoice->due_date->format('M j, Y') }}</span>
                </div>
                @if($invoice->project)
                    <div class="invoice-detail">
                        <span class="invoice-detail-label">Project:</span>
                        <span class="invoice-detail-value">{{ $invoice->project->name }}</span>
                    </div>
                @endif
                <div class="invoice-detail">
                    <span class="invoice-detail-label">Status:</span>
                    <span class="invoice-detail-value">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty/Hours</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <div class="item-description">{{ $item->description }}</div>
                        @if($item->type === 'time')
                            <div class="item-type">Time Entry</div>
                        @elseif($item->type === 'fixed')
                            <div class="item-type">Fixed Item</div>
                        @elseif($item->type === 'expense')
                            <div class="item-type">Expense</div>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($item->type === 'time')
                            {{ number_format($item->quantity, 2) }}h
                        @else
                            {{ number_format($item->quantity, 0) }}
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($item->rate, 2) }}</td>
                    <td class="text-right">${{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div class="totals-table">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">${{ number_format($invoice->subtotal, 2) }}</div>
            </div>
            
            @if($invoice->tax_rate > 0)
                <div class="totals-row">
                    <div class="totals-label">Tax ({{ $invoice->tax_rate }}%):</div>
                    <div class="totals-value">${{ number_format($invoice->tax_amount, 2) }}</div>
                </div>
            @endif
            
            <div class="totals-row total-row">
                <div class="totals-label">Total:</div>
                <div class="totals-value">${{ number_format($invoice->total, 2) }}</div>
            </div>
        </div>
    </div>

    @if($invoice->notes)
        <!-- Notes -->
        <div class="notes">
            <div class="notes-title">Notes</div>
            <div class="notes-content">{{ $invoice->notes }}</div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('M j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>