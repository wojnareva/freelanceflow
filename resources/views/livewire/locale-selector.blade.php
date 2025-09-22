<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    <button @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200">
        <span class="text-lg">{{ $availableLocales[$currentLocale]['flag'] ?? '🌍' }}</span>
        <span>{{ $availableLocales[$currentLocale]['name'] ?? 'Language' }}</span>
        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-600 z-50">
        
        @foreach($availableLocales as $code => $locale)
            <button wire:click="changeLocale('{{ $code }}')"
                    class="flex items-center w-full px-4 py-2 text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700 {{ $currentLocale === $code ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300' }} first:rounded-t-md last:rounded-b-md">
                <span class="text-lg mr-3">{{ $locale['flag'] ?? '🌍' }}</span>
                <span>{{ $locale['name'] ?? 'Unknown' }}</span>
                @if($currentLocale === $code)
                    <svg class="ml-auto h-4 w-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>