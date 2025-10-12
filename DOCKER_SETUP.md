# 🐳 PHP Portal Docker Setup

Bu proje, Laravel 12 ve PHP 8.4 ile geliştirilmiş PHP Portal uygulaması için kapsamlı bir Docker ortamı sağlar.

## 📋 İçerik

- **PHP 8.4** - Laravel uygulaması için
- **MySQL 8.0** - Ana veritabanı (Port: 3310)
- **Redis 7** - Cache ve session yönetimi
- **MongoDB 7** - Activity log ve system log verileri
- **RabbitMQ 3** - Message queue sistemi
- **Nginx** - Web sunucusu
- **Laravel Horizon** - Queue yönetimi
- **Supervisor** - Process yönetimi

## 🚀 Hızlı Başlangıç

### 1. Gereksinimler

- Docker Desktop
- Docker Compose
- Git

### 2. Projeyi Başlatma

```bash
# Projeyi klonlayın
git clone <repository-url>
cd phportal

# Docker container'larını başlatın
docker-compose up -d

# Otomatik kurulum scriptini çalıştırın
chmod +x docker-start.sh
./docker-start.sh
```

### 3. Manuel Kurulum (Alternatif)

```bash
# Container'ları başlatın
docker-compose up -d

# Environment dosyasını kopyalayın
cp docker.env .env

# Composer bağımlılıklarını yükleyin
docker-compose exec app composer install

# Application key oluşturun
docker-compose exec app php artisan key:generate

# Migration'ları çalıştırın
docker-compose exec app php artisan migrate --force

# Database'i seed edin
docker-compose exec app php artisan db:seed --force

# Cache'leri temizleyin ve yeniden oluşturun
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize
```

## 🌐 Servis URL'leri

| Servis | URL | Port | Kullanıcı Adı | Şifre |
|--------|-----|------|---------------|-------|
| Web Uygulaması | http://localhost | 80 | - | - |
| MySQL | localhost:3310 | 3310 | phportal_user | phportal_password |
| Redis | localhost:6379 | 6379 | - | - |
| MongoDB | localhost:27017 | 27017 | phportal_user | phportal_password |
| RabbitMQ Management | http://localhost:15672 | 15672 | phportal_user | phportal_password |
| Laravel Horizon | http://localhost/horizon | 80 | - | - |

## 📁 Docker Yapısı

```
docker/
├── php/
│   └── local.ini              # PHP konfigürasyonu
├── nginx/
│   └── conf.d/
│       └── app.conf           # Nginx konfigürasyonu
├── mysql/
│   └── my.cnf                 # MySQL konfigürasyonu
├── redis/
│   └── redis.conf             # Redis konfigürasyonu
├── mongodb/
│   └── init.js                # MongoDB başlangıç scripti
├── supervisor/
│   └── supervisord.conf       # Supervisor konfigürasyonu
└── cron/
    └── laravel-cron           # Cron görevleri
```

## 🔧 Konfigürasyon

### PHP 8.4 Extensions

Aşağıdaki PHP extension'ları otomatik olarak yüklenir:

- **Database**: pdo_mysql, pdo_sqlite
- **Cache**: redis, memcached, apcu
- **NoSQL**: mongodb
- **Queue**: amqp (RabbitMQ)
- **Image Processing**: gd, imagick
- **Development**: xdebug
- **Utilities**: zip, bz2, gmp, intl, soap, xsl, ldap, imap

### MongoDB Activity Logs

MongoDB'de aşağıdaki collection'lar otomatik oluşturulur:

- `activity_logs` - Laravel activity log verileri
- `system_logs` - Sistem logları
- `error_logs` - Hata logları
- `access_logs` - Erişim logları
- `performance_logs` - Performans logları
- `realtime_logs` - Gerçek zamanlı loglar

### Redis Kullanımı

Redis farklı veritabanları için kullanılır:

- **DB 0**: Genel cache
- **DB 1**: Queue işlemleri
- **DB 2**: Laravel cache
- **DB 3**: Session verileri

## 🛠️ Docker Komutları

### Temel Komutlar

```bash
# Container'ları başlat
docker-compose up -d

# Container'ları durdur
docker-compose down

# Log'ları görüntüle
docker-compose logs -f

# Belirli bir servisin log'larını görüntüle
docker-compose logs -f app

# Container'lara erişim
docker-compose exec app bash
docker-compose exec mysql mysql -u phportal_user -p phportal
docker-compose exec redis redis-cli
docker-compose exec mongodb mongosh
```

### Laravel Komutları

```bash
# Artisan komutları
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Composer komutları
docker-compose exec app composer install
docker-compose exec app composer update
docker-compose exec app composer require package-name
```

### Veritabanı İşlemleri

```bash
# MySQL'e bağlan
docker-compose exec mysql mysql -u phportal_user -p phportal

# MongoDB'ye bağlan
docker-compose exec mongodb mongosh -u phportal_user -p phportal_password

# Redis'e bağlan
docker-compose exec redis redis-cli

# RabbitMQ Management'a eriş
# http://localhost:15672
```

## 🔍 Monitoring ve Debugging

### Laravel Horizon

Queue işlemlerini izlemek için:
- URL: http://localhost/horizon
- Dashboard'da job'ları, worker'ları ve performansı izleyebilirsiniz

### Supervisor

Process'leri izlemek için:
```bash
docker-compose exec app supervisorctl status
docker-compose exec app supervisorctl restart laravel-queue
```

### Log Dosyaları

```bash
# Laravel log'ları
docker-compose exec app tail -f storage/logs/laravel.log

# Supervisor log'ları
docker-compose exec app tail -f /var/log/supervisor/supervisord.log

# Nginx log'ları
docker-compose logs -f nginx
```

## 🔒 Güvenlik

### Environment Variables

Sensitive bilgileri `.env` dosyasında saklayın:

```bash
# Production için şifreleri değiştirin
DB_PASSWORD=your_secure_password
REDIS_PASSWORD=your_redis_password
MONGODB_PASSWORD=your_mongodb_password
RABBITMQ_PASSWORD=your_rabbitmq_password
```

### SSL/HTTPS

HTTPS için SSL sertifikalarını `docker/nginx/ssl/` klasörüne ekleyin ve nginx konfigürasyonundaki HTTPS bölümünü aktif edin.

## 🚀 Production Deployment

Production ortamı için:

1. **Environment'ı güncelleyin**:
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Güvenlik ayarlarını yapın**:
   - Güçlü şifreler kullanın
   - SSL sertifikaları ekleyin
   - Firewall kurallarını yapılandırın

3. **Performance optimizasyonu**:
   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

4. **Monitoring ekleyin**:
   - Laravel Telescope
   - Application monitoring tools
   - Log aggregation

## 🐛 Troubleshooting

### Yaygın Sorunlar

1. **Port çakışması**:
   ```bash
   # Port'ları kontrol edin
   netstat -tulpn | grep :80
   netstat -tulpn | grep :3310
   ```

2. **Permission sorunları**:
   ```bash
   # Storage klasörü izinlerini düzeltin
   docker-compose exec app chown -R phportal:phportal /var/www/storage
   docker-compose exec app chmod -R 775 /var/www/storage
   ```

3. **Database bağlantı sorunları**:
   ```bash
   # MySQL'i yeniden başlatın
   docker-compose restart mysql
   
   # Migration'ları yeniden çalıştırın
   docker-compose exec app php artisan migrate:fresh --seed
   ```

### Log Kontrolü

```bash
# Tüm servislerin log'larını kontrol edin
docker-compose logs

# Belirli bir servisin log'larını kontrol edin
docker-compose logs app
docker-compose logs mysql
docker-compose logs redis
```

## 📚 Ek Kaynaklar

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Horizon Documentation](https://laravel.com/docs/horizon)
- [MongoDB PHP Driver](https://docs.mongodb.com/php-library/current/)
- [Redis PHP Extension](https://github.com/phpredis/phpredis)

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

---

**Not**: Bu Docker setup'ı development ortamı için optimize edilmiştir. Production ortamı için ek güvenlik ve performance ayarları yapılması gerekir.
