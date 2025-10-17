# PHPortal Ollama Model Kurulum Script'i (PowerShell)
# Bu script özel PHPortal modelini Ollama'ya yükler

Write-Host "PHPortal Ollama Model Kurulumu Başlıyor..." -ForegroundColor Cyan

# Ollama'nın yüklü olup olmadığını kontrol et
if (-not (Get-Command ollama -ErrorAction SilentlyContinue)) {
    Write-Host "HATA: Ollama yüklü değil!" -ForegroundColor Red
    Write-Host "Ollama'yı yüklemek için: https://ollama.ai/download" -ForegroundColor Yellow
    exit 1
}

# Ollama servisinin çalışıp çalışmadığını kontrol et
try {
    $response = Invoke-WebRequest -Uri "http://localhost:11434/api/tags" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✓ Ollama servisi çalışıyor" -ForegroundColor Green
} catch {
    Write-Host "HATA: Ollama servisi çalışmıyor!" -ForegroundColor Red
    Write-Host "Ollama servisini başlatmak için: 'ollama serve' komutunu çalıştırın" -ForegroundColor Yellow
    exit 1
}

# Modelfile'ın varlığını kontrol et
if (-not (Test-Path "Modelfile")) {
    Write-Host "HATA: Modelfile bulunamadı!" -ForegroundColor Red
    Write-Host "Lütfen bu script'i ollama\ dizininde çalıştırın" -ForegroundColor Yellow
    exit 1
}

Write-Host "✓ Modelfile bulundu" -ForegroundColor Green

# CodeLlama modelini kontrol et ve çek
Write-Host "CodeLlama modeli kontrol ediliyor..." -ForegroundColor Cyan
$models = ollama list
if ($models -notmatch "codellama:13b") {
    Write-Host "CodeLlama:13b modeli indiriliyor... (Bu işlem uzun sürebilir)" -ForegroundColor Yellow
    ollama pull codellama:13b
} else {
    Write-Host "✓ CodeLlama:13b modeli zaten yüklü" -ForegroundColor Green
}

# Özel PHPortal modelini oluştur
Write-Host "PHPortal asistan modeli oluşturuluyor..." -ForegroundColor Cyan
ollama create phportal-assistant -f Modelfile

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ PHPortal asistan modeli başarıyla oluşturuldu!" -ForegroundColor Green
    
    # Modeli test et
    Write-Host ""
    Write-Host "Model test ediliyor..." -ForegroundColor Cyan
    Write-Host "Test sorusu: 'Laravel controller best practice nedir?'" -ForegroundColor Yellow
    Write-Host ""
    
    ollama run phportal-assistant "Laravel controller best practice nedir? Kısaca açıkla."
    
    Write-Host ""
    Write-Host "==========================================" -ForegroundColor Cyan
    Write-Host "✓ Kurulum tamamlandı!" -ForegroundColor Green
    Write-Host "Model adı: phportal-assistant" -ForegroundColor White
    Write-Host ""
    Write-Host "Kullanım:" -ForegroundColor Yellow
    Write-Host "  ollama run phportal-assistant" -ForegroundColor White
    Write-Host ""
    Write-Host "veya FastAPI servisi üzerinden kullanabilirsiniz." -ForegroundColor White
    Write-Host "==========================================" -ForegroundColor Cyan
} else {
    Write-Host "HATA: Model oluşturma başarısız!" -ForegroundColor Red
    exit 1
}

