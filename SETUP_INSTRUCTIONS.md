# PHPortal LLM Entegrasyonu - Kurulum Talimatları

Bu belge, LLM entegrasyonunun adım adım kurulum sürecini açıklar.

## 🚀 Hızlı Başlangıç

### 1. Ollama Kurulumu

#### Windows
1. [Ollama](https://ollama.ai/download) sitesinden Windows sürümünü indirin
2. Kurulum dosyasını çalıştırın
3. Kurulum tamamlandıktan sonra:
```powershell
cd ollama
.\setup-model.ps1
```

#### Linux/Mac
```bash
# Ollama'yı yükle
curl -fsSL https://ollama.ai/install.sh | sh

# Model kur
cd ollama
chmod +x setup-model.sh
./setup-model.sh
```

### 2. FastAPI Servisi Kurulumu

#### Windows
```powershell
cd llm_api
.\start-service.ps1
```

#### Linux/Mac
```bash
cd llm_api
chmod +x start-service.sh
./start-service.sh
```

### 3. Laravel Yapılandırması

`.env` dosyanıza ekleyin:
```env
LLM_API_URL=http://localhost:8000
LLM_DEFAULT_MODEL=phportal-assistant
LLM_TIMEOUT=300
```

### 4. Test Edin

```bash
# Sağlık kontrolü
php artisan tinker
>>> app(App\Services\LlmService::class)->healthCheck()

# Kod analizi
php artisan llm:analyze app/Http/Controllers/HomeController.php

# Proje öğrenimi
php artisan llm:learn
```

## 📋 Detaylı Kurulum

### Adım 1: Gereksinimler

- **PHP**: 8.1 veya üzeri
- **Python**: 3.9 veya üzeri
- **Ollama**: En son sürüm
- **RAM**: En az 8GB (13B model için)
- **Disk**: En az 10GB boş alan

### Adım 2: Ollama Model Kurulumu

```bash
# Ollama servisini başlat (ayrı bir terminal)
ollama serve

# Model kur
cd ollama
# Windows için:
.\setup-model.ps1
# Linux/Mac için:
./setup-model.sh
```

Model kurulumu tamamlandığında test edin:
```bash
ollama run phportal-assistant "Merhaba, Laravel hakkında bilgin var mı?"
```

### Adım 3: FastAPI Servisi

```bash
cd llm_api

# Sanal ortam oluştur
python -m venv venv

# Aktifleştir (Windows)
venv\Scripts\activate
# Aktifleştir (Linux/Mac)
source venv/bin/activate

# Gereksinimleri yükle
pip install -r requirements.txt

# Servisi başlat
python main.py
```

Servis çalışıyorsa şu adresten erişebilirsiniz:
- API: http://localhost:8000
- Dokümantasyon: http://localhost:8000/docs

### Adım 4: Laravel Entegrasyonu

1. **Config dosyası** zaten güncellenmiştir (`config/services.php`)

2. **.env dosyasını** güncelleyin:
```env
LLM_API_URL=http://localhost:8000
LLM_DEFAULT_MODEL=phportal-assistant
LLM_TIMEOUT=300
```

3. **Route ekleyin** (`routes/api.php` veya `routes/web.php`):
```php
use App\Http\Controllers\LlmController;

Route::prefix('llm')->middleware(['auth'])->group(function () {
    Route::post('/analyze-code', [LlmController::class, 'analyzeCode']);
    Route::post('/fix-bug', [LlmController::class, 'fixBug']);
    Route::post('/learn-project', [LlmController::class, 'learnProject']);
    Route::post('/chat', [LlmController::class, 'chat']);
    Route::get('/health', [LlmController::class, 'healthCheck']);
    Route::get('/models', [LlmController::class, 'listModels']);
});
```

4. **Cache temizle**:
```bash
php artisan config:clear
php artisan cache:clear
```

## 🧪 Test ve Doğrulama

### 1. Servis Sağlık Kontrolü

```bash
php artisan tinker
```

```php
$llm = app(App\Services\LlmService::class);
$health = $llm->healthCheck();
print_r($health);
```

Beklenen çıktı:
```
Array
(
    [success] => 1
    [status] => healthy
    [data] => Array
        (
            [status] => healthy
            [ollama_status] => connected
        )
)
```

### 2. Kod Analizi Testi

```bash
php artisan llm:analyze app/Http/Controllers/HomeController.php
```

### 3. Proje Öğrenimi Testi

```bash
php artisan llm:learn
```

### 4. Web API Testi

```bash
curl -X POST http://localhost:8000/health
```

## 🔧 Sorun Giderme

### Sorun: Ollama bağlantı hatası

**Çözüm**:
```bash
# Ollama servisini başlat
ollama serve

# Farklı bir terminal'de modeli çalıştır
ollama run phportal-assistant
```

### Sorun: FastAPI başlamıyor

**Kontroller**:
1. Python sürümünü kontrol edin: `python --version` (3.9+)
2. Port 8000 kullanımda mı?: `netstat -an | findstr 8000`
3. Gereksinimleri yükleyin: `pip install -r requirements.txt`

### Sorun: Laravel bağlanamıyor

**Kontroller**:
1. `.env` dosyası doğru mu?
2. `LLM_API_URL` doğru mu?
3. FastAPI servisi çalışıyor mu?: `curl http://localhost:8000/health`

### Sorun: Model yavaş çalışıyor

**Çözümler**:
1. Daha küçük model kullanın (7B yerine 13B)
2. GPU desteğini aktifleştirin
3. Context boyutunu azaltın

## 📚 Ek Kaynaklar

- [LLM Integration Guide](./LLM_INTEGRATION_GUIDE.md) - Detaylı kullanım kılavuzu
- [Ollama README](./ollama/README.md) - Model yapılandırması
- [FastAPI README](./llm_api/README.md) - API dokümantasyonu

## 🎯 Production Notları

### Güvenlik

1. **CORS**: Production'da sadece kendi domain'inizi izin verin
2. **Authentication**: API endpoint'lerine auth middleware ekleyin
3. **Rate Limiting**: API çağrılarını sınırlayın

### Performans

1. **Cache**: LLM cevaplarını cache'leyin
2. **Queue**: Uzun işlemleri queue'ya atın
3. **Load Balancer**: Birden fazla FastAPI instance kullanın

### Monitoring

1. Log tüm LLM çağrılarını
2. Response time'ları izleyin
3. Error rate'leri takip edin

## 🤝 Destek

Sorun yaşarsanız:
1. Loglara bakın: `storage/logs/laravel.log`
2. FastAPI loglarını kontrol edin
3. Ollama loglarını inceleyin

## ✅ Kurulum Kontrol Listesi

- [ ] Ollama yüklendi ve çalışıyor
- [ ] phportal-assistant modeli oluşturuldu
- [ ] FastAPI servisi çalışıyor (http://localhost:8000)
- [ ] Laravel .env dosyası yapılandırıldı
- [ ] Sağlık kontrolü başarılı
- [ ] Artisan komutları çalışıyor
- [ ] API endpoint'leri erişilebilir

Tüm adımlar tamamlandıysa, sistem kullanıma hazır! 🎉

