<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('time.time_tracking_calendar') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('time-tracking.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('time.list_view_button') }}
                </a>
                <a href="{{ route('time-tracking.bulk-edit') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    {{ __('time.bulk_edit_button') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:time-tracking.time-entries-calendar />
        </div>
    </div>
</x-app-layout>