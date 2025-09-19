<div class="fixed bottom-4 right-4 z-50 w-80">
    <!-- Simple Working Timer Widget -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border-2 border-blue-200 dark:border-blue-600 overflow-hidden">
        <!-- Header -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-600">
            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 flex items-center">
                🕐 Time Tracker
            </h3>
        </div>
        
        <!-- Body -->
        <div class="p-4 space-y-4">
            <!-- Timer Display -->
            <div class="text-center">
                <div class="text-2xl font-mono font-bold text-gray-900 dark:text-white">
                    {{ $isRunning ? '⏱️ RUNNING' : '⏸️ STOPPED' }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Projects: {{ count($projects) }}
                </div>
            </div>
            
            @if(!$isRunning)
                <!-- Project Selection -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Project
                    </label>
                    <select wire:model="selectedProjectId" 
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                        <option value="">Select project...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Description
                    </label>
                    <input type="text" 
                           wire:model="description" 
                           placeholder="What are you working on?"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                </div>
                
                <!-- Start Button -->
                <button wire:click="startTimer" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-md">
                    Start Timer
                </button>
            @else
                <!-- Stop Button -->
                <button wire:click="stopTimer" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-md">
                    Stop Timer
                </button>
            @endif
        </div>
    </div>
</div>