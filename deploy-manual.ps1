# Manuel Deployment Script
# Bu script local'den direkt deployment yapar

Write-Host "🚀 Manuel Deployment Başlatılıyor..." -ForegroundColor Cyan

# Parametreler
$FTP_SERVER = "153.92.220.80"
$FTP_USER = "u529018053.irepair.com.tr"
$FTP_PASS = "@198711Ad@"
$REMOTE_DIR = "/public_html/"

# 1. Composer install
Write-Host "`n📦 Composer dependencies yükleniyor..." -ForegroundColor Yellow
try {
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
    Write-Host "✅ Composer install başarılı" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer install hatası: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 2. NPM build
Write-Host "`n🎨 Frontend assets build ediliyor..." -ForegroundColor Yellow
try {
    npm ci
    npm run production
    Write-Host "✅ NPM build başarılı" -ForegroundColor Green
} catch {
    Write-Host "❌ NPM build hatası: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 3. Laravel optimize
Write-Host "`n🗜️ Laravel optimize ediliyor..." -ForegroundColor Yellow
try {
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    Write-Host "✅ Laravel optimize başarılı" -ForegroundColor Green
} catch {
    Write-Host "❌ Laravel optimize hatası: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 4. FTP Upload
Write-Host "`n📤 FTP upload başlatılıyor..." -ForegroundColor Yellow

# WinSCP kullanarak upload
$WinSCP_Script = @"
open ftp://$FTP_USER`:$FTP_PASS@$FTP_SERVER
cd $REMOTE_DIR
put -recurse -transfer=binary . /public_html/
close
exit
"@

$ScriptFile = "winscp_script.txt"
$WinSCP_Script | Out-File -FilePath $ScriptFile -Encoding UTF8

try {
    # WinSCP komut satırı ile upload
    & "C:\Program Files (x86)\WinSCP\WinSCP.exe" /script=$ScriptFile
    Write-Host "✅ FTP upload başarılı" -ForegroundColor Green
} catch {
    Write-Host "❌ FTP upload hatası: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "💡 Alternatif: FileZilla veya başka FTP client kullanın" -ForegroundColor Yellow
}

# Cleanup
if (Test-Path $ScriptFile) {
    Remove-Item $ScriptFile
}

Write-Host "`n🏁 Deployment tamamlandı!" -ForegroundColor Cyan
Write-Host "🌐 Site: https://irepair.com.tr" -ForegroundColor Green
