@props(['lines' => 3, 'avatar' => false, 'button' => false])

<div {{ $attributes->merge(['class' => 'animate-pulse']) }}>
    @if($avatar)
    <div class="flex items-center space-x-4 mb-4">
        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
        <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
        </div>
    </div>
    @endif

    <div class="space-y-3">
        @for($i = 0; $i < $lines; $i++)
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
        @endfor
    </div>

    @if($button)
    <div class="mt-4">
        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
    </div>
    @endif
</div>