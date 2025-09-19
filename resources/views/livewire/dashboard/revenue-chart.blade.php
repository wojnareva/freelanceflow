<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Revenue Overview</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Last 6 months</p>
            </div>
            <button wire:click="refreshChart" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <svg wire:loading.remove class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                </svg>
                <svg wire:loading class="animate-spin w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="flex items-center space-x-1 text-sm">
                        @if($growthPercentage > 0)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-600 dark:text-green-400 font-medium">+{{ number_format(abs($growthPercentage), 1) }}%</span>
                        @elseif($growthPercentage < 0)
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-red-600 dark:text-red-400 font-medium">{{ number_format($growthPercentage, 1) }}%</span>
                        @else
                            <span class="text-gray-500 dark:text-gray-400 font-medium">0%</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Average</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRevenue / 6, 2) }}</p>
            </div>
        </div>

        <!-- Chart -->
        <div class="relative">
            <div class="flex items-end justify-between space-x-2 h-64">
                @if($chartData && count($chartData['data']) > 0)
                    @php
                        $maxValue = max($chartData['data']) ?: 1;
                    @endphp
                    @foreach($chartData['labels'] as $index => $label)
                        @php
                            $value = $chartData['data'][$index] ?? 0;
                            $height = $maxValue > 0 ? ($value / $maxValue) * 100 : 0;
                        @endphp
                        <div class="flex flex-col items-center flex-1 group">
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-t relative overflow-hidden flex-1 flex items-end">
                                <div class="w-full bg-blue-500 hover:bg-blue-600 transition-colors duration-200 rounded-t relative group-hover:bg-blue-600" 
                                     style="height: {{ $height }}%"
                                     title="${{ number_format($value, 2) }}">
                                </div>
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 dark:bg-gray-700 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                    ${{ number_format($value, 2) }}
                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-gray-800 dark:border-t-gray-700"></div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">{{ $label }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="w-full text-center py-16">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No revenue data</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create and mark invoices as paid to see revenue.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>