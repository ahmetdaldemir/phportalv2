#!/bin/bash

# PHPortal Ollama Model Kurulum Script'i
# Bu script özel PHPortal modelini Ollama'ya yükler

echo "PHPortal Ollama Model Kurulumu Başlıyor..."

# Ollama'nın çalışıp çalışmadığını kontrol et
if ! command -v ollama &> /dev/null; then
    echo "HATA: Ollama yüklü değil!"
    echo "Ollama'yı yüklemek için: https://ollama.ai/download"
    exit 1
fi

# Ollama servisinin çalışıp çalışmadığını kontrol et
if ! curl -s http://localhost:11434/api/tags &> /dev/null; then
    echo "HATA: Ollama servisi çalışmıyor!"
    echo "Ollama servisini başlatmak için: 'ollama serve' komutunu çalıştırın"
    exit 1
fi

echo "✓ Ollama servisi çalışıyor"

# Modelfile'ın varlığını kontrol et
if [ ! -f "Modelfile" ]; then
    echo "HATA: Modelfile bulunamadı!"
    echo "Lütfen bu script'i ollama/ dizininde çalıştırın"
    exit 1
fi

echo "✓ Modelfile bulundu"

# CodeLlama modelini çek (eğer yoksa)
echo "CodeLlama modeli kontrol ediliyor..."
if ! ollama list | grep -q "codellama:13b"; then
    echo "CodeLlama:13b modeli indiriliyor... (Bu işlem uzun sürebilir)"
    ollama pull codellama:13b
else
    echo "✓ CodeLlama:13b modeli zaten yüklü"
fi

# Özel PHPortal modelini oluştur
echo "PHPortal asistan modeli oluşturuluyor..."
ollama create phportal-assistant -f Modelfile

if [ $? -eq 0 ]; then
    echo "✓ PHPortal asistan modeli başarıyla oluşturuldu!"
    
    # Modeli test et
    echo ""
    echo "Model test ediliyor..."
    echo "Test sorusu: 'Laravel controller best practice nedir?'"
    echo ""
    
    ollama run phportal-assistant "Laravel controller best practice nedir? Kısaca açıkla."
    
    echo ""
    echo "=========================================="
    echo "✓ Kurulum tamamlandı!"
    echo "Model adı: phportal-assistant"
    echo ""
    echo "Kullanım:"
    echo "  ollama run phportal-assistant"
    echo ""
    echo "veya FastAPI servisi üzerinden kullanabilirsiniz."
    echo "=========================================="
else
    echo "HATA: Model oluşturma başarısız!"
    exit 1
fi

