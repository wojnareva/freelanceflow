<div class="space-y-6">
    <!-- Timeline Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $project->name }} - Timeline</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $project->client->name }} • {{ $project->status->label() }}
                </p>
            </div>
            
            <!-- View Mode Toggle -->
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button wire:click="setViewMode('week')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $viewMode === 'week' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    Week
                </button>
                <button wire:click="setViewMode('month')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $viewMode === 'month' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    Month
                </button>
                <button wire:click="setViewMode('quarter')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $viewMode === 'quarter' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                    Quarter
                </button>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button wire:click="previousPeriod" 
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $periodTitle }}</h3>
                <button wire:click="nextPeriod" 
                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            
            <button wire:click="today" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                Today
            </button>
        </div>
    </div>

    <!-- Project Milestones -->
    @if($projectMilestones->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Project Milestones</h3>
            <div class="space-y-3">
                @foreach($projectMilestones as $milestone)
                    <div class="flex items-center space-x-4 p-3 rounded-lg 
                        {{ $milestone['type'] === 'start' ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                        {{ $milestone['type'] === 'due' ? 'bg-red-50 dark:bg-red-900/20' : '' }}
                        {{ $milestone['type'] === 'task' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        
                        <div class="flex-shrink-0">
                            @if($milestone['icon'] === 'play')
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @elseif($milestone['icon'] === 'flag')
                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $milestone['title'] }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $milestone['date']->format('M j, Y') }}
                                @if(isset($milestone['priority']))
                                    • {{ ucfirst($milestone['priority']) }} Priority
                                @endif
                                @if(isset($milestone['status']))
                                    • {{ ucfirst(str_replace('_', ' ', $milestone['status'])) }}
                                @endif
                            </p>
                        </div>
                        
                        @if($milestone['date']->isToday())
                            <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">Today</span>
                        @elseif($milestone['date']->isPast())
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full">Past</span>
                        @else
                            <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 px-2 py-1 rounded-full">
                                {{ $milestone['date']->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Timeline View -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 overflow-hidden">
        @if($timelineData->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No activity in this period</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Try changing the date range or view mode.</p>
            </div>
        @else
            <div class="p-6">
                <div class="space-y-6">
                    @foreach($timelineData as $date => $data)
                        @if($data['tasks']->count() > 0 || $data['time_entries']->count() > 0)
                            <div class="relative">
                                <!-- Date Header -->
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ $data['date']->format('j') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $data['date']->format('l, F j, Y') }}
                                            @if($data['date']->isToday())
                                                <span class="text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full ml-2">Today</span>
                                            @endif
                                        </h3>
                                        @if($data['total_hours'] > 0)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ number_format($data['total_hours'], 1) }} hours logged
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Timeline Content -->
                                <div class="ml-14 space-y-4">
                                    <!-- Tasks Due -->
                                    @if($data['tasks']->count() > 0)
                                        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                                            <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200 mb-2">
                                                Tasks Due ({{ $data['tasks']->count() }})
                                            </h4>
                                            <div class="space-y-2">
                                                @foreach($data['tasks'] as $task)
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-2 h-2 rounded-full 
                                                                {{ $task->status === 'completed' ? 'bg-green-500' : '' }}
                                                                {{ $task->status === 'in_progress' ? 'bg-blue-500' : '' }}
                                                                {{ $task->status === 'todo' ? 'bg-gray-400' : '' }}
                                                                {{ $task->status === 'cancelled' ? 'bg-red-500' : '' }}">
                                                            </div>
                                                            <span class="text-sm text-gray-900 dark:text-white {{ $task->status === 'completed' ? 'line-through' : '' }}">
                                                                {{ $task->title }}
                                                            </span>
                                                        </div>
                                                        <span class="text-xs px-2 py-1 rounded-full 
                                                            {{ $task->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : '' }}
                                                            {{ $task->priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200' : '' }}
                                                            {{ $task->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : '' }}
                                                            {{ $task->priority === 'low' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : '' }}">
                                                            {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Time Entries -->
                                    @if($data['time_entries']->count() > 0)
                                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                            <h4 class="text-sm font-medium text-green-800 dark:text-green-200 mb-2">
                                                Time Logged ({{ number_format($data['total_hours'], 1) }}h)
                                            </h4>
                                            <div class="space-y-2">
                                                @foreach($data['time_entries'] as $entry)
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <span class="text-sm text-gray-900 dark:text-white">{{ $entry->description }}</span>
                                                            @if($entry->task)
                                                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">• {{ $entry->task->title }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                                            {{ number_format($entry->duration / 60, 1) }}h
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Timeline Line -->
                                @if(!$loop->last)
                                    <div class="absolute left-5 top-16 w-0.5 h-8 bg-gray-200 dark:bg-gray-600"></div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>