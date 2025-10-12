#!/bin/bash
set -e

# Örnek: Ortam değişkenlerinin ayarlanması
echo "Ayarlanan Değişken: $EXAMPLE_ENV_VARIABLE"

# Örnek: Yapılandırma dosyalarının kopyalanması
cp /config-files/* /app/config/

# Örnek: Servis başlatma komutu
exec "$@"
