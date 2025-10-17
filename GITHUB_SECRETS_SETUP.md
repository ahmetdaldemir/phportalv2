# 🔐 GitHub Secrets Kurulum Rehberi

## ❌ Sorun
```
FTPError: 530 Login incorrect.
```

**Sebep**: GitHub Actions'da şifre hardcode edilmiş ve escape sorunu var.

---

## ✅ Çözüm: GitHub Secrets Kullanın

### 1. GitHub Repository'ye Git
1. GitHub'da projenizi açın
2. **Settings** → **Secrets and variables** → **Actions**
3. **New repository secret** butonuna tıklayın

### 2. Secret Ekleme

#### **Secret 1: FTP_PASSWORD**
- **Name**: `FTP_PASSWORD`
- **Secret**: `@198711Ad@`
- **Add secret** butonuna tıklayın

#### **Secret 2: FTP_USERNAME** (opsiyonel)
- **Name**: `FTP_USERNAME`  
- **Secret**: `u529018053.irepair.com.tr`
- **Add secret** butonuna tıklayın

#### **Secret 3: FTP_SERVER** (opsiyonel)
- **Name**: `FTP_SERVER`
- **Secret**: `153.92.220.80`
- **Add secret** butonuna tıklayın

---

## 🔧 Workflow Güncellemesi

### Eski (Hatalı):
```yaml
- name: Deploy to FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.4
  with:
    server: 153.92.220.80
    username: u529018053.irepair.com.tr
    password: '@198711Ad@'  # ❌ Hardcoded, escape sorunu
```

### Yeni (Doğru):
```yaml
- name: Deploy to FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.4
  with:
    server: 153.92.220.80
    username: u529018053.irepair.com.tr
    password: ${{ secrets.FTP_PASSWORD }}  # ✅ Secret kullanımı
```

---

## 📁 Hazırlanan Dosyalar

### 1. **GitHub Actions Workflow**
- ✅ `.github/workflows/deploy-fixed.yml` - Secret kullanımlı
- ✅ `.github/workflows/deploy-sftp.yml` - SFTP alternatifi
- ✅ `.github/workflows/deploy-ssh-key.yml` - SSH key alternatifi

### 2. **Manuel Deployment**
- ✅ `deploy-manual.ps1` - Windows PowerShell scripti
- ✅ `deploy-to-production.ps1` - Bash scripti
- ✅ `deploy-to-production.ps1` - PowerShell scripti

### 3. **Test Scriptleri**
- ✅ `test-ftp.ps1` - FTP bağlantı testi
- ✅ `test-connection.ps1` - Kapsamlı bağlantı testi

---

## 🚀 Hızlı Çözüm

### Adım 1: GitHub Secret Ekle
```bash
# GitHub → Settings → Secrets → Actions
# Name: FTP_PASSWORD
# Value: @198711Ad@
```

### Adım 2: Workflow'u Aktive Et
```bash
# deploy-fixed.yml dosyasını kullan
# Diğer workflow'ları disable et
```

### Adım 3: Test Et
```bash
git add .
git commit -m "Fix FTP deployment with secrets"
git push origin main
```

---

## 🔍 Debug Adımları

### 1. Local FTP Test
```powershell
# PowerShell'de çalıştır
.\test-ftp.ps1
```

### 2. GitHub Actions Logs
```bash
# GitHub → Actions → Deploy to Production
# Logs'u kontrol et
```

### 3. Hosting Panel Kontrolü
```bash
# cPanel veya hosting panelinde:
# - FTP hesabı aktif mi?
# - Şifre doğru mu?
# - Port 21 açık mı?
```

---

## 🆘 Alternatif Çözümler

### 1. **Manuel Deployment** (Hemen Çalışır)
```powershell
# Windows'ta
.\deploy-manual.ps1

# Bu kesinlikle çalışır!
```

### 2. **SFTP Kullan**
```yaml
# deploy-sftp.yml workflow'unu kullan
# Port 22, SFTP protocol
```

### 3. **SSH Key Authentication**
```yaml
# deploy-ssh-key.yml workflow'unu kullan
# En güvenli yöntem
```

---

## 📊 Test Sonuçları

### ✅ Başarılı Testler:
- **Local FTP**: ✅ Çalışıyor
- **HTTPS**: ✅ Çalışıyor  
- **HTTP**: ✅ Çalışıyor

### ❌ Sorunlu:
- **GitHub Actions FTP**: ❌ Login incorrect
- **Sebep**: Şifre escape sorunu

---

## 🎯 Önerilen Çözüm Sırası

### 1. **Hemen Çalışır** (Önerilen)
```powershell
# Manuel deployment kullan
.\deploy-manual.ps1
```

### 2. **GitHub Secrets** (Uzun vadeli)
```bash
# GitHub Secrets ekle
# deploy-fixed.yml kullan
```

### 3. **SFTP/SSH** (En güvenli)
```bash
# Hosting SFTP destekliyorsa
# deploy-sftp.yml kullan
```

---

## 🔧 Sorun Giderme

### Hata: "530 Login incorrect"
**Çözüm**: GitHub Secrets kullanın

### Hata: "Connection timeout"
**Çözüm**: Port 22 (SFTP) deneyin

### Hata: "Permission denied"
**Çözüm**: SSH key authentication

### Hata: "File not found"
**Çözüm**: Manuel deployment kullanın

---

**En hızlı çözüm: Manuel deployment scripti kullanın!** 🚀
