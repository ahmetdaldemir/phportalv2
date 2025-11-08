<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\StockCard;
use App\Models\Transfer;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Seller;
use App\Models\Category;

class OptimizeSlowPages extends Command
{
    protected $signature = 'optimize:slow-pages 
                            {--clear : Clear existing cache first}
                            {--company-id=1 : Company ID to optimize for}';

    protected $description = 'Pre-cache slow queries and optimize problematic pages';

    public function handle()
    {
        $companyId = $this->option('company-id');
        $clear = $this->option('clear');

        if ($clear) {
            $this->info('🧹 Clearing existing cache...');
            Cache::flush();
        }

        $this->info('🚀 Starting slow page optimization...');
        $this->info("📊 Company ID: {$companyId}");
        $this->newLine();

        // Pre-cache critical data
        $this->cacheStaticData($companyId);
        $this->cacheStockCardData($companyId);
        $this->cacheTransferData($companyId);
        $this->cacheCategoryTree($companyId);
        
        $this->newLine();
        $this->info('✅ Slow page optimization completed!');
        $this->info('💡 Cached data will expire based on configured TTL values');

        return Command::SUCCESS;
    }

    private function cacheStaticData($companyId)
    {
        $this->info('📋 Caching static data...');

        // Cache brands (bypass global scopes for command)
        Cache::remember("brands_company_{$companyId}", 3600, function () use ($companyId) {
            return Brand::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        // Cache colors
        Cache::remember("colors_company_{$companyId}", 3600, function () use ($companyId) {
            return Color::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        // Cache sellers
        Cache::remember("sellers_company_{$companyId}", 1800, function () use ($companyId) {
            return Seller::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        $this->line('  ✓ Static data cached');
    }

    private function cacheStockCardData($companyId)
    {
        $this->info('📦 Caching StockCard data...');

        // Cache active stock cards with relationships
        Cache::remember("stockcards_active_company_{$companyId}", 1800, function () use ($companyId) {
            return StockCard::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('is_status', 1)
                ->with(['brand:id,name'])
                ->select('id', 'name', 'brand_id', 'category_id', 'sku')
                ->limit(200)
                ->get();
        });

        // Cache stock card counts by category
        Cache::remember("stockcard_counts_company_{$companyId}", 1800, function () use ($companyId) {
            return DB::table('stock_cards')
                ->where('company_id', $companyId)
                ->where('is_status', 1)
                ->selectRaw('category_id, count(*) as count')
                ->groupBy('category_id')
                ->get()
                ->keyBy('category_id');
        });

        $this->line('  ✓ StockCard data cached');
    }

    private function cacheTransferData($companyId)
    {
        $this->info('🚚 Caching Transfer data...');

        // Cache recent transfers
        Cache::remember("transfers_recent_company_{$companyId}", 1800, function () use ($companyId) {
            return Transfer::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->with(['mainSeller:id,name', 'deliverySeller:id,name'])
                ->select('id', 'main_seller_id', 'delivery_seller_id', 'is_status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        });

        // Cache transfer statistics
        Cache::remember("transfer_stats_company_{$companyId}", 3600, function () use ($companyId) {
            return [
                'total' => Transfer::withoutGlobalScopes()->where('company_id', $companyId)->count(),
                'pending' => Transfer::withoutGlobalScopes()->where('company_id', $companyId)->where('is_status', 1)->count(),
                'completed' => Transfer::withoutGlobalScopes()->where('company_id', $companyId)->where('is_status', 3)->count(),
            ];
        });

        $this->line('  ✓ Transfer data cached');
    }

    private function cacheCategoryTree($companyId)
    {
        $this->info('🌳 Caching category tree...');

        // Cache category hierarchy
        Cache::remember("category_tree_company_{$companyId}", 3600, function () use ($companyId) {
            return DB::select("WITH RECURSIVE category_path (id, name, parent_id, path) AS
            (
              SELECT id, name, parent_id, name as path
              FROM categories
              WHERE parent_id = 0 and deleted_at is null and company_id = ?
              UNION ALL
              SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
              FROM category_path cp JOIN categories k
              ON cp.id = k.parent_id 
              WHERE deleted_at is null and company_id = ?
            )
            SELECT * FROM category_path ORDER BY path", [$companyId, $companyId]);
        });

        $this->line('  ✓ Category tree cached');
    }
}
