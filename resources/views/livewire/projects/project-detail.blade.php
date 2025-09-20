<div class="space-y-6">
    <!-- Project Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Project Info -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Project Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $project->client->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $project->status->value === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : '' }}
                                {{ $project->status->value === 'on_hold' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : '' }}
                                {{ $project->status->value === 'completed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' : '' }}
                                {{ $project->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : '' }}">
                                {{ $project->status->label() }}
                            </span>
                        </dd>
                    </div>
                    
                    @if($project->start_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $project->start_date->format('M j, Y') }}</dd>
                        </div>
                    @endif
                    
                    @if($project->end_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $project->end_date->format('M j, Y') }}</dd>
                        </div>
                    @endif
                    
                    @if($project->budget)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($project->budget ?? 0, 0) }}</dd>
                        </div>
                    @endif
                    
                    @if($project->hourly_rate)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hourly Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($project->hourly_rate ?? 0, 0) }}/hr</dd>
                        </div>
                    @endif
                </div>
                
                @if($project->description)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $project->description }}</dd>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Project Stats -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Progress</h3>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                        <span>Completion</span>
                        <span>{{ $projectStats['progress'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $projectStats['progress'] }}%"></div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tasks</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $projectStats['completed_tasks'] }}/{{ $projectStats['total_tasks'] }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Time Logged</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $this->formatDuration($projectStats['total_time']) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Earnings</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                            ${{ number_format($projectStats['total_earnings'], 0) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tasks Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tasks</h3>
                <button wire:click="createTask" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Add Task
                </button>
            </div>
        </div>
        
        <div class="p-6">
            @if($tasksByStatus->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No tasks yet</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Get started by creating your first task.</p>
                    <div class="mt-6">
                        <button wire:click="createTask" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Add Task
                        </button>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- To Do -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">To Do ({{ $tasksByStatus->get('todo', collect())->count() }})</h4>
                        <div class="space-y-3">
                            @foreach($tasksByStatus->get('todo', collect()) as $task)
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</h5>
                                    @if($task->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($task->description, 60) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs px-2 py-1 rounded {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($task->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        <div class="flex space-x-1">
                                            <button wire:click="editTask({{ $task->id }})" class="text-blue-600 hover:text-blue-700 text-xs">Edit</button>
                                            <button wire:click="deleteTask({{ $task->id }})" class="text-red-600 hover:text-red-700 text-xs">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- In Progress -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">In Progress ({{ $tasksByStatus->get('in_progress', collect())->count() }})</h4>
                        <div class="space-y-3">
                            @foreach($tasksByStatus->get('in_progress', collect()) as $task)
                                <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg border border-blue-200 dark:border-blue-600">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</h5>
                                    @if($task->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($task->description, 60) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs px-2 py-1 rounded {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($task->priority === 'high' ? 'bg-orange-100 text-orange-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        <div class="flex space-x-1">
                                            <button wire:click="editTask({{ $task->id }})" class="text-blue-600 hover:text-blue-700 text-xs">Edit</button>
                                            <button wire:click="deleteTask({{ $task->id }})" class="text-red-600 hover:text-red-700 text-xs">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Completed -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Completed ({{ $tasksByStatus->get('completed', collect())->count() }})</h4>
                        <div class="space-y-3">
                            @foreach($tasksByStatus->get('completed', collect()) as $task)
                                <div class="bg-green-50 dark:bg-green-900/30 p-3 rounded-lg border border-green-200 dark:border-green-600">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</h5>
                                    @if($task->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($task->description, 60) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                        <div class="flex space-x-1">
                                            <button wire:click="editTask({{ $task->id }})" class="text-blue-600 hover:text-blue-700 text-xs">Edit</button>
                                            <button wire:click="deleteTask({{ $task->id }})" class="text-red-600 hover:text-red-700 text-xs">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Cancelled -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Cancelled ({{ $tasksByStatus->get('cancelled', collect())->count() }})</h4>
                        <div class="space-y-3">
                            @foreach($tasksByStatus->get('cancelled', collect()) as $task)
                                <div class="bg-red-50 dark:bg-red-900/30 p-3 rounded-lg border border-red-200 dark:border-red-600">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</h5>
                                    @if($task->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($task->description, 60) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-800">
                                            Cancelled
                                        </span>
                                        <div class="flex space-x-1">
                                            <button wire:click="editTask({{ $task->id }})" class="text-blue-600 hover:text-blue-700 text-xs">Edit</button>
                                            <button wire:click="deleteTask({{ $task->id }})" class="text-red-600 hover:text-red-700 text-xs">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Time Entries -->
    @if($recentTimeEntries->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Time Entries</h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($recentTimeEntries as $entry)
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $entry->description }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $entry->date->format('M j, Y') }}
                                    @if($entry->task)
                                        • {{ $entry->task->title }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $this->formatDuration($entry->duration) }}</span>
                                @if($entry->billable)
                                    <span class="block text-xs text-green-600 dark:text-green-400">${{ number_format(($entry->duration * $entry->hourly_rate) / 60, 0) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Task Modal -->
    @if($showTaskModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeTaskModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveTask">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                {{ $editingTask ? 'Edit Task' : 'Create New Task' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Task Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Task Title</label>
                                    <input type="text" wire:model="taskTitle" 
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('taskTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea wire:model="taskDescription" rows="3"
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('taskDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Status and Priority -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select wire:model="taskStatus" 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="todo">To Do</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        @error('taskStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                        <select wire:model="taskPriority" 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                        @error('taskPriority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <!-- Due Date and Estimated Hours -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                                        <input type="date" wire:model="taskDueDate" 
                                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('taskDueDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Hours</label>
                                        <input type="number" step="0.5" wire:model="taskEstimatedHours" 
                                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('taskEstimatedHours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingTask ? 'Update Task' : 'Create Task' }}
                            </button>
                            <button type="button" wire:click="closeTaskModal" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>