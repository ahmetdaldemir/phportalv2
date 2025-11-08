<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryAnalyzer extends Command
{
    protected $signature = 'query:analyze 
                            {--threshold=1000 : Slow query threshold in milliseconds}
                            {--show-queries : Show actual SQL queries}
                            {--export : Export results to file}';

    protected $description = 'Analyze database queries and find performance bottlenecks';

    public function handle()
    {
        $threshold = $this->option('threshold');
        $showQueries = $this->option('show-queries');
        $export = $this->option('export');

        $this->info('🔍 Starting Query Analysis...');
        $this->info("📊 Slow query threshold: {$threshold}ms");
        $this->newLine();

        // Enable query logging
        DB::enableQueryLog();
        
        // Analyze different parts of the application
        $results = [
            'StockCard Queries' => $this->analyzeStockCardQueries(),
            'Transfer Queries' => $this->analyzeTransferQueries(),
            'Home Dashboard' => $this->analyzeHomeQueries(),
            'Phone Queries' => $this->analyzePhoneQueries(),
        ];

        $allQueries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->displayResults($results, $allQueries, $threshold, $showQueries);

        if ($export) {
            $this->exportResults($results, $allQueries);
        }

        return Command::SUCCESS;
    }

    private function analyzeStockCardQueries()
    {
        $startTime = microtime(true);
        
        try {
            // Simulate common StockCard operations
            \App\Models\StockCard::with(['brand', 'category', 'color'])
                ->where('is_status', 1)
                ->limit(10)
                ->get();

            \App\Models\StockCard::where('company_id', 1)
                ->select('id', 'name', 'brand_id', 'category_id')
                ->limit(5)
                ->get();

            $endTime = microtime(true);
            return [
                'execution_time' => round(($endTime - $startTime) * 1000, 2),
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'execution_time' => 0,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function analyzeTransferQueries()
    {
        $startTime = microtime(true);
        
        try {
            \App\Models\Transfer::with(['mainSeller', 'deliverySeller'])
                ->where('company_id', 1)
                ->select('id', 'main_seller_id', 'delivery_seller_id', 'is_status')
                ->limit(10)
                ->get();

            $endTime = microtime(true);
            return [
                'execution_time' => round(($endTime - $startTime) * 1000, 2),
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'execution_time' => 0,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function analyzeHomeQueries()
    {
        $startTime = microtime(true);
        
        try {
            \App\Models\StockCard::where('is_status', 1)
                ->select('id', 'name', 'brand_id', 'category_id')
                ->limit(50)
                ->get();

            \App\Models\Color::select('id', 'name')->limit(20)->get();
            \App\Models\Brand::select('id', 'name')->limit(20)->get();

            $endTime = microtime(true);
            return [
                'execution_time' => round(($endTime - $startTime) * 1000, 2),
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'execution_time' => 0,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function analyzePhoneQueries()
    {
        $startTime = microtime(true);
        
        try {
            \App\Models\Phone::with(['brand:id,name', 'color:id,name'])
                ->where('company_id', 1)
                ->select('id', 'imei', 'brand_id', 'color_id', 'status')
                ->limit(10)
                ->get();

            $endTime = microtime(true);
            return [
                'execution_time' => round(($endTime - $startTime) * 1000, 2),
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            return [
                'execution_time' => 0,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    private function displayResults($results, $queries, $threshold, $showQueries)
    {
        // Display section results
        $this->info('📈 SECTION ANALYSIS RESULTS:');
        $this->table(
            ['Section', 'Execution Time (ms)', 'Status'],
            collect($results)->map(function ($result, $section) {
                return [
                    $section,
                    $result['execution_time'] . 'ms',
                    $result['status'] === 'success' ? '✅ Success' : '❌ Error'
                ];
            })->toArray()
        );

        // Analyze individual queries
        $slowQueries = collect($queries)->filter(function ($query) use ($threshold) {
            return $query['time'] >= $threshold;
        });

        $this->newLine();
        $this->info('🐌 SLOW QUERIES (>= ' . $threshold . 'ms):');
        
        if ($slowQueries->isEmpty()) {
            $this->info('✅ No slow queries found!');
        } else {
            $slowQueries->each(function ($query, $index) use ($showQueries) {
                $this->warn("Query #" . ($index + 1) . ": {$query['time']}ms");
                if ($showQueries) {
                    $this->line("SQL: " . $query['query']);
                    $this->line("Bindings: " . json_encode($query['bindings']));
                    $this->newLine();
                }
            });
        }

        // Query statistics
        $totalQueries = count($queries);
        $totalTime = collect($queries)->sum('time');
        $avgTime = $totalQueries > 0 ? round($totalTime / $totalQueries, 2) : 0;

        $this->newLine();
        $this->info('📊 QUERY STATISTICS:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Queries', $totalQueries],
                ['Total Execution Time', round($totalTime, 2) . 'ms'],
                ['Average Query Time', $avgTime . 'ms'],
                ['Slow Queries Count', $slowQueries->count()],
                ['Slow Queries %', $totalQueries > 0 ? round(($slowQueries->count() / $totalQueries) * 100, 2) . '%' : '0%']
            ]
        );

        // Recommendations
        $this->newLine();
        $this->info('💡 RECOMMENDATIONS:');
        
        if ($slowQueries->count() > 0) {
            $this->warn('• Consider optimizing slow queries with indexes');
            $this->warn('• Use eager loading to reduce N+1 queries');
            $this->warn('• Implement query caching for repeated queries');
        }
        
        if ($totalQueries > 50) {
            $this->warn('• High query count detected - consider query optimization');
        }
        
        if ($avgTime > 100) {
            $this->warn('• Average query time is high - review database performance');
        }

        if ($slowQueries->isEmpty() && $totalQueries < 20 && $avgTime < 50) {
            $this->info('✅ Great! Your queries are well optimized');
        }
    }

    private function exportResults($results, $queries)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = storage_path("logs/query-analysis-{$timestamp}.json");

        $exportData = [
            'timestamp' => now()->toISOString(),
            'analysis_results' => $results,
            'query_details' => $queries,
            'summary' => [
                'total_queries' => count($queries),
                'total_time' => collect($queries)->sum('time'),
                'avg_time' => count($queries) > 0 ? collect($queries)->avg('time') : 0,
            ]
        ];

        file_put_contents($filename, json_encode($exportData, JSON_PRETTY_PRINT));
        
        $this->info("📁 Results exported to: {$filename}");
    }
}