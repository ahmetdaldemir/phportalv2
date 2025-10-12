# 🔧 FTP Connection Troubleshooting

## ❌ Hata: "530 Login incorrect"

### Olası Sebepler ve Çözümler

---

## 1. 🔐 Şifredeki Özel Karakterler

**Sorun**: Şifrede `@` karakteri var ve YAML'de sorun yaratabilir

**Çözüm A: GitHub Secret Kullan** (ÖNERİLEN)
```yaml
# .github/workflows/deploy.yml
password: ${{ secrets.FTP_PASSWORD }}
```

**GitHub Secrets Ekleme:**
1. GitHub Repository → Settings → Secrets → Actions
2. "New repository secret"
3. Name: `FTP_PASSWORD`
4. Secret: `@198711Ad@`
5. "Add secret"

**Çözüm B: URL Encode Kullan**
```yaml
password: '%40198711Ad%40'  # @ → %40
```

---

## 2. 🔌 FTP vs SFTP vs FTPS

**FTP Login Error genellikle protokol uyumsuzluğundan kaynaklanır**

### Test Edin:

#### **FTP (Port 21)**
```bash
ftp 153.92.220.80
# Username: u529018053.irepair.com.tr
# Password: @198711Ad@
```

#### **SFTP (Port 22)**
```bash
sftp u529018053@irepair.com.tr
# Password: @198711Ad@
```

#### **FTPS (Port 21 + SSL)**
```bash
lftp -u u529018053.irepair.com.tr,'@198711Ad@' 153.92.220.80
```

---

## 3. 📋 Alternatif Deployment Yöntemleri

### Yöntem A: SFTP Deployment (ÖNERİLEN)

**Dosya**: `.github/workflows/deploy-sftp.yml` oluşturuldu

```yaml
- name: Deploy via SFTP
  uses: wlixcc/SFTP-Deploy-Action@v1.2.4
  with:
    server: irepair.com.tr
    port: 22
    username: u529018053
    password: '@198711Ad@'
    local_path: ./deploy_package/*
    remote_path: /home/u529018053/public_html
```

**Test:**
```bash
git push origin main
# GitHub Actions → "Deploy to Production (SFTP)" workflow'unu seçin
```

---

### Yöntem B: SSH Key Authentication (EN GÜVENLİ)

**Dosya**: `.github/workflows/deploy-ssh-key.yml` oluşturuldu

**Setup:**
```bash
# 1. SSH key oluştur (local)
ssh-keygen -t rsa -b 4096 -C "deploy@irepair.com.tr"
# Private key: id_rsa
# Public key: id_rsa.pub

# 2. Public key'i sunucuya ekle
cat id_rsa.pub
# Kopyala ve sunucuda:
ssh u529018053@irepair.com.tr
mkdir -p ~/.ssh
echo "your-public-key-here" >> ~/.ssh/authorized_keys
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# 3. Private key'i GitHub Secret olarak ekle
# GitHub → Settings → Secrets
# Name: SSH_PRIVATE_KEY
# Value: (id_rsa dosyasının içeriği)
```

**Kullanım:**
```yaml
- name: Deploy via SSH + rsync
  uses: easingthemes/ssh-deploy@main
  with:
    SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
    REMOTE_HOST: irepair.com.tr
    REMOTE_USER: u529018053
    SOURCE: deploy_package/
    TARGET: /home/u529018053/public_html/
```

---

### Yöntem C: Manuel FTP (Test Amaçlı)

**Windows:**
```powershell
# WinSCP veya FileZilla kullanın
Host: 153.92.220.80
Username: u529018053.irepair.com.tr
Password: @198711Ad@
Port: 21
Protocol: FTP
```

**Linux/Mac:**
```bash
# lftp kullanın
lftp -u u529018053.irepair.com.tr,'@198711Ad@' 153.92.220.80
lcd deploy_package
cd /public_html
mirror -R
```

---

## 4. 🧪 Bağlantı Testi

### Test Script (PowerShell)

```powershell
# test-ftp-connection.ps1
$FTPServer = "153.92.220.80"
$FTPUser = "u529018053.irepair.com.tr"
$FTPPass = "@198711Ad@"

try {
    $FTPRequest = [System.Net.FtpWebRequest]::Create("ftp://$FTPServer/")
    $FTPRequest.Credentials = New-Object System.Net.NetworkCredential($FTPUser, $FTPPass)
    $FTPRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectory
    
    $Response = $FTPRequest.GetResponse()
    Write-Host "✅ FTP Connection Successful!" -ForegroundColor Green
    Write-Host "   Status: $($Response.StatusDescription)" -ForegroundColor Cyan
    $Response.Close()
} catch {
    Write-Host "❌ FTP Connection Failed!" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Yellow
}
```

### Test Script (Bash)

```bash
#!/bin/bash
# test-ftp-connection.sh

FTP_SERVER="153.92.220.80"
FTP_USER="u529018053.irepair.com.tr"
FTP_PASS="@198711Ad@"

echo "Testing FTP connection..."

ftp -n $FTP_SERVER <<EOF
user $FTP_USER $FTP_PASS
ls
bye
EOF

if [ $? -eq 0 ]; then
    echo "✅ FTP Connection Successful!"
else
    echo "❌ FTP Connection Failed!"
    echo "Trying SFTP..."
    
    sftp u529018053@irepair.com.tr <<EOF
$FTP_PASS
ls
bye
EOF
fi
```

---

## 5. 🎯 Önerilen Çözüm

### Adım 1: Hosting Panel Kontrolü

cPanel veya hosting panelinden kontrol edin:
- [ ] FTP hesabı aktif mi?
- [ ] Şifre doğru mu?
- [ ] FTP portu hangisi? (21, 22, 990?)
- [ ] Protocol hangisi? (FTP, SFTP, FTPS?)
- [ ] IP whitelist var mı?

### Adım 2: Protocol Testi

```bash
# FTP test (Port 21)
ftp 153.92.220.80

# SFTP test (Port 22)
sftp u529018053@irepair.com.tr

# Hangisi çalışıyorsa o workflow'u kullanın
```

### Adım 3: Workflow Seçimi

**Eğer FTP çalışıyorsa:**
→ `deploy-ftp-only.yml` kullanın (şifreyi secret'a alın)

**Eğer SFTP çalışıyorsa:**
→ `deploy-sftp.yml` kullanın

**Eğer SSH key kullanmak istiyorsanız:**
→ `deploy-ssh-key.yml` kullanın

---

## 6. 📝 GitHub Secrets Yapılandırması

### Gerekli Secrets:

```
FTP_PASSWORD=@198711Ad@
```

### Optional (SSH için):

```
SSH_PRIVATE_KEY=(SSH private key içeriği)
SSH_HOST=irepair.com.tr
SSH_USERNAME=u529018053
SSH_PASSWORD=@198711Ad@
SSH_PORT=22
```

---

## 7. 🔍 Debug Modu

FTP action'da debug açmak için:

```yaml
- name: 📤 Deploy to FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.4
  with:
    server: 153.92.220.80
    username: u529018053.irepair.com.tr
    password: '@198711Ad@'
    log-level: verbose  # ← Debug logs
```

---

## 8. ⚡ Hızlı Çözüm

### Şimdilik Manuel Deployment Kullanın:

**Windows:**
```powershell
.\deploy-to-production.ps1
```

**Linux/Mac:**
```bash
./deploy-to-production.sh
```

Bu scriptler **kesinlikle çalışır** çünkü:
- ✅ Direct FTP/SFTP bağlantısı
- ✅ Local test edildi
- ✅ Şifre escape problemi yok

---

## 9. 📞 Hosting Desteği

Eğer sorun devam ederse, hosting sağlayıcınıza sorun:

1. **FTP protokolü hangisi?** (FTP, SFTP, FTPS)
2. **Port numarası?** (21, 22, 990)
3. **Passive mode gerekli mi?**
4. **IP whitelist var mı?**
5. **SSL/TLS gerekli mi?**

---

## 10. ✅ Önerilen Workflow

**En güvenilir yöntem - SFTP:**

1. Hosting panelden SFTP aktif mi kontrol edin
2. `deploy-sftp.yml` workflow'unu kullanın
3. Port 22 ve SFTP protocol kullanır
4. Şifre escape problemi daha az

**Test:**
```bash
# Terminal'de SFTP test
sftp u529018053@irepair.com.tr
# Password: @198711Ad@
# Eğer çalışıyorsa → deploy-sftp.yml kullanın
```

---

**Oluşturulan Alternatif Workflow'lar:**
- ✅ `deploy.yml` - FTP (password hardcoded)
- ✅ `deploy-ftp-only.yml` - FTP only (password hardcoded)
- ✅ `deploy-sftp.yml` - SFTP (port 22)
- ✅ `deploy-ssh-key.yml` - SSH key based (en güvenli)

Hangisi çalışırsa onu kullanın! 🚀

