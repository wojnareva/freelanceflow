@if($showGuidance && $currentStepData)
<!-- Guidance Modal Overlay -->
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showGuidance') }">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <!-- Progress indicator -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Step {{ $currentStep + 1 }} of {{ $totalSteps }}
                    </span>
                    <button wire:click="skipGuidance" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ (($currentStep + 1) / $totalSteps) * 100 }}%"></div>
                </div>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                    {{ $currentStepData['title'] }}
                </h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $currentStepData['content'] }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <button wire:click="previousStep" 
                        @if($currentStep === 0) disabled @endif
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    Previous
                </button>
                
                <div class="flex gap-2">
                    <button wire:click="skipGuidance" 
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Skip Tour
                    </button>
                    <button wire:click="nextStep" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        {{ $currentStepData['action'] }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
