#!/bin/bash

# Pre-Deployment Check Script
# Deployment öncesi tüm kontrolleri yapar

echo "🔍 Pre-Deployment Kontrolleri"
echo "============================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0

# Check 1: PHP Version
echo "1️⃣  PHP versiyon kontrolü..."
PHP_VERSION=$(php -r "echo PHP_VERSION;")
if [[ "$PHP_VERSION" =~ ^8\.[4-9] ]]; then
    echo -e "${GREEN}✅ PHP $PHP_VERSION${NC}"
else
    echo -e "${RED}❌ PHP 8.4+ gerekli, mevcut: $PHP_VERSION${NC}"
    ERRORS=$((ERRORS+1))
fi
echo ""

# Check 2: Composer
echo "2️⃣  Composer kontrolü..."
if command -v composer &> /dev/null; then
    echo -e "${GREEN}✅ Composer kurulu${NC}"
else
    echo -e "${RED}❌ Composer kurulu değil${NC}"
    ERRORS=$((ERRORS+1))
fi
echo ""

# Check 3: NPM
echo "3️⃣  NPM kontrolü..."
if command -v npm &> /dev/null; then
    echo -e "${GREEN}✅ NPM kurulu${NC}"
else
    echo -e "${RED}❌ NPM kurulu değil${NC}"
    ERRORS=$((ERRORS+1))
fi
echo ""

# Check 4: Duplicate Routes
echo "4️⃣  Route duplicate kontrolü..."
php artisan route:clear > /dev/null 2>&1

if php artisan route:cache > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Route cache başarılı (duplicate yok)${NC}"
    php artisan route:clear > /dev/null 2>&1
else
    echo -e "${RED}❌ Route cache hatası (duplicate route var)${NC}"
    echo -e "${YELLOW}   Hatayı görmek için: php artisan route:cache${NC}"
    ERRORS=$((ERRORS+1))
    php artisan route:clear > /dev/null 2>&1
fi
echo ""

# Check 5: Config Cache
echo "5️⃣  Config cache kontrolü..."
php artisan config:clear > /dev/null 2>&1

if php artisan config:cache > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Config cache başarılı${NC}"
    php artisan config:clear > /dev/null 2>&1
else
    echo -e "${RED}❌ Config cache hatası${NC}"
    ERRORS=$((ERRORS+1))
    php artisan config:clear > /dev/null 2>&1
fi
echo ""

# Check 6: View Cache
echo "6️⃣  View cache kontrolü..."
php artisan view:clear > /dev/null 2>&1

if php artisan view:cache > /dev/null 2>&1; then
    echo -e "${GREEN}✅ View cache başarılı${NC}"
    php artisan view:clear > /dev/null 2>&1
else
    echo -e "${RED}❌ View cache hatası${NC}"
    ERRORS=$((ERRORS+1))
    php artisan view:clear > /dev/null 2>&1
fi
echo ""

# Check 7: .env file
echo "7️⃣  .env dosyası kontrolü..."
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ .env dosyası mevcut${NC}"
    
    # Check critical env variables
    if grep -q "APP_KEY=" .env && ! grep -q "APP_KEY=$" .env && ! grep -q "APP_KEY=null" .env; then
        echo -e "${GREEN}   ✅ APP_KEY tanımlı${NC}"
    else
        echo -e "${RED}   ❌ APP_KEY tanımlı değil${NC}"
        echo -e "${YELLOW}      Çalıştırın: php artisan key:generate${NC}"
        ERRORS=$((ERRORS+1))
    fi
else
    echo -e "${RED}❌ .env dosyası bulunamadı${NC}"
    echo -e "${YELLOW}   cp .env.example .env${NC}"
    ERRORS=$((ERRORS+1))
fi
echo ""

# Check 8: Storage Permissions
echo "8️⃣  Storage izinleri kontrolü..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    echo -e "${GREEN}✅ Storage klasörleri yazılabilir${NC}"
else
    echo -e "${YELLOW}⚠️  Storage izinleri eksik olabilir${NC}"
    echo -e "${YELLOW}   Çalıştırın: chmod -R 755 storage bootstrap/cache${NC}"
fi
echo ""

# Check 9: Composer Dependencies
echo "9️⃣  Composer dependencies kontrolü..."
if [ -d "vendor" ]; then
    echo -e "${GREEN}✅ Vendor klasörü mevcut${NC}"
else
    echo -e "${YELLOW}⚠️  Vendor klasörü yok${NC}"
    echo -e "${YELLOW}   Çalıştırın: composer install${NC}"
fi
echo ""

# Check 10: NPM Dependencies  
echo "🔟 NPM dependencies kontrolü..."
if [ -d "node_modules" ]; then
    echo -e "${GREEN}✅ node_modules mevcut${NC}"
else
    echo -e "${YELLOW}⚠️  node_modules yok${NC}"
    echo -e "${YELLOW}   Çalıştırın: npm install${NC}"
fi
echo ""

# Check 11: Built Assets
echo "1️⃣1️⃣  Built assets kontrolü..."
if [ -d "public/build" ] || [ -f "public/mix-manifest.json" ]; then
    echo -e "${GREEN}✅ Built assets mevcut${NC}"
else
    echo -e "${YELLOW}⚠️  Built assets yok${NC}"
    echo -e "${YELLOW}   Çalıştırın: npm run production${NC}"
fi
echo ""

# Final Summary
echo "============================="
if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ TÜM KONTROLLER BAŞARILI!${NC}"
    echo -e "${GREEN}✅ Deployment için hazır${NC}"
    echo ""
    echo "🚀 Deployment başlatmak için:"
    echo "   ./deploy-to-production.sh"
    echo "   veya"
    echo "   git push origin main"
    exit 0
else
    echo -e "${RED}❌ $ERRORS HATA BULUNDU!${NC}"
    echo -e "${RED}❌ Deployment yapılamaz${NC}"
    echo ""
    echo "🔧 Hataları düzeltip tekrar deneyin"
    exit 1
fi

