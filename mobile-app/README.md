# PH Portal Mobil Uygulaması (React Native)

Bu klasör PH Portal backend’i ile konuşan Expo tabanlı React Native mobil uygulamasının iskeletini içerir. Uygulama 3 ana modüle odaklanır:

1. **Login** – Laravel oturum/Token bazlı kimlik doğrulama
2. **Seri Numaraları** – Seri listesi, fiyat güncelleme, sevk başlatma
3. **QR ile Satış** – QR/karekod okuyarak hızlı satış akışını tetikleme

## Kurulum

```bash
cd mobile-app
npm install
npx expo start
```

### Çevresel Değerler

`APP_BASE_URL` için `.env` dosyası oluşturun (Expo 51 için `.env` yerine `app.config.js` veya `EXPO_PUBLIC_*` değişkeni kullanılabilir):

```bash
EXPO_PUBLIC_API_URL=https://sizin-domaininiz.test
```

## Mimarî

- **Navigasyon**: Stack + Bottom Tabs
- **Durum Yönetimi**: Zustand (`src/store/useAuthStore.ts`)
- **HTTP**: Axios (`src/hooks/useApi.tsx`)
- **Giriş**: SecureStore ile token saklama
- **Seri İşlemleri**: `SerialListScreen` API çağrıları (`/stockcard/serialList`, `/stockcard/singlepriceupdate`, `/transfer/store`)
- **QR Satış**: `expo-barcode-scanner`, backend’de var olan `stock/check` endpoint’i

## Yapılması Gerekenler

- Laravel backend’inde API endpoint’leri için gerekli izin/middleware düzenlemeleri
- Üretim için ikon/splash görselleri (`mobile-app/assets/`)
- Gerekiyorsa token tabanlı kimlik doğrulama (Sanctum/Passport)

## Klasör Yapısı

```
mobile-app/
├─ App.tsx
├─ app.json
├─ package.json
├─ tsconfig.json
├─ src/
│  ├─ components/
│  ├─ hooks/
│  ├─ navigation/
│  ├─ screens/
│  ├─ services/
│  └─ store/
└─ assets/
```

> Not: Kod temel scaffolding sağlar; gerçek endpoint URL’leri/yanıt formatlarına göre küçük uyarlamalar yapmanız gerekebilir.

