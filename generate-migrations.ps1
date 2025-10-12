# Migration Generator Script (PowerShell)
# SQL Schema dosyasından otomatik migration oluşturur

Write-Host "🚀 Migration Oluşturma Scripti" -ForegroundColor Cyan
Write-Host "==============================" -ForegroundColor Cyan
Write-Host ""

# Check if SQL file exists
if (-not (Test-Path "SqlShemaDump.sql")) {
    Write-Host "❌ Hata: SqlShemaDump.sql dosyası bulunamadı!" -ForegroundColor Red
    exit 1
}

# Check if PHP exists
try {
    $phpVersion = php -v
    Write-Host "✅ PHP bulundu" -ForegroundColor Green
} catch {
    Write-Host "❌ Hata: PHP kurulu değil!" -ForegroundColor Red
    exit 1
}

# Backup existing migrations
Write-Host "📦 Mevcut migration'lar yedekleniyor..." -ForegroundColor Yellow

if (Test-Path "database/migrations") {
    # Create backup directory
    New-Item -ItemType Directory -Force -Path "database/migrations/backup" | Out-Null
    
    # Count and move existing migrations
    $migrations = Get-ChildItem -Path "database/migrations" -Filter "*.php" -File
    $count = $migrations.Count
    
    if ($count -gt 0) {
        Move-Item -Path "database/migrations/*.php" -Destination "database/migrations/backup/" -Force -ErrorAction SilentlyContinue
        Write-Host "✅ $count migration backup'a taşındı" -ForegroundColor Green
    } else {
        Write-Host "ℹ️  Taşınacak migration bulunamadı" -ForegroundColor Gray
    }
} else {
    New-Item -ItemType Directory -Force -Path "database/migrations" | Out-Null
    Write-Host "✅ Migration klasörü oluşturuldu" -ForegroundColor Green
}

Write-Host ""
Write-Host "🔧 Migration generator çalıştırılıyor..." -ForegroundColor Cyan
Write-Host ""

# Run the PHP generator script
php generate-migrations-from-sql.php

# Check if migrations were created
$createdMigrations = Get-ChildItem -Path "database/migrations" -Filter "*.php" -File
$createdCount = $createdMigrations.Count

Write-Host ""
Write-Host "==============================" -ForegroundColor Cyan
Write-Host "✅ İşlem tamamlandı!" -ForegroundColor Green
Write-Host "📊 Oluşturulan migration sayısı: $createdCount" -ForegroundColor Cyan
Write-Host ""
Write-Host "📝 Sonraki adımlar:" -ForegroundColor Yellow
Write-Host "   1. Migration'ları gözden geçirin: Get-ChildItem database/migrations/"
Write-Host "   2. Migration'ları çalıştırın: php artisan migrate:fresh"
Write-Host "   3. Seed'leri çalıştırın: php artisan db:seed"
Write-Host ""

# List created migrations
Write-Host "📋 Oluşturulan migration'lar:" -ForegroundColor Cyan
$createdMigrations | ForEach-Object {
    Write-Host "   - $($_.Name)" -ForegroundColor Gray
}

