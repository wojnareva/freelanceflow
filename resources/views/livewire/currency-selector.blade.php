<div class="space-y-3">
    <div class="relative">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Currency
        </label>
        <select 
            wire:model.live="selectedCurrency"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-300"
        >
            @foreach($currencies as $currency)
                <option value="{{ $currency['value'] }}">
                    {{ $currency['label'] }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Currency Toggle -->
    @if(!$showAllCurrencies || $onlyPopular)
        <div class="flex items-center justify-between">
            <button 
                wire:click="toggleCurrencyList"
                type="button"
                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
            >
                @if($onlyPopular)
                    Show all currencies
                @else
                    Show popular currencies only
                @endif
            </button>
        </div>
    @endif

    <!-- Exchange Rate Display -->
    @if($exchangeRate && $showExchangeRate)
        <div class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                <span>
                    Exchange Rate: 1 {{ $baseCurrency }} = {{ number_format($exchangeRate, 4) }} {{ $selectedCurrency }}
                </span>
            </div>
        </div>
    @endif

    <!-- Currency Symbol Display -->
    @if($selectedCurrency)
        @php
            $currency = \App\Enums\Currency::from($selectedCurrency);
        @endphp
        <div class="text-xs text-gray-500 dark:text-gray-400">
            Symbol: <span class="font-mono font-bold">{{ $currency->getSymbol() }}</span>
        </div>
    @endif
</div>