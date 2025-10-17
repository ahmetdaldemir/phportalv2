# Simple FTP Connection Test
Write-Host "Testing FTP connection..." -ForegroundColor Cyan

try {
    $FTPRequest = [System.Net.FtpWebRequest]::Create("ftp://153.92.220.80/")
    $FTPRequest.Credentials = New-Object System.Net.NetworkCredential("u529018053.irepair.com.tr", "@198711Ad@")
    $FTPRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectory
    
    $Response = $FTPRequest.GetResponse()
    Write-Host "SUCCESS: FTP Connection works!" -ForegroundColor Green
    Write-Host "Status: $($Response.StatusDescription)" -ForegroundColor Cyan
    $Response.Close()
} catch {
    Write-Host "FAILED: FTP Connection failed!" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host "Test completed!" -ForegroundColor Cyan
