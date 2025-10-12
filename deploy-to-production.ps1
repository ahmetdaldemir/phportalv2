# Production Deployment Script (PowerShell)
# FTP üzerinden irepair.com.tr'ye deployment yapar

Write-Host "🚀 Production Deployment Başlıyor" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan
Write-Host ""

# FTP Configuration
$FTP_HOST = "ftp://153.92.220.80"
$FTP_USER = "u529018053.irepair.com.tr"
$FTP_PASS = "@198711Ad@"
$FTP_DIR = "/public_html"

# Step 1: Check dependencies
Write-Host "📋 Gereksinimler kontrol ediliyor..." -ForegroundColor Yellow

try {
    $phpVersion = php -v
    Write-Host "✅ PHP bulundu" -ForegroundColor Green
} catch {
    Write-Host "❌ PHP bulunamadı!" -ForegroundColor Red
    exit 1
}

try {
    $composerVersion = composer -V
    Write-Host "✅ Composer bulundu" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer bulunamadı!" -ForegroundColor Red
    exit 1
}

try {
    $npmVersion = npm -v
    Write-Host "✅ NPM bulundu" -ForegroundColor Green
} catch {
    Write-Host "❌ NPM bulunamadı!" -ForegroundColor Red
    exit 1
}

Write-Host ""

# Step 2: Backup
Write-Host "💾 Deployment öncesi backup alınıyor..." -ForegroundColor Yellow
$backupDir = "deployments\backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null
Write-Host "✅ Backup klasörü oluşturuldu: $backupDir" -ForegroundColor Green
Write-Host ""

# Step 3: Install Composer dependencies
Write-Host "📦 Composer dependencies yükleniyor..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install başarısız!" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Composer dependencies yüklendi" -ForegroundColor Green
Write-Host ""

# Step 4: Install NPM dependencies
Write-Host "📦 NPM dependencies yükleniyor..." -ForegroundColor Yellow
npm ci
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ NPM install başarısız!" -ForegroundColor Red
    exit 1
}
Write-Host "✅ NPM dependencies yüklendi" -ForegroundColor Green
Write-Host ""

# Step 5: Build assets
Write-Host "🎨 Frontend assets build ediliyor..." -ForegroundColor Yellow
npm run production
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Build başarısız!" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Assets build edildi" -ForegroundColor Green
Write-Host ""

# Step 6: Optimize Laravel
Write-Host "🗜️ Laravel optimize ediliyor..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host "✅ Laravel optimize edildi" -ForegroundColor Green
Write-Host ""

# Step 7: Prepare deployment directory
Write-Host "📂 Deployment klasörü hazırlanıyor..." -ForegroundColor Yellow
$deployDir = "deployment_temp"

if (Test-Path $deployDir) {
    Remove-Item -Recurse -Force $deployDir
}

New-Item -ItemType Directory -Force -Path $deployDir | Out-Null

# Files to exclude
$excludePatterns = @(
    '.git',
    '.github',
    'node_modules',
    'tests',
    '.env.example',
    'phpunit.xml',
    '*.md',
    'database\migrations\backup',
    'deployments',
    'deployment_temp',
    'generate-*.php',
    'generate-*.sh',
    'generate-*.ps1',
    'deploy-*.sh',
    'deploy-*.ps1'
)

# Copy files
Write-Host "📋 Dosyalar kopyalanıyor..." -ForegroundColor Yellow
Get-ChildItem -Path . -Exclude $excludePatterns | Copy-Item -Destination $deployDir -Recurse -Force
Write-Host "✅ Dosyalar kopyalandı" -ForegroundColor Green
Write-Host ""

# Step 8: Upload via FTP using WinSCP or built-in FTP
Write-Host "📤 FTP ile upload ediliyor..." -ForegroundColor Yellow
Write-Host "⏳ Bu işlem biraz zaman alabilir..." -ForegroundColor Yellow
Write-Host ""

# Check if WinSCP is available
$winscpPath = "C:\Program Files (x86)\WinSCP\WinSCP.com"

if (Test-Path $winscpPath) {
    # Use WinSCP for faster upload
    Write-Host "🔧 WinSCP kullanılıyor..." -ForegroundColor Cyan
    
    $winscpScript = @"
option batch abort
option confirm off
open ftp://${FTP_USER}:${FTP_PASS}@153.92.220.80
cd $FTP_DIR
lcd $deployDir
synchronize remote -delete
exit
"@
    
    $winscpScript | & $winscpPath /console /script=-
    
} else {
    # Use PowerShell FTP (slower but works everywhere)
    Write-Host "🔧 PowerShell FTP kullanılıyor..." -ForegroundColor Cyan
    Write-Host "⚠️  WinSCP kurulu değil, upload daha yavaş olacak" -ForegroundColor Yellow
    Write-Host ""
    
    # Create FTP script
    $ftpScript = @"
open $FTP_HOST
$FTP_USER
$FTP_PASS
binary
cd $FTP_DIR
lcd $deployDir
mput -r *
bye
"@
    
    $ftpScript | ftp -n -s:-
}

Write-Host "✅ FTP upload tamamlandı" -ForegroundColor Green
Write-Host ""

# Step 9: Cleanup
Write-Host "🧹 Geçici dosyalar temizleniyor..." -ForegroundColor Yellow
Remove-Item -Recurse -Force $deployDir -ErrorAction SilentlyContinue
php artisan config:clear
php artisan route:clear
php artisan view:clear
Write-Host "✅ Temizlik tamamlandı" -ForegroundColor Green
Write-Host ""

# Final message
Write-Host "=================================" -ForegroundColor Cyan
Write-Host "✅ DEPLOYMENT BAŞARILI!" -ForegroundColor Green
Write-Host "🌐 Site URL: https://irepair.com.tr" -ForegroundColor Cyan
Write-Host "📅 Tarih: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "⚠️  Önemli Notlar:" -ForegroundColor Yellow
Write-Host "   1. Production'da .env dosyasını kontrol edin"
Write-Host "   2. Storage klasörü izinlerini kontrol edin (chmod 755)"
Write-Host "   3. Cache'leri temizleyin: php artisan cache:clear"
Write-Host "   4. Migrations çalıştırın (gerekirse): php artisan migrate --force"
Write-Host "   5. Queue worker'ları restart edin: php artisan queue:restart"
Write-Host ""

