<?php
/**
 * Basit Devir Hızı Test Script'i
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Laravel uygulamasını başlat
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Basit Devir Hızı Test\n";
echo "======================\n\n";

try {
    // 1. Sales tablosu var mı?
    $salesExists = DB::select("SHOW TABLES LIKE 'sales'");
    echo "📊 Sales tablosu var mı: " . (count($salesExists) > 0 ? 'EVET' : 'HAYIR') . "\n";
    
    if (count($salesExists) == 0) {
        echo "❌ Sales tablosu bulunamadı!\n";
        exit;
    }
    
    // 2. Sales kayıtları
    $salesCount = DB::table('sales')->count();
    echo "📊 Toplam sales kayıtları: " . $salesCount . "\n";
    
    // 3. Stock Card Movements tablosu
    $movementsExists = DB::select("SHOW TABLES LIKE 'stock_card_movements'");
    echo "📦 Stock Card Movements tablosu var mı: " . (count($movementsExists) > 0 ? 'EVET' : 'HAYIR') . "\n";
    
    if (count($movementsExists) > 0) {
        $movementsCount = DB::table('stock_card_movements')->count();
        echo "📦 Toplam movements kayıtları: " . $movementsCount . "\n";
        
        $type1Count = DB::table('stock_card_movements')->where('type', 1)->count();
        echo "📦 Type 1 movements (giriş): " . $type1Count . "\n";
    }
    
    // 4. Basit join testi
    echo "\n🔗 Join Test:\n";
    $joinTest = DB::select("
        SELECT 
            s.id as sale_id,
            s.created_at as sale_date,
            scm.stock_card_id,
            scm.type,
            scm.created_at as movement_date
        FROM sales s
        LEFT JOIN stock_card_movements scm ON s.stock_card_movement_id = scm.id
        LIMIT 5
    ");
    
    echo "Join sonuçları (ilk 5 kayıt):\n";
    foreach ($joinTest as $row) {
        echo "Sale ID: {$row->sale_id}, Stock Card ID: {$row->stock_card_id}, Type: {$row->type}\n";
    }
    
    // 5. Tarih filtreli test
    echo "\n📅 Son 90 günlük satışlar:\n";
    $recentSales = DB::select("
        SELECT COUNT(*) as count
        FROM sales 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
    ");
    
    echo "Son 90 günde satış sayısı: " . $recentSales[0]->count . "\n";
    
    // 6. Alternatif tarih aralığı (son 1 yıl)
    echo "\n📅 Son 1 yıllık satışlar:\n";
    $yearlySales = DB::select("
        SELECT COUNT(*) as count
        FROM sales 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
    ");
    
    echo "Son 1 yılda satış sayısı: " . $yearlySales[0]->count . "\n";
    
    // 7. Tüm satışları kontrol et (tarih filtresi olmadan)
    echo "\n📅 Tüm satışlar:\n";
    $allSales = DB::select("
        SELECT COUNT(*) as count
        FROM sales
    ");
    
    echo "Toplam satış sayısı: " . $allSales[0]->count . "\n";
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ Test tamamlandı!\n";
