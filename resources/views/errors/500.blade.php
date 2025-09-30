<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Server Error') }} - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Logo -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    FreelanceFlow
                </h1>
            </div>
            
            <!-- Error Illustration -->
            <div class="mb-8">
                <div class="mx-auto w-48 h-48 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900 dark:to-red-800 rounded-full flex items-center justify-center">
                    <svg class="w-24 h-24 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Error Content -->
            <div class="mb-8">
                <h2 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                    500
                </h2>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Server Error') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('Something went wrong on our end. We have been notified and are working to fix this issue.') }}
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-4">
                <button onclick="window.location.reload()" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ __('Try Again') }}
                </button>
                
                <div class="text-center">
                    <a href="{{ route('dashboard') }}" 
                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition duration-150 ease-in-out">
                        {{ __('Go to Dashboard') }}
                    </a>
                </div>
            </div>
            
            <!-- Error ID -->
            @if(config('app.debug') && isset($exception))
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <details class="text-left">
                    <summary class="text-sm text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-300">
                        {{ __('Technical Details') }}
                    </summary>
                    <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs text-gray-600 dark:text-gray-400 font-mono">
                        <p><strong>{{ __('Error') }}:</strong> {{ get_class($exception) }}</p>
                        <p><strong>{{ __('Message') }}:</strong> {{ $exception->getMessage() }}</p>
                        <p><strong>{{ __('File') }}:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
                    </div>
                </details>
            </div>
            @endif
            
            <!-- Help Links -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    {{ __('Need help?') }}
                </p>
                <div class="flex justify-center space-x-4 text-sm">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Dashboard') }}
                    </a>
                    <button onclick="history.back()" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Go Back') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>