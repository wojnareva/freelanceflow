# 🇨🇿 Česká lokalizace FreelanceFlow

## 📋 Přehled funkcí

**FreelanceFlow** bude nativně podporovat českou lokalizaci včetně:

### 🌍 **Jazyková lokalizace**
- Český jazyk jako výchozí nastavení
- Všechny UI texty, email šablony, PDF faktury v češtině
- Správná pluralizace (1 projekt, 2-4 projekty, 5+ projektů)
- Validační zprávy v českém jazyce

### 💰 **Česká měna a formátování**
- **CZK** jako výchozí měna
- **České číselné formáty:** `2 700,50 Kč` místo `2,700.50 CZK`
- **Tisícové oddělovače:** mezera místo čárky
- **Desetinná čárka** místo tečky

### 📅 **České datum a čas**
- **Formát data:** `21. 9. 2025` místo `2025-09-21`
- **České názvy měsíců:** září, říjen, listopad
- **České dny v týdnu:** pondělí, úterý, středa
- **24hodinový formát:** `14:30` místo `2:30 PM`

### 🏢 **ARES API integrace**
- Automatické vyplňování údajů klientů podle **IČO**
- Načítání z oficiálního registru ARES
- **IČO validace** s kontrolním algoritmem
- Background aktualizace firemních dat

### ⚙️ **Flexibilní nastavení**
- Výběr lokalizace **před registrací**
- Změna jazyka a formátů v **user settings**
- Persistentní uložení v databázi
- Browser detection jako fallback

---

## 🛠️ Technická implementace

### 1. **Laravel Localization Setup**

```php
// config/app.php
'locale' => 'cs',
'fallback_locale' => 'en',
'available_locales' => ['cs', 'en', 'sk'],

// Middleware registrace
'middleware' => [
    \App\Http\Middleware\SetLocale::class,
]
```

### 2. **Database Schema - User Preferences**

```php
// Migration: add_locale_preferences_to_users_table
Schema::table('users', function (Blueprint $table) {
    $table->string('locale', 5)->default('cs');
    $table->string('currency', 3)->default('CZK');
    $table->string('number_format')->default('cs');
    $table->string('date_format')->default('cs');
    $table->string('timezone')->default('Europe/Prague');
});

// Migration: add_registry_fields_to_clients
Schema::table('clients', function (Blueprint $table) {
    $table->string('ico', 8)->nullable()->index();
    $table->string('dic', 15)->nullable();
    $table->json('company_registry_data')->nullable();
    $table->timestamp('registry_updated_at')->nullable();
});
```

### 3. **Language Files Structure**

```
resources/lang/
├── cs/
│   ├── app.php           # Obecné texty
│   ├── dashboard.php     # Dashboard
│   ├── projects.php      # Projekty
│   ├── clients.php       # Klienti  
│   ├── invoices.php      # Faktury
│   ├── time.php          # Time tracking
│   ├── auth.php          # Autentizace
│   └── validation.php    # Validace
└── en/
    └── [same structure]
```

### 4. **Příklady jazykových souborů**

```php
// resources/lang/cs/dashboard.php
return [
    'title' => 'Přehled',
    'stats' => [
        'monthly_revenue' => 'Měsíční příjem',
        'unpaid_invoices' => 'Nezaplacené faktury', 
        'active_projects' => 'Aktivní projekty',
        'hours_this_week' => 'Hodin tento týden',
    ],
    'quick_actions' => [
        'new_project' => 'Nový projekt',
        'create_invoice' => 'Vytvořit fakturu',
        'add_client' => 'Přidat klienta',
        'start_timer' => 'Spustit časomíru',
    ],
    'activity_feed' => 'Poslední aktivita',
    'revenue_chart' => 'Příjmy za posledních 6 měsíců',
];

// resources/lang/cs/clients.php
return [
    'title' => 'Klienti',
    'create' => 'Nový klient',
    'edit' => 'Upravit klienta',
    'name' => 'Jméno',
    'company' => 'Firma',
    'email' => 'E-mail',
    'phone' => 'Telefon',
    'address' => 'Adresa',
    'ico' => 'IČO',
    'dic' => 'DIČ',
    'hourly_rate' => 'Hodinová sazba',
    'currency' => 'Měna',
    'auto_fill_company_data' => 'Automatické vyplnění údajů firmy',
    'lookup_company' => 'Vyhledat firmu',
    'company_data_loaded' => 'Údaje firmy byly načteny z registru',
    'ico_not_found' => 'Firma s tímto IČO nebyla nalezena',
    'ico_invalid' => 'Neplatné IČO',
];

// resources/lang/cs/invoices.php
return [
    'title' => 'Faktury',
    'create' => 'Vytvořit fakturu',
    'edit' => 'Upravit fakturu',
    'status' => [
        'draft' => 'Koncept',
        'sent' => 'Odesláno',
        'paid' => 'Zaplaceno',
        'overdue' => 'Po splatnosti',
        'cancelled' => 'Stornováno',
    ],
    'details' => [
        'invoice_number' => 'Číslo faktury',
        'issue_date' => 'Datum vystavení',
        'due_date' => 'Datum splatnosti',
        'subtotal' => 'Mezisoučet',
        'tax' => 'DPH',
        'total' => 'Celkem k úhradě',
        'payment_method' => 'Způsob platby',
        'bank_transfer' => 'Bankovní převod',
        'cash' => 'Hotově',
        'card' => 'Karta',
    ],
];
```

### 5. **LocalizationService - Formátování**

```php
// app/Services/LocalizationService.php
<?php

namespace App\Services;

use NumberFormatter;
use Carbon\Carbon;

class LocalizationService
{
    public static function formatMoney($amount, $currency = null): string
    {
        $locale = app()->getLocale();
        $currency = $currency ?? auth()->user()?->currency ?? 'CZK';
        
        if ($locale === 'cs') {
            // České formátování: 2 700,50 Kč
            if ($currency === 'CZK') {
                return number_format($amount, 2, ',', ' ') . ' Kč';
            }
            
            $formatter = new NumberFormatter('cs_CZ', NumberFormatter::CURRENCY);
            return $formatter->formatCurrency($amount, $currency);
        }
        
        // Anglické formátování
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }
    
    public static function formatNumber($number, $decimals = 2): string 
    {
        $locale = app()->getLocale();
        
        if ($locale === 'cs') {
            // České: 2 700,50
            return number_format($number, $decimals, ',', ' ');
        }
        
        // Anglické: 2,700.50  
        return number_format($number, $decimals, '.', ',');
    }
    
    public static function formatDate($date, $format = null): string
    {
        $locale = app()->getLocale();
        $carbon = Carbon::parse($date);
        
        if ($locale === 'cs') {
            $carbon->locale('cs');
            return $format ? $carbon->translatedFormat($format) : $carbon->translatedFormat('j. n. Y');
        }
        
        return $carbon->format($format ?? 'Y-m-d');
    }
    
    public static function formatDateTime($datetime, $format = null): string
    {
        $locale = app()->getLocale();
        $carbon = Carbon::parse($datetime);
        
        if ($locale === 'cs') {
            $carbon->locale('cs');
            $defaultFormat = 'j. n. Y v H:i';
            return $carbon->translatedFormat($format ?? $defaultFormat);
        }
        
        return $carbon->format($format ?? 'Y-m-d H:i');
    }
}
```

### 6. **SetLocale Middleware**

```php
// app/Http/Middleware/SetLocale.php
<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = $this->determineLocale($request);
        
        app()->setLocale($locale);
        Carbon::setLocale($locale);
        
        return $next($request);
    }
    
    private function determineLocale($request): string
    {
        // 1. Uživatelovo nastavení (pokud je přihlášený)
        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }
        
        // 2. Session (pro nepřihlášené)
        if (session('locale')) {
            return session('locale');
        }
        
        // 3. Browser detection
        $browserLocale = $request->getPreferredLanguage(['cs', 'en', 'sk']);
        if ($browserLocale) {
            return substr($browserLocale, 0, 2);
        }
        
        // 4. Default
        return config('app.locale', 'cs');
    }
}
```

---

## 🏢 ARES API Integrace

### 1. **AresService - API komunikace**

```php
// app/Services/AresService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AresService
{
    private const ARES_API_URL = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty';
    
    public function getCompanyData(string $ico): ?array
    {
        // Cache na 24 hodin - data se mění pomalu
        return Cache::remember("ares_company_{$ico}", 86400, function () use ($ico) {
            return $this->fetchFromAres($ico);
        });
    }
    
    private function fetchFromAres(string $ico): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::ARES_API_URL . '/' . $ico);
                
            if (!$response->successful()) {
                return null;
            }
            
            $data = $response->json();
            return $this->parseAresResponse($data);
            
        } catch (\Exception $e) {
            logger()->error('ARES API Error', [
                'ico' => $ico,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    private function parseAresResponse(array $data): array
    {
        $company = $data['ekonomickySubjekt'] ?? null;
        
        if (!$company) {
            return [];
        }
        
        $address = $company['sidlo'] ?? [];
        $legal = $company['pravniForma'] ?? [];
        
        return [
            'ico' => $company['ico'] ?? '',
            'dic' => $company['dic'] ?? null,
            'company_name' => $company['obchodniJmeno'] ?? $company['nazev'] ?? '',
            'legal_form' => $legal['nazev'] ?? '',
            'address' => $this->formatAddress($address),
            'street' => $address['nazevUlice'] ?? '',
            'street_number' => ($address['cisloDomovni'] ?? '') . '/' . ($address['cisloOrientacni'] ?? ''),
            'city' => $address['nazevObce'] ?? '',
            'postal_code' => $address['psc'] ?? '',
            'state' => 'Česká republika',
            'is_active' => $company['stavZanikuZivnosti'] !== 'ZANIKLÝ',
            'business_activities' => $this->getBusinessActivities($company),
        ];
    }
    
    private function formatAddress(array $address): string
    {
        $parts = [];
        
        if (!empty($address['nazevUlice'])) {
            $street = $address['nazevUlice'];
            if (!empty($address['cisloDomovni'])) {
                $street .= ' ' . $address['cisloDomovni'];
                if (!empty($address['cisloOrientacni'])) {
                    $street .= '/' . $address['cisloOrientacni'];
                }
            }
            $parts[] = $street;
        }
        
        if (!empty($address['nazevObce'])) {
            $city = $address['nazevObce'];
            if (!empty($address['psc'])) {
                $city = $address['psc'] . ' ' . $city;
            }
            $parts[] = $city;
        }
        
        return implode(', ', $parts);
    }
    
    private function getBusinessActivities(array $company): array
    {
        $activities = [];
        
        if (isset($company['seznamRegistraci'])) {
            foreach ($company['seznamRegistraci'] as $registration) {
                if (isset($registration['predmetyPodnikani'])) {
                    foreach ($registration['predmetyPodnikani'] as $activity) {
                        $activities[] = $activity['nazev'] ?? '';
                    }
                }
            }
        }
        
        return array_filter($activities);
    }
}
```

### 2. **IČO Validace**

```php
// app/Rules/ValidIco.php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidIco implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (!preg_match('/^[0-9]{8}$/', $value)) {
            return false;
        }
        
        // Kontrolní algoritmus IČO
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        
        for ($i = 0; $i < 7; $i++) {
            $sum += (int)$value[$i] * $weights[$i];
        }
        
        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;
        
        return (int)$value[7] === $checkDigit;
    }
    
    public function message(): string
    {
        return __('validation.ico_invalid');
    }
}
```

---

## 🎨 UI Komponenty

### 1. **LocaleSelector Livewire Component**

```php
// app/Livewire/LocaleSelector.php
<?php

namespace App\Livewire;

use Livewire\Component;

class LocaleSelector extends Component  
{
    public $currentLocale;
    public $showDropdown = false;
    public $availableLocales = [
        'cs' => ['name' => 'Čeština', 'flag' => '🇨🇿'],
        'en' => ['name' => 'English', 'flag' => '🇺🇸'],
        'sk' => ['name' => 'Slovenčina', 'flag' => '🇸🇰'],
    ];

    public function mount()
    {
        $this->currentLocale = app()->getLocale();
    }

    public function changeLocale($locale)
    {
        session(['locale' => $locale]);
        
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        $this->showDropdown = false;
        return redirect()->refresh();
    }

    public function render()
    {
        return view('livewire.locale-selector');
    }
}
```

### 2. **Locale Selector Template**

```blade
{{-- resources/views/livewire/locale-selector.blade.php --}}
<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    <button @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <span class="text-lg">{{ $availableLocales[$currentLocale]['flag'] }}</span>
        <span>{{ $availableLocales[$currentLocale]['name'] }}</span>
        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
        
        @foreach($availableLocales as $code => $locale)
            <button wire:click="changeLocale('{{ $code }}')"
                    class="flex items-center w-full px-4 py-2 text-sm text-left hover:bg-gray-50 {{ $currentLocale === $code ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="text-lg mr-3">{{ $locale['flag'] }}</span>
                <span>{{ $locale['name'] }}</span>
                @if($currentLocale === $code)
                    <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>
```

### 3. **ClientForm s ARES integrací**

```blade
{{-- resources/views/livewire/clients/client-form.blade.php --}}
<form wire:submit="save" class="space-y-6">
    
    {{-- IČO lookup section --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-blue-900">
                {{ __('clients.auto_fill_company_data') }}
            </h4>
            <button type="button" 
                    wire:click="toggleAutoFill"
                    class="text-sm text-blue-600 hover:text-blue-800">
                {{ $autoFillEnabled ? __('app.disable') : __('app.enable') }}
            </button>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            {{-- IČO input --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('clients.ico') }}
                </label>
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.500ms="ico"
                           placeholder="12345678"
                           maxlength="8"
                           pattern="[0-9]{8}"
                           class="form-input w-full {{ $errors->has('ico') ? 'border-red-300' : '' }}">
                    
                    {{-- Loading indicator --}}
                    <div wire:loading wire:target="updatedIco" 
                         class="absolute right-3 top-3">
                        <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                    </div>
                    
                    {{-- Success indicator --}}
                    @if($companyDataFound)
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
            
            {{-- Manual lookup button --}}
            <div class="flex items-end">
                <button type="button" 
                        wire:click="fetchCompanyData"
                        class="btn btn-secondary w-full"
                        {{ empty($ico) || strlen($ico) !== 8 ? 'disabled' : '' }}>
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ __('clients.lookup_company') }}
                </button>
            </div>
        </div>
        
        {{-- Company data preview --}}
        @if($companyDataFound)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-green-800">
                        ✅ {{ __('clients.company_data_loaded') }}
                    </div>
                    <button type="button" 
                            wire:click="clearCompanyData"
                            class="text-xs text-green-600 hover:text-green-800">
                        {{ __('app.clear') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
    
    {{-- Regular form fields with Czech labels --}}
    <div class="grid grid-cols-2 gap-6">
        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('clients.name') }} *
            </label>
            <input type="text" wire:model="name" class="form-input w-full" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Company --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('clients.company') }}
            </label>
            <input type="text" wire:model="company" class="form-input w-full">
            @error('company') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- DIC --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('clients.dic') }}
            </label>
            <input type="text" wire:model="dic" class="form-input w-full">
            @error('dic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('clients.email') }} *
            </label>
            <input type="email" wire:model="email" class="form-input w-full" required>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Hourly Rate with Czech formatting --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('clients.hourly_rate') }}
            </label>
            <div class="relative">
                <input type="number" 
                       wire:model="hourly_rate" 
                       class="form-input w-full pr-12"
                       placeholder="1500"
                       step="50">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">Kč/hod</span>
                </div>
            </div>
            @error('hourly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        {{-- Currency --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('clients.currency') }}
            </label>
            <select wire:model="currency" class="form-select w-full">
                <option value="CZK">CZK - Koruna česká</option>
                <option value="EUR">EUR - Euro</option>
                <option value="USD">USD - US Dollar</option>
            </select>
            @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
    
    {{-- Address --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('clients.address') }}
        </label>
        <textarea wire:model="address" 
                  rows="3" 
                  class="form-textarea w-full"
                  placeholder="Ulice a číslo popisné, PSČ Město"></textarea>
        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    
    {{-- Submit buttons --}}
    <div class="flex justify-end space-x-3">
        <button type="button" wire:click="$dispatch('closeModal')" class="btn btn-secondary">
            {{ __('app.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
            {{ $clientId ? __('app.update') : __('app.create') }}
        </button>
    </div>
</form>
```

---

## 📋 Implementation Checklist

### **Phase 1: Základní lokalizace** ✅
- [ ] Nastavit Laravel localization config (`config/app.php`)
- [ ] Vytvořit `SetLocale` middleware
- [ ] Přidat locale sloupce do `users` tabulky
- [ ] Vytvořit `LocalizationService` helper třídu
- [ ] Browser detection pro výchozí jazyk

### **Phase 2: Jazykové soubory** ✅
- [ ] Vytvořit kompletní českou translaci pro všechny moduly
- [ ] `resources/lang/cs/app.php` - obecné texty
- [ ] `resources/lang/cs/dashboard.php` - dashboard texty
- [ ] `resources/lang/cs/clients.php` - klienti
- [ ] `resources/lang/cs/projects.php` - projekty  
- [ ] `resources/lang/cs/invoices.php` - faktury
- [ ] `resources/lang/cs/time.php` - time tracking
- [ ] `resources/lang/cs/auth.php` - autentizace
- [ ] `resources/lang/cs/validation.php` - validace
- [ ] Přeložit email šablony
- [ ] Přeložit PDF faktury

### **Phase 3: ARES API integrace** ✅
- [ ] Implementovat `AresService` s HTTP klientem
- [ ] Přidat IČO/DIČ sloupce do `clients` tabulky  
- [ ] Vytvořit `ValidIco` validation rule
- [ ] Implementovat cache strategii pro API responses
- [ ] Error handling a logging pro API failures
- [ ] Background job pro aktualizaci dat (`UpdateClientCompanyDataJob`)

### **Phase 4: UI komponenty** ✅
- [ ] `LocaleSelector` Livewire komponenta
- [ ] Locale switch v registračním formuláři
- [ ] Rozšířit `ClientForm` o IČO lookup funkcionalitu
- [ ] User settings page s lokalizačními volbami
- [ ] Loading states a success indikátory
- [ ] Company data preview po načtení z ARES

### **Phase 5: Formátování a helpers** ✅
- [ ] Implementovat české číselné formáty (mezera jako tisícový oddělovač)
- [ ] České měnové formáty (`2 700,50 Kč`)
- [ ] České formáty data (`21. 9. 2025`)
- [ ] 24hodinový časový formát
- [ ] Carbon lokalizace pro české názvy měsíců/dnů
- [ ] Blade directives pro quick formatting

### **Phase 6: Testing & validace** ✅
- [ ] Unit testy pro `AresService`
- [ ] Unit testy pro `LocalizationService`
- [ ] Feature testy pro IČO lookup workflow
- [ ] Livewire testy pro `LocaleSelector`
- [ ] Validace IČO kontrolního algoritmu
- [ ] Test všech českých formátů

### **Phase 7: Polish & dokumentace** ✅
- [ ] Mobile responsive locale selector
- [ ] Keyboard shortcuts (Alt+L pro locale switch)
- [ ] SEO meta tags podle jazyka
- [ ] Czech cookie consent text
- [ ] Performance optimalizace (lazy loading translations)
- [ ] Documentation pro přidání nových jazyků

---

## 🎯 User Experience Flow

### **Nový uživatel workflow:**

1. **Landing page** → Automaticky čeština (browser detection)
2. **Registrace** → Locale selector nahoře, CZK předvybrané
3. **First login** → Dashboard v češtině, české formáty
4. **Klient onboarding:**
   - Zadá IČO → automatické načtení z ARES
   - Vše předvyplněné v českých formátech
   - Hodinová sazba v "Kč/hod"

### **Existující uživatel:**
1. **Settings** → Může změnit jazyk, měnu, formáty
2. **Persistentní** → Nastavení se uchovává napříč sessions
3. **Flexibility** → Může mít různé klienty v různých měnách

---

## 🌟 Klíčové benefity

### **Pro českého freelancera:**
- ⚡ **Rychlost** - žádné vypisování firemních údajů
- ✅ **Přesnost** - data přímo z oficiálního registru  
- 🇨🇿 **Nativnost** - vše v českém jazyce a formátech
- 💰 **Profesionalita** - faktury v českých standardech
- 📱 **UX** - intuitivní česká terminologie

### **Technické výhody:**
- 🔄 **Automatizace** - ARES lookup + cache strategy
- 🛡️ **Validace** - IČO kontrolní algoritmus
- 🌍 **Extensibilita** - jednoduché přidání SK/EN
- ⚙️ **Flexibility** - per-user locale preferences  
- 🚀 **Performance** - cached responses, optimized queries

---

## 📊 Testing Strategy

### **Testovací scénáře:**

1. **Locale detection:**
   - Browser čeština → automaticky CS locale
   - Session precedence over browser detection
   - User preference precedence over session

2. **ARES API:**
   - Validní IČO → úspěšné načtení dat
   - Nevalidní IČO → error handling
   - API timeout → graceful fallback
   - Cache hit/miss scenarios

3. **Formátování:**
   - Numbers: `2700.5` → `2 700,50`
   - Currency: `2700.5 CZK` → `2 700,50 Kč`
   - Dates: `2025-09-21` → `21. 9. 2025`
   - DateTime: `2025-09-21 14:30` → `21. 9. 2025 v 14:30`

4. **UI/UX:**
   - Locale selector responsiveness
   - Loading states během ARES lookup
   - Error messages v správném jazyce
   - Form validation v češtině

---

## 🚀 Deployment Notes

### **Environment variables:**
```env
# Locale settings
APP_LOCALE=cs
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=Europe/Prague

# ARES API (žádné klíče potřeba - veřejné API)
ARES_CACHE_TTL=86400
ARES_API_TIMEOUT=10

# Available locales
APP_AVAILABLE_LOCALES=cs,en,sk
```

### **Production optimizations:**
- Preload translations do cache
- CDN pro language assets
- Redis cache pro ARES responses
- Queue jobs pro background ARES updates
- Monitoring API response times

---

## 🎉 Závěr

Česká lokalizace + ARES integrace výrazně zvýší **value proposition** FreelanceFlow pro český trh:

✅ **Nativní experience** - vše v češtině a českých formátech  
✅ **Time savings** - automatické vyplňování firemních údajů  
✅ **Professional výstup** - faktury v českých standardech  
✅ **Compliance ready** - připraveno pro český účetní systém  
✅ **Rozšiřitelnost** - jednoduchá podpora dalších trhů (SK, PL)

**Next step:** Implementace Phase 1-3 pro MVP funkcionalita, pak postupné rozšiřování o pokročilé funkce! 🚀