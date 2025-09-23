@props(['loading' => false, 'loadingText' => 'Loading...', 'disabled' => false])

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed']) }} 
        {{ $loading || $disabled ? 'disabled' : '' }}>
    @if($loading)
        <x-loading-spinner size="sm" class="mr-2" />
        {{ $loadingText }}
    @else
        {{ $slot }}
    @endif
</button>