# ML Eğitim Sorun Giderme Kılavuzu

## 🔍 "ML Beklemede" Durumunda Yapılacaklar

### 1. Browser Console Kontrolü

**Adımlar:**
1. Sayfada sağ tık → **İncele (Inspect)**
2. **Console** sekmesine git
3. Aşağıdaki mesajları ara:

#### ✅ Başarılı Durum:
```
Dashboard app mounted
Chart.js available: true
Sellers loaded from backend: X
Loading stock turnover...
Stock turnover response: {success: true, data: [...]}
🤖 ML Model eğitimi başlatılıyor...
Epoch 0: loss = 0.XXXX
Epoch 10: loss = 0.XXXX
...
Epoch 40: loss = 0.XXXX
✅ ML Model başarıyla eğitildi!
```

#### ❌ Hatalı Durum:
```
❌ TensorFlow.js yüklenemedi
❌ stockMLPredictor is undefined
❌ Stok verileri yüklenemedi
❌ ML eğitim hatası: ...
```

---

### 2. TensorFlow.js Kontrolü

**Console'da test edin:**
```javascript
// TensorFlow.js yüklü mü?
console.log(typeof tf);
// "object" olmalı, "undefined" ise yüklenmemiş

// ML Predictor var mı?
console.log(typeof window.stockMLPredictor);
// "object" olmalı

// Model oluşturulabilir mi?
const testModel = tf.sequential();
console.log('TensorFlow çalışıyor!');
```

---

### 3. Stok Verisi Kontrolü

**Console'da kontrol:**
```javascript
// Vue instance'a eriş
const app = document.getElementById('dashboard-app').__vueParentComponent;

// Stok verisi var mı?
console.log('Stok sayısı:', app.ctx.stockTurnover.length);
// Minimum 10 olmalı

// ML durumu
console.log('ML Status:', app.ctx.mlStatus);
```

---

### 4. Network Kontrolü

**Adımlar:**
1. İncele → **Network** sekmesi
2. Sayfayı yenile (F5)
3. Şunları kontrol et:

#### API Çağrıları:
- ✅ `/api/dashboard/stock-turnover` → Status: 200
- ✅ `/api/dashboard/stock-turnover-ai` → Status: 200

#### Scripts:
- ✅ `tf.min.js` (TensorFlow.js) → Status: 200
- ✅ `ml-predictor.js` → Status: 200

---

## 🛠️ Yaygın Sorunlar ve Çözümleri

### Sorun 1: TensorFlow.js Yüklenmedi

**Belirtiler:**
- Console: `tf is not defined`
- ML durumu: Beklemede

**Çözüm:**
```html
<!-- CDN değiştir -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.11.0/dist/tf.min.js"></script>

<!-- VEYA alternatif CDN -->
<script src="https://unpkg.com/@tensorflow/tfjs@4.11.0/dist/tf.min.js"></script>
```

---

### Sorun 2: ML Predictor Script Hatası

**Belirtiler:**
- Console: `stockMLPredictor is undefined`
- Script yüklenmiş ama çalışmıyor

**Çözüm:**
```bash
# Script dosyasını kontrol et
ls public/assets/js/ml-predictor.js

# Doğru yolda mı?
{{ asset('assets/js/ml-predictor.js') }}
```

**Console'da manuel yükle:**
```javascript
const script = document.createElement('script');
script.src = '/assets/js/ml-predictor.js';
document.head.appendChild(script);
```

---

### Sorun 3: Yetersiz Veri

**Belirtiler:**
- Console: `Yeterli veri yok, model eğitilemedi`
- Stok sayısı < 10

**Çözüm:**
```sql
-- Veritabanında stok kontrolü
SELECT COUNT(*) FROM stock_cards 
WHERE company_id = YOUR_COMPANY_ID;

-- Son 90 günde satış var mı?
SELECT COUNT(*) FROM sales 
WHERE company_id = YOUR_COMPANY_ID
  AND created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

### Sorun 4: CORS veya CDN Bloke

**Belirtiler:**
- Network: tf.min.js → Failed
- Console: CORS error

**Çözüm:**
```javascript
// Local TensorFlow.js kullan
// 1. İndir:
npm install @tensorflow/tfjs

// 2. Public klasöre kopyala:
cp node_modules/@tensorflow/tfjs/dist/tf.min.js public/assets/js/

// 3. Script'i güncelle:
<script src="{{ asset('assets/js/tf.min.js') }}"></script>
```

---

### Sorun 5: Eğitim Başladı Ama Bitmedi

**Belirtiler:**
- "ML Eğitiliyor..." sonsuza kadar
- Progress takılı kaldı

**Çözüm:**
```javascript
// Console'da manuel eğitim başlat
const app = document.getElementById('dashboard-app').__vueParentComponent;
const stockData = app.ctx.stockTurnover;

await window.stockMLPredictor.trainModel(stockData)
    .then(success => {
        console.log('Eğitim tamamlandı:', success);
        app.ctx.mlStatus.isTrained = true;
        app.ctx.mlStatus.isTraining = false;
    })
    .catch(err => console.error('Hata:', err));
```

---

## 📊 Performans Metrikleri

### Normal Eğitim Süreleri:

| Stok Sayısı | Eğitim Süresi | Epoch | Performans |
|-------------|---------------|-------|------------|
| 10-50       | 1-2 sn       | 50    | Hızlı      |
| 50-100      | 2-3 sn       | 50    | Normal     |
| 100-500     | 3-5 sn       | 50    | İyi        |
| 500+        | 5-10 sn      | 50    | Yavaş      |

### Browser Gereksinimleri:

✅ **Desteklenen:**
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

❌ **Desteklenmeyen:**
- IE 11 ve öncesi
- Çok eski mobile browserlar

---

## 🔧 Manuel Test

### Console'da Adım Adım Test:

```javascript
// 1. TensorFlow.js var mı?
console.log('TF Version:', tf.version.tfjs);

// 2. Model oluştur
const model = tf.sequential();
model.add(tf.layers.dense({units: 10, inputShape: [4]}));
model.compile({optimizer: 'adam', loss: 'meanSquaredError'});
console.log('Model oluşturuldu ✅');

// 3. Örnek eğitim
const xs = tf.tensor2d([[1,2,3,4], [5,6,7,8]]);
const ys = tf.tensor2d([[10], [20]]);
await model.fit(xs, ys, {epochs: 10});
console.log('Eğitim başarılı ✅');

// 4. Tahmin yap
const prediction = model.predict(tf.tensor2d([[2,3,4,5]]));
prediction.print();
console.log('Tahmin yapıldı ✅');
```

---

## 🎯 Beklenen Console Çıktısı

### Başarılı Eğitim:
```
Dashboard app mounted
Chart.js available: true
Sellers loaded from backend: 5
Loading stock turnover...
Stock turnover response: {success: true, data: Array(45)}
🤖 ML Model eğitimi başlatılıyor...
Epoch 0: loss = 12345.6789
Epoch 10: loss = 8234.5678
Epoch 20: loss = 4567.8901
Epoch 30: loss = 2345.6789
Epoch 40: loss = 1234.5678
✅ ML Model başarıyla eğitildi!
[Notification] ML Model başarıyla eğitildi! 🤖
```

### Loss (Kayıp) Değeri:
- **İlk epoch**: Yüksek (10000+)
- **Ortalar**: Düşüyor (5000-2000)
- **Son epoch**: Düşük (1000-500)
- Sürekli düşüyorsa → ✅ Öğreniyor
- Sabit kalıyorsa → ⚠️ Problematik

---

## 🚀 Hızlı Çözüm (Quick Fix)

### Cache Temizle + Hard Reload:

**Chrome/Edge:**
```
Ctrl + Shift + Delete
→ Cached images and files (Son 1 saat)
→ Clear data
→ Ctrl + F5 (Hard reload)
```

**Firefox:**
```
Ctrl + Shift + Delete
→ Cache
→ Clear
→ Ctrl + Shift + R
```

### Script Önceliğini Değiştir:

```html
<!-- ÖNCE TensorFlow.js -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.11.0/dist/tf.min.js"></script>

<!-- SONRA ML Predictor -->
<script src="{{ asset('assets/js/ml-predictor.js') }}"></script>

<!-- EN SON Vue.js App -->
<script>
    const { createApp } = Vue;
    createApp({...}).mount('#dashboard-app');
</script>
```

---

## 📞 İleri Seviye Debug

### TensorFlow.js Debug Mode:

```javascript
// Detaylı log aktif et
tf.ENV.set('DEBUG', true);

// Backend bilgisi
console.log('TF Backend:', tf.getBackend());
// "webgl" veya "cpu" olmalı

// Memory kullanımı
console.log('Memory:', tf.memory());
```

### Vue.js DevTools:

1. Chrome Extension yükle: **Vue.js devtools**
2. Components → dashboard-app
3. `mlStatus` state'ini izle
4. Gerçek zamanlı değişiklikleri gör

---

## ✅ Başarılı Kurulum Checklist

- [ ] TensorFlow.js CDN yüklü
- [ ] ml-predictor.js dosyası var
- [ ] Console'da hata yok
- [ ] Stok verisi >= 10
- [ ] API response başarılı
- [ ] Eğitim başladı
- [ ] Loss değeri düşüyor
- [ ] "ML Aktif" göstergesi yeşil
- [ ] Notification göründü

---

**Sorun devam ediyorsa:**
1. Console screenshot'u al
2. Network tab screenshot'u al
3. `mlStatus` objesini konsola yazdır
4. Veri varsa destek ekibine ilet

