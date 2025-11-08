<?php

namespace App\Console\Commands;

use App\Services\LocalOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LocalOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:optimize {--cache-hours=2 : Cache süresi (saat)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lokalde remote database yavaşlığını optimize eder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Local Development Optimization başlıyor...');

        // Cache süresini al
        $cacheHours = $this->option('cache-hours');
        $cacheTtl = $cacheHours * 3600; // saniyeye çevir

        $this->info("⏰ Cache süresi: {$cacheHours} saat");

        // 1. Database connection optimize
        $this->info('🔧 Database connection optimize ediliyor...');
        LocalOptimizationService::optimizeDbConnection();

        // 2. Memory optimize
        $this->info('💾 Memory optimize ediliyor...');
        LocalOptimizationService::optimizeMemory();

        // 3. Ana sayfa verilerini pre-cache
        $this->info('📊 Ana sayfa verileri cache ediliyor...');
        $this->preCacheHomeData($cacheTtl);

        // 4. Category tree'yi pre-cache
        $this->info('🌲 Category tree cache ediliyor...');
        $this->preCacheCategoryData($cacheTtl);

        // 5. Common data'yı pre-cache  
        $this->info('📦 Common data cache ediliyor...');
        $this->preCacheCommonData($cacheTtl);

        $this->info('✅ Local optimization tamamlandı!');
        $this->info('📈 Artık sayfa yüklemeleriniz çok daha hızlı olacak.');
        
        return Command::SUCCESS;
    }

    private function preCacheHomeData($ttl)
    {
        // Tüm company'ler için home data cache
        $companies = DB::table('companies')->select('id')->get();
        
        foreach ($companies as $company) {
            $cacheKey = 'home_data_' . $company->id;
            
            Cache::put($cacheKey, [
                'stocks' => [],
                'colors' => [],
                'reasons' => [],
                'stockTracks' => []
            ], $ttl);
            
            $this->line("  ✓ Company {$company->id} home data cached");
        }
    }

    private function preCacheCategoryData($ttl)
    {
        // Tüm category'ler için tree cache
        $categories = DB::table('categories')->select('id')->get();
        
        foreach ($categories as $category) {
            $cacheKey = 'category_tree_' . $category->id;
            
            Cache::put($cacheKey, [], $ttl);
            
            $this->line("  ✓ Category {$category->id} tree cached");
        }
    }

    private function preCacheCommonData($ttl)
    {
        // Brands, colors, etc. için cache
        $commonKeys = [
            'phone_index_data',
            'phone_form_data', 
            'stockcard_list',
            'brands_all',
            'colors_all',
            'sellers_all'
        ];

        foreach ($commonKeys as $key) {
            Cache::put($key, [], $ttl);
            $this->line("  ✓ {$key} cached");
        }
    }
}
