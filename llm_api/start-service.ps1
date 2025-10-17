# PHPortal LLM API Servisini Başlatma Script'i (PowerShell)

Write-Host "PHPortal LLM API Servisi Başlatılıyor..." -ForegroundColor Cyan

# Python kontrolü
if (-not (Get-Command python -ErrorAction SilentlyContinue)) {
    Write-Host "HATA: Python yüklü değil!" -ForegroundColor Red
    Write-Host "Python'u yüklemek için: https://www.python.org/downloads/" -ForegroundColor Yellow
    exit 1
}

# Sanal ortamın varlığını kontrol et
if (-not (Test-Path "venv")) {
    Write-Host "Sanal ortam bulunamadı. Oluşturuluyor..." -ForegroundColor Yellow
    python -m venv venv
    Write-Host "✓ Sanal ortam oluşturuldu" -ForegroundColor Green
}

# Sanal ortamı aktifleştir
Write-Host "Sanal ortam aktifleştiriliyor..." -ForegroundColor Cyan
& .\venv\Scripts\Activate.ps1

# Gereksinimleri yükle
if (-not (Test-Path "venv\Lib\site-packages\fastapi")) {
    Write-Host "Gereksinimler yükleniyor..." -ForegroundColor Yellow
    pip install -r requirements.txt
    Write-Host "✓ Gereksinimler yüklendi" -ForegroundColor Green
}

# .env dosyası kontrolü
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Write-Host "⚠ .env dosyası bulunamadı. .env.example kopyalanıyor..." -ForegroundColor Yellow
        Copy-Item ".env.example" ".env"
        Write-Host "Lütfen .env dosyasını yapılandırın!" -ForegroundColor Yellow
    }
}

# Ollama kontrolü
Write-Host "Ollama servisi kontrol ediliyor..." -ForegroundColor Cyan
try {
    $response = Invoke-WebRequest -Uri "http://localhost:11434/api/tags" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✓ Ollama servisi çalışıyor" -ForegroundColor Green
} catch {
    Write-Host "⚠ Ollama servisi çalışmıyor!" -ForegroundColor Yellow
    Write-Host "Ollama'yı başlatmak için başka bir terminal'de 'ollama serve' komutunu çalıştırın" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "FastAPI Servisi Başlatılıyor..." -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Servis adresi: http://localhost:8000" -ForegroundColor White
Write-Host "API Dokümantasyonu: http://localhost:8000/docs" -ForegroundColor White
Write-Host ""
Write-Host "Durdurmak için: Ctrl+C" -ForegroundColor Yellow
Write-Host ""

# FastAPI servisini başlat
python main.py

