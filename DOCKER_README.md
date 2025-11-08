# PhPortal Docker Setup

Bu proje PHP 8.3, Laravel 12, MySQL 8.0, Redis, RabbitMQ, MongoDB ve PHPMyAdmin içeren Docker ortamı ile çalışır.

## Servisler

- **Laravel App**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8081
- **RabbitMQ Management**: http://localhost:15672
- **MySQL**: localhost:3311
- **Redis**: localhost:6379
- **MongoDB**: localhost:27017

## Kurulum

### 1. Docker Compose ile başlatma

```bash
# Tüm servisleri başlat
docker-compose up -d

# Sadece belirli servisleri başlat
docker-compose up -d mysql redis nginx app
```

### 2. Laravel kurulumu

```bash
# Container'a bağlan
docker-compose exec app bash

# Composer dependencies yükle
composer install

# .env dosyasını kopyala
cp .env.example .env

# .env dosyasını düzenle (Docker ayarları için)
# DB_HOST=mysql
# REDIS_HOST=redis
# MONGODB_HOST=mongodb
# RABBITMQ_HOST=rabbitmq

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

### 3. Frontend assets derleme

```bash
# Node modules yükle
npm install

# Assets derle
npm run dev

# Production için
npm run build
```

## Veritabanı Erişimi

### MySQL
- **Host**: localhost
- **Port**: 3311
- **Database**: phportal
- **Username**: phportal
- **Password**: phportal123
- **Root Password**: root123

### PHPMyAdmin
- **URL**: http://localhost:8081
- **Username**: phportal
- **Password**: phportal123

### MongoDB
- **Host**: localhost
- **Port**: 27017
- **Database**: phportal
- **Username**: phportal
- **Password**: phportal123

### Redis
- **Host**: localhost
- **Port**: 6379
- **Password**: (yok)

### RabbitMQ
- **Host**: localhost
- **Port**: 5672
- **Management**: http://localhost:15672
- **Username**: phportal
- **Password**: phportal123

## Komutlar

### Servis yönetimi
```bash
# Tüm servisleri başlat
docker-compose up -d

# Servisleri durdur
docker-compose down

# Servisleri yeniden başlat
docker-compose restart

# Logları görüntüle
docker-compose logs -f

# Belirli servisin loglarını görüntüle
docker-compose logs -f app
```

### Laravel komutları
```bash
# Container'a bağlan
docker-compose exec app bash

# Migration çalıştır
docker-compose exec app php artisan migrate

# Cache temizle
docker-compose exec app php artisan cache:clear

# Queue worker başlat
docker-compose exec app php artisan queue:work

# Horizon başlat
docker-compose exec app php artisan horizon
```

### Veritabanı işlemleri
```bash
# MySQL'e bağlan
docker-compose exec mysql mysql -u phportal -p phportal

# Redis CLI
docker-compose exec redis redis-cli

# MongoDB shell
docker-compose exec mongodb mongosh -u phportal -p phportal123
```

## Troubleshooting

### Port çakışması
Eğer portlar kullanımdaysa, `docker-compose.yml` dosyasında port mapping'leri değiştirin.

### Permission hatası
```bash
# Storage klasörü için permission ayarla
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Memory hatası
Docker Desktop'ta memory limitini artırın (en az 4GB önerilir).

### Slow performance
```bash
# OPcache'i etkinleştir
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Development

### Hot reload için
```bash
# NPM watch mode
npm run dev

# Laravel Mix watch
npm run watch
```

### Debug için
- Clockwork: http://localhost:8000/__clockwork
- Laravel Debugbar otomatik olarak aktif

### Log dosyaları
```bash
# Laravel logları
docker-compose exec app tail -f storage/logs/laravel.log

# Nginx logları
docker-compose logs -f nginx

# MySQL logları
docker-compose logs -f mysql
```
