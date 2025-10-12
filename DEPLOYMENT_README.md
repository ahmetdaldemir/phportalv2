# 🚀 Deployment Rehberi - irepair.com.tr

## Hızlı Başlangıç

### 🤖 Otomatik Deployment (GitHub Actions)

1. **GitHub Secrets'ı Ekleyin**:
   - Repository → Settings → Secrets → Actions
   - `FTP_PASSWORD` ekleyin: `@198711Ad@`

2. **Kodu Push Edin**:
```bash
git add .
git commit -m "feat: yeni özellik"
git push origin main
```

3. **Deployment Otomatik Başlar**:
   - GitHub → Actions sekmesinden izleyin

### 🖱️ Manuel Deployment

#### Windows:
```powershell
.\deploy-to-production.ps1
```

#### Linux/Mac:
```bash
chmod +x deploy-to-production.sh
./deploy-to-production.sh
```

---

## 📋 FTP Bilgileri

```
FTP Host: 153.92.220.80
FTP User: u529018053.irepair.com.tr  
FTP Pass: @198711Ad@ (GitHub Secrets'da)
FTP Dir:  /public_html
Site URL: https://irepair.com.tr
```

---

## 🔧 İlk Kurulum

### 1. GitHub Repository Oluştur

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/yourusername/phportal.git
git push -u origin main
```

### 2. GitHub Secrets Ekle

**Gerekli Secrets**:
| Secret Name | Value |
|-------------|-------|
| `FTP_PASSWORD` | `@198711Ad@` |

**Optional Secrets** (SSH deployment için):
| Secret Name | Value |
|-------------|-------|
| `SSH_HOST` | `irepair.com.tr` |
| `SSH_USERNAME` | `u529018053` |
| `SSH_PASSWORD` | `@198711Ad@` |
| `DB_HOST` | `localhost` |
| `DB_DATABASE` | `u529018053_phportal` |
| `DB_USERNAME` | `u529018053_phportal` |
| `DB_PASSWORD` | `[your-db-password]` |
| `APP_KEY` | `base64:...` (php artisan key:generate --show) |

### 3. İlk Deployment

```bash
git push origin main
# GitHub Actions otomatik deployment yapar
```

### 4. Production'da İlk Setup

**SSH ile bağlanın**:
```bash
ssh u529018053@irepair.com.tr
cd public_html
```

**Komutlar**:
```bash
# .env dosyasını düzenleyin (FTP veya SSH)
# env.production.example dosyasını .env olarak kopyalayın

# Storage izinleri
chmod -R 755 storage bootstrap/cache

# Migrations
php artisan migrate --force

# Storage link
php artisan storage:link

# Optimize
php artisan optimize
```

---

## 📊 Deployment Workflow

```
Developer → Git Push → GitHub Actions → Build → FTP Upload → Production
```

**Timeline**:
1. **00:00** - Git push
2. **00:01** - GitHub Actions başlar
3. **00:02** - PHP & Composer setup
4. **00:03** - NPM build
5. **00:04** - Laravel optimize
6. **00:05** - FTP upload
7. **00:07** - Deployment complete

**Toplam Süre**: ~7 dakika

---

## 🔍 Troubleshooting

### Deployment Başarısız Olursa

1. **GitHub Actions Logs**:
   - Actions → Failed workflow → View logs

2. **FTP Connection Test**:
```bash
ftp 153.92.220.80
# User: u529018053.irepair.com.tr
# Pass: @198711Ad@
```

3. **Site Check**:
```bash
curl -I https://irepair.com.tr
```

4. **Laravel Logs**:
   - FTP ile `storage/logs/laravel.log` kontrol edin

---

## 🎯 Deployment Checklist

### Pre-Deployment
- [ ] Testler geçiyor
- [ ] .env.production.example güncel
- [ ] Migration'lar test edildi
- [ ] Build local'de çalışıyor
- [ ] Database backup alındı

### Post-Deployment
- [ ] Site açılıyor
- [ ] Login çalışıyor
- [ ] Database bağlantısı OK
- [ ] Assets yükleniyor
- [ ] Queue çalışıyor (eğer varsa)

---

## 📞 İletişim

**Sorun mu yaşıyorsunuz?**
- GitHub Issues açın
- Deployment logs'u kontrol edin
- SSH ile production'a bağlanıp debug edin

---

**Versiyon**: 1.0  
**Son Güncelleme**: 12 Ekim 2025

