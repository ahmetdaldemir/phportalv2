#!/bin/bash

echo "🧹 Laravel 12 için eski dosyaları temizleme"
echo "=========================================="

# RouteServiceProvider artık gerekli değil
if [ -f "app/Providers/RouteServiceProvider.php" ]; then
    echo "🗑️ RouteServiceProvider.php siliniyor..."
    rm app/Providers/RouteServiceProvider.php
fi

# Eski Kernel.php artık gerekli değil
if [ -f "app/Http/Kernel.php" ]; then
    echo "🗑️ Http/Kernel.php siliniyor..."
    rm app/Http/Kernel.php
fi

# Eski Console/Kernel.php artık gerekli değil
if [ -f "app/Console/Kernel.php" ]; then
    echo "🗑️ Console/Kernel.php siliniyor..."
    rm app/Console/Kernel.php
fi

# Eski Exception Handler artık gerekli değil
if [ -f "app/Exceptions/Handler.php" ]; then
    echo "🗑️ Exceptions/Handler.php siliniyor..."
    rm app/Exceptions/Handler.php
fi

# Eski config dosyaları
echo "📝 Config dosyaları güncelleniyor..."

# Cache temizle
echo "🧹 Cache temizleniyor..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ Temizlik tamamlandı!"
echo "📋 Silinen dosyalar:"
echo "- RouteServiceProvider.php"
echo "- Http/Kernel.php"
echo "- Console/Kernel.php"
echo "- Exceptions/Handler.php"
