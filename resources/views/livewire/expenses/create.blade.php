<div>
    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Expense Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    wire:model="title" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    placeholder="{{ __('expenses.placeholders.enter_title') }}"
                >
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Amount <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="amount" 
                    wire:model="amount" 
                    step="0.01" 
                    min="0" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    placeholder="{{ __('expenses.placeholders.enter_amount') }}"
                >
                @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Currency -->
            <div>
                <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Currency <span class="text-red-500">*</span>
                </label>
                <select 
                    id="currency" 
                    wire:model="currency" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                    @foreach($currencies as $code => $name)
                        <option value="{{ $code }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Category <span class="text-red-500">*</span>
                </label>
                <select 
                    id="category" 
                    wire:model="category" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                    <option value="">Select a category</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Project -->
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Project
                </label>
                <select 
                    id="project_id" 
                    wire:model="project_id" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                    <option value="">Select a project (optional)</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                @error('project_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Expense Date -->
            <div>
                <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Expense Date <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="expense_date" 
                    wire:model="expense_date" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                @error('expense_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Billable -->
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="billable" 
                        wire:model="billable" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="billable" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        This expense is billable to a client
                    </label>
                </div>
                @if($billable)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        This expense can be included in client invoices and marked as billed when invoiced.
                    </p>
                @endif
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Description
                </label>
                <textarea 
                    id="description" 
                    wire:model="description" 
                    rows="3" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    placeholder="{{ __('expenses.placeholders.enter_description') }}"
                ></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Receipt Upload -->
            <div class="md:col-span-2">
                <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Receipt (Optional)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="receipt" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a file</span>
                                <input id="receipt" wire:model="receipt" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            PNG, JPG, PDF up to 10MB
                        </p>
                    </div>
                </div>
                @if($receipt)
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <p>Selected: {{ $receipt->getClientOriginalName() }}</p>
                    </div>
                @endif
                @error('receipt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Create Expense</span>
                <span wire:loading>Creating...</span>
            </button>
        </div>
    </form>

    <!-- Loading Overlay -->
    <div wire:loading.flex class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Creating expense...</span>
            </div>
        </div>
    </div>
</div>