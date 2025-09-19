<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
            <button wire:click="refreshFeed" 
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

        @if($activities && count($activities) > 0)
            <div class="flow-root">
                <ul class="-mb-8">
                    @foreach($activities as $index => $activity)
                        <li>
                            <div class="relative pb-8">
                                @if($index < count($activities) - 1)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        @php
                                            $iconColorClasses = [
                                                'purple' => 'bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400',
                                                'green' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400',
                                                'yellow' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400',
                                                'blue' => 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400',
                                                'gray' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                                'red' => 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400',
                                            ][$activity['color']] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                                        @endphp
                                        <span class="h-8 w-8 rounded-full {{ $iconColorClasses }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            @switch($activity['icon'])
                                                @case('clock')
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    @break
                                                @case('document-text')
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    @break
                                                @case('folder')
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                                    </svg>
                                                    @break
                                                @case('check-circle')
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    @break
                                            @endswitch
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $activity['title'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $activity['description'] }}</p>
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                                @foreach($activity['details'] as $key => $value)
                                                    <div class="flex items-center space-x-2">
                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span>{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $activity['created_at']->toISOString() }}">
                                                {{ $activity['created_at']->diffForHumans() }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent activity</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start by creating projects or logging time entries.</p>
            </div>
        @endif
    </div>
</div>