<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only monitor in local environment
        if (!app()->environment('local')) {
            return $next($request);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Enable query logging for this request
        DB::enableQueryLog();
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $queries = DB::getQueryLog();
        
        // Calculate metrics
        $executionTime = round(($endTime - $startTime) * 1000, 2); // ms
        $memoryUsage = round(($endMemory - $startMemory) / 1024 / 1024, 2); // MB
        $queryCount = count($queries);
        $totalQueryTime = collect($queries)->sum('time');
        
        // Identify slow queries (>1000ms)
        $slowQueries = collect($queries)->filter(function ($query) {
            return $query['time'] >= 1000;
        });
        
        // Log performance metrics
        $performanceData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'execution_time_ms' => $executionTime,
            'memory_usage_mb' => $memoryUsage,
            'query_count' => $queryCount,
            'total_query_time_ms' => round($totalQueryTime, 2),
            'slow_queries_count' => $slowQueries->count(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ];
        
        // Log slow requests (>2000ms) or requests with many queries (>20)
        if ($executionTime > 2000 || $queryCount > 20 || $slowQueries->count() > 0) {
            Log::channel('hourly')->warning('Slow Request Detected', [
                'performance' => $performanceData,
                'slow_queries' => $slowQueries->map(function ($query) {
                    return [
                        'sql' => $query['query'],
                        'time_ms' => $query['time'],
                        'bindings' => $query['bindings']
                    ];
                })->toArray()
            ]);
        }
        
        // Add performance headers for debugging
        if (app()->environment('local')) {
            $response->headers->set('X-Execution-Time', $executionTime . 'ms');
            $response->headers->set('X-Memory-Usage', $memoryUsage . 'MB');
            $response->headers->set('X-Query-Count', $queryCount);
            $response->headers->set('X-Total-Query-Time', round($totalQueryTime, 2) . 'ms');
            
            if ($slowQueries->count() > 0) {
                $response->headers->set('X-Slow-Queries', $slowQueries->count());
            }
        }
        
        DB::disableQueryLog();
        
        return $response;
    }
}