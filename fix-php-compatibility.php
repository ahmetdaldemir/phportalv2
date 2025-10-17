<?php
/**
 * PHP Compatibility Fix
 * Bu script sunucudaki PHP versiyonu uyumsuzluğunu çözer
 */

echo "🔍 PHP Versiyonu Kontrol Ediliyor...\n";

// PHP versiyonunu kontrol et
$phpVersion = PHP_VERSION;
echo "Mevcut PHP Versiyonu: $phpVersion\n";

// Minimum gereken versiyon
$minVersion = '8.0.0';

if (version_compare($phpVersion, $minVersion, '<')) {
    echo "❌ HATA: PHP $minVersion veya üzeri gerekli!\n";
    echo "Mevcut versiyon: $phpVersion\n";
    echo "\n💡 Çözümler:\n";
    echo "1. Hosting sağlayıcınızdan PHP versiyonunu güncelleyin\n";
    echo "2. cPanel'de PHP versiyonunu 8.0+ yapın\n";
    echo "3. Alternatif: Laravel versiyonunu düşürün\n";
    exit(1);
} else {
    echo "✅ PHP versiyonu uyumlu: $phpVersion\n";
}

// Laravel versiyonunu kontrol et
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    $laravelVersion = $composer['require']['laravel/framework'] ?? 'unknown';
    echo "Laravel Versiyonu: $laravelVersion\n";
}

echo "\n🔧 Cache temizleme işlemi başlatılıyor...\n";

// Cache temizleme komutları
$commands = [
    'php artisan cache:clear',
    'php artisan view:clear', 
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    echo "Çalıştırılıyor: $command\n";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "Çıktı: $output\n";
    }
}

echo "\n✅ Cache temizleme tamamlandı!\n";
echo "🌐 Site: https://irepair.com.tr\n";
?>
