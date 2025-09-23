@if($showOnboarding)
<!-- Onboarding Modal Overlay -->
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showOnboarding') }">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-8">
            <!-- Progress indicator -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Step {{ $step }} of {{ $totalSteps }}
                    </span>
                    <button wire:click="skipOnboarding" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
                </div>
            </div>

            <!-- Content -->
            <div class="text-center mb-8">
                <!-- Icon -->
                <div class="text-6xl mb-4">{{ $stepData['icon'] }}</div>
                
                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $stepData['title'] }}
                </h2>
                
                <!-- Description -->
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                    {{ $stepData['content'] }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <button wire:click="previousStep" 
                        @if($step === 1) disabled @endif
                        class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Previous
                </button>
                
                <div class="flex gap-3">
                    @if($step === 2)
                        <!-- Special step 2 actions -->
                        <button wire:click="nextStep" 
                                class="px-6 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            {{ $stepData['action_alt'] }}
                        </button>
                        <button wire:click="createSampleData" 
                                class="px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ $stepData['action'] }}
                        </button>
                    @else
                        <!-- Regular next button -->
                        <button wire:click="nextStep" 
                                class="px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ $stepData['action'] }}
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Skip option -->
            <div class="mt-6 text-center">
                <button wire:click="skipOnboarding" 
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 underline">
                    Skip and start using FreelanceFlow
                </button>
            </div>
        </div>
    </div>
</div>
@endif
