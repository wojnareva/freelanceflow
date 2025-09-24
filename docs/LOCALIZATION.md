# Localization Guide for FreelanceFlow

This document provides comprehensive guidance on how to work with localization in the FreelanceFlow application.

## Table of Contents

1. [Overview](#overview)
2. [Supported Locales](#supported-locales)
3. [How Locale Detection Works](#how-locale-detection-works)
4. [Adding New Translations](#adding-new-translations)
5. [Translation File Structure](#translation-file-structure)
6. [Using Translations in Views](#using-translations-in-views)
7. [Calendar and Date Formatting](#calendar-and-date-formatting)
8. [Currency Formatting](#currency-formatting)
9. [Testing Translations](#testing-translations)
10. [Best Practices](#best-practices)
11. [Troubleshooting](#troubleshooting)

## Overview

FreelanceFlow uses Laravel's built-in localization system with custom enhancements for:
- Automatic locale detection from browser headers
- User preference storage
- Session-based locale switching
- Locale-aware calendar settings (Monday vs Sunday week start)
- Currency formatting based on locale

## Supported Locales

Currently supported locales:
- **Czech (cs)** - Primary locale with complete translations
- **English (en)** - Secondary locale with complete translations

Configuration is stored in `config/app.php`:
```php
'available_locales' => [
    'cs' => [
        'name' => 'Čeština',
        'flag' => '🇨🇿',
    ],
    'en' => [
        'name' => 'English', 
        'flag' => '🇺🇸',
    ],
],
```

## How Locale Detection Works

The locale is determined in the following priority order:

1. **Authenticated User Preference** - `users.locale` column
2. **Session Locale** - `session('locale')`  
3. **Browser Language Detection** - `Accept-Language` header
4. **Default Locale** - Falls back to `cs` (Czech)

This logic is handled by `App\Http\Middleware\SetLocale`.

## Adding New Translations

### 1. Create Translation Files

For each new locale (e.g., `de` for German):

```bash
mkdir resources/lang/de
```

Copy existing structure from `resources/lang/cs/` or `resources/lang/en/`:

```bash
cp -r resources/lang/cs/* resources/lang/de/
```

### 2. Update Configuration

Add the new locale to `config/app.php`:

```php
'available_locales' => [
    'cs' => ['name' => 'Čeština', 'flag' => '🇨🇿'],
    'en' => ['name' => 'English', 'flag' => '🇺🇸'],
    'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
],
```

### 3. Update LocaleHelper

Add locale-specific settings in `app/Helpers/LocaleHelper.php`:

```php
match($locale) {
    'cs' => 1, // Monday
    'en' => 0, // Sunday  
    'de' => 1, // Monday (German standard)
    default => 1,
}
```

### 4. Update Calendar Configuration

Add date/time formats and day names in `LocaleHelper::getCalendarConfig()`.

## Translation File Structure

### Core Translation Files

- **`app.php`** - General UI terms, CRUD actions, status messages
- **`auth.php`** - Authentication-related texts
- **`dashboard.php`** - Dashboard-specific content
- **`clients.php`** - Client management 
- **`projects.php`** - Project management
- **`invoices.php`** - Invoice system
- **`time.php`** - Time tracking functionality
- **`expenses.php`** - Expense management
- **`validation.php`** - Laravel validation messages

### File Structure Example

```php
<?php
// resources/lang/cs/clients.php

return [
    'title' => 'Klienti',
    'create_client' => 'Vytvořit klienta',
    'edit_client' => 'Upravit klienta',
    
    // Nested arrays for grouped translations
    'placeholders' => [
        'enter_client_name' => 'Zadejte jméno klienta',
        'enter_email' => 'Zadejte emailovou adresu',
    ],
    
    'validation' => [
        'name_required' => 'Jméno je povinné',
        'email_format' => 'E-mail má neplatný formát',
    ],
];
```

## Using Translations in Views

### Basic Translation Function

```blade
{{ __('clients.title') }}
{{ __('app.create') }}
```

### With Parameters

```blade
{{ __('clients.welcome_message', ['name' => $client->name]) }}
```

### In Translation Files

```php
'welcome_message' => 'Vítejte, :name!'
```

### Pluralization

```blade
{{ trans_choice('clients.client_count', $count) }}
```

```php
'client_count' => '{0} žádní klienti|{1} jeden klient|[2,4] :count klienti|[5,*] :count klientů'
```

### Blade Directives for Localization

FreelanceFlow provides custom Blade directives:

```blade
@money(1500)           {{-- Czech: "1 500,00 Kč" | English: "$1,500.00" --}}
@number(1500.50)       {{-- Czech: "1 500,50" | English: "1,500.50" --}}
@czdate($date)         {{-- Czech: "21. 9. 2025" | English: "9/21/2025" --}}
@czdatetime($datetime) {{-- Czech: "21. 9. 2025 v 14:30" | English: "9/21/2025 2:30 PM" --}}
@cztime($time)         {{-- Czech: "14:30" | English: "2:30 PM" --}}
```

## Calendar and Date Formatting

### Week Start Configuration

- **Czech (cs)**: Monday first
- **English (en)**: Sunday first

### Using Calendar Service

```php
use App\Services\CalendarService;

// Get locale-aware week start
$weekStart = CalendarService::getWeekStart($date);

// Get calendar configuration for JavaScript
$calendarConfig = CalendarService::getConfig();

// Get localized day names
$dayNames = CalendarService::getDayNames();
```

### In Views

Calendar configuration is automatically available in all views:

```blade
<script>
const calendarConfig = @json($calendarConfig);
// calendarConfig.firstDay = 1 (Monday) for Czech, 0 (Sunday) for English
// calendarConfig.dayNames = ['Pondělí', 'Úterý', ...] for Czech
</script>
```

## Currency Formatting

### Using Format Helper

```php
format_money(1500.00) // Czech: "1 500,00 Kč" | English: "$1,500.00"
```

### Configuration

Czech formatting uses:
- Dot (.) as thousands separator
- Comma (,) as decimal separator  
- Space before currency symbol
- Format: "1.234,50 Kč"

English formatting uses:
- Comma (,) as thousands separator
- Dot (.) as decimal separator
- Currency symbol before amount
- Format: "$1,234.50"

## Testing Translations

### Unit Tests

Run localization tests:

```bash
php artisan test --filter=LocaleHelperTest
php artisan test --filter=CalendarServiceTest
php artisan test --filter=LocalizationTest
```

### Feature Tests

Test complete localization flow:

```bash
php artisan test --filter=CzechLocalizationFeatureTest
```

### Manual Testing Checklist

1. **Language Switching**
   - [ ] Locale selector appears in navigation
   - [ ] Clicking switches language immediately
   - [ ] Preference persists after reload
   - [ ] Session stores selected locale

2. **Calendar Functionality**
   - [ ] Czech locale starts week on Monday
   - [ ] English locale starts week on Sunday
   - [ ] Day names display correctly
   - [ ] Month/week views work in both locales

3. **Currency Display**
   - [ ] Czech shows "1.234,50 Kč" format
   - [ ] English shows "$1,234.50" format
   - [ ] All monetary values format correctly

4. **Page Coverage**
   - [ ] Dashboard translations
   - [ ] Client management pages
   - [ ] Project management pages
   - [ ] Invoice system pages
   - [ ] Time tracking pages
   - [ ] Expense pages

## Best Practices

### Translation Keys

1. **Use Descriptive Keys**
   ```php
   // Good
   'client_form_name_placeholder' => 'Enter client name'
   
   // Avoid
   'placeholder1' => 'Enter client name'
   ```

2. **Group Related Translations**
   ```php
   'validation' => [
       'name_required' => 'Name is required',
       'email_invalid' => 'Invalid email format',
   ],
   ```

3. **Use Consistent Naming**
   ```php
   'create_client' => 'Create Client',
   'create_project' => 'Create Project', 
   'create_invoice' => 'Create Invoice',
   ```

### Code Organization

1. **Never Hardcode Text**
   ```blade
   {{-- Good --}}
   {{ __('clients.title') }}
   
   {{-- Bad --}}
   Clients
   ```

2. **Use Appropriate Translation Files**
   - General UI → `app.php`
   - Feature-specific → `clients.php`, `projects.php`, etc.
   - Validation → `validation.php`

3. **Handle Missing Translations**
   ```blade
   {{ __('key.that.might.not.exist', [], 'Fallback text') }}
   ```

### Performance

1. **Cache Translations in Production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

2. **Avoid Dynamic Translation Keys**
   ```php
   // Good - keys are statically analyzable
   __('clients.title')
   
   // Avoid - dynamic keys are harder to track
   __("clients.{$dynamicKey}")
   ```

## Troubleshooting

### Common Issues

1. **Translations Not Updating**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Wrong Locale Detected**
   - Check `SetLocale` middleware priority
   - Verify `available_locales` configuration
   - Clear browser cache and cookies

3. **Calendar Not Switching**
   - Verify `LocaleHelper::getFirstDayOfWeek()` 
   - Check if `CalendarService` is being used
   - Clear view cache: `php artisan view:clear`

4. **Currency Format Issues**
   - Check `LocalizationService::formatMoney()`
   - Verify locale-specific number formatting
   - Test with different currency values

### Debug Commands

```bash
# Check current locale
php artisan tinker
>>> app()->getLocale()

# Test translation
>>> __('clients.title')

# Check calendar config  
>>> App\Services\CalendarService::getConfig()
```

### Log Analysis

Check Laravel logs for localization issues:
```bash
tail -f storage/logs/laravel.log | grep -i locale
```

## Contributing Translation Improvements

1. **Adding Missing Translations**
   - Identify untranslated strings using browser developer tools
   - Add keys to appropriate translation files
   - Update both Czech and English versions
   - Test changes in both locales

2. **Improving Existing Translations**
   - Review translations for accuracy and consistency
   - Consider context and user experience
   - Test changes with native speakers when possible

3. **Extending Locale Support**
   - Follow the "Adding New Translations" section
   - Ensure calendar and currency formatting work correctly
   - Add appropriate tests for new locale
   - Update this documentation

---

For questions or issues with localization, please check the existing tests in `tests/Unit/` and `tests/Feature/` for examples of expected behavior.