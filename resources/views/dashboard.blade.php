<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Onboarding -->
            <livewire:components.user-onboarding />
            
            <!-- Stats Overview -->
            <livewire:dashboard.stats-overview />
            
            <!-- Revenue Chart -->
            <div class="mb-8">
                <livewire:dashboard.revenue-chart />
            </div>
            
            <!-- Dashboard Components -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column - Activity Feed -->
                <livewire:dashboard.activity-feed />

                <!-- Right Column - Quick Actions -->
                <livewire:dashboard.quick-actions />
            </div>
        </div>
    </div>
</x-app-layout>
