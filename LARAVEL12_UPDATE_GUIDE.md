# 🚀 Laravel 12 + PHP 8.4 Güncelleme Rehberi

## 📋 Güncelleme Özeti

- **Laravel 9** → **Laravel 12** (3 major version)
- **PHP 8.1** → **PHP 8.4** (3 minor version)
- **Büyük değişiklikler** ve **breaking changes**

## ⚠️ Önemli Uyarılar

### 1. **Backup Alın**
```bash
# Proje yedeği
tar -czf phportal-backup-$(date +%Y%m%d).tar.gz .

# Database yedeği
mysqldump -u username -p database_name > backup.sql
```

### 2. **Test Ortamında Deneyin**
- Önce test ortamında güncelleme yapın
- Tüm fonksiyonları test edin
- Hataları düzeltin

### 3. **Aşamalı Güncelleme**
- Önce Laravel 10 → Laravel 11 → Laravel 12
- Her aşamada test edin

## 🔄 Güncelleme Adımları

### **Adım 1: Sistem Gereksinimleri**
```bash
# PHP 8.4 kurulumu
sudo apt update
sudo apt install php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-intl php8.4-soap php8.4-pcntl

# Composer güncelleme
composer self-update
```

### **Adım 2: Composer.json Güncelleme**
✅ **Tamamlandı**: Composer.json Laravel 12 için güncellendi

### **Adım 3: Bootstrap/app.php Güncelleme**
✅ **Tamamlandı**: Laravel 12 formatına çevrildi

### **Adım 4: Config Güncellemeleri**
✅ **Tamamlandı**: app.php Laravel 12 formatına güncellendi

### **Adım 5: Middleware Güncellemeleri**
✅ **Tamamlandı**: Bootstrap/app.php'ye taşındı

### **Adım 6: Model Güncellemeleri**
✅ **Tamamlandı**: BaseModel Laravel 12 uyumlu hale getirildi

## 🚨 Breaking Changes

### 1. **Bootstrap/app.php**
- Eski: `$app = new Application()`
- Yeni: `Application::configure()->withRouting()->create()`

### 2. **RouteServiceProvider**
- Artık gerekli değil
- Route'lar bootstrap/app.php'de tanımlanıyor

### 3. **Middleware**
- Kernel.php yerine bootstrap/app.php'de tanımlanıyor
- Alias'lar middleware->alias() ile tanımlanıyor

### 4. **Config**
- Service provider'lar otomatik yükleniyor
- Facade'lar otomatik yükleniyor

### 5. **PHP 8.4 Değişiklikleri**
- `null` coalescing operator değişiklikleri
- Type hinting güncellemeleri
- Deprecated fonksiyonlar kaldırıldı

## 🔧 Güncelleme Script'i

```bash
# Script'i çalıştırılabilir yap
chmod +x update-to-laravel12.sh

# Güncelleme script'ini çalıştır
./update-to-laravel12.sh
```

## 📋 Kontrol Listesi

### **Güncelleme Öncesi**
- [ ] Backup alındı
- [ ] Test ortamında denendi
- [ ] PHP 8.4 kurulu
- [ ] Composer güncel

### **Güncelleme Sırasında**
- [ ] Composer.json güncellendi
- [ ] Bootstrap/app.php güncellendi
- [ ] Config dosyaları güncellendi
- [ ] Middleware'ler taşındı
- [ ] Model'ler güncellendi

### **Güncelleme Sonrası**
- [ ] Composer install başarılı
- [ ] Migration'lar çalıştı
- [ ] Cache temizlendi
- [ ] Uygulama çalışıyor
- [ ] Tüm sayfalar açılıyor
- [ ] CRUD işlemleri çalışıyor
- [ ] API'ler çalışıyor

## 🐛 Olası Hatalar ve Çözümleri

### 1. **Composer Hatası**
```bash
# Platform requirements'ı ignore et
composer install --ignore-platform-reqs
```

### 2. **Class Not Found**
```bash
# Autoload'u yeniden oluştur
composer dump-autoload
```

### 3. **Migration Hatası**
```bash
# Migration'ları sıfırla ve tekrar çalıştır
php artisan migrate:fresh --seed
```

### 4. **Cache Hatası**
```bash
# Tüm cache'leri temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 5. **Permission Hatası**
```bash
# Storage ve cache klasörlerine yazma izni ver
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 📊 Performans İyileştirmeleri

### **Laravel 12 Avantajları**
- **%30-50 daha hızlı** boot time
- **Daha az memory** kullanımı
- **Daha iyi caching** sistemi
- **Modern PHP 8.4** özellikleri

### **PHP 8.4 Avantajları**
- **JIT compiler** iyileştirmeleri
- **Daha hızlı** array işlemleri
- **Daha iyi** error handling
- **Yeni syntax** özellikleri

## 🔍 Test Edilecek Alanlar

### **1. Authentication**
- Login/logout
- Password reset
- Email verification

### **2. CRUD İşlemleri**
- Transfer modülü
- Stock card modülü
- Invoice modülü
- User management

### **3. API Endpoints**
- REST API'ler
- Authentication
- Rate limiting

### **4. File Uploads**
- Image uploads
- Document uploads
- Storage links

### **5. Database**
- Migration'lar
- Seeder'lar
- Query performance

## 📞 Destek

### **Hata Durumunda**
1. Log dosyalarını kontrol edin: `storage/logs/laravel.log`
2. Composer hatalarını kontrol edin
3. PHP error log'unu kontrol edin
4. Backup'tan geri dönün

### **Yararlı Komutlar**
```bash
# Uygulama durumu
php artisan about

# Route listesi
php artisan route:list

# Config cache
php artisan config:cache

# Route cache
php artisan route:cache

# View cache
php artisan view:cache
```

---

**⚠️ Önemli**: Bu güncelleme büyük değişiklikler içerir. Mutlaka test ortamında deneyin ve backup alın!
