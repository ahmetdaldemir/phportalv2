<?php
/**
 * Manuel Cache Temizleme Scripti
 * PHP versiyonu uyumsuzluğu durumunda kullanılır
 */

echo "🧹 Manuel Cache Temizleme Başlatılıyor...\n";

// Cache dizinlerini temizle
$cacheDirs = [
    'bootstrap/cache',
    'storage/framework/cache',
    'storage/framework/views',
    'storage/framework/sessions'
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        echo "Temizleniyor: $dir\n";
        
        // Dizindeki dosyaları sil
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                echo "  Silindi: " . basename($file) . "\n";
            }
        }
    } else {
        echo "Dizin bulunamadı: $dir\n";
    }
}

// Config cache dosyasını sil
$configCache = 'bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "Config cache silindi\n";
}

// Route cache dosyasını sil  
$routeCache = 'bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "Route cache silindi\n";
}

// View cache dosyalarını sil
$viewCacheDir = 'storage/framework/views';
if (is_dir($viewCacheDir)) {
    $viewFiles = glob($viewCacheDir . '/*');
    foreach ($viewFiles as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "View cache temizlendi\n";
}

echo "\n✅ Manuel cache temizleme tamamlandı!\n";
echo "🌐 Site: https://irepair.com.tr\n";
echo "\n💡 Artık sayfayı yenileyebilirsiniz!\n";
?>
