<div>
    <form wire:submit="save" class="space-y-6">
        <!-- Template Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('invoices.template_name') }}
            </label>
            <input type="text" wire:model="name"
                   class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Client -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('invoices.client') }}
            </label>
            <select wire:model="client_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">{{ __('invoices.select_client') }}</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
            @error('client_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Frequency -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('invoices.frequency') }}
            </label>
            <select wire:model="frequency"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="weekly">{{ __('invoices.weekly') }}</option>
                <option value="monthly">{{ __('invoices.monthly') }}</option>
                <option value="quarterly">{{ __('invoices.quarterly') }}</option>
                <option value="yearly">{{ __('invoices.yearly') }}</option>
            </select>
            @error('frequency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('invoices.description') }}
            </label>
            <textarea wire:model="description" rows="3"
                      class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Start Date and End Date -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('invoices.start_date') }} *
                </label>
                <input type="date" wire:model="start_date"
                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('invoices.end_date') }}
                </label>
                <input type="date" wire:model="end_date"
                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Amount and Days Until Due -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('invoices.amount') }} *
                </label>
                <input type="number" step="0.01" wire:model="amount"
                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('invoices.days_until_due') }} *
                </label>
                <input type="number" wire:model="days_until_due"
                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('days_until_due') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('invoice-templates.index') }}"
               class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                {{ __('app.cancel') }}
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ __('invoices.create_template') }}
            </button>
        </div>
    </form>
</div>