# Pre-Deployment Check Script (PowerShell)
# Deployment öncesi tüm kontrolleri yapar

Write-Host "🔍 Pre-Deployment Kontrolleri" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host ""

$ERRORS = 0

# Check 1: PHP Version
Write-Host "1️⃣  PHP versiyon kontrolü..." -ForegroundColor Yellow
try {
    $phpVersion = php -r "echo PHP_VERSION;"
    if ($phpVersion -match '^8\.[4-9]') {
        Write-Host "✅ PHP $phpVersion" -ForegroundColor Green
    } else {
        Write-Host "❌ PHP 8.4+ gerekli, mevcut: $phpVersion" -ForegroundColor Red
        $ERRORS++
    }
} catch {
    Write-Host "❌ PHP bulunamadı!" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 2: Composer
Write-Host "2️⃣  Composer kontrolü..." -ForegroundColor Yellow
try {
    $composerVersion = composer -V
    Write-Host "✅ Composer kurulu" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer kurulu değil" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 3: NPM
Write-Host "3️⃣  NPM kontrolü..." -ForegroundColor Yellow
try {
    $npmVersion = npm -v
    Write-Host "✅ NPM kurulu" -ForegroundColor Green
} catch {
    Write-Host "❌ NPM kurulu değil" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 4: Duplicate Routes
Write-Host "4️⃣  Route duplicate kontrolü..." -ForegroundColor Yellow
php artisan route:clear 2>&1 | Out-Null

try {
    $routeCacheResult = php artisan route:cache 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Route cache başarılı (duplicate yok)" -ForegroundColor Green
        php artisan route:clear 2>&1 | Out-Null
    } else {
        Write-Host "❌ Route cache hatası (duplicate route var)" -ForegroundColor Red
        Write-Host "   Hata: $routeCacheResult" -ForegroundColor Yellow
        $ERRORS++
        php artisan route:clear 2>&1 | Out-Null
    }
} catch {
    Write-Host "❌ Route cache hatası" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 5: Config Cache
Write-Host "5️⃣  Config cache kontrolü..." -ForegroundColor Yellow
php artisan config:clear 2>&1 | Out-Null

try {
    php artisan config:cache 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Config cache başarılı" -ForegroundColor Green
        php artisan config:clear 2>&1 | Out-Null
    } else {
        Write-Host "❌ Config cache hatası" -ForegroundColor Red
        $ERRORS++
    }
} catch {
    Write-Host "❌ Config cache hatası" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 6: View Cache
Write-Host "6️⃣  View cache kontrolü..." -ForegroundColor Yellow
php artisan view:clear 2>&1 | Out-Null

try {
    php artisan view:cache 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ View cache başarılı" -ForegroundColor Green
        php artisan view:clear 2>&1 | Out-Null
    } else {
        Write-Host "❌ View cache hatası" -ForegroundColor Red
        $ERRORS++
    }
} catch {
    Write-Host "❌ View cache hatası" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 7: .env file
Write-Host "7️⃣  .env dosyası kontrolü..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Write-Host "✅ .env dosyası mevcut" -ForegroundColor Green
    
    # Check APP_KEY
    $envContent = Get-Content .env -Raw
    if ($envContent -match "APP_KEY=base64:.+" -or $envContent -match "APP_KEY=[a-zA-Z0-9]{32}") {
        Write-Host "   ✅ APP_KEY tanımlı" -ForegroundColor Green
    } else {
        Write-Host "   ❌ APP_KEY tanımlı değil" -ForegroundColor Red
        Write-Host "      Çalıştırın: php artisan key:generate" -ForegroundColor Yellow
        $ERRORS++
    }
} else {
    Write-Host "❌ .env dosyası bulunamadı" -ForegroundColor Red
    Write-Host "   Çalıştırın: copy .env.example .env" -ForegroundColor Yellow
    $ERRORS++
}
Write-Host ""

# Check 8: Storage Permissions
Write-Host "8️⃣  Storage izinleri kontrolü..." -ForegroundColor Yellow
if ((Test-Path "storage") -and (Test-Path "bootstrap/cache")) {
    Write-Host "✅ Storage klasörleri mevcut" -ForegroundColor Green
} else {
    Write-Host "❌ Storage klasörleri eksik" -ForegroundColor Red
    $ERRORS++
}
Write-Host ""

# Check 9: Composer Dependencies
Write-Host "9️⃣  Composer dependencies kontrolü..." -ForegroundColor Yellow
if (Test-Path "vendor") {
    Write-Host "✅ Vendor klasörü mevcut" -ForegroundColor Green
} else {
    Write-Host "⚠️  Vendor klasörü yok" -ForegroundColor Yellow
    Write-Host "   Çalıştırın: composer install" -ForegroundColor Yellow
}
Write-Host ""

# Check 10: NPM Dependencies
Write-Host "🔟 NPM dependencies kontrolü..." -ForegroundColor Yellow
if (Test-Path "node_modules") {
    Write-Host "✅ node_modules mevcut" -ForegroundColor Green
} else {
    Write-Host "⚠️  node_modules yok" -ForegroundColor Yellow
    Write-Host "   Çalıştırın: npm install" -ForegroundColor Yellow
}
Write-Host ""

# Check 11: Built Assets
Write-Host "1️⃣1️⃣  Built assets kontrolü..." -ForegroundColor Yellow
if ((Test-Path "public/build") -or (Test-Path "public/mix-manifest.json")) {
    Write-Host "✅ Built assets mevcut" -ForegroundColor Green
} else {
    Write-Host "⚠️  Built assets yok" -ForegroundColor Yellow
    Write-Host "   Çalıştırın: npm run production" -ForegroundColor Yellow
}
Write-Host ""

# Final Summary
Write-Host "=============================" -ForegroundColor Cyan
if ($ERRORS -eq 0) {
    Write-Host "✅ TÜM KONTROLLER BAŞARILI!" -ForegroundColor Green
    Write-Host "✅ Deployment için hazır" -ForegroundColor Green
    Write-Host ""
    Write-Host "🚀 Deployment başlatmak için:" -ForegroundColor Cyan
    Write-Host "   .\deploy-to-production.ps1" -ForegroundColor White
    Write-Host "   veya" -ForegroundColor White
    Write-Host "   git push origin main" -ForegroundColor White
    exit 0
} else {
    Write-Host "❌ $ERRORS HATA BULUNDU!" -ForegroundColor Red
    Write-Host "❌ Deployment yapılamaz" -ForegroundColor Red
    Write-Host ""
    Write-Host "🔧 Hataları düzeltip tekrar deneyin" -ForegroundColor Yellow
    exit 1
}

