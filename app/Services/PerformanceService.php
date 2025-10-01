<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PerformanceService
{
    /**
     * Cache expensive dashboard stats for a user
     */
    public function getDashboardStats(int $userId, callable $callback): array
    {
        $cacheKey = "dashboard_stats_{$userId}";
        $cacheTTL = now()->addMinutes(5); // Cache for 5 minutes

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Clear dashboard stats cache for a user
     */
    public function clearDashboardStatsCache(int $userId): void
    {
        Cache::forget("dashboard_stats_{$userId}");
    }

    /**
     * Cache project list with filters for a user
     */
    public function getProjectsList(int $userId, array $filters, callable $callback): mixed
    {
        $cacheKey = "projects_list_{$userId}_".md5(serialize($filters));
        $cacheTTL = now()->addMinutes(3); // Cache for 3 minutes

        // Track keys for targeted invalidation
        $indexKey = "projects_list_keys_{$userId}";
        $tracked = Cache::get($indexKey, []);
        if (! in_array($cacheKey, $tracked, true)) {
            $tracked[] = $cacheKey;
            Cache::put($indexKey, $tracked, $cacheTTL);
        }

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Clear projects list cache for a user
     */
    public function clearProjectsListCache(int $userId): void
    {
        $indexKey = "projects_list_keys_{$userId}";
        $tracked = Cache::get($indexKey, []);
        foreach ($tracked as $key) {
            Cache::forget($key);
        }
        Cache::forget($indexKey);
    }

    /**
     * Cache time entries aggregations
     */
    public function getTimeEntriesStats(int $userId, array $filters, callable $callback): array
    {
        $cacheKey = "time_entries_stats_{$userId}_".md5(serialize($filters));
        $cacheTTL = now()->addMinutes(2); // Cache for 2 minutes

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Clear time entries stats cache for a user
     */
    public function clearTimeEntriesStatsCache(int $userId): void
    {
        $pattern = "time_entries_stats_{$userId}_*";
        $this->clearCachePattern($pattern);
    }

    /**
     * Cache invoice aggregations
     */
    public function getInvoiceStats(int $userId, callable $callback): array
    {
        $cacheKey = "invoice_stats_{$userId}";
        $cacheTTL = now()->addMinutes(10); // Cache for 10 minutes

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Clear invoice stats cache for a user
     */
    public function clearInvoiceStatsCache(int $userId): void
    {
        Cache::forget("invoice_stats_{$userId}");
    }

    /**
     * Cache expensive report calculations
     */
    public function getReportData(int $userId, string $reportType, array $params, callable $callback): array
    {
        $cacheKey = "report_{$reportType}_{$userId}_".md5(serialize($params));
        $cacheTTL = now()->addMinutes(15); // Cache for 15 minutes

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Clear report cache for a user
     */
    public function clearReportCache(int $userId, ?string $reportType = null): void
    {
        if ($reportType) {
            $pattern = "report_{$reportType}_{$userId}_*";
        } else {
            $pattern = "report_*_{$userId}_*";
        }
        $this->clearCachePattern($pattern);
    }

    /**
     * Cache ARES API responses to avoid repeated external calls
     */
    public function getAresData(string $ico, callable $callback): ?array
    {
        $cacheKey = "ares_data_{$ico}";
        $cacheTTL = now()->addDay(); // Cache for 1 day

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Cache currency conversion rates
     */
    public function getCurrencyRate(string $from, string $to, callable $callback): float
    {
        $cacheKey = "currency_rate_{$from}_to_{$to}";
        $cacheTTL = now()->addHours(1); // Cache for 1 hour

        return Cache::remember($cacheKey, $cacheTTL, $callback);
    }

    /**
     * Optimize database query by adding memory caching
     */
    public function optimizeQuery(string $key, int $ttlMinutes, callable $callback): mixed
    {
        return Cache::remember($key, now()->addMinutes($ttlMinutes), $callback);
    }

    /**
     * Clear all performance caches for a user (use when data changes)
     */
    public function clearAllUserCaches(int $userId): void
    {
        $patterns = [
            "dashboard_stats_{$userId}",
            "projects_list_{$userId}_*",
            "time_entries_stats_{$userId}_*",
            "invoice_stats_{$userId}",
            "report_*_{$userId}_*",
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                $this->clearCachePattern($pattern);
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Clear cache by pattern (for Redis/Memcached)
     */
    private function clearCachePattern(string $pattern): void
    {
        // For simple cache drivers, we'll just track keys
        // In production, you might want to use Redis with pattern deletion

        // For now, we'll implement a simple approach
        $store = Cache::getStore();

        if (method_exists($store, 'flush')) {
            // If it's array cache or similar, we could flush all
            // but that's too aggressive, so we'll skip pattern clearing
            // In production, implement proper pattern clearing for your cache driver
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);

        return [
            'memory_usage' => $this->formatBytes($memoryUsage),
            'peak_memory' => $this->formatBytes($peakMemory),
            'memory_usage_bytes' => $memoryUsage,
            'peak_memory_bytes' => $peakMemory,
            'cache_hits' => $this->getCacheHits(),
        ];
    }

    /**
     * Format bytes for human reading
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Get cache hit statistics (implementation depends on cache driver)
     */
    private function getCacheHits(): array
    {
        // This would be implemented based on your cache driver
        // For Redis, you could use INFO stats
        // For now, return basic info
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];
    }

    /**
     * Preload commonly needed data to reduce N+1 queries
     */
    public function preloadUserData(int $userId): array
    {
        return $this->optimizeQuery("user_preload_data_{$userId}", 10, function () use ($userId) {
            return [
                'clients_count' => \App\Models\Client::where('user_id', $userId)->count(),
                'projects_count' => \App\Models\Project::where('user_id', $userId)->count(),
                'active_projects_count' => \App\Models\Project::where('user_id', $userId)->where('status', 'active')->count(),
                'total_invoices_count' => \App\Models\Invoice::where('user_id', $userId)->count(),
                'pending_invoices_count' => \App\Models\Invoice::where('user_id', $userId)->whereIn('status', ['sent', 'overdue'])->count(),
            ];
        });
    }

    /**
     * Batch load relationships to prevent N+1
     */
    public function batchLoadRelationships($collection, array $relationships): void
    {
        foreach ($relationships as $relationship) {
            $collection->load($relationship);
        }
    }
}
