<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StockTurnoverAIService
{
    /**
     * Stok devir hızı AI analizi
     * Makine öğrenimi benzeri algoritmalarla stok performansını değerlendirir
     */
    
    const ANALYSIS_CACHE_KEY = 'stock_turnover_ai_analysis';
    const CACHE_DURATION = 3600; // 1 saat
    
    /**
     * Ana analiz fonksiyonu
     */
    public function analyzeStockPerformance($companyId, $sellerId = null)
    {
        $cacheKey = $this->getCacheKey($companyId, $sellerId);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($companyId, $sellerId) {
            $stockData = $this->getStockData($companyId, $sellerId);
            
            return [
                'summary' => $this->generateSummary($stockData),
                'insights' => $this->generateInsights($stockData),
                'predictions' => $this->generatePredictions($stockData),
                'recommendations' => $this->generateRecommendations($stockData),
                'anomalies' => $this->detectAnomalies($stockData),
                'trends' => $this->analyzeTrends($stockData),
                'seasonality' => $this->analyzeSeasonality($companyId, $sellerId),
                'pricing' => $this->analyzePricingOpportunities($stockData),
                'fast_movers' => $this->getFastMovers($stockData),
                'slow_movers' => $this->getSlowMovers($stockData),
                'warehouse_performance' => $this->analyzeWarehousePerformance($companyId, $sellerId),
                'score' => $this->calculateHealthScore($stockData),
                'generated_at' => Carbon::now()->toDateTimeString()
            ];
        });
    }
    
    /**
     * Stok verilerini topla
     */
    private function getStockData($companyId, $sellerId = null)
    {
        $query = "
            SELECT 
                sc.id,
                sc.name as stock_name,
                c.name as category,
                sel.name as seller_name,
                COUNT(s.id) as total_sold,
                AVG(DATEDIFF(s.created_at, scm.created_at)) as avg_days_to_sell,
                MIN(DATEDIFF(s.created_at, scm.created_at)) as min_days_to_sell,
                MAX(DATEDIFF(s.created_at, scm.created_at)) as max_days_to_sell,
                STDDEV(DATEDIFF(s.created_at, scm.created_at)) as stddev_days,
                SUM(s.customer_price) as total_revenue,
                AVG(s.customer_price) as avg_price,
                SUM(CASE WHEN scm_current.quantity > 0 AND scm_current.type = 1 THEN scm_current.quantity ELSE 0 END) as current_stock,
                COUNT(DISTINCT DATE(s.created_at)) as active_days,
                COUNT(CASE WHEN s.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7_days_sales,
                COUNT(CASE WHEN s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30_days_sales
            FROM sales s
            INNER JOIN stock_card_movements scm ON s.stock_card_movement_id = scm.id
            INNER JOIN stock_cards sc ON scm.stock_card_id = sc.id
            LEFT JOIN categories c ON sc.category_id = c.id
            LEFT JOIN sellers sel ON s.seller_id = sel.id
            LEFT JOIN stock_card_movements scm_current ON scm_current.stock_card_id = sc.id 
                AND scm_current.company_id = ? 
                AND scm_current.type = 1
            WHERE s.company_id = ?
                AND s.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                AND scm.type = 1
        ";
        
        $params = [$companyId, $companyId];
        
        if ($sellerId) {
            $query .= " AND s.seller_id = ?";
            $params[] = $sellerId;
        }
        
        $query .= "
            GROUP BY sc.id, sc.name, c.name, sel.name
            HAVING avg_days_to_sell IS NOT NULL
        ";
        
        return collect(DB::select($query, $params));
    }
    
    /**
     * Özet bilgiler oluştur
     */
    private function generateSummary($stockData)
    {
        if ($stockData->isEmpty()) {
            return [
                'total_products' => 0,
                'avg_turnover_rate' => 0,
                'total_revenue' => 0,
                'health_status' => 'unknown'
            ];
        }
        
        $avgTurnover = $stockData->avg('avg_days_to_sell');
        
        return [
            'total_products' => $stockData->count(),
            'avg_turnover_rate' => round($avgTurnover, 2),
            'total_revenue' => $stockData->sum('total_revenue'),
            'total_sales' => $stockData->sum('total_sold'),
            'avg_stock_level' => round($stockData->avg('current_stock'), 0),
            'health_status' => $this->getHealthStatus($avgTurnover)
        ];
    }
    
    /**
     * AI Insights - Akıllı içgörüler
     */
    private function generateInsights($stockData)
    {
        $insights = [];
        
        // Hızlı hareket eden ürünler
        $fastMovers = $stockData->filter(function ($item) {
            return $item->avg_days_to_sell <= 7 && $item->total_sold >= 5;
        })->sortByDesc('total_sold')->take(5);
        
        if ($fastMovers->isNotEmpty()) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'bx-trending-up',
                'title' => 'Yüksek Performans',
                'message' => $fastMovers->count() . ' ürün çok hızlı satılıyor (7 gün altı)',
                'products' => $fastMovers->pluck('stock_name')->toArray(),
                'action' => 'Bu ürünlerin stoklarını artırın'
            ];
        }
        
        // Yavaş hareket eden ürünler
        $slowMovers = $stockData->filter(function ($item) {
            return $item->avg_days_to_sell > 30 && $item->current_stock > 5;
        })->sortByDesc('avg_days_to_sell')->take(5);
        
        if ($slowMovers->isNotEmpty()) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'bx-error',
                'title' => 'Yavaş Hareket',
                'message' => $slowMovers->count() . ' ürün yavaş satılıyor (30+ gün)',
                'products' => $slowMovers->pluck('stock_name')->toArray(),
                'action' => 'İndirim veya promosyon düşünün'
            ];
        }
        
        // Stok tükenmesi riski
        $stockOutRisk = $stockData->filter(function ($item) {
            $dailySalesRate = $item->total_sold / 90; // 90 günlük ortalama
            $daysUntilStockOut = $item->current_stock > 0 ? $item->current_stock / max($dailySalesRate, 0.1) : 0;
            return $daysUntilStockOut > 0 && $daysUntilStockOut <= 7;
        })->sortBy(function ($item) {
            $dailySalesRate = $item->total_sold / 90;
            return $item->current_stock / max($dailySalesRate, 0.1);
        })->take(5);
        
        if ($stockOutRisk->isNotEmpty()) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'bx-package',
                'title' => 'Stok Tükenme Riski',
                'message' => $stockOutRisk->count() . ' ürün 7 gün içinde tükenebilir',
                'products' => $stockOutRisk->pluck('stock_name')->toArray(),
                'action' => 'Acil sipariş verin'
            ];
        }
        
        // Aşırı stok
        $overstock = $stockData->filter(function ($item) {
            $dailySalesRate = $item->total_sold / 90;
            $daysUntilStockOut = $item->current_stock > 0 ? $item->current_stock / max($dailySalesRate, 0.1) : 999;
            return $daysUntilStockOut > 60;
        })->sortByDesc('current_stock')->take(5);
        
        if ($overstock->isNotEmpty()) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'bx-box',
                'title' => 'Aşırı Stok',
                'message' => $overstock->count() . ' üründe fazla stok var (60+ gün)',
                'products' => $overstock->pluck('stock_name')->toArray(),
                'action' => 'Stok seviyelerini azaltın'
            ];
        }
        
        return $insights;
    }
    
    /**
     * Tahminler üret (Basit ML algoritması)
     */
    private function generatePredictions($stockData)
    {
        $predictions = [];
        
        foreach ($stockData as $item) {
            // Son 7 gün vs son 30 gün trend analizi
            $recentTrend = $item->last_7_days_sales / max($item->last_30_days_sales / 4, 1);
            
            $dailySalesRate = $item->total_sold / 90;
            $projectedNextWeek = round($dailySalesRate * 7 * $recentTrend);
            $projectedNextMonth = round($dailySalesRate * 30 * $recentTrend);
            
            $daysUntilStockOut = $item->current_stock > 0 ? 
                round($item->current_stock / max($dailySalesRate * $recentTrend, 0.1)) : 999;
            
            $predictions[] = [
                'stock_name' => $item->stock_name,
                'current_stock' => $item->current_stock,
                'daily_sales_rate' => round($dailySalesRate, 2),
                'trend_factor' => round($recentTrend, 2),
                'projected_next_week_sales' => $projectedNextWeek,
                'projected_next_month_sales' => $projectedNextMonth,
                'days_until_stockout' => min($daysUntilStockOut, 999),
                'recommendation' => $this->getStockRecommendation($daysUntilStockOut, $item->current_stock)
            ];
        }
        
        return collect($predictions)->sortBy('days_until_stockout')->take(20)->values()->toArray();
    }
    
    /**
     * Öneriler üret
     */
    private function generateRecommendations($stockData)
    {
        $recommendations = [];
        
        $avgTurnover = $stockData->avg('avg_days_to_sell');
        
        if ($avgTurnover > 20) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Genel Devir Hızını İyileştirin',
                'description' => 'Ortalama devir hızı ' . round($avgTurnover, 1) . ' gün. İdeal 14 gün altı olmalı.',
                'actions' => [
                    'Yavaş hareket eden ürünlerde indirim yapın',
                    'Pazarlama kampanyaları başlatın',
                    'Stok seviyelerini optimize edin'
                ]
            ];
        }
        
        // Kategori bazlı analiz
        $categoryPerformance = $stockData->groupBy('category')->map(function ($items) {
            return [
                'count' => $items->count(),
                'avg_turnover' => $items->avg('avg_days_to_sell'),
                'total_revenue' => $items->sum('total_revenue')
            ];
        })->sortByDesc('total_revenue');
        
        if ($categoryPerformance->isNotEmpty()) {
            $topCategory = $categoryPerformance->first();
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Kategori Stratejisi',
                'description' => 'En çok gelir getiren kategoriye odaklanın',
                'actions' => [
                    'Bu kategorideki ürün çeşitliliğini artırın',
                    'Stok seviyelerini bu kategoriye göre ayarlayın'
                ]
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Anomali tespiti
     */
    private function detectAnomalies($stockData)
    {
        $anomalies = [];
        
        $avgTurnover = $stockData->avg('avg_days_to_sell');
        $stdDev = $stockData->map(function ($item) use ($avgTurnover) {
            return pow($item->avg_days_to_sell - $avgTurnover, 2);
        })->avg();
        $stdDev = sqrt($stdDev);
        
        // Z-score ile anomali tespiti
        foreach ($stockData as $item) {
            $zScore = ($item->avg_days_to_sell - $avgTurnover) / max($stdDev, 1);
            
            if (abs($zScore) > 2) { // 2 standart sapma dışı
                $anomalies[] = [
                    'stock_name' => $item->stock_name,
                    'turnover_rate' => round($item->avg_days_to_sell, 1),
                    'z_score' => round($zScore, 2),
                    'type' => $zScore > 0 ? 'Çok Yavaş' : 'Çok Hızlı',
                    'severity' => abs($zScore) > 3 ? 'critical' : 'warning'
                ];
            }
        }
        
        return $anomalies;
    }
    
    /**
     * Trend analizi
     */
    private function analyzeTrends($stockData)
    {
        return [
            'velocity' => $this->calculateVelocityTrend($stockData),
            'revenue' => $this->calculateRevenueTrend($stockData),
            'efficiency' => $this->calculateEfficiencyTrend($stockData)
        ];
    }
    
    /**
     * Sağlık skoru hesapla (0-100)
     */
    private function calculateHealthScore($stockData)
    {
        if ($stockData->isEmpty()) {
            return 0;
        }
        
        $score = 100;
        
        // Ortalama devir hızı penaltı
        $avgTurnover = $stockData->avg('avg_days_to_sell');
        if ($avgTurnover > 30) {
            $score -= 30;
        } elseif ($avgTurnover > 20) {
            $score -= 20;
        } elseif ($avgTurnover > 14) {
            $score -= 10;
        }
        
        // Stok tükenme riski
        $stockOutRisk = $stockData->filter(function ($item) {
            $dailySalesRate = $item->total_sold / 90;
            $daysUntilStockOut = $item->current_stock > 0 ? $item->current_stock / max($dailySalesRate, 0.1) : 999;
            return $daysUntilStockOut <= 7;
        })->count();
        
        $score -= min($stockOutRisk * 5, 20);
        
        // Yavaş hareket eden ürünler
        $slowMovers = $stockData->filter(function ($item) {
            return $item->avg_days_to_sell > 45;
        })->count();
        
        $score -= min($slowMovers * 3, 15);
        
        return max(0, $score);
    }
    
    /**
     * Yardımcı metodlar
     */
    private function getHealthStatus($avgTurnover)
    {
        if ($avgTurnover <= 7) return 'excellent';
        if ($avgTurnover <= 14) return 'good';
        if ($avgTurnover <= 30) return 'fair';
        return 'poor';
    }
    
    private function getStockRecommendation($daysUntilStockOut, $currentStock)
    {
        if ($currentStock == 0) return 'Stok Yok - Acil Sipariş';
        if ($daysUntilStockOut <= 3) return 'Kritik - Hemen Sipariş';
        if ($daysUntilStockOut <= 7) return 'Düşük - Sipariş Planlayın';
        if ($daysUntilStockOut <= 14) return 'Normal - İzleyin';
        if ($daysUntilStockOut <= 30) return 'İyi - Rutin Kontrol';
        if ($daysUntilStockOut > 60) return 'Fazla - Stok Azaltın';
        return 'Optimal';
    }
    
    private function getCacheKey($companyId, $sellerId)
    {
        return self::ANALYSIS_CACHE_KEY . "_{$companyId}_" . ($sellerId ?? 'all');
    }
    
    /**
     * Satış hızı trendi - Son 7 gün vs önceki 7 gün karşılaştırması
     */
    private function calculateVelocityTrend($stockData)
    {
        if ($stockData->isEmpty()) {
            return ['direction' => 'stable', 'strength' => 0, 'change_percent' => 0];
        }
        
        $recentSales = $stockData->sum('last_7_days_sales');
        $previousSales = $stockData->map(function($item) {
            // Son 30 günden son 7 günü çıkar ve 7 güne böl
            return max(0, ($item->last_30_days_sales - $item->last_7_days_sales) / 23 * 7);
        })->sum();
        
        if ($previousSales == 0) {
            return ['direction' => 'stable', 'strength' => 0, 'change_percent' => 0];
        }
        
        $changePercent = (($recentSales - $previousSales) / $previousSales) * 100;
        $direction = $changePercent > 5 ? 'up' : ($changePercent < -5 ? 'down' : 'stable');
        $strength = min(abs($changePercent) / 100, 1); // 0-1 arası normalize
        
        return [
            'direction' => $direction,
            'strength' => round($strength, 2),
            'change_percent' => round($changePercent, 1),
            'recent_sales' => $recentSales,
            'previous_sales' => round($previousSales, 0)
        ];
    }
    
    /**
     * Gelir trendi - Son hafta vs önceki haftalar
     */
    private function calculateRevenueTrend($stockData)
    {
        if ($stockData->isEmpty()) {
            return ['direction' => 'stable', 'strength' => 0, 'change_percent' => 0];
        }
        
        // Son 7 günün ortalama geliri
        $recentAvgRevenue = $stockData->map(function($item) {
            return ($item->last_7_days_sales > 0) ? ($item->total_revenue / $item->total_sold * $item->last_7_days_sales) : 0;
        })->sum() / 7;
        
        // Önceki 23 günün ortalama günlük geliri
        $previousAvgRevenue = $stockData->map(function($item) {
            $previousSales = max(0, $item->last_30_days_sales - $item->last_7_days_sales);
            return ($previousSales > 0) ? ($item->total_revenue / $item->total_sold * $previousSales) : 0;
        })->sum() / 23;
        
        if ($previousAvgRevenue == 0) {
            return ['direction' => 'stable', 'strength' => 0, 'change_percent' => 0];
        }
        
        $changePercent = (($recentAvgRevenue - $previousAvgRevenue) / $previousAvgRevenue) * 100;
        $direction = $changePercent > 5 ? 'up' : ($changePercent < -5 ? 'down' : 'stable');
        $strength = min(abs($changePercent) / 100, 1);
        
        return [
            'direction' => $direction,
            'strength' => round($strength, 2),
            'change_percent' => round($changePercent, 1),
            'recent_daily_revenue' => round($recentAvgRevenue, 2),
            'previous_daily_revenue' => round($previousAvgRevenue, 2)
        ];
    }
    
    /**
     * Verimlilik trendi - Devir hızı iyileşmesi
     */
    private function calculateEfficiencyTrend($stockData)
    {
        if ($stockData->isEmpty()) {
            return ['direction' => 'stable', 'strength' => 0, 'change_percent' => 0];
        }
        
        // Son 7 günün ortalama devir hızı
        $recentTurnover = $stockData->filter(function($item) {
            return $item->last_7_days_sales > 0;
        })->avg('avg_days_to_sell') ?? 0;
        
        // Genel ortalama devir hızı (90 gün)
        $overallTurnover = $stockData->avg('avg_days_to_sell') ?? 0;
        
        if ($overallTurnover == 0) {
            return ['direction' => 'stable', 'strength' => 0, 'change_percent' => 0];
        }
        
        // Devir hızı düşüyorsa (daha hızlı satıyorsa) pozitif trend
        $changePercent = (($overallTurnover - $recentTurnover) / $overallTurnover) * 100;
        $direction = $changePercent > 5 ? 'improving' : ($changePercent < -5 ? 'declining' : 'stable');
        $strength = min(abs($changePercent) / 100, 1);
        
        return [
            'direction' => $direction,
            'strength' => round($strength, 2),
            'change_percent' => round($changePercent, 1),
            'recent_avg_days' => round($recentTurnover, 1),
            'overall_avg_days' => round($overallTurnover, 1)
        ];
    }
    
    /**
     * Sezonalite Analizi - Mevsimsel satış paternleri
     */
    private function analyzeSeasonality($companyId, $sellerId = null)
    {
        try {
            // Son 12 ayın aylık satış verilerini al
            $query = "
                SELECT 
                    MONTH(s.created_at) as month,
                    YEAR(s.created_at) as year,
                    COUNT(s.id) as total_sales,
                    SUM(s.customer_price) as total_revenue,
                    AVG(s.customer_price) as avg_price
                FROM sales s
                WHERE s.company_id = ?
                    AND s.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            ";
            
            $params = [$companyId];
            
            if ($sellerId) {
                $query .= " AND s.seller_id = ?";
                $params[] = $sellerId;
            }
            
            $query .= " GROUP BY YEAR(s.created_at), MONTH(s.created_at) ORDER BY year DESC, month DESC";
            
            $monthlyData = collect(DB::select($query, $params));
            
            if ($monthlyData->isEmpty()) {
                return [
                    'has_data' => false,
                    'message' => 'Sezonalite analizi için yeterli veri yok'
                ];
            }
            
            // Aylık ortalamalar
            $avgSales = $monthlyData->avg('total_sales');
            $avgRevenue = $monthlyData->avg('total_revenue');
            
            // En yüksek ve en düşük aylar
            $bestMonth = $monthlyData->sortByDesc('total_revenue')->first();
            $worstMonth = $monthlyData->sortBy('total_revenue')->first();
            
            // Mevsimsel paternler (Türkiye için)
            $seasons = [
                'winter' => [12, 1, 2],  // Kış
                'spring' => [3, 4, 5],   // İlkbahar
                'summer' => [6, 7, 8],   // Yaz
                'autumn' => [9, 10, 11]  // Sonbahar
            ];
            
            $seasonalPerformance = [];
            foreach ($seasons as $season => $months) {
                $seasonData = $monthlyData->whereIn('month', $months);
                if ($seasonData->isNotEmpty()) {
                    $seasonalPerformance[$season] = [
                        'avg_sales' => round($seasonData->avg('total_sales'), 0),
                        'avg_revenue' => round($seasonData->avg('total_revenue'), 2),
                        'performance_vs_avg' => $avgSales > 0 ? round((($seasonData->avg('total_sales') - $avgSales) / $avgSales) * 100, 1) : 0
                    ];
                }
            }
            
            // Trend yönü
            $recentMonths = $monthlyData->take(3)->avg('total_sales');
            $olderMonths = $monthlyData->skip(3)->take(3)->avg('total_sales');
            $trend = $olderMonths > 0 ? (($recentMonths - $olderMonths) / $olderMonths) * 100 : 0;
            
            return [
                'has_data' => true,
                'monthly_data' => $monthlyData->values()->toArray(),
                'best_month' => [
                    'month' => $bestMonth->month,
                    'year' => $bestMonth->year,
                    'sales' => $bestMonth->total_sales,
                    'revenue' => $bestMonth->total_revenue
                ],
                'worst_month' => [
                    'month' => $worstMonth->month,
                    'year' => $worstMonth->year,
                    'sales' => $worstMonth->total_sales,
                    'revenue' => $worstMonth->total_revenue
                ],
                'seasonal_performance' => $seasonalPerformance,
                'trend' => [
                    'direction' => $trend > 5 ? 'improving' : ($trend < -5 ? 'declining' : 'stable'),
                    'change_percent' => round($trend, 1)
                ],
                'insights' => $this->generateSeasonalInsights($seasonalPerformance, $bestMonth, $worstMonth)
            ];
            
        } catch (\Exception $e) {
            Log::error('Sezonalite analizi hatası: ' . $e->getMessage());
            return ['has_data' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Sezonalite içgörüleri
     */
    private function generateSeasonalInsights($seasonalPerformance, $bestMonth, $worstMonth)
    {
        $insights = [];
        
        // En iyi mevsim
        $bestSeason = collect($seasonalPerformance)->sortByDesc('avg_revenue')->first();
        if ($bestSeason) {
            $seasonNames = [
                'winter' => 'Kış',
                'spring' => 'İlkbahar',
                'summer' => 'Yaz',
                'autumn' => 'Sonbahar'
            ];
            
            $bestSeasonName = $seasonNames[collect($seasonalPerformance)->sortByDesc('avg_revenue')->keys()->first()] ?? 'Bilinmeyen';
            
            $insights[] = "En iyi performans {$bestSeasonName} mevsiminde gerçekleşiyor";
        }
        
        // Ay bazında öneriler
        $monthNames = [
            1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
            5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
            9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
        ];
        
        $insights[] = "{$monthNames[$bestMonth->month]} ayı en yüksek satış ayınız";
        $insights[] = "{$monthNames[$worstMonth->month]} ayında özel kampanyalar planlayın";
        
        return $insights;
    }
    
    /**
     * Fiyat Optimizasyonu Analizi
     */
    private function analyzePricingOpportunities($stockData)
    {
        if ($stockData->isEmpty()) {
            return ['has_data' => false];
        }
        
        $opportunities = [];
        
        foreach ($stockData as $item) {
            $dailySalesRate = $item->total_sold / 90;
            $daysUntilStockOut = $item->current_stock > 0 ? 
                $item->current_stock / max($dailySalesRate, 0.1) : 999;
            
            $suggestion = null;
            $reason = null;
            $priority = 'low';
            
            // Hızlı satan + yüksek stok = fiyat artırma fırsatı
            if ($item->avg_days_to_sell <= 10 && $daysUntilStockOut > 30) {
                $suggestion = 'price_increase';
                $suggestedChange = 10; // %10 artış
                $reason = 'Yüksek talep, bol stok';
                $priority = 'high';
            }
            // Yavaş satan + fazla stok = indirim gerekli
            elseif ($item->avg_days_to_sell > 30 && $item->current_stock > 10) {
                $suggestion = 'price_decrease';
                $suggestedChange = -15; // %15 indirim
                $reason = 'Yavaş hareket, fazla stok';
                $priority = 'high';
            }
            // Stok tükenme riski = fiyat artırma veya acil sipariş
            elseif ($daysUntilStockOut <= 7 && $item->avg_days_to_sell <= 14) {
                $suggestion = 'price_increase';
                $suggestedChange = 5; // %5 artış
                $reason = 'Tükenme riski, yüksek talep';
                $priority = 'medium';
            }
            // Orta hızda satan + düşük stok = stabil fiyat
            elseif ($item->avg_days_to_sell > 10 && $item->avg_days_to_sell <= 20 && $daysUntilStockOut > 14) {
                $suggestion = 'maintain';
                $suggestedChange = 0;
                $reason = 'Optimal performans';
                $priority = 'low';
            }
            
            if ($suggestion) {
                $opportunities[] = [
                    'stock_name' => $item->stock_name,
                    'current_price' => round($item->avg_price, 2),
                    'suggestion' => $suggestion,
                    'suggested_change_percent' => $suggestedChange,
                    'suggested_price' => round($item->avg_price * (1 + $suggestedChange / 100), 2),
                    'reason' => $reason,
                    'priority' => $priority,
                    'metrics' => [
                        'avg_days_to_sell' => round($item->avg_days_to_sell, 1),
                        'current_stock' => $item->current_stock,
                        'days_until_stockout' => min($daysUntilStockOut, 999)
                    ]
                ];
            }
        }
        
        // Önceliğe göre sırala
        $sortedOpportunities = collect($opportunities)->sortByDesc(function($item) {
            $priorities = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $priorities[$item['priority']] ?? 0;
        })->take(15)->values()->toArray();
        
        return [
            'has_data' => true,
            'opportunities' => $sortedOpportunities,
            'summary' => [
                'price_increase_count' => collect($opportunities)->where('suggestion', 'price_increase')->count(),
                'price_decrease_count' => collect($opportunities)->where('suggestion', 'price_decrease')->count(),
                'maintain_count' => collect($opportunities)->where('suggestion', 'maintain')->count(),
                'total_analyzed' => count($opportunities)
            ]
        ];
    }
    
    /**
     * Hızlı Hareket Eden Ürünler (Top 10)
     */
    private function getFastMovers($stockData)
    {
        if ($stockData->isEmpty()) {
            return [];
        }
        
        return $stockData
            ->filter(function ($item) {
                return $item->avg_days_to_sell <= 15 && $item->total_sold >= 3;
            })
            ->sortBy('avg_days_to_sell')
            ->take(10)
            ->map(function ($item) {
                return [
                    'stock_name' => $item->stock_name,
                    'category' => $item->category ?? 'Kategori Yok',
                    'avg_days_to_sell' => round($item->avg_days_to_sell, 1),
                    'total_sold' => $item->total_sold,
                    'total_revenue' => round($item->total_revenue, 2),
                    'current_stock' => $item->current_stock,
                    'avg_price' => round($item->avg_price, 2),
                    'performance' => 'Mükemmel'
                ];
            })
            ->values()
            ->toArray();
    }
    
    /**
     * Yavaş Hareket Eden Ürünler (Top 10)
     */
    private function getSlowMovers($stockData)
    {
        if ($stockData->isEmpty()) {
            return [];
        }
        
        return $stockData
            ->filter(function ($item) {
                return $item->avg_days_to_sell > 30 && $item->current_stock > 0;
            })
            ->sortByDesc('avg_days_to_sell')
            ->take(10)
            ->map(function ($item) {
                $dailySalesRate = $item->total_sold / 90;
                $daysUntilStockOut = $item->current_stock > 0 ? 
                    round($item->current_stock / max($dailySalesRate, 0.1)) : 999;
                
                return [
                    'stock_name' => $item->stock_name,
                    'category' => $item->category ?? 'Kategori Yok',
                    'avg_days_to_sell' => round($item->avg_days_to_sell, 1),
                    'total_sold' => $item->total_sold,
                    'total_revenue' => round($item->total_revenue, 2),
                    'current_stock' => $item->current_stock,
                    'avg_price' => round($item->avg_price, 2),
                    'days_until_stockout' => min($daysUntilStockOut, 999),
                    'recommendation' => $this->getSlowMoverRecommendation($item)
                ];
            })
            ->values()
            ->toArray();
    }
    
    /**
     * Mağaza Bazlı Performans Analizi
     */
    private function analyzeWarehousePerformance($companyId, $sellerId = null)
    {
        try {
            $query = "
                SELECT 
                    w.name as warehouse_name,
                    sc.name as stock_name,
                    c.name as category,
                    COUNT(s.id) as total_sold,
                    AVG(DATEDIFF(s.created_at, scm.created_at)) as avg_days_to_sell,
                    SUM(s.customer_price) as total_revenue,
                    AVG(s.customer_price) as avg_price,
                    SUM(CASE WHEN scm_current.quantity > 0 AND scm_current.type = 1 THEN scm_current.quantity ELSE 0 END) as current_stock
                FROM sales s
                INNER JOIN stock_card_movements scm ON s.stock_card_movement_id = scm.id
                INNER JOIN stock_cards sc ON scm.stock_card_id = sc.id
                INNER JOIN warehouses w ON scm.warehouse_id = w.id
                LEFT JOIN categories c ON sc.category_id = c.id
                LEFT JOIN stock_card_movements scm_current ON scm_current.stock_card_id = sc.id 
                    AND scm_current.company_id = ? 
                    AND scm_current.type = 1
                    AND scm_current.warehouse_id = w.id
                WHERE s.company_id = ?
                    AND s.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                    AND scm.type = 1
            ";
            
            $params = [$companyId, $companyId];
            
            if ($sellerId) {
                $query .= " AND s.seller_id = ?";
                $params[] = $sellerId;
            }
            
            $query .= "
                GROUP BY w.id, w.name, sc.id, sc.name, c.name
                HAVING avg_days_to_sell IS NOT NULL
                ORDER BY w.name, avg_days_to_sell
            ";
            
            $data = collect(DB::select($query, $params));
            
            if ($data->isEmpty()) {
                return ['has_data' => false, 'warehouses' => []];
            }
            
            // Mağaza bazında grupla
            $warehouseGroups = $data->groupBy('warehouse_name');
            
            $warehouseAnalysis = [];
            
            foreach ($warehouseGroups as $warehouseName => $items) {
                // Her mağaza için en hızlı ve en yavaş 5 ürün
                $fastest = $items->sortBy('avg_days_to_sell')->take(5);
                $slowest = $items->sortByDesc('avg_days_to_sell')->take(5);
                
                $warehouseAnalysis[] = [
                    'warehouse_name' => $warehouseName,
                    'total_products' => $items->count(),
                    'avg_turnover' => round($items->avg('avg_days_to_sell'), 1),
                    'total_sales' => $items->sum('total_sold'),
                    'total_revenue' => round($items->sum('total_revenue'), 2),
                    'fastest_products' => $fastest->map(function($item) {
                        return [
                            'stock_name' => $item->stock_name,
                            'category' => $item->category ?? '-',
                            'avg_days' => round($item->avg_days_to_sell, 1),
                            'total_sold' => $item->total_sold,
                            'revenue' => round($item->total_revenue, 2)
                        ];
                    })->values()->toArray(),
                    'slowest_products' => $slowest->map(function($item) {
                        return [
                            'stock_name' => $item->stock_name,
                            'category' => $item->category ?? '-',
                            'avg_days' => round($item->avg_days_to_sell, 1),
                            'total_sold' => $item->total_sold,
                            'current_stock' => $item->current_stock
                        ];
                    })->values()->toArray()
                ];
            }
            
            return [
                'has_data' => true,
                'warehouses' => $warehouseAnalysis
            ];
            
        } catch (\Exception $e) {
            Log::error('Mağaza performans analizi hatası: ' . $e->getMessage());
            return ['has_data' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Yavaş hareket eden ürün için öneri
     */
    private function getSlowMoverRecommendation($item)
    {
        $avgDays = $item->avg_days_to_sell;
        
        if ($avgDays > 60 && $item->current_stock > 10) {
            return 'Acil İndirim Gerekli (%20-30)';
        } elseif ($avgDays > 45 && $item->current_stock > 5) {
            return 'İndirim Önerilir (%15)';
        } elseif ($avgDays > 30) {
            return 'Promosyon Düşünün (%10)';
        }
        
        return 'İzlemeye Devam';
    }
    
    /**
     * Cache'i temizle
     */
    public function clearCache($companyId, $sellerId = null)
    {
        $cacheKey = $this->getCacheKey($companyId, $sellerId);
        Cache::forget($cacheKey);
    }
}

