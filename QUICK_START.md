# LLM Entegrasyonu - Hızlı Başlangıç

3 adımda LLM entegrasyonunu başlatın!

## 1️⃣ Ollama Kur

### Windows
```powershell
# 1. İndir: https://ollama.ai/download
# 2. Kur ve çalıştır:
cd ollama
.\setup-model.ps1
```

### Linux/Mac
```bash
curl -fsSL https://ollama.ai/install.sh | sh
cd ollama
chmod +x setup-model.sh
./setup-model.sh
```

## 2️⃣ FastAPI Başlat

### Windows
```powershell
cd llm_api
.\start-service.ps1
```

### Linux/Mac
```bash
cd llm_api
chmod +x start-service.sh
./start-service.sh
```

## 3️⃣ Laravel Yapılandır

`.env` dosyasına ekle:
```env
LLM_API_URL=http://localhost:8000
LLM_DEFAULT_MODEL=phportal-assistant
LLM_TIMEOUT=300
```

## ✅ Test Et

```bash
# Sağlık kontrolü
curl http://localhost:8000/health

# Kod analizi
php artisan llm:analyze app/Http/Controllers/HomeController.php

# Proje öğrenimi
php artisan llm:learn
```

## 🎯 Kullanım

### PHP'den
```php
$llm = app(\App\Services\LlmService::class);

// Kod analizi
$result = $llm->analyzeCode($code, $filePath);

// Bug düzelt
$result = $llm->fixBug($code, $errorMessage);

// Sohbet et
$result = $llm->chat("Laravel best practice nedir?");
```

### API'den
```bash
curl -X POST http://your-app.com/api/llm/analyze-code \
  -H "Content-Type: application/json" \
  -d '{"code": "<?php ... ?>"}'
```

## 📖 Detaylı Dokümantasyon

- [Tam Kurulum Kılavuzu](./SETUP_INSTRUCTIONS.md)
- [Kullanım Kılavuzu](./LLM_INTEGRATION_GUIDE.md)
- [Sorun Giderme](./LLM_INTEGRATION_GUIDE.md#sorun-giderme)

## 🆘 Sorun mu var?

1. Ollama çalışıyor mu? → `ollama serve`
2. FastAPI çalışıyor mu? → `curl http://localhost:8000/health`
3. Model var mı? → `ollama list`

Hâlâ sorun varsa [SETUP_INSTRUCTIONS.md](./SETUP_INSTRUCTIONS.md#sorun-giderme) bakın.

