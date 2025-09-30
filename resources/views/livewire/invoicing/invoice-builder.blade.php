<div class="space-y-6">
    <!-- Progress Steps -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-4">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                        {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                        @if($step > 1)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <span class="text-sm font-medium">1</span>
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.select_time_entries') }}</span>
                </div>
                
                <!-- Connector -->
                <div class="w-8 h-0.5 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                
                <!-- Step 2 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                        {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                        @if($step > 2)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <span class="text-sm font-medium">2</span>
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.invoice_details') }}</span>
                </div>
                
                <!-- Connector -->
                <div class="w-8 h-0.5 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                
                <!-- Step 3 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full 
                        {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                        <span class="text-sm font-medium">3</span>
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('invoices.review_and_create') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Select Time Entries -->
    @if($step === 1)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('invoices.select_time_entries_to_invoice') }}</h2>
            
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.client') }}</label>
                    <select wire:model="selectedClient" 
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('invoices.all_clients') }}</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.project') }}</label>
                    <select wire:model="selectedProject" 
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            {{ !$selectedClient ? 'disabled' : '' }}>
                        <option value="">{{ __('invoices.all_projects') }}</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.from_date') }}</label>
                    <input type="date" wire:model="dateFrom" 
                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.to_date') }}</label>
                    <input type="date" wire:model="dateTo" 
                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Selection Actions -->
            @if($availableTimeEntries->count() > 0)
                <div class="flex justify-between items-center mb-4">
                    <div class="space-x-2">
                        <button wire:click="selectAllTimeEntries" 
                                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                            {{ __('invoices.select_all') }}
                        </button>
                        <button wire:click="clearSelectedTimeEntries" 
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                            {{ __('invoices.clear_all') }}
                        </button>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('invoices.entries_selected_count', ['count' => count($selectedTimeEntries), 'total' => $availableTimeEntries->count()]) }}
                        @if(count($selectedTimeEntries) > 0)
                            • {{ \App\Services\LocalizationService::formatMoney($subtotal) }} {{ __('invoices.subtotal') }}
                        @endif
                    </div>
                </div>
            @endif

            <!-- Time Entries List -->
            @if($availableTimeEntries->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($availableTimeEntries as $entry)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                             wire:click="toggleTimeEntry({{ $entry->id }})">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" 
                                           {{ in_array($entry->id, $selectedTimeEntries) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $entry->description }}</h4>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ \App\Services\LocalizationService::formatDate($entry->date) }} •
                                            {{ $entry->project->client->name }} •
                                            {{ $entry->project->name }}
                                            @if($entry->task)
                                                • {{ $entry->task->title }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format($entry->duration / 60, 1) }}h × {{ \App\Services\LocalizationService::formatMoney($entry->hourly_rate) }}
                                    </div>
                                    <div class="text-sm text-green-600 dark:text-green-400 font-medium">
                                        {{ \App\Services\LocalizationService::formatMoney(($entry->duration / 60) * $entry->hourly_rate) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('invoices.no_billable_entries') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('invoices.try_adjusting_filters') }}</p>
                </div>
            @endif

            <!-- Navigation -->
            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-600 mt-6">
                <button wire:click="nextStep"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('invoices.continue') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Step 2: Invoice Details -->
    @if($step === 2)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('invoices.invoice_details') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.issue_date') }}</label>
                        <input type="date" wire:model="issueDate" 
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('issueDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.due_date') }}</label>
                        <input type="date" wire:model="dueDate" 
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('dueDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.tax_rate_percent') }}</label>
                            <input type="number" step="0.01" min="0" max="100" wire:model="taxRate" 
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('taxRate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.currency') }}</label>
                            <select wire:model="currency" 
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @foreach(\App\Enums\Currency::getAll() as $currencyEnum)
                                    <option value="{{ $currencyEnum->value }}">{{ $currencyEnum->getName() }} ({{ $currencyEnum->value }})</option>
                                @endforeach
                            </select>
                            @error('currency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.client_details') }}</label>
                        <textarea wire:model="clientDetails" rows="4" placeholder="{{ __('invoices.placeholders.client_details') }}"
                                  class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('clientDetails') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('invoices.notes') }}</label>
                        <textarea wire:model="notes" rows="3" placeholder="{{ __('invoices.placeholders.payment_terms_notes') }}"
                                  class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Invoice Summary -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">{{ __('invoices.invoice_summary') }}</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('invoices.subtotal_colon') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \App\Services\LocalizationService::formatMoney($subtotal, $currency) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('invoices.tax_rate_colon', ['rate' => $taxRate]) }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \App\Services\LocalizationService::formatMoney($taxAmount, $currency) }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900 dark:text-white">{{ __('invoices.total_colon') }}</span>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ \App\Services\LocalizationService::formatMoney($total, $currency) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-600 mt-6">
                <button wire:click="previousStep" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('invoices.back') }}
                </button>
                <button wire:click="nextStep" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('invoices.review_invoice') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Step 3: Review & Create -->
    @if($step === 3)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('invoices.review_invoice') }}</h2>
            
            <!-- Invoice Preview -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('invoices.invoice_details') }}</h3>
                        <div class="text-sm space-y-1">
                            <div><span class="text-gray-600 dark:text-gray-400">{{ __('invoices.issue_date_colon') }}</span> {{ \App\Services\LocalizationService::formatDate($issueDate) }}</div>
                            <div><span class="text-gray-600 dark:text-gray-400">{{ __('invoices.due_date_colon') }}</span> {{ \App\Services\LocalizationService::formatDate($dueDate) }}</div>
                            <div><span class="text-gray-600 dark:text-gray-400">{{ __('invoices.currency_colon') }}</span> {{ $currency }}</div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('invoices.client') }}</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $clientDetails }}</div>
                    </div>
                </div>

                <!-- Time Entries -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">{{ __('invoices.time_entries_items', ['count' => count($selectedTimeEntries)]) }}</h3>
                    <div class="space-y-2">
                        @foreach($availableTimeEntries->whereIn('id', $selectedTimeEntries) as $entry)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $entry->description }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $entry->date->format('M j, Y') }} • {{ number_format($entry->duration / 60, 1) }}h
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ \App\Services\LocalizationService::formatMoney(($entry->duration / 60) * $entry->hourly_rate) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Totals -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('invoices.subtotal_colon') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \App\Services\LocalizationService::formatMoney($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('invoices.tax_rate_colon', ['rate' => $taxRate]) }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \App\Services\LocalizationService::formatMoney($taxAmount) }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900 dark:text-white">{{ __('invoices.total_colon') }}</span>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ \App\Services\LocalizationService::formatMoney($total) }} {{ $currency }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($notes)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('invoices.notes') }}</h4>
                        <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="flex justify-between">
                <button wire:click="previousStep" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('invoices.back') }}
                </button>
                <button wire:click="createInvoice" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('invoices.create_invoice') }}
                </button>
            </div>
        </div>
    @endif
</div>