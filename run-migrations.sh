#!/bin/bash

echo "🚀 Migration'ları çalıştırma script'i"
echo "====================================="

# Cache temizle
echo "🧹 Cache temizleniyor..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migration'ları sıfırla
echo "🔄 Migration'lar sıfırlanıyor..."
php artisan migrate:reset --force

# Migration'ları çalıştır
echo "📦 Migration'lar çalıştırılıyor..."
php artisan migrate --force

# Seeder'ları çalıştır
echo "🌱 Seeder'lar çalıştırılıyor..."
php artisan db:seed --force

# Cache'leri optimize et
echo "⚡ Cache'ler optimize ediliyor..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Migration'lar başarıyla tamamlandı!"
echo "🌐 Uygulama hazır: http://localhost:8000"
