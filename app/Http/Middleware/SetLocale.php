<?php

namespace App\Http\Middleware;

use App\Helpers\LocaleHelper;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        // Set application locale
        app()->setLocale($locale);

        // Configure Carbon and other locale-specific settings
        LocaleHelper::configureCarbon();

        return $next($request);
    }

    /**
     * Determine the appropriate locale for the request.
     */
    private function determineLocale(Request $request): string
    {
        // 1. User preference (for authenticated users)
        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }

        // 2. Session locale (for guest users)
        if (session('locale')) {
            $sessionLocale = session('locale');
            if ($this->isValidLocale($sessionLocale)) {
                return $sessionLocale;
            }
        }

        // 3. Browser language detection
        $browserLocale = $this->detectBrowserLocale($request);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 4. Default to application locale
        return config('app.locale', 'cs');
    }

    /**
     * Detect locale from browser Accept-Language header.
     */
    private function detectBrowserLocale(Request $request): ?string
    {
        $availableLocales = array_keys(config('app.available_locales', ['cs' => [], 'en' => []]));

        // Get preferred language from browser
        $preferred = $request->getPreferredLanguage($availableLocales);

        if ($preferred) {
            // Extract language code (e.g., 'cs' from 'cs-CZ')
            $locale = substr($preferred, 0, 2);

            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        return null;
    }

    /**
     * Check if the given locale is valid and supported.
     */
    private function isValidLocale(string $locale): bool
    {
        return array_key_exists($locale, config('app.available_locales', []));
    }
}
