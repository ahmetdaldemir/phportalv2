# FTP/SFTP Bağlantı Test Scripti
# Bu script farklı protokolleri test eder

Write-Host "🔍 FTP/SFTP Bağlantı Testi Başlatılıyor..." -ForegroundColor Cyan

# Test 1: FTP (Port 21)
Write-Host "`n📡 Test 1: FTP (Port 21)" -ForegroundColor Yellow
try {
    $FTPRequest = [System.Net.FtpWebRequest]::Create("ftp://153.92.220.80/")
    $FTPRequest.Credentials = New-Object System.Net.NetworkCredential("u529018053.irepair.com.tr", "@198711Ad@")
    $FTPRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectory
    
    $Response = $FTPRequest.GetResponse()
    Write-Host "✅ FTP Connection Successful!" -ForegroundColor Green
    Write-Host "   Status: $($Response.StatusDescription)" -ForegroundColor Cyan
    $Response.Close()
} catch {
    Write-Host "❌ FTP Connection Failed!" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Yellow
}

# Test 2: SFTP (Port 22) - PowerShell ile
Write-Host "`n📡 Test 2: SFTP (Port 22)" -ForegroundColor Yellow
try {
    # SFTP test için WinSCP .NET assembly kullanabiliriz
    Write-Host "SFTP test için WinSCP gerekli..." -ForegroundColor Yellow
} catch {
    Write-Host "❌ SFTP test edilemedi" -ForegroundColor Red
}

# Test 3: HTTPS (Port 443)
Write-Host "`n📡 Test 3: HTTPS (Port 443)" -ForegroundColor Yellow
try {
    $WebRequest = [System.Net.WebRequest]::Create("https://irepair.com.tr")
    $WebRequest.Timeout = 10000
    $Response = $WebRequest.GetResponse()
    Write-Host "✅ HTTPS Connection Successful!" -ForegroundColor Green
    Write-Host "   Status: $($Response.StatusCode)" -ForegroundColor Cyan
    $Response.Close()
} catch {
    Write-Host "❌ HTTPS Connection Failed!" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Yellow
}

# Test 4: HTTP (Port 80)
Write-Host "`n📡 Test 4: HTTP (Port 80)" -ForegroundColor Yellow
try {
    $WebRequest = [System.Net.WebRequest]::Create("http://irepair.com.tr")
    $WebRequest.Timeout = 10000
    $Response = $WebRequest.GetResponse()
    Write-Host "✅ HTTP Connection Successful!" -ForegroundColor Green
    Write-Host "   Status: $($Response.StatusCode)" -ForegroundColor Cyan
    $Response.Close()
} catch {
    Write-Host "❌ HTTP Connection Failed!" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host "`n🏁 Test tamamlandı!" -ForegroundColor Cyan
Write-Host "`nOneriler:" -ForegroundColor Magenta
Write-Host "1. Hosting panelinden FTP/SFTP ayarlarını kontrol edin" -ForegroundColor White
Write-Host "2. Şifredeki özel karakterleri escape edin" -ForegroundColor White
Write-Host "3. SFTP kullanmayı deneyin" -ForegroundColor White
Write-Host "4. Manuel deployment scripti kullanın" -ForegroundColor White
