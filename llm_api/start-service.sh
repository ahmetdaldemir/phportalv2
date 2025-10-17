#!/bin/bash

# PHPortal LLM API Servisini Başlatma Script'i

echo "PHPortal LLM API Servisi Başlatılıyor..."

# Python kontrolü
if ! command -v python3 &> /dev/null; then
    echo "HATA: Python3 yüklü değil!"
    echo "Python'u yüklemek için paket yöneticinizi kullanın"
    exit 1
fi

# Sanal ortamın varlığını kontrol et
if [ ! -d "venv" ]; then
    echo "Sanal ortam bulunamadı. Oluşturuluyor..."
    python3 -m venv venv
    echo "✓ Sanal ortam oluşturuldu"
fi

# Sanal ortamı aktifleştir
echo "Sanal ortam aktifleştiriliyor..."
source venv/bin/activate

# Gereksinimleri yükle
if [ ! -f "venv/lib/python*/site-packages/fastapi/__init__.py" ]; then
    echo "Gereksinimler yükleniyor..."
    pip install -r requirements.txt
    echo "✓ Gereksinimler yüklendi"
fi

# .env dosyası kontrolü
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo "⚠ .env dosyası bulunamadı. .env.example kopyalanıyor..."
        cp .env.example .env
        echo "Lütfen .env dosyasını yapılandırın!"
    fi
fi

# Ollama kontrolü
echo "Ollama servisi kontrol ediliyor..."
if curl -s http://localhost:11434/api/tags &> /dev/null; then
    echo "✓ Ollama servisi çalışıyor"
else
    echo "⚠ Ollama servisi çalışmıyor!"
    echo "Ollama'yı başlatmak için başka bir terminal'de 'ollama serve' komutunu çalıştırın"
fi

echo ""
echo "========================================="
echo "FastAPI Servisi Başlatılıyor..."
echo "========================================="
echo ""
echo "Servis adresi: http://localhost:8000"
echo "API Dokümantasyonu: http://localhost:8000/docs"
echo ""
echo "Durdurmak için: Ctrl+C"
echo ""

# FastAPI servisini başlat
python main.py

