<?php
/**
 * AI Cache Temizleme Script'i - Basit Versiyon
 * PHP 7.4+ uyumlu
 */

echo "🧹 AI Analiz Cache temizleniyor...\n";

// Laravel cache dosyalarını temizle
$cachePath = __DIR__ . '/bootstrap/cache/';

if (is_dir($cachePath)) {
    $files = glob($cachePath . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            echo "✅ Temizlendi: " . basename($file) . "\n";
        }
    }
}

// Redis cache temizleme (eğer Redis varsa)
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $keys = $redis->keys('*stock_turnover_ai_analysis*');
    foreach ($keys as $key) {
        $redis->del($key);
        echo "✅ Redis temizlendi: " . $key . "\n";
    }
    $redis->close();
} catch (Exception $e) {
    echo "⚠️ Redis bulunamadı, sadece dosya cache temizlendi.\n";
}

echo "🎉 AI Cache başarıyla temizlendi!\n";
echo "Şimdi sayfayı yenileyin ve yeni veriler gelecek.\n";

