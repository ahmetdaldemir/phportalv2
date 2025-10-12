#!/bin/bash

# Production Deployment Script
# FTP üzerinden irepair.com.tr'ye deployment yapar

echo "🚀 Production Deployment Başlıyor"
echo "================================="
echo ""

# FTP Configuration
FTP_HOST="153.92.220.80"
FTP_USER="u529018053.irepair.com.tr"
FTP_PASS="@198711Ad@"
FTP_DIR="/public_html"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check dependencies
echo "📋 Gereksinimler kontrol ediliyor..."

if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP bulunamadı!${NC}"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer bulunamadı!${NC}"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo -e "${RED}❌ NPM bulunamadı!${NC}"
    exit 1
fi

if ! command -v lftp &> /dev/null; then
    echo -e "${YELLOW}⚠️  lftp bulunamadı. Kuruluyor...${NC}"
    # Ubuntu/Debian
    sudo apt-get install -y lftp 2>/dev/null || \
    # CentOS/RHEL
    sudo yum install -y lftp 2>/dev/null || \
    # macOS
    brew install lftp 2>/dev/null
fi

echo -e "${GREEN}✅ Tüm gereksinimler mevcut${NC}"
echo ""

# Step 2: Backup current deployment
echo "💾 Deployment öncesi backup alınıyor..."
BACKUP_DIR="deployments/backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✅ Backup klasörü oluşturuldu: $BACKUP_DIR${NC}"
echo ""

# Step 3: Install dependencies
echo "📦 Composer dependencies yükleniyor..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
echo -e "${GREEN}✅ Composer dependencies yüklendi${NC}"
echo ""

echo "📦 NPM dependencies yükleniyor..."
npm ci
echo -e "${GREEN}✅ NPM dependencies yüklendi${NC}"
echo ""

# Step 4: Build assets
echo "🎨 Frontend assets build ediliyor..."
npm run production
echo -e "${GREEN}✅ Assets build edildi${NC}"
echo ""

# Step 5: Optimize Laravel
echo "🗜️ Laravel optimize ediliyor..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Laravel optimize edildi${NC}"
echo ""

# Step 6: Create deployment package
echo "📦 Deployment paketi hazırlanıyor..."
DEPLOY_DIR="deployment_temp"
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

# Copy files
rsync -av \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='.env.example' \
    --exclude='phpunit.xml' \
    --exclude='*.md' \
    --exclude='database/migrations/backup' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='deployments' \
    --exclude='deployment_temp' \
    ./ "$DEPLOY_DIR/"

echo -e "${GREEN}✅ Deployment paketi hazır${NC}"
echo ""

# Step 7: Upload via FTP
echo "📤 FTP ile upload ediliyor..."
echo -e "${YELLOW}⏳ Bu işlem biraz zaman alabilir...${NC}"

lftp -c "
set ftp:ssl-allow no;
set ftp:passive-mode on;
open -u $FTP_USER,$FTP_PASS $FTP_HOST;
lcd $DEPLOY_DIR;
cd $FTP_DIR;
mirror --reverse --delete --verbose --exclude-glob .git/ --exclude-glob node_modules/ --exclude-glob tests/;
bye;
"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ FTP upload başarılı${NC}"
else
    echo -e "${RED}❌ FTP upload başarısız${NC}"
    exit 1
fi
echo ""

# Step 8: Cleanup
echo "🧹 Geçici dosyalar temizleniyor..."
rm -rf "$DEPLOY_DIR"
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Temizlik tamamlandı${NC}"
echo ""

# Final message
echo "================================="
echo -e "${GREEN}✅ DEPLOYMENT BAŞARILI!${NC}"
echo "🌐 Site URL: https://irepair.com.tr"
echo "📅 Tarih: $(date)"
echo "================================="
echo ""
echo "⚠️  Önemli Not:"
echo "   - Production'da migrations çalıştırmayı unutmayın"
echo "   - Cache'leri temizleyin: php artisan cache:clear"
echo "   - Queue worker'ları restart edin: php artisan queue:restart"

