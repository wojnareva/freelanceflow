<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Invoice {{ $invoice->invoice_number }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $invoice->client->name }} • 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $invoice->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : '' }}
                        {{ $invoice->status === 'sent' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' : '' }}
                        {{ $invoice->status === 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : '' }}
                        {{ $invoice->status === 'overdue' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : '' }}
                        {{ $invoice->status === 'cancelled' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : '' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    ← Back to Invoices
                </a>
                @if($invoice->status === 'draft')
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Edit Invoice
                    </button>
                @endif
                <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Invoice Preview -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                <!-- Invoice Header -->
                <div class="p-8 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">INVOICE</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mt-1">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">FreelanceFlow</h2>
                            <p class="text-gray-600 dark:text-gray-400">Your Business Name</p>
                            <p class="text-gray-600 dark:text-gray-400">Your Business Address</p>
                        </div>
                    </div>
                </div>

                <!-- Client and Invoice Details -->
                <div class="p-8 border-b border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Bill To -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Bill To</h3>
                            <div class="mt-2">
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $invoice->client->name }}</p>
                                @if($invoice->client->company)
                                    <p class="text-gray-600 dark:text-gray-400">{{ $invoice->client->company }}</p>
                                @endif
                                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->client->email }}</p>
                                @if($invoice->client->address)
                                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $invoice->client->address }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Invoice Details -->
                        <div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Issue Date:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $invoice->issue_date->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Due Date:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $invoice->due_date->format('M j, Y') }}</span>
                                </div>
                                @if($invoice->project)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Project:</span>
                                        <span class="text-gray-900 dark:text-white">{{ $invoice->project->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty/Hours</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $item->description }}
                                            @if($item->type === 'time')
                                                <br><span class="text-xs text-gray-500 dark:text-gray-400">Time Entry</span>
                                            @elseif($item->type === 'fixed')
                                                <br><span class="text-xs text-gray-500 dark:text-gray-400">Fixed Item</span>
                                            @elseif($item->type === 'expense')
                                                <br><span class="text-xs text-gray-500 dark:text-gray-400">Expense</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white">
                                            @if($item->type === 'time')
                                                {{ number_format($item->quantity, 2) }}h
                                            @else
                                                {{ number_format($item->quantity, 0) }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white">${{ number_format($item->rate, 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white">${{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Invoice Totals -->
                    <div class="mt-8 flex justify-end">
                        <div class="w-64">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Subtotal:</span>
                                    <span class="text-gray-900 dark:text-white">${{ number_format($invoice->subtotal, 2) }}</span>
                                </div>
                                @if($invoice->tax_rate > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Tax ({{ $invoice->tax_rate }}%):</span>
                                        <span class="text-gray-900 dark:text-white">${{ number_format($invoice->tax_amount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($invoice->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($invoice->notes)
                        <!-- Notes -->
                        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-600">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Notes</h4>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $invoice->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information (if any payments exist) -->
            @if($invoice->payments->count() > 0)
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment History</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($invoice->payments as $payment)
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            Payment via {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $payment->date->format('M j, Y') }}
                                            @if($payment->reference)
                                                • Ref: {{ $payment->reference }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                        +${{ number_format($payment->amount, 2) }}
                                    </span>
                                </div>
                            @endforeach
                            
                            @php
                                $remainingAmount = $invoice->total - $invoice->payments->sum('amount');
                            @endphp
                            
                            @if($remainingAmount > 0)
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Remaining Balance:</span>
                                        <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                            ${{ number_format($remainingAmount, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>