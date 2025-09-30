<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.invoice_number') }}</label>
                <input type="text" wire:model="invoiceNumber" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('invoiceNumber') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div></div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.issue_date_label') }}</label>
                <input type="date" wire:model="issueDate" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('issueDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.due_date_label') }}</label>
                <input type="date" wire:model="dueDate" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('dueDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.tax_rate') }} (%)</label>
                <input type="number" step="0.01" wire:model="taxRate" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('taxRate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.currency') }}</label>
                <input type="text" maxlength="3" wire:model="currency" class="mt-1 uppercase block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('currency') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.notes') }}</label>
                <textarea rows="3" wire:model="notes" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.bill_to') }}</label>
                <textarea rows="3" wire:model="clientDetails" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('clientDetails') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('invoices.line_items') }}</h3>
            <button type="button" wire:click="addItem" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700">+ {{ __('app.add') }}</button>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('invoices.item') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('invoices.type') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('invoices.qty_hours_header') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('invoices.rate_header') }}</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('invoices.amount_header') }}</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($items as $index => $item)
                        <tr>
                            <td class="px-3 py-2 text-sm">
                                <input type="text" wire:model="items.{{ $index }}.description" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('invoices.description') }}">
                                @error('items.'.$index.'.description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2 text-sm">
                                <select wire:model="items.{{ $index }}.type" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="time">{{ __('invoices.time_entry') }}</option>
                                    <option value="fixed">{{ __('invoices.fixed_item') }}</option>
                                    <option value="expense">{{ __('invoices.expense_item') }}</option>
                                </select>
                                @error('items.'.$index.'.type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2 text-sm text-right">
                                <input type="number" step="0.01" wire:model="items.{{ $index }}.quantity" class="w-28 text-right border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('items.'.$index.'.quantity') <span class="text-xs text-red-500 block text-right">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2 text-sm text-right">
                                <input type="number" step="0.01" wire:model="items.{{ $index }}.rate" class="w-28 text-right border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @error('items.'.$index.'.rate') <span class="text-xs text-red-500 block text-right">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-3 py-2 text-sm text-right">
                                {{ number_format((float)($item['quantity'] ?? 0) * (float)($item['rate'] ?? 0), 2) }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 dark:text-red-400 hover:underline text-sm">{{ __('app.remove') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <div class="w-64">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('invoices.subtotal_label') }}</span>
                    <span class="text-gray-900 dark:text-white">{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('invoices.tax_label') }} ({{ $taxRate }}%)</span>
                    <span class="text-gray-900 dark:text-white">{{ number_format($taxAmount, 2) }}</span>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('invoices.total_label') }}</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3">
        <a href="{{ route('invoices.show', $invoice) }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">{{ __('app.cancel') }}</a>
        <button type="button" wire:click="save" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <span wire:loading.remove>{{ __('app.save') }}</span>
            <span wire:loading>{{ __('app.saving') }}...</span>
        </button>
    </div>

    <div x-data="{ show: @entangle('flash').defer }"></div>
</div>


