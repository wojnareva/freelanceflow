<div class="fixed bottom-4 right-2 sm:right-4 z-50 w-72 sm:w-80 transition-all duration-300 ease-in-out">
    <!-- Simple Working Timer Widget -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border-2 border-blue-200 dark:border-blue-600 overflow-hidden transform hover:scale-105 transition-transform duration-200">
        <!-- Header -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-600">
            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 flex items-center">
                🕐 {{ __('time.time_tracker') }}
            </h3>
        </div>
        
        <!-- Body -->
        <div class="p-4 space-y-4">
            <!-- Timer Display -->
            <div class="text-center">
                <div class="text-2xl font-mono font-bold text-gray-900 dark:text-white">
                    @if($isRunning)
                        <span class="text-green-600 dark:text-green-400 animate-pulse">⏱️ {{ strtoupper(__('time.running')) }}</span>
                    @else
                        <span class="text-gray-600 dark:text-gray-400">⏸️ {{ strtoupper(__('time.stopped')) }}</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('time.projects') }}: {{ count($projects) }}
                </div>
            </div>
            
            @if(!$isRunning)
                <!-- Project Selection -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('time.project') }}
                    </label>
                    <select wire:model="selectedProjectId" 
                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                        <option value="">{{ __('time.select_project') }}...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">
                                {{ $project->name }} ({{ $project->client->name }})
                                @if($project->status->value !== 'active')
                                    - {{ $project->status->label() }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('time.description') }}
                    </label>
                    <input type="text" 
                           wire:model="description" 
                           placeholder="{{ __('time.what_are_you_working_on') }}"
                           class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                </div>
                
                <!-- Start Button -->
                <button wire:click="startTimer" 
                        wire:loading.attr="disabled"
                        wire:target="startTimer"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white text-sm font-medium py-2 px-4 rounded-md transition-all duration-200 transform active:scale-95 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="startTimer">{{ __('time.start_timer') }}</span>
                    <span wire:loading wire:target="startTimer" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('time.starting') }}...
                    </span>
                </button>
            @else
                <!-- Stop Button -->
                <button wire:click="stopTimer" 
                        wire:loading.attr="disabled"
                        wire:target="stopTimer"
                        class="w-full bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white text-sm font-medium py-2 px-4 rounded-md transition-all duration-200 transform active:scale-95 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="stopTimer">{{ __('time.stop_timer') }}</span>
                    <span wire:loading wire:target="stopTimer" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('time.stopping') }}...
                    </span>
                </button>
            @endif
        </div>
    </div>
</div>