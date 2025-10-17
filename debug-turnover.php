<?php
/**
 * Devir Hızı Debug Script'i
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Laravel uygulamasını başlat
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Devir Hızı Debug Raporu\n";
echo "==========================\n\n";

try {
    // 1. Sales tablosu kontrolü
    $totalSales = DB::table('sales')->count();
    echo "📊 Toplam Sales Kayıtları: " . $totalSales . "\n";
    
    $recentSales = DB::table('sales')
        ->where('created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 90 DAY)'))
        ->count();
    echo "📅 Son 90 Günlük Sales: " . $recentSales . "\n\n";
    
    if ($recentSales == 0) {
        echo "❌ Sorun: Son 90 günde hiç satış yok!\n";
        echo "💡 Çözüm: Sales tablosuna test verisi ekleyin veya tarih aralığını genişletin.\n\n";
    }
    
    // 2. Stock Card Movements kontrolü
    $totalMovements = DB::table('stock_card_movements')->count();
    echo "📦 Toplam Stock Card Movements: " . $totalMovements . "\n";
    
    $type1Movements = DB::table('stock_card_movements')
        ->where('type', 1)
        ->count();
    echo "📦 Type 1 Movements (Giriş): " . $type1Movements . "\n\n";
    
    // 3. Sales + Stock Card Movements Join kontrolü
    $joinResult = DB::select("
        SELECT COUNT(*) as count
        FROM sales s
        INNER JOIN stock_card_movements scm ON s.stock_card_movement_id = scm.id
        WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
        AND scm.type = 1
    ");
    
    echo "🔗 Sales + Movements Join Sonucu: " . $joinResult[0]->count . "\n\n";
    
    if ($joinResult[0]->count == 0) {
        echo "❌ Sorun: Sales ve Stock Card Movements arasında join sonucu yok!\n";
        echo "💡 Kontrol edilecekler:\n";
        echo "   - sales.stock_card_movement_id değerleri doğru mu?\n";
        echo "   - stock_card_movements.type = 1 kayıtları var mı?\n";
        echo "   - Tarih filtreleri çok kısıtlayıcı mı?\n\n";
    }
    
    // 4. Örnek bir stok kartı için detaylı analiz
    $sampleStock = DB::table('stock_cards')->first();
    if ($sampleStock) {
        echo "🔍 Örnek Stok Kartı Analizi (ID: " . $sampleStock->id . ")\n";
        echo "Stok Adı: " . $sampleStock->name . "\n";
        
        $turnoverQuery = "
            SELECT 
                COUNT(s.id) as total_sold,
                AVG(DATEDIFF(s.created_at, scm.created_at)) as avg_days_to_sell,
                MIN(s.created_at) as first_sale,
                MAX(s.created_at) as last_sale
            FROM sales s
            INNER JOIN stock_card_movements scm ON s.stock_card_movement_id = scm.id
            WHERE s.company_id = ? 
                AND scm.stock_card_id = ?
                AND s.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                AND scm.type = 1
        ";
        
        $result = DB::select($turnoverQuery, [$sampleStock->company_id ?? 1, $sampleStock->id]);
        
        if (!empty($result)) {
            echo "Satış Sayısı: " . $result[0]->total_sold . "\n";
            echo "Ortalama Devir Süresi: " . ($result[0]->avg_days_to_sell ?? 'NULL') . " gün\n";
            echo "İlk Satış: " . ($result[0]->first_sale ?? 'Yok') . "\n";
            echo "Son Satış: " . ($result[0]->last_sale ?? 'Yok') . "\n";
        } else {
            echo "❌ Bu stok kartı için hiç satış bulunamadı!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

echo "\n✅ Debug tamamlandı!\n";
