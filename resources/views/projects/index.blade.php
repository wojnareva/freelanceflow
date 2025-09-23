<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('projects.projects') }}
            </h2>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <a href="{{ route('projects.timeline-all') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 text-center">
                    <span class="hidden sm:inline">📅 {{ __('projects.all_projects_timeline') }}</span>
                    <span class="sm:hidden">📅 {{ __('projects.timeline_short') }}</span>
                </a>
                <a href="{{ route('projects.kanban', ['project' => 1]) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 text-center">
                    <span class="hidden sm:inline">{{ __('projects.sample_kanban') }}</span>
                    <span class="sm:hidden">{{ __('projects.kanban_short') }}</span>
                </a>
                <a href="{{ route('projects.timeline', ['project' => 1]) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 text-center">
                    <span class="hidden sm:inline">{{ __('projects.sample_timeline') }}</span>
                    <span class="sm:hidden">{{ __('projects.timeline_short') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:projects.projects-list />
        </div>
    </div>
</x-app-layout>