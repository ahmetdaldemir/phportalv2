#!/bin/bash

echo "🚀 Laravel 12 + PHP 8.4 Güncelleme Script'i"
echo "=========================================="

# Yedek oluştur
echo "📦 Yedek oluşturuluyor..."
cp composer.json composer.json.backup
cp .env .env.backup

# Composer cache temizle
echo "🧹 Composer cache temizleniyor..."
composer clear-cache

# Vendor klasörünü sil
echo "🗑️ Vendor klasörü siliniyor..."
rm -rf vendor/
rm -rf composer.lock

# Composer install
echo "📥 Composer bağımlılıkları yükleniyor..."
composer install --ignore-platform-reqs

# Cache temizle
echo "🧹 Laravel cache temizleniyor..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migration'ları çalıştır
echo "🗄️ Migration'lar çalıştırılıyor..."
php artisan migrate --force

# Optimize
echo "⚡ Optimize ediliyor..."
php artisan optimize

echo "✅ Güncelleme tamamlandı!"
echo "📋 Yapılması gerekenler:"
echo "1. .env dosyasını kontrol edin"
echo "2. Uygulamayı test edin"
echo "3. Hata loglarını kontrol edin"
echo "4. Gerekirse ek güncellemeler yapın"
