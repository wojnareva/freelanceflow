<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Language & Localization Settings -->
        <div class="space-y-4">
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Language & Regional Settings') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Preferred Language -->
                <div>
                    <x-input-label for="locale" :value="__('Preferred Language')" />
                    <select id="locale" name="locale" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        @foreach(config('app.available_locales') as $code => $data)
                            <option value="{{ $code }}" {{ old('locale', $user->locale ?? config('app.locale')) === $code ? 'selected' : '' }}>
                                {{ $data['flag'] }} {{ $data['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('locale')" />
                </div>

                <!-- Currency -->
                <div>
                    <x-input-label for="currency" :value="__('Currency')" />
                    <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="CZK" {{ old('currency', $user->currency ?? 'CZK') === 'CZK' ? 'selected' : '' }}>🇨🇿 Czech Koruna (CZK)</option>
                        <option value="EUR" {{ old('currency', $user->currency ?? 'CZK') === 'EUR' ? 'selected' : '' }}>🇪🇺 Euro (EUR)</option>
                        <option value="USD" {{ old('currency', $user->currency ?? 'CZK') === 'USD' ? 'selected' : '' }}>🇺🇸 US Dollar (USD)</option>
                        <option value="GBP" {{ old('currency', $user->currency ?? 'CZK') === 'GBP' ? 'selected' : '' }}>🇬🇧 British Pound (GBP)</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                </div>

                <!-- Timezone -->
                <div>
                    <x-input-label for="timezone" :value="__('Timezone')" />
                    <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="Europe/Prague" {{ old('timezone', $user->timezone ?? 'Europe/Prague') === 'Europe/Prague' ? 'selected' : '' }}>Europe/Prague (Central European Time)</option>
                        <option value="Europe/London" {{ old('timezone', $user->timezone ?? 'Europe/Prague') === 'Europe/London' ? 'selected' : '' }}>Europe/London (Greenwich Mean Time)</option>
                        <option value="Europe/Berlin" {{ old('timezone', $user->timezone ?? 'Europe/Prague') === 'Europe/Berlin' ? 'selected' : '' }}>Europe/Berlin (Central European Time)</option>
                        <option value="America/New_York" {{ old('timezone', $user->timezone ?? 'Europe/Prague') === 'America/New_York' ? 'selected' : '' }}>America/New_York (Eastern Time)</option>
                        <option value="UTC" {{ old('timezone', $user->timezone ?? 'Europe/Prague') === 'UTC' ? 'selected' : '' }}>UTC (Coordinated Universal Time)</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                </div>

                <!-- Number Format -->
                <div>
                    <x-input-label for="number_format" :value="__('Number Format')" />
                    <select id="number_format" name="number_format" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="czech_space" {{ old('number_format', $user->number_format ?? 'czech_space') === 'czech_space' ? 'selected' : '' }}>1 234,56 Kč (Česky s mezerou)</option>
                        <option value="czech_dot" {{ old('number_format', $user->number_format ?? 'czech_space') === 'czech_dot' ? 'selected' : '' }}>1.234,56 Kč (Česky s tečkou)</option>
                        <option value="us" {{ old('number_format', $user->number_format ?? 'czech_space') === 'us' ? 'selected' : '' }}>$1,234.56 (US/International)</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('number_format')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
