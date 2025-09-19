<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Filter Time Entries</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Search descriptions..."
                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project</label>
                <select wire:model.live="projectFilter" 
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->client->name }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" wire:model.live="dateFrom" 
                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" wire:model.live="dateTo" 
                       class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" wire:model.live="showOnlyBillable"
                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-blue-500">
                <span>Show only billable entries</span>
            </label>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(count($selectedEntries) > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                Bulk Actions ({{ count($selectedEntries) }} selected)
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Action</label>
                    <select wire:model.live="bulkAction" 
                            class="w-full border-blue-300 dark:border-blue-600 dark:bg-blue-900/30 dark:text-blue-100 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select action...</option>
                        <option value="delete">Delete entries</option>
                        <option value="update_project">Change project</option>
                        <option value="update_billable">Change billable status</option>
                        <option value="update_rate">Update hourly rate</option>
                        <option value="append_description">Append to description</option>
                    </select>
                </div>
                
                <div>
                    @if($bulkAction === 'update_project')
                        <label class="block text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">New Project</label>
                        <select wire:model="bulkProjectId" 
                                class="w-full border-blue-300 dark:border-blue-600 dark:bg-blue-900/30 dark:text-blue-100 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->client->name }})</option>
                            @endforeach
                        </select>
                    @elseif($bulkAction === 'update_billable')
                        <label class="block text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Billable Status</label>
                        <select wire:model="bulkBillable" 
                                class="w-full border-blue-300 dark:border-blue-600 dark:bg-blue-900/30 dark:text-blue-100 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select status...</option>
                            <option value="1">Billable</option>
                            <option value="0">Non-billable</option>
                        </select>
                    @elseif($bulkAction === 'update_rate')
                        <label class="block text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Hourly Rate ($)</label>
                        <input type="number" wire:model="bulkHourlyRate" step="0.01" min="0"
                               placeholder="0.00"
                               class="w-full border-blue-300 dark:border-blue-600 dark:bg-blue-900/30 dark:text-blue-100 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @elseif($bulkAction === 'append_description')
                        <label class="block text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Text to Append</label>
                        <input type="text" wire:model="bulkDescription"
                               placeholder="Additional description..."
                               class="w-full border-blue-300 dark:border-blue-600 dark:bg-blue-900/30 dark:text-blue-100 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @endif
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-4">
                <button wire:click="resetBulkForm" 
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                    Clear Selection
                </button>
                
                <div class="flex space-x-2">
                    @if($bulkAction === 'delete')
                        <button wire:click="applyBulkAction" 
                                wire:confirm="Are you sure you want to delete {{ count($selectedEntries) }} time entries? This action cannot be undone."
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Delete Selected
                        </button>
                    @else
                        <button wire:click="applyBulkAction" 
                                @if(!$bulkAction) disabled @endif
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Apply Changes
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Time Entries Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Time Entries</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $timeEntries->total() }} entries found
                </span>
            </div>
        </div>
        
        @if($timeEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="selectAll"
                                           class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Select All</span>
                                </label>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Billable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Earnings</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($timeEntries as $entry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ in_array($entry->id, $selectedEntries) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" wire:model.live="selectedEntries" value="{{ $entry->id }}"
                                           class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $entry->date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $entry->project->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $entry->project->client->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">{{ $entry->description }}</div>
                                    @if($entry->task)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Task: {{ $entry->task->title }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                    {{ $this->formatDuration($entry->duration) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    ${{ number_format($entry->hourly_rate, 0) }}/hr
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($entry->billable)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                            Billable
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            Non-billable
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @if($entry->billable)
                                        ${{ number_format(($entry->duration * $entry->hourly_rate) / 60, 0) }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $timeEntries->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No time entries found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters or create some time entries first.</p>
                </div>
            </div>
        @endif
    </div>
</div>