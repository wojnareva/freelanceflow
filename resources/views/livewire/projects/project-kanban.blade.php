<div class="space-y-6">
    <!-- Project Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $project->name }} - Kanban Board</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $project->client->name }} • {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </p>
            </div>
            <button wire:click="createTask" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Add Task
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 overflow-hidden">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 h-screen">
                <!-- To Do Column -->
                <div class="border-r border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="font-medium text-gray-900 dark:text-white flex items-center justify-between">
                            <span>To Do</span>
                            <span class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs px-2 py-1 rounded-full">
                                {{ $tasksByStatus->get('todo', collect())->count() }}
                            </span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto h-full pb-20 drop-zone"
                         ondragover="event.preventDefault(); event.currentTarget.classList.add('bg-blue-50', 'dark:bg-blue-900/20')"
                         ondragleave="event.currentTarget.classList.remove('bg-blue-50', 'dark:bg-blue-900/20')"
                         ondrop="handleDrop(event, 'todo')"
                         data-status="todo">
                        @foreach($tasksByStatus->get('todo', collect()) as $task)
                            <div class="task-card bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm cursor-move hover:shadow-md transition-shadow"
                                 draggable="true"
                                 ondragstart="event.dataTransfer.setData('text/plain', '{{ $task->id }}')"
                                 data-task-id="{{ $task->id }}">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm mb-2">{{ $task->title }}</h4>
                                @if($task->description)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $task->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : '' }}
                                        {{ $task->priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200' : '' }}
                                        {{ $task->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : '' }}
                                        {{ $task->priority === 'low' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : '' }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $task->due_date->format('M j') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button wire:click="editTask({{ $task->id }})" 
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                        Edit
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" 
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- In Progress Column -->
                <div class="border-r border-gray-200 dark:border-gray-600 bg-blue-50 dark:bg-blue-900/10">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="font-medium text-gray-900 dark:text-white flex items-center justify-between">
                            <span>In Progress</span>
                            <span class="bg-blue-200 dark:bg-blue-600 text-blue-700 dark:text-blue-200 text-xs px-2 py-1 rounded-full">
                                {{ $tasksByStatus->get('in_progress', collect())->count() }}
                            </span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto h-full pb-20 drop-zone"
                         ondragover="event.preventDefault(); event.currentTarget.classList.add('bg-blue-100', 'dark:bg-blue-900/30')"
                         ondragleave="event.currentTarget.classList.remove('bg-blue-100', 'dark:bg-blue-900/30')"
                         ondrop="handleDrop(event, 'in_progress')"
                         data-status="in_progress">
                        @foreach($tasksByStatus->get('in_progress', collect()) as $task)
                            <div class="task-card bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-blue-600 shadow-sm cursor-move hover:shadow-md transition-shadow"
                                 draggable="true"
                                 ondragstart="event.dataTransfer.setData('text/plain', '{{ $task->id }}')"
                                 data-task-id="{{ $task->id }}">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm mb-2">{{ $task->title }}</h4>
                                @if($task->description)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $task->priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : '' }}
                                        {{ $task->priority === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200' : '' }}
                                        {{ $task->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : '' }}
                                        {{ $task->priority === 'low' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : '' }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $task->due_date->format('M j') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button wire:click="editTask({{ $task->id }})" 
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                        Edit
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" 
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Completed Column -->
                <div class="border-r border-gray-200 dark:border-gray-600 bg-green-50 dark:bg-green-900/10">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="font-medium text-gray-900 dark:text-white flex items-center justify-between">
                            <span>Completed</span>
                            <span class="bg-green-200 dark:bg-green-600 text-green-700 dark:text-green-200 text-xs px-2 py-1 rounded-full">
                                {{ $tasksByStatus->get('completed', collect())->count() }}
                            </span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto h-full pb-20 drop-zone"
                         ondragover="event.preventDefault(); event.currentTarget.classList.add('bg-green-100', 'dark:bg-green-900/30')"
                         ondragleave="event.currentTarget.classList.remove('bg-green-100', 'dark:bg-green-900/30')"
                         ondrop="handleDrop(event, 'completed')"
                         data-status="completed">
                        @foreach($tasksByStatus->get('completed', collect()) as $task)
                            <div class="task-card bg-white dark:bg-gray-800 p-3 rounded-lg border border-green-200 dark:border-green-600 shadow-sm cursor-move hover:shadow-md transition-shadow opacity-75"
                                 draggable="true"
                                 ondragstart="event.dataTransfer.setData('text/plain', '{{ $task->id }}')"
                                 data-task-id="{{ $task->id }}">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm mb-2 line-through">{{ $task->title }}</h4>
                                @if($task->description)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                        Completed
                                    </span>
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $task->due_date->format('M j') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button wire:click="editTask({{ $task->id }})" 
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                        Edit
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" 
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Cancelled Column -->
                <div class="bg-red-50 dark:bg-red-900/10">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="font-medium text-gray-900 dark:text-white flex items-center justify-between">
                            <span>Cancelled</span>
                            <span class="bg-red-200 dark:bg-red-600 text-red-700 dark:text-red-200 text-xs px-2 py-1 rounded-full">
                                {{ $tasksByStatus->get('cancelled', collect())->count() }}
                            </span>
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto h-full pb-20 drop-zone"
                         ondragover="event.preventDefault(); event.currentTarget.classList.add('bg-red-100', 'dark:bg-red-900/30')"
                         ondragleave="event.currentTarget.classList.remove('bg-red-100', 'dark:bg-red-900/30')"
                         ondrop="handleDrop(event, 'cancelled')"
                         data-status="cancelled">
                        @foreach($tasksByStatus->get('cancelled', collect()) as $task)
                            <div class="task-card bg-white dark:bg-gray-800 p-3 rounded-lg border border-red-200 dark:border-red-600 shadow-sm cursor-move hover:shadow-md transition-shadow opacity-75"
                                 draggable="true"
                                 ondragstart="event.dataTransfer.setData('text/plain', '{{ $task->id }}')"
                                 data-task-id="{{ $task->id }}">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm mb-2 line-through">{{ $task->title }}</h4>
                                @if($task->description)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($task->description, 80) }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                        Cancelled
                                    </span>
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $task->due_date->format('M j') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button wire:click="editTask({{ $task->id }})" 
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                        Edit
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" 
                                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

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

<script>
function handleDrop(event, newStatus) {
    event.preventDefault();
    
    // Remove any highlight classes
    event.currentTarget.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'bg-blue-100', 'dark:bg-blue-900/30', 'bg-green-100', 'dark:bg-green-900/30', 'bg-red-100', 'dark:bg-red-900/30');
    
    const taskId = event.dataTransfer.getData('text/plain');
    
    if (taskId && newStatus) {
        console.log('Moving task', taskId, 'to status', newStatus);
        
        // Call Livewire method to update task status
        @this.moveTask(parseInt(taskId), newStatus).then(() => {
            console.log('Task moved successfully');
        }).catch((error) => {
            console.error('Error moving task:', error);
        });
    }
}
</script>