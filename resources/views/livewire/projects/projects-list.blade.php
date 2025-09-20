<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Projects</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Active</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['completed'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['on_hold'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">On Hold</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Draft</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-4">
            <div class="text-2xl font-bold text-slate-600 dark:text-slate-400">{{ $stats['archived'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Archived</div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Left side - Search and filters -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Search projects, clients..."
                           class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Status Filter -->
                <div class="sm:w-48">
                    <select wire:model.live="statusFilter" 
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                
                <!-- Client Filter -->
                <div class="sm:w-48">
                    <select wire:model.live="clientFilter" 
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Right side - Create button -->
            <div>
                <button wire:click="createProject" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    New Project
                </button>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                <!-- Project Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                                {{ $project->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $project->client->name }}
                            </p>
                        </div>
                        
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $project->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' : '' }}
                            {{ $project->status->value === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200' : '' }}
                            {{ $project->status->value === 'on_hold' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200' : '' }}
                            {{ $project->status->value === 'completed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' : '' }}
                            {{ $project->status === 'archived' ? 'bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200' : '' }}">
                            {{ $project->status->label() }}
                        </span>
                    </div>
                    
                    @if($project->description)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                            {{ $project->description }}
                        </p>
                    @endif
                </div>
                
                <!-- Project Stats -->
                <div class="p-6 space-y-4">
                    <!-- Progress Info -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Tasks:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $project->tasks_count }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Time:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $this->formatDuration($project->time_entries_sum_duration) }}</span>
                        </div>
                    </div>
                    
                    <!-- Budget Info -->
                    @if($project->budget || $project->hourly_rate)
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($project->budget)
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Budget:</span>
                                    <span class="font-medium text-gray-900 dark:text-white ml-1">${{ number_format($project->budget ?? 0, 0) }}</span>
                                </div>
                            @endif
                            @if($project->hourly_rate)
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Rate:</span>
                                    <span class="font-medium text-gray-900 dark:text-white ml-1">${{ number_format($project->hourly_rate ?? 0, 0) }}/hr</span>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Dates -->
                    @if($project->start_date || $project->end_date)
                        <div class="text-sm">
                            @if($project->start_date)
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Started:</span>
                                    <span class="text-gray-900 dark:text-white ml-1">{{ $project->start_date->format('M j, Y') }}</span>
                                </div>
                            @endif
                            @if($project->end_date)
                                <div class="mt-1">
                                    <span class="text-gray-500 dark:text-gray-400">Due:</span>
                                    <span class="text-gray-900 dark:text-white ml-1">{{ $project->end_date->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2">
                            <button wire:click="editProject({{ $project->id }})" 
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                                Edit
                            </button>
                            <button onclick="if(confirm('Are you sure you want to delete this project? This action cannot be undone.')) { $wire.deleteProject({{ $project->id }}) }"
                                    class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm font-medium">
                                Delete
                            </button>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 text-sm font-medium">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No projects found</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Get started by creating your first project.</p>
                    <div class="mt-6">
                        <button wire:click="createProject" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Create Project
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif

    <!-- Create/Edit Project Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit="saveProject">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        {{ $editingProject ? 'Edit Project' : 'Create New Project' }}
                                    </h3>
                                    
                                    <div class="mt-6 grid grid-cols-1 gap-6">
                                        <!-- Project Name -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                                            <input type="text" wire:model="name" 
                                                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <!-- Client -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client</label>
                                            <select wire:model="clientId" 
                                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select a client...</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('clientId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                            <textarea wire:model="description" rows="3"
                                                      class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <!-- Status and Dates -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                                <select wire:model="status" 
                                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="draft">Draft</option>
                                                    <option value="active">Active</option>
                                                    <option value="on_hold">On Hold</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="archived">Archived</option>
                                                </select>
                                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                                <input type="date" wire:model="startDate" 
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                @error('startDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                                <input type="date" wire:model="endDate" 
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                @error('endDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Budget and Rates -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget ($)</label>
                                                <input type="number" step="0.01" wire:model="budget" 
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                @error('budget') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hourly Rate ($)</label>
                                                <input type="number" step="0.01" wire:model="hourlyRate" 
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                @error('hourlyRate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Hours</label>
                                                <input type="number" step="0.5" wire:model="estimatedHours" 
                                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                @error('estimatedHours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingProject ? 'Update Project' : 'Create Project' }}
                            </button>
                            <button type="button" wire:click="closeModal" 
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