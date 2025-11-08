<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Local Development Performance Optimization Service
 * Lokalde remote database yavaşlığını optimize eder
 */
class LocalOptimizationService
{
    /**
     * Agresif cache ile database query'lerini optimize et
     */
    public static function cacheQuery($key, $query, $ttl = null)
    {
        $ttl = $ttl ?? config('local-optimization.cache.default_ttl', 1800);
        
        return Cache::remember($key, $ttl, $query);
    }

    /**
     * Toplu eager loading ile N+1 problemini önle
     */
    public static function optimizeQuery($query, $relations = [])
    {
        $defaultRelations = config('local-optimization.default_eager_load', []);
        $allRelations = array_merge($defaultRelations, $relations);
        
        return $query->with($allRelations);
    }

    /**
     * Database connection pool optimize et
     */
    public static function optimizeDbConnection()
    {
        // Environment-based timeout optimization
        $timeout = app()->environment('local') ? 300 : 60;
        $memory = app()->environment('local') ? '2048M' : '512M';
        
        // PDO connection optimization
        Config::set('database.connections.mysql.options', [
            \PDO::ATTR_PERSISTENT => app()->environment('local'),
            \PDO::ATTR_TIMEOUT => $timeout,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        
        // Memory and execution time optimization
        ini_set('max_execution_time', $timeout);
        ini_set('memory_limit', $memory);
        
        // Connection pool optimization
        Config::set('database.connections.mysql.pool', [
            'min_connections' => 1,
            'max_connections' => app()->environment('local') ? 10 : 5,
        ]);
    }

    /**
     * Pagination optimize et
     */
    public static function optimizePagination($perPage = null)
    {
        $defaultPerPage = config('local-optimization.pagination.default_per_page', 20);
        $maxPerPage = config('local-optimization.pagination.max_per_page', 50);
        
        $perPage = $perPage ?? $defaultPerPage;
        
        return min($perPage, $maxPerPage);
    }

    /**
     * Cache'i agresif kullan
     */
    public static function aggressiveCache($key, $data, $ttl = null)
    {
        $ttl = $ttl ?? config('local-optimization.cache.long_ttl', 3600);
        
        Cache::put($key, $data, $ttl);
        
        return $data;
    }

    /**
     * Query debugging lokalde disable et
     */
    public static function disableQueryLogging()
    {
        if (app()->environment('local')) {
            DB::disableQueryLog();
        }
    }

    /**
     * Lokalde kullanılması gereken cache sürelerini döndür
     */
    public static function getCacheTtl($type = 'default')
    {
        return config("local-optimization.cache.{$type}_ttl", 1800);
    }

    /**
     * Memory usage optimize et
     */
    public static function optimizeMemory()
    {
        // Garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        // Memory limit artır
        ini_set('memory_limit', '512M');
    }

    /**
     * Batch query processing
     */
    public static function batchQueries(array $queries)
    {
        if (!config('local-optimization.query_batching', false)) {
            return array_map(fn($query) => $query(), $queries);
        }

        DB::transaction(function () use ($queries, &$results) {
            $results = array_map(fn($query) => $query(), $queries);
        });

        return $results ?? [];
    }
}
