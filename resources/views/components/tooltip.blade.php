<div class="relative inline-block" x-data="{ showTooltip: false }">
    <!-- Trigger element -->
    <div 
        @mouseenter="showTooltip = true" 
        @mouseleave="showTooltip = false"
        @focus="showTooltip = true"
        @blur="showTooltip = false"
        class="cursor-help"
    >
        {{ $slot }}
    </div>
    
    <!-- Tooltip -->
    <div 
        x-show="showTooltip"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute z-50 {{ $getPositionClasses() }} {{ $getSizeClasses() }} bg-gray-800 text-white rounded-lg shadow-lg"
    >
        {{ $text }}
        <!-- Arrow -->
        <div class="absolute w-0 h-0 border-4 {{ $getArrowClasses() }}"></div>
    </div>
</div>