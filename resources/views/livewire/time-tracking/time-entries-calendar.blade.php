<div class="space-y-6">
    <!-- Filters and Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- View Type Toggle -->
            <div class="flex items-center space-x-2">
                <button wire:click="setViewType('month')"
                        class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $viewType === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ __('time.month_view') }}
                </button>
                <button wire:click="setViewType('week')"
                        class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $viewType === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ __('time.week_view') }}
                </button>
            </div>
            
            <!-- Navigation -->
            <div class="flex items-center space-x-2">
                <button wire:click="previousPeriod" 
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white min-w-48 text-center">
                    {{ $viewType === 'month' ? $currentDate->format('F Y') : $currentDate->format('M j') . ' - ' . $currentDate->copy()->endOfWeek()->format('M j, Y') }}
                </h2>
                
                <button wire:click="nextPeriod" 
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                
                <button wire:click="today" 
                        class="ml-2 px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors duration-200">
                    {{ __('app.today') }}
                </button>
            </div>
            
            <!-- Filters -->
            <div class="flex items-center space-x-3">
                <select wire:model.live="selectedProjectId" 
                        class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('app.all') }} {{ __('projects.projects') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                
                <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" wire:model.live="showOnlyBillable"
                           class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-blue-500">
                    <span>{{ __('time.show_billable_only') }}</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 overflow-hidden">
        <!-- Calendar Header -->
        @if($viewType === 'month')
            <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-700">
                @foreach($dayNamesShort as $day)
                    <div class="p-3 text-xs font-medium text-gray-500 dark:text-gray-400 text-center border-r border-gray-200 dark:border-gray-600 last:border-r-0">
                        {{ $day }}
                    </div>
                @endforeach
            </div>
        @endif
        
        <!-- Calendar Body -->
        @foreach($calendarDays as $week)
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                @foreach($week as $day)
                    @php
                        $isCurrentMonth = $viewType === 'week' || $day->month === $currentDate->month;
                        $isToday = $day->isToday();
                        $dayEntries = $timeEntries->get($day->format('Y-m-d'), collect());
                        $dailyTotal = $this->getDailyTotal($day);
                        $dailyEarnings = $this->getDailyEarnings($day);
                    @endphp
                    
                    <div class="min-h-[120px] p-2 border-r border-gray-200 dark:border-gray-600 last:border-r-0 {{ !$isCurrentMonth ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                        <!-- Day Header -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium {{ $isToday ? 'text-blue-600 dark:text-blue-400' : ($isCurrentMonth ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500') }}">
                                {{ $day->format('j') }}
                                @if($viewType === 'week')
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $day->format('D') }}</span>
                                @endif
                            </span>
                            
                            @if($dailyTotal > 0)
                                <div class="text-xs text-right">
                                    <div class="text-gray-600 dark:text-gray-400">
                                        {{ $this->formatDuration($dailyTotal) }}
                                    </div>
                                    @if($dailyEarnings > 0)
                                        <div class="text-green-600 dark:text-green-400 font-medium">
                                            {{ \App\Services\LocalizationService::formatMoney($dailyEarnings) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- Time Entries -->
                        <div class="space-y-1">
                            @foreach($dayEntries->take(3) as $entry)
                                <div class="text-xs p-1 rounded {{ $entry->billable ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                    <div class="font-medium truncate">{{ $entry->project->name }}</div>
                                    <div class="truncate opacity-75">{{ $entry->description }}</div>
                                    <div class="font-mono">{{ $this->formatDuration($entry->duration) }}</div>
                                </div>
                            @endforeach
                            
                            @if($dayEntries->count() > 3)
                                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                    +{{ $dayEntries->count() - 3 }} more
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $periodEntries = $timeEntries->flatten();
            $totalHours = $periodEntries->sum('duration') / 60;
            $totalEarnings = $periodEntries->sum(function($entry) { return ($entry->duration * $entry->hourly_rate) / 60; });
            $billableHours = $periodEntries->where('billable', true)->sum('duration') / 60;
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalHours, 1) }}h</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('time.total_hours') }}</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ format_money($totalEarnings) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('time.total_amount') }}</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($billableHours, 1) }}h</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('time.billable_hours') }}</div>
        </div>
    </div>
</div>