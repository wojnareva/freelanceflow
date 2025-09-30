<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Page Not Found') }} - {{ config('app.name') }}</title>
    
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
                <div class="mx-auto w-48 h-48 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-full flex items-center justify-center">
                    <svg class="w-24 h-24 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.137 0-4.146-.832-5.636-2.177M3 12l8.003 8.997L21 12H3z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Error Content -->
            <div class="mb-8">
                <h2 class="text-6xl font-bold text-gray-800 dark:text-gray-200 mb-4">
                    404
                </h2>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('Page Not Found') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('The page you are looking for could not be found. It might have been moved, deleted, or you entered the wrong URL.') }}
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    {{ __('Go to Dashboard') }}
                </a>
                
                <div class="text-center">
                    <button onclick="history.back()" 
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition duration-150 ease-in-out">
                        {{ __('Go Back') }}
                    </button>
                </div>
            </div>
            
            <!-- Help Links -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    {{ __('Need help?') }}
                </p>
                <div class="flex justify-center space-x-4 text-sm">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('projects.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Projects') }}
                    </a>
                    <a href="{{ route('clients.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Clients') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>