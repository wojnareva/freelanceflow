# Fixing Locale Switching in FreelanceFlow

## Issue Diagnosis
The language selector dropdown appears in the UI, but selecting a new locale (e.g., from CS to EN) doesn't apply changes across the app. The page may reload, but text remains in the original language. This occurs because:

- The `LocaleSelector` Livewire component sets the session/user locale correctly but uses `redirect()->to(request()->fullUrl())`, which in Livewire contexts may not fully reload the page or re-trigger middleware consistently.
- The `SetLocale` middleware (registered in `bootstrap/app.php`) runs on each request but relies on session/user data, which isn't always refreshed post-Livewire interaction.
- No errors in logs (from recent checks), but default fallback to 'cs' persists without explicit app locale update during the Livewire action.
- Browser locale detection and Carbon formatting work, but the switch lacks immediate app-level enforcement.

## Step-by-Step Fix Instructions

### 1. Update the LocaleSelector Component (app/Livewire/LocaleSelector.php)
Replace the `changeLocale` method with this version to set the app locale immediately, update component state, and force a full page navigation (avoids Livewire's partial updates):

```php
public function changeLocale($locale)
{
    // Validate locale
    if (!LocalizationService::isValidLocale($locale)) {
        session()->flash('error', 'Invalid locale selected.');
        return;
    }

    // Set session locale
    session(['locale' => $locale]);
    
    // Update user preference if authenticated
    if (auth()->check()) {
        auth()->user()->update(['locale' => $locale]);
    }
    
    // Set application locale immediately (triggers middleware logic)
    app()->setLocale($locale);
    
    // Update component state for UI
    $this->currentLocale = $locale;
    $this->showDropdown = false;
    
    // Force full navigation to current URL (re-triggers middleware and full reload)
    return $this->redirect(request()->url(), navigate: true);
}
```

- **Why?** `app()->setLocale()` ensures immediate effect. `$this->redirect(..., navigate: true)` forces a browser navigation, reloading the page fully so middleware re-runs with updated session.

### 2. Verify Middleware Registration
Confirm `SetLocale` middleware is appended to web group in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
})
```

- If missing, add it as shown. This ensures it runs early in the request pipeline after session is loaded.

### 3. Update URL Generation for Locale Support (Optional but Recommended)
To make URLs locale-aware (e.g., `/en/dashboard`), add a route prefix. In `routes/web.php`, wrap routes:

```php
Route::prefix('{locale?}')->where('locale', 'cs|en|sk')->group(function () {
    // All your web routes here
    Route::get('/', function () { /* ... */ })->name('home');
    // ...
});

// Add a middleware alias if needed in bootstrap/app.php:
$middleware->alias([
    'setlocale' => \App\Http\Middleware\SetLocale::class,
]);
```

- Then, in `SetLocale.php`, after determining locale, add:
```php
// In determineLocale method, if no locale in URL, redirect to add it
if (!$request->segment(1) || !in_array($request->segment(1), array_keys(config('app.available_locales')))) {
    $locale = $this->determineLocale($request);
    return redirect()->to("/{$locale}" . $request->getPathInfo());
}
```

- Update `changeLocale` to include locale in redirect: `return $this->redirect("/{$locale}" . request()->path(), navigate: true);`
- **Why?** Prevents duplicate content and ensures locale persistence in URLs. Update links accordingly (e.g., use `route('home', ['locale' => app()->getLocale()])`).

### 4. Enhance LocalizationService (app/Services/LocalizationService.php)
Add a method to force refresh locale in components:

```php
public static function refreshLocale(string $locale): void
{
    if (self::isValidLocale($locale)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
}
```

- Call it in `changeLocale` before redirect: `LocalizationService::refreshLocale($locale);`

### 5. Test the Fix
- Run: `php artisan optimize:clear` (clears caches).
- Start server: `php artisan serve` and `npm run dev`.
- Log in, go to a page with the selector (e.g., dashboard).
- Switch locale (CS → EN), confirm:
  - Page reloads.
  - Text changes (e.g., "Dashboard" → Czech equivalent).
  - Session persists: In Tinker (`php artisan tinker`), run `session('locale')` (should match selection).
  - User preference: Check DB `users` table `locale` column.
- Test guest mode: Clear session, switch locale, verify browser detection fallback.
- Add a test: Create `tests/Feature/LocaleSwitchTest.php` with:

```php
public function test_locale_switching()
{
    $user = User::factory()->create(['locale' => 'cs']);
    $response = $this->actingAs($user)->post('/livewire/message/app.livewire.locale-selector', [
        'locale' => 'en'
    ]);
    
    $this->assertEquals('en', session('locale'));
    $this->assertDatabaseHas('users', ['id' => $user->id, 'locale' => 'en']);
}
```

- Run: `php artisan test --filter=LocaleSwitchTest`.

### 6. Edge Cases to Handle
- **No translations:** Ensure `resources/lang/en/*` and `cs/*` files exist and match keys (e.g., `dashboard.stats_overview`).
- **Carbon/Date issues:** Verify `Carbon::setLocale()` in middleware applies.
- **JS/Alpine conflicts:** The dropdown uses Alpine; test with `wire:navigate` off if needed.
- If still broken: Check browser console for Livewire errors; ensure no URL params interfere with redirect.

### Potential Rollback
If issues arise, revert `changeLocale` to original and use manual page refresh (F5) as temp fix.

Apply these changes manually. After fixing, run `php artisan test` and commit with `git commit -m "fix: resolve locale switching via component and middleware refresh"`. If errors persist, share log snippet or symptoms.

Last Updated: 2025-09-21