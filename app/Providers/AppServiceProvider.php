<?php

namespace App\Providers;

use App\Services\LocalizationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Register Czech formatting Blade directives
        $this->registerBladeDirectives();
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
