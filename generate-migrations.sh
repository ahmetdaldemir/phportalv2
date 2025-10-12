#!/bin/bash

# Migration Generator Script
# SQL Schema dosyasından otomatik migration oluşturur

echo "🚀 Migration Oluşturma Scripti"
echo "=============================="
echo ""

# Check if SQL file exists
if [ ! -f "SqlShemaDump.sql" ]; then
    echo "❌ Hata: SqlShemaDump.sql dosyası bulunamadı!"
    exit 1
fi

# Check if PHP exists
if ! command -v php &> /dev/null; then
    echo "❌ Hata: PHP kurulu değil!"
    exit 1
fi

# Backup existing migrations
echo "📦 Mevcut migration'lar yedekleniyor..."
if [ -d "database/migrations" ]; then
    mkdir -p database/migrations/backup
    
    # Count existing migrations
    count=$(find database/migrations -maxdepth 1 -name "*.php" 2>/dev/null | wc -l)
    
    if [ $count -gt 0 ]; then
        mv database/migrations/*.php database/migrations/backup/ 2>/dev/null
        echo "✅ $count migration backup'a taşındı"
    else
        echo "ℹ️  Taşınacak migration bulunamadı"
    fi
else
    mkdir -p database/migrations
    echo "✅ Migration klasörü oluşturuldu"
fi

echo ""
echo "🔧 Migration generator çalıştırılıyor..."
echo ""

# Run the PHP generator script
php generate-migrations-from-sql.php

# Check if migrations were created
created_count=$(find database/migrations -maxdepth 1 -name "*.php" 2>/dev/null | wc -l)

echo ""
echo "=============================="
echo "✅ İşlem tamamlandı!"
echo "📊 Oluşturulan migration sayısı: $created_count"
echo ""
echo "📝 Sonraki adımlar:"
echo "   1. Migration'ları gözden geçirin: ls -la database/migrations/"
echo "   2. Migration'ları çalıştırın: php artisan migrate:fresh"
echo "   3. Seed'leri çalıştırın: php artisan db:seed"
echo ""

