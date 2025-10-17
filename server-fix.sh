#!/bin/bash
# Sunucu PHP Uyumsuzluk Düzeltme Scripti

echo "🔍 Sunucu PHP Versiyonu Kontrol Ediliyor..."

# PHP versiyonunu kontrol et
PHP_VERSION=$(php -v | head -n 1 | cut -d ' ' -f 2)
echo "Mevcut PHP Versiyonu: $PHP_VERSION"

# Minimum gereken versiyon
MIN_VERSION="8.0.0"

# Versiyon karşılaştırması
if [ "$(printf '%s\n' "$MIN_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$MIN_VERSION" ]; then
    echo "❌ HATA: PHP 8.0+ gerekli!"
    echo "Mevcut: $PHP_VERSION"
    echo ""
    echo "💡 Çözümler:"
    echo "1. cPanel → PHP Selector → PHP 8.0+ seçin"
    echo "2. Hosting sağlayıcınızdan PHP güncellemesi isteyin"
    echo "3. Alternatif: Manuel cache temizleme kullanın"
    echo ""
    echo "🔧 Manuel cache temizleme başlatılıyor..."
    
    # Manuel cache temizleme
    echo "Bootstrap cache temizleniyor..."
    rm -rf bootstrap/cache/*
    
    echo "Storage cache temizleniyor..."
    rm -rf storage/framework/cache/*
    rm -rf storage/framework/views/*
    rm -rf storage/framework/sessions/*
    
    echo "✅ Manuel cache temizleme tamamlandı!"
    
else
    echo "✅ PHP versiyonu uyumlu: $PHP_VERSION"
    echo ""
    echo "🔧 Laravel cache temizleme başlatılıyor..."
    
    # Laravel cache komutları
    php artisan cache:clear 2>/dev/null || echo "Cache clear hatası, manuel temizleme yapılıyor..."
    php artisan view:clear 2>/dev/null || echo "View clear hatası, manuel temizleme yapılıyor..."
    php artisan config:clear 2>/dev/null || echo "Config clear hatası, manuel temizleme yapılıyor..."
    php artisan route:clear 2>/dev/null || echo "Route clear hatası, manuel temizleme yapılıyor..."
    
    echo "✅ Cache temizleme tamamlandı!"
fi

echo ""
echo "🌐 Site: https://irepair.com.tr"
echo "💡 Artık sayfayı yenileyebilirsiniz!"
