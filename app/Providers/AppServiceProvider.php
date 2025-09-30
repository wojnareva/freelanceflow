<?php

namespace App\Providers;

use App\Helpers\LocaleHelper;
use App\Models\InvoiceItem;
use App\Observers\InvoiceItemObserver;
use App\Services\CalendarService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        InvoiceItem::observe(InvoiceItemObserver::class);

        // Register Czech formatting Blade directives
        $this->registerBladeDirectives();

        // Share locale and calendar configuration with all views
        View::composer('*', function ($view) {
            $view->with([
                'calendarConfig' => CalendarService::getConfig(),
                'firstDayOfWeek' => LocaleHelper::getFirstDayOfWeek(),
                'dayNames' => LocaleHelper::getDayNamesOrdered(),
                'dayNamesShort' => LocaleHelper::getDayNamesShortOrdered(),
            ]);
        });
    }

    /**
     * Register Blade directives for Czech formatting.
     */
    private function registerBladeDirectives(): void
    {
        // Czech money formatting: @money(1500) -> "1 500,00 Kč"
        Blade::directive('money', function ($expression) {
            return "<?php echo App\\Services\\LocalizationService::formatMoney({$expression}); ?>";
        });

        // Czech number formatting: @number(1500.50) -> "1 500,50"
        Blade::directive('number', function ($expression) {
            return "<?php echo App\\Services\\LocalizationService::formatNumber({$expression}); ?>";
        });

        // Czech date formatting: @czdate($date) -> "21. 9. 2025"
        Blade::directive('czdate', function ($expression) {
            return "<?php echo App\\Services\\LocalizationService::formatDate({$expression}); ?>";
        });

        // Czech datetime formatting: @czdatetime($datetime) -> "21. 9. 2025 v 14:30"
        Blade::directive('czdatetime', function ($expression) {
            return "<?php echo App\\Services\\LocalizationService::formatDateTime({$expression}); ?>";
        });

        // Czech time formatting: @cztime($time) -> "14:30"
        Blade::directive('cztime', function ($expression) {
            return "<?php echo App\\Services\\LocalizationService::formatTime({$expression}); ?>";
        });
    }
}
