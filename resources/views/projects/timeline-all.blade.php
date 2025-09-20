<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    All Projects Timeline
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Overview of all project activities and milestones
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('projects.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    ← Projects List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:projects.project-timeline />
        </div>
    </div>
</x-app-layout>