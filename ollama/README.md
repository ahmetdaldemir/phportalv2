# PHPortal Ollama Model Yapılandırması

Bu dizin PHPortal projesi için özel optimize edilmiş Ollama LLM modelini içerir.

## Gereksinimler

1. **Ollama**: https://ollama.ai/download
2. **CodeLlama 13B** (otomatik indirilecek)

## Kurulum

### Windows

```powershell
cd ollama
.\setup-model.ps1
```

### Linux/Mac

```bash
cd ollama
chmod +x setup-model.sh
./setup-model.sh
```

## Manuel Kurulum

1. Ollama'yı başlat:
```bash
ollama serve
```

2. Modeli oluştur:
```bash
ollama create phportal-assistant -f Modelfile
```

3. Modeli test et:
```bash
ollama run phportal-assistant
```

## Model Özellikleri

- **Base Model**: CodeLlama 13B
- **Uzmanlaşma**: PHP, Laravel, PHPortal projesi
- **Context Boyutu**: 4096 token
- **Temperature**: 0.7 (dengeli yaratıcılık)

## Kullanım

### Komut Satırından

```bash
ollama run phportal-assistant "Bu kod nasıl iyileştirilebilir?"
```

### FastAPI Servisi Üzerinden

API servisi otomatik olarak bu modeli kullanır:

```php
$llmService = new LlmService();
$result = $llmService->analyzeCode($code);
```

## Model Güncelleme

Model dosyasını güncelledikten sonra:

```bash
ollama create phportal-assistant -f Modelfile
```

## Sorun Giderme

### Ollama çalışmıyor
```bash
# Servisi başlat
ollama serve

# Farklı port kullan
OLLAMA_HOST=0.0.0.0:11435 ollama serve
```

### Model bulunamadı
```bash
# Mevcut modelleri listele
ollama list

# Modeli yeniden oluştur
ollama create phportal-assistant -f Modelfile
```

### Bellek hatası
- Daha küçük model kullan: `codellama:7b`
- Context boyutunu azalt: `PARAMETER num_ctx 2048`

## İpuçları

1. **Model Performansı**: İlk çalıştırmada yavaş olabilir, sonraki çalıştırmalarda hızlanır
2. **GPU Desteği**: NVIDIA GPU varsa otomatik kullanılır
3. **Bellek**: 13B model için en az 8GB RAM önerilir

