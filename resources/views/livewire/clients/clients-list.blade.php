<div>
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1 max-w-md relative">
            <input 
                type="text" 
                wire:model.live="search"
                placeholder="{{ __('clients.search_clients_placeholder') }}"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            >
            <!-- Loading indicator for search -->
            <div wire:loading wire:target="search" class="absolute right-3 top-3">
                <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <span wire:loading.remove wire:target="search">{{ $clients->total() }} {{ $clients->total() === 1 ? __('clients.client_found') : __('clients.clients_found') }}</span>
            <span wire:loading wire:target="search" class="text-blue-600 dark:text-blue-400">{{ __('clients.searching') }}</span>
        </div>
    </div>

    <!-- Clients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading skeleton -->
        <div wire:loading wire:target="search" class="col-span-full">
            @for($i = 0; $i < 6; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 animate-pulse">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                    </div>
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                </div>
            @endfor
        </div>
        
        <div wire:loading.remove wire:target="search" class="col-span-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($clients as $client)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 transform">
                <!-- Client Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-semibold text-lg">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $client->name }}</h3>
                            @if($client->company)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $client->company }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button 
                            wire:click="editClient({{ $client->id }})"
                            wire:loading.attr="disabled"
                            wire:target="editClient({{ $client->id }})"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200 disabled:opacity-50"
                            title="{{ __('clients.edit_client_title') }}"
                        >
                            <svg wire:loading.remove wire:target="editClient({{ $client->id }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <svg wire:loading wire:target="editClient({{ $client->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                        <button 
                            wire:click="deleteClient({{ $client->id }})"
                            wire:confirm="{{ __('clients.delete_client_confirm') }}"
                            wire:loading.attr="disabled"
                            wire:target="deleteClient({{ $client->id }})"
                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors duration-200 disabled:opacity-50"
                            title="{{ __('clients.delete_client_title') }}"
                        >
                            <svg wire:loading.remove wire:target="deleteClient({{ $client->id }})" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <svg wire:loading wire:target="deleteClient({{ $client->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Client Info -->
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        {{ $client->email }}
                    </div>
                    @if($client->phone)
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $client->phone }}
                        </div>
                    @endif
                </div>

                <!-- Client Stats -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('clients.projects_label') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100 ml-1">{{ $client->projects_count }}</span>
                    </div>
                    <a 
                        href="{{ route('clients.show', $client) }}" 
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium"
                    >
                        {{ __('clients.view_details') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('clients.no_clients_found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($search)
                            {{ __('clients.no_clients_match_search') }}
                        @else
                            {{ __('clients.get_started_create_client') }}
                        @endif
                    </p>
                    @if(!$search)
                        <div class="mt-6">
                            <a 
                                href="{{ route('clients.create') }}" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ __('clients.add_first_client') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
        <div class="mt-8">
            {{ $clients->links() }}
        </div>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal && $editingClient)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <livewire:clients.client-form :client="$editingClient" :key="$editingClient->id" />
                </div>
            </div>
        </div>
    @endif
</div>
