# 🐳 PhPortal Docker Kurulum ve Kullanım Kılavuzu

## 📋 İçindekiler
- [Genel Bakış](#genel-bakış)
- [Sistem Gereksinimleri](#sistem-gereksinimleri)
- [Hızlı Başlangıç](#hızlı-başlangıç)
- [Servisler ve Erişim Bilgileri](#servisler-ve-erim-bilgileri)
- [Kurulum Adımları](#kurulum-adımları)
- [Kullanım Komutları](#kullanım-komutları)
- [Sorun Giderme](#sorun-giderme)
- [Geliştirme İpuçları](#geliştirme-ipuçları)

## 🎯 Genel Bakış

Bu proje PHP 8.3, Laravel 12, MySQL 8.0, Redis, RabbitMQ, MongoDB ve PHPMyAdmin içeren tam kapsamlı bir Docker ortamı ile çalışır.

### 🏗️ Teknoloji Stack'i
- **Backend**: PHP 8.3 + Laravel 12
- **Web Server**: Nginx
- **Database**: MySQL 8.0
- **Cache**: Redis 7
- **Message Queue**: RabbitMQ 3
- **NoSQL**: MongoDB 7
- **Database Management**: PHPMyAdmin

## 💻 Sistem Gereksinimleri

- Docker Desktop (en az 4GB RAM)
- Docker Compose
- Git
- Node.js (frontend için)

## 🚀 Hızlı Başlangıç

### 1. Projeyi Klonlayın
```bash
git clone <repository-url>
cd phportal
```

### 2. Docker Servislerini Başlatın
```bash
# Tüm servisleri başlat
docker-compose up -d

# Sadece veritabanı servislerini başlat
docker-compose up -d mysql redis rabbitmq mongodb phpmyadmin
```

### 3. Laravel Kurulumu
```bash
# Container'a bağlan
docker-compose exec app bash

# Composer dependencies yükle
composer install

# .env dosyasını oluştur
cp .env.example .env

# Application key oluştur
php artisan key:generate

# Migration'ları çalıştır
php artisan migrate

# Seed data oluştur
php artisan db:seed

# Storage link oluştur
php artisan storage:link

# Cache'leri temizle
php artisan optimize:clear
```

### 4. Frontend Assets
```bash
# Node modules yükle
npm install

# Development için
npm run dev

# Production için
npm run build
```

## 🌐 Servisler ve Erişim Bilgileri

### ✅ Çalışan Servisler

| Servis | URL | Port | Durum |
|--------|-----|------|-------|
| **Laravel App** | http://localhost:8000 | 8000 | ✅ |
| **PHPMyAdmin** | http://localhost:8081 | 8081 | ✅ |
| **RabbitMQ Management** | http://localhost:15672 | 15672 | ✅ |
| **MySQL** | localhost:3311 | 3311 | ✅ |
| **Redis** | localhost:6379 | 6379 | ✅ |
| **MongoDB** | localhost:27017 | 27017 | ✅ |

### 🔐 Giriş Bilgileri

#### PHPMyAdmin
- **URL**: http://localhost:8081
- **Username**: `phportal`
- **Password**: `phportal123`
- **Server**: mysql (otomatik)

#### RabbitMQ Management
- **URL**: http://localhost:15672
- **Username**: `phportal`
- **Password**: `phportal123`

#### MySQL (Doğrudan Bağlantı)
- **Host**: localhost
- **Port**: 3311
- **Database**: phportal
- **Username**: phportal
- **Password**: phportal123
- **Root Password**: root123

#### Redis
- **Host**: localhost
- **Port**: 6379
- **Password**: (yok)

#### MongoDB
- **Host**: localhost
- **Port**: 27017
- **Database**: phportal
- **Username**: phportal
- **Password**: phportal123

## 🛠️ Kurulum Adımları

### Docker Compose Yapılandırması

Proje aşağıdaki Docker servislerini içerir:

```yaml
services:
  app:          # Laravel PHP 8.3 Application
  nginx:        # Web Server
  mysql:        # MySQL 8.0 Database
  redis:        # Redis 7 Cache
  rabbitmq:     # RabbitMQ 3 Message Queue
  mongodb:      # MongoDB 7 NoSQL Database
  phpmyadmin:   # Database Management
  horizon:      # Laravel Horizon Queue Worker
  queue:        # Laravel Queue Worker
```

### Port Yapılandırması

| Servis | Internal Port | External Port |
|--------|---------------|---------------|
| Nginx | 80 | 8000 |
| PHPMyAdmin | 80 | 8081 |
| MySQL | 3306 | 3311 |
| Redis | 6379 | 6379 |
| RabbitMQ | 5672 | 5672 |
| RabbitMQ Management | 15672 | 15672 |
| MongoDB | 27017 | 27017 |

## 📝 Kullanım Komutları

### 🐳 Docker Komutları

```bash
# Tüm servisleri başlat
docker-compose up -d

# Tüm servisleri durdur
docker-compose down

# Servisleri yeniden başlat
docker-compose restart

# Belirli servisi başlat
docker-compose up -d mysql

# Belirli servisi durdur
docker-compose stop nginx

# Logları görüntüle
docker-compose logs -f

# Belirli servisin loglarını görüntüle
docker-compose logs -f app

# Container'a bağlan
docker-compose exec app bash

# Container'ların durumunu kontrol et
docker-compose ps

# Volume'ları listele
docker volume ls

# Volume'u temizle
docker volume rm phportal_mysql_data
```

### 🎯 Laravel Komutları

```bash
# Container'a bağlan
docker-compose exec app bash

# Migration çalıştır
docker-compose exec app php artisan migrate

# Migration'ları geri al
docker-compose exec app php artisan migrate:rollback

# Seed data oluştur
docker-compose exec app php artisan db:seed

# Cache temizle
docker-compose exec app php artisan cache:clear

# Config cache temizle
docker-compose exec app php artisan config:clear

# Route cache temizle
docker-compose exec app php artisan route:clear

# View cache temizle
docker-compose exec app php artisan view:clear

# Tüm cache'leri temizle
docker-compose exec app php artisan optimize:clear

# Queue worker başlat
docker-compose exec app php artisan queue:work

# Horizon başlat
docker-compose exec app php artisan horizon

# Storage link oluştur
docker-compose exec app php artisan storage:link

# Composer autoload yenile
docker-compose exec app composer dump-autoload
```

### 🗄️ Veritabanı Komutları

```bash
# MySQL'e bağlan
docker-compose exec mysql mysql -u phportal -p phportal

# MySQL root ile bağlan
docker-compose exec mysql mysql -u root -p

# Redis CLI
docker-compose exec redis redis-cli

# MongoDB shell
docker-compose exec mongodb mongosh -u phportal -p phportal123

# Veritabanı yedekle
docker-compose exec mysql mysqldump -u phportal -p phportal > backup.sql

# Veritabanı geri yükle
docker-compose exec -T mysql mysql -u phportal -p phportal < backup.sql
```

### 📦 Frontend Komutları

```bash
# Node modules yükle
npm install

# Development mode
npm run dev

# Watch mode
npm run watch

# Production build
npm run build

# Hot reload
npm run hot
```

## 🔧 Sorun Giderme

### MySQL Bağlantı Sorunları

```bash
# MySQL loglarını kontrol et
docker-compose logs mysql

# MySQL volume'unu temizle
docker-compose down
docker volume rm phportal_mysql_data
docker-compose up -d mysql

# MySQL'e bağlanmayı test et
docker-compose exec mysql mysql -u phportal -pphportal123 -e "SELECT 1;"
```

### PHPMyAdmin Bağlantı Sorunları

```bash
# PHPMyAdmin loglarını kontrol et
docker-compose logs phpmyadmin

# Port çakışması varsa port değiştir
# docker-compose.yml dosyasında:
# ports: - "8082:80"  # 8081 yerine 8082 kullan

# PHPMyAdmin'i yeniden başlat
docker-compose restart phpmyadmin

# Upload limit hatası için PHP ayarlarını kontrol et
# docker/phpmyadmin/php.ini dosyasında:
# upload_max_filesize = 200M
# post_max_size = 200M

# Session hatası için configuration'ı kontrol et
# docker/phpmyadmin/config.user.inc.php dosyasında session ayarları
```

#### Yaygın PHPMyAdmin Hataları ve Çözümleri

**1. Upload Limit Hatası:**
```
Warning: POST Content-Length of X bytes exceeds the limit of Y bytes
```
**Çözüm:** `docker/phpmyadmin/php.ini` dosyasında `upload_max_filesize` ve `post_max_size` değerlerini artırın.

**2. Session Hatası:**
```
Error during session start; please check your PHP and/or webserver log file
```
**Çözüm:** `docker/phpmyadmin/config.user.inc.php` dosyasında session ayarlarını kontrol edin.

**3. Headers Already Sent Hatası:**
```
Cannot modify header information - headers already sent
```
**Çözüm:** PHP configuration dosyalarında output buffering'i etkinleştirin.

**4. Memory Limit Hatası:**
```
Fatal error: Allowed memory size of X bytes exhausted
```
**Çözüm:** `memory_limit` değerini artırın (örn: 2048M).

### Laravel App Sorunları

```bash
# App loglarını kontrol et
docker-compose logs app

# Permission sorunları için
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache

# Composer cache temizle
docker-compose exec app composer clear-cache

# Laravel cache temizle
docker-compose exec app php artisan optimize:clear
```

### Memory Sorunları

```bash
# Docker Desktop'ta memory limitini artır (en az 4GB)
# Docker Desktop > Settings > Resources > Memory

# Container'ları yeniden başlat
docker-compose down
docker-compose up -d
```

### Port Çakışması

```bash
# Kullanılan portları kontrol et
lsof -i :8000
lsof -i :8081
lsof -i :3311

# Port'u kullanan process'i durdur
kill -9 <PID>

# Veya docker-compose.yml'da port değiştir
```

## 💡 Geliştirme İpuçları

### Performance Optimizasyonu

```bash
# OPcache'i etkinleştir
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Redis cache kullan
# .env dosyasında:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Debug Araçları

```bash
# Clockwork (Performance monitoring)
# http://localhost:8000/__clockwork

# Laravel Debugbar
# Otomatik olarak aktif (development)

# Query loglarını etkinleştir
docker-compose exec app php artisan query:analyze
```

### Hot Reload

```bash
# Frontend için
npm run watch

# Laravel için (development)
# .env dosyasında APP_DEBUG=true
```

### Log Dosyaları

```bash
# Laravel logları
docker-compose exec app tail -f storage/logs/laravel.log

# Nginx logları
docker-compose logs -f nginx

# MySQL logları
docker-compose logs -f mysql

# PHP error logları
docker-compose exec app tail -f storage/logs/php_errors.log
```

## 📁 Dosya Yapısı

```
phportal/
├── docker-compose.yml          # Ana Docker yapılandırması
├── Dockerfile                  # PHP 8.3 container yapılandırması
├── docker/                     # Docker yapılandırma dosyaları
│   ├── nginx/                  # Nginx yapılandırması
│   ├── php/                    # PHP yapılandırması
│   ├── mysql/                  # MySQL yapılandırması
│   ├── redis/                  # Redis yapılandırması
│   ├── mongodb/                # MongoDB yapılandırması
│   ├── phpmyadmin/             # PHPMyAdmin yapılandırması
│   ├── supervisor/             # Supervisor yapılandırması
│   └── cron/                   # Cron job yapılandırması
├── DOCKER_SETUP.md             # Bu dosya
└── DOCKER_README.md            # Kısa kullanım kılavuzu
```

## 🔄 Güncelleme ve Bakım

### Düzenli Bakım

```bash
# Haftalık cache temizleme
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Aylık log temizleme
docker-compose exec app php artisan logs:clean --days=30

# Composer dependencies güncelleme
docker-compose exec app composer update

# Node dependencies güncelleme
npm update
```

### Backup Stratejisi

```bash
# Veritabanı yedekleme (günlük)
docker-compose exec mysql mysqldump -u phportal -p phportal > backup_$(date +%Y%m%d).sql

# MongoDB yedekleme
docker-compose exec mongodb mongodump -u phportal -p phportal123 --db phportal

# Redis yedekleme
docker-compose exec redis redis-cli BGSAVE
```

## 📞 Destek

Sorun yaşadığınızda:

1. **Logları kontrol edin**: `docker-compose logs -f [service-name]`
2. **Container durumunu kontrol edin**: `docker-compose ps`
3. **Cache'leri temizleyin**: `docker-compose exec app php artisan optimize:clear`
4. **Servisleri yeniden başlatın**: `docker-compose restart`

---

**Son Güncelleme**: 20 Ağustos 2025  
**Versiyon**: 1.0  
**Docker Compose**: 3.8  
**PHP**: 8.3  
**Laravel**: 12
