<div class="bg-white dark:bg-gray-800 p-6">
    <form wire:submit="save">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ $client ? __('clients.edit_client') : __('clients.create_new_client') }}
                </h3>
                @if($client)
                    <button type="button" wire:click="$dispatch('closeModal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        id="name"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="{{ __('clients.placeholders.enter_client_name') }}"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('app.email') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        wire:model="email" 
                        id="email"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="{{ __('clients.placeholders.enter_email') }}"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('clients.phone') }}
                    </label>
                    <input 
                        type="tel" 
                        wire:model="phone" 
                        id="phone"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                        placeholder="{{ __('clients.placeholders.enter_phone') }}"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ARES Czech Business Lookup Section -->
                <div class="md:col-span-2">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                {{ __('clients.auto_fill_company_data') }}
                            </h4>
                            <button type="button" 
                                    wire:click="toggleAutoFill"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                {{ $autoFillEnabled ? __('app.disable') : __('app.enable') }}
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- IČO input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('clients.ico') }}
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           wire:model.live.debounce.500ms="ico"
                                           placeholder="12345678"
                                           maxlength="8"
                                           pattern="[0-9]{8}"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ico') border-red-500 @enderror">
                                    
                                    <!-- Loading indicator -->
                                    <div wire:loading wire:target="updatedIco,fetchCompanyData" 
                                         class="absolute right-3 top-3">
                                        <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                                    </div>
                                    
                                    <!-- Success indicator -->
                                    @if($companyDataFound && !$aresLookupLoading)
                                        <div class="absolute right-3 top-3 text-green-500">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                @error('ico') 
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                @enderror
                            </div>
                            
                            <!-- Manual lookup button -->
                            <div class="flex items-end">
                                <button type="button" 
                                        wire:click="fetchCompanyData"
                                        class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center justify-center"
                                        wire:loading.attr="disabled"
                                        wire:target="fetchCompanyData"
                                        {{ empty($ico) || strlen($ico) !== 8 ? 'disabled' : '' }}>
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    {{ __('clients.lookup_company') }}
                                </button>
                            </div>
                        </div>
                        
                        <!-- Company data preview -->
                        @if($companyDataFound)
                            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-green-800 dark:text-green-300">
                                        ✅ {{ __('clients.company_data_loaded') }}
                                    </div>
                                    <button type="button" 
                                            wire:click="clearCompanyData"
                                            class="text-xs text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                                        {{ __('app.clear') }}
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- ARES Success Message -->
                        @if(session('ares_success'))
                            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                                <div class="text-sm text-green-800 dark:text-green-300">
                                    {{ session('ares_success') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- DIČ -->
                <div>
                    <label for="dic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('clients.dic') }}
                    </label>
                    <input 
                        type="text" 
                        wire:model="dic" 
                        id="dic"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dic') border-red-500 @enderror"
                        placeholder="CZ12345678"
                    >
                    @error('dic')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company -->
                <div class="md:col-span-2">
                    <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('clients.company') }}
                    </label>
                    <input 
                        type="text" 
                        wire:model="company" 
                        id="company"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('company') border-red-500 @enderror"
                        placeholder="{{ __('clients.placeholders.enter_company_name') }}"
                    >
                    @error('company')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('clients.address') }}
                    </label>
                    <textarea 
                        wire:model="address" 
                        id="address"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                        placeholder="Enter client address"
                    ></textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea 
                        wire:model="notes" 
                        id="notes"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                        placeholder="Add any additional notes about this client"
                    ></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                @if(!$client)
                    <a 
                        href="{{ route('clients.index') }}" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200"
                    >
                        Cancel
                    </a>
                @else
                    <button 
                        type="button" 
                        wire:click="$dispatch('closeModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200"
                    >
                        Cancel
                    </button>
                @endif
                
                <button 
                    type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>
                        {{ $client ? 'Update Client' : 'Create Client' }}
                    </span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
