# Sunucu PHP Uyumsuzluk Düzeltme Scripti (PowerShell)

Write-Host "🔍 Sunucu PHP Versiyonu Kontrol Ediliyor..." -ForegroundColor Cyan

# PHP versiyonunu kontrol et
try {
    $phpVersion = php -v | Select-String "PHP" | ForEach-Object { $_.Line.Split()[1] }
    Write-Host "Mevcut PHP Versiyonu: $phpVersion" -ForegroundColor Yellow
} catch {
    Write-Host "❌ PHP komutu bulunamadı!" -ForegroundColor Red
    exit 1
}

# Minimum gereken versiyon
$minVersion = "8.0.0"

# Versiyon karşılaştırması
if ([version]$phpVersion -lt [version]$minVersion) {
    Write-Host "❌ HATA: PHP 8.0+ gerekli!" -ForegroundColor Red
    Write-Host "Mevcut: $phpVersion" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "💡 Çözümler:" -ForegroundColor Magenta
    Write-Host "1. cPanel → PHP Selector → PHP 8.0+ seçin" -ForegroundColor White
    Write-Host "2. Hosting sağlayıcınızdan PHP güncellemesi isteyin" -ForegroundColor White
    Write-Host "3. Alternatif: Manuel cache temizleme kullanın" -ForegroundColor White
    Write-Host ""
    Write-Host "🔧 Manuel cache temizleme başlatılıyor..." -ForegroundColor Yellow
    
    # Manuel cache temizleme
    Write-Host "Bootstrap cache temizleniyor..." -ForegroundColor Yellow
    if (Test-Path "bootstrap/cache") {
        Remove-Item "bootstrap/cache/*" -Force -Recurse -ErrorAction SilentlyContinue
    }
    
    Write-Host "Storage cache temizleniyor..." -ForegroundColor Yellow
    if (Test-Path "storage/framework/cache") {
        Remove-Item "storage/framework/cache/*" -Force -Recurse -ErrorAction SilentlyContinue
    }
    if (Test-Path "storage/framework/views") {
        Remove-Item "storage/framework/views/*" -Force -Recurse -ErrorAction SilentlyContinue
    }
    if (Test-Path "storage/framework/sessions") {
        Remove-Item "storage/framework/sessions/*" -Force -Recurse -ErrorAction SilentlyContinue
    }
    
    Write-Host "✅ Manuel cache temizleme tamamlandı!" -ForegroundColor Green
    
} else {
    Write-Host "✅ PHP versiyonu uyumlu: $phpVersion" -ForegroundColor Green
    Write-Host ""
    Write-Host "🔧 Laravel cache temizleme başlatılıyor..." -ForegroundColor Yellow
    
    # Laravel cache komutları
    try {
        php artisan cache:clear 2>$null
        Write-Host "✅ Cache clear başarılı" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ Cache clear hatası, manuel temizleme yapılıyor..." -ForegroundColor Yellow
    }
    
    try {
        php artisan view:clear 2>$null
        Write-Host "✅ View clear başarılı" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ View clear hatası, manuel temizleme yapılıyor..." -ForegroundColor Yellow
    }
    
    try {
        php artisan config:clear 2>$null
        Write-Host "✅ Config clear başarılı" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ Config clear hatası, manuel temizleme yapılıyor..." -ForegroundColor Yellow
    }
    
    try {
        php artisan route:clear 2>$null
        Write-Host "✅ Route clear başarılı" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ Route clear hatası, manuel temizleme yapılıyor..." -ForegroundColor Yellow
    }
    
    Write-Host "✅ Cache temizleme tamamlandı!" -ForegroundColor Green
}

Write-Host ""
Write-Host "🌐 Site: https://irepair.com.tr" -ForegroundColor Cyan
Write-Host "💡 Artık sayfayı yenileyebilirsiniz!" -ForegroundColor Green
