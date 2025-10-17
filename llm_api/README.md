# PHPortal LLM API Service

FastAPI servisi - Laravel ve Ollama arasında köprü görevi görür.

## Kurulum

1. Python sanal ortamı oluştur:
```bash
python -m venv venv
```

2. Sanal ortamı aktifleştir:
```bash
# Windows
venv\Scripts\activate

# Linux/Mac
source venv/bin/activate
```

3. Gereksinimleri yükle:
```bash
pip install -r requirements.txt
```

4. Ortam değişkenlerini yapılandır:
```bash
cp .env.example .env
# .env dosyasını düzenle
```

## Çalıştırma

```bash
python main.py
```

veya

```bash
uvicorn main:app --reload --host 0.0.0.0 --port 8000
```

## API Endpoints

- `GET /` - Health check
- `GET /health` - Detaylı health check
- `POST /api/analyze-code` - Kod analizi
- `POST /api/fix-bug` - Bug düzeltme
- `POST /api/learn-project` - Proje öğrenme
- `POST /api/chat` - Genel sohbet
- `GET /api/models` - Mevcut modeller

## Dokümantasyon

API çalıştıktan sonra şu adresten erişilebilir:
- Swagger UI: http://localhost:8000/docs
- ReDoc: http://localhost:8000/redoc

