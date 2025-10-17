# 📋 Table Page Framework - Kullanım Kılavuzu

## 🎯 Amaç
Tüm tablo sayfalarında aynı UI/UX'i kullanmak için ortak CSS framework'ü ve Blade component'i.

## 📁 Dosya Yapısı
```
public/assets/css/table-page-framework.css    # Ortak CSS
resources/views/components/table-page.blade.php # Ortak Component
resources/views/examples/                     # Örnek kullanımlar
```

## 🚀 Hızlı Başlangıç

### 1. Temel Kullanım
```php
@extends('layouts.admin')

@section('content')
    <x-table-page
        :title="'SAYFA BAŞLIĞI'"
        :subtitle="'Sayfa açıklaması'"
        :icon="'bx-grid-alt'"
    >
        {{-- İçerik buraya --}}
    </x-table-page>
@endsection
```

### 2. Tam Özellikli Kullanım
```php
<x-table-page
    :title="'SATIŞ LİSTESİ'"
    :subtitle="'Satış fiyatları ve kar zarar yönetimi'"
    :icon="'bx-shopping-bag'"
    :actions="[
        [
            'type' => 'link',
            'text' => 'YENİ SATIŞ',
            'icon' => 'bx-plus',
            'class' => 'btn-primary',
            'url' => route('sales.create')
        ],
        [
            'type' => 'button',
            'text' => 'Excel',
            'icon' => 'bx-download',
            'class' => 'btn-success',
            'onclick' => 'exportExcel()'
        ]
    ]"
>
    {{-- SLOTS --}}
</x-table-page>
```

## 🧩 Component Parametreleri

### Ana Parametreler
| Parametre | Tip | Açıklama | Varsayılan |
|-----------|-----|----------|------------|
| `title` | string | Sayfa başlığı | 'Sayfa Başlığı' |
| `subtitle` | string | Sayfa açıklaması | 'Sayfa açıklaması' |
| `icon` | string | Başlık ikonu | 'bx-grid-alt' |
| `actions` | array | Header butonları | [] |

### Actions Array Yapısı
```php
:actions="[
    [
        'type' => 'link',           // 'link' veya 'button'
        'text' => 'YENİ KAYIT',     // Buton metni
        'icon' => 'bx-plus',        // İkon (opsiyonel)
        'class' => 'btn-primary',   // CSS sınıfı
        'url' => route('create'),   // Link için URL
        'onclick' => 'func()'       // Button için onclick
    ]
]"
```

## 🎨 CSS Sınıfları

### 1. Page Header
```html
<div class="table-page-header">
    <div class="header-content">
        <div class="header-left">
            <div class="header-icon">
                <i class="bx bx-shopping-bag"></i>
            </div>
            <div class="header-text">
                <h2>BAŞLIK</h2>
                <p>Açıklama</p>
            </div>
        </div>
        <div class="header-actions">
            <!-- Butonlar -->
        </div>
    </div>
</div>
```

### 2. Filter Section
```html
<div class="table-page-filters">
    <div class="filter-header">
        <h6><i class="bx bx-filter me-2"></i>Filtreler</h6>
        <small>Açıklama</small>
    </div>
    <div class="filter-body">
        <div class="filter-row">
            <div class="filter-group">
                <label class="filter-label">
                    <i class="bx bx-search"></i> Arama
                </label>
                <input type="text" class="filter-input" placeholder="Ara...">
            </div>
        </div>
    </div>
</div>
```

### 3. Summary Cards
```html
<div class="table-page-summary">
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon primary">
                <i class="bx bx-receipt"></i>
            </div>
            <div class="card-value">100</div>
            <div class="card-label">Toplam</div>
        </div>
    </div>
</div>
```

### 4. Data Table
```html
<div class="table-page-table">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>KOLON 1</th>
                    <th>KOLON 2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Veri 1</td>
                    <td>Veri 2</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### 5. Pagination
```html
<div class="table-page-pagination">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <!-- Sayfa numaraları -->
        </ul>
    </nav>
    <div class="pagination-info">
        <small>1-10 / 100 kayıt</small>
    </div>
</div>
```

## 🎨 CSS Sınıf Referansı

### Filter Groups
| Sınıf | Açıklama |
|-------|----------|
| `.filter-group` | Normal genişlik |
| `.filter-group.compact` | Kompakt genişlik |
| `.filter-group.small` | Küçük genişlik |
| `.filter-group.auto` | Otomatik genişlik |

### Card Icons
| Sınıf | Renk |
|-------|------|
| `.card-icon.primary` | Mavi gradient |
| `.card-icon.success` | Yeşil gradient |
| `.card-icon.warning` | Turuncu gradient |
| `.card-icon.info` | Açık mavi gradient |

### Utility Classes
| Sınıf | Açıklama |
|-------|----------|
| `.text-compact` | Metin kısaltma |
| `.price-display` | Para formatı |
| `.badge-modern` | Modern badge |
| `.btn-modern` | Modern buton |

## 📱 Responsive Design

### Breakpoints
- **Desktop**: 768px+
- **Tablet**: 576px - 767px
- **Mobile**: < 576px

### Mobil Özellikler
- Filter'lar dikey sıralanır
- Tablo yatay kaydırılabilir
- Kartlar tek sütun
- Butonlar tam genişlik

## 🎭 Animasyonlar

### Fade In Animasyonları
```html
<div class="table-page-fade-in">                    <!-- Hemen -->
<div class="table-page-fade-in-delay-1">            <!-- 0.1s gecikme -->
<div class="table-page-fade-in-delay-2">            <!-- 0.2s gecikme -->
<div class="table-page-fade-in-delay-3">            <!-- 0.3s gecikme -->
```

## 🔧 Vue.js Entegrasyonu

### Temel Yapı
```javascript
createApp({
    delimiters: ['[[', ']]'], // Blade ile çakışmaması için
    data() {
        return {
            // Veriler
        }
    },
    methods: {
        // Metodlar
    }
}).mount('#app');
```

### Önemli Metodlar
```javascript
// Para formatı
formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount) + ' ₺';
}

// Tarih formatı
formatDate(date) {
    // Tarih formatlama logic'i
}

// Sayfa değiştirme
changePage(page) {
    this.searchForm.page = page;
    this.searchData();
}
```

## 📋 Örnek Sayfalar

### 1. Satış Listesi
```php
// resources/views/module/sale/index.blade.php
<x-table-page
    :title="'SATIŞ LİSTESİ'"
    :subtitle="'Satış fiyatları ve kar zarar yönetimi'"
    :icon="'bx-shopping-bag'"
    :actions="[
        ['type' => 'link', 'text' => 'YENİ SATIŞ', 'icon' => 'bx-plus', 'class' => 'btn-primary', 'url' => route('sales.create')],
        ['type' => 'button', 'text' => 'Excel', 'icon' => 'bx-download', 'class' => 'btn-success', 'onclick' => 'exportExcel()']
    ]"
>
    <!-- Slots -->
</x-table-page>
```

### 2. Stok Kartları
```php
// resources/views/module/stockcard/index.blade.php
<x-table-page
    :title="'STOK KARTLARI'"
    :subtitle="'Stok kartları yönetimi ve takibi'"
    :icon="'bx-package'"
    :actions="[
        ['type' => 'link', 'text' => 'YENİ STOK KARTI', 'icon' => 'bx-plus', 'class' => 'btn-primary', 'url' => route('stockcard.create')]
    ]"
>
    <!-- Slots -->
</x-table-page>
```

## 🚀 Migration Rehberi

### Mevcut Sayfayı Dönüştürme
1. **Eski CSS'i kaldır**
2. **Component'i dahil et**
3. **HTML yapısını güncelle**
4. **Vue.js kodunu koru**

### Adım Adım
```php
// 1. Eski yapıyı kaldır
// <div class="card professional-card">
//     <div class="card-header professional-header">
//         <!-- Eski header -->
//     </div>
// </div>

// 2. Yeni component'i kullan
<x-table-page
    :title="'YENİ BAŞLIK'"
    :subtitle="'Yeni açıklama'"
    :icon="'bx-grid-alt'"
>
    <!-- İçerik -->
</x-table-page>
```

## 🎯 Best Practices

### 1. Tutarlı İkonlar
- Header: `bx-grid-alt`, `bx-shopping-bag`, `bx-package`
- Filter: `bx-search`, `bx-tag`, `bx-calendar`
- Actions: `bx-plus`, `bx-edit`, `bx-trash`

### 2. Renk Paleti
- Primary: Mavi gradient
- Success: Yeşil gradient  
- Warning: Turuncu gradient
- Info: Açık mavi gradient

### 3. Responsive
- Mobilde filter'lar dikey
- Tablo yatay kaydırılabilir
- Kartlar tek sütun

## 🔍 Debug ve Test

### Console Logları
```javascript
console.log('Page loaded:', this.data);
console.log('Search params:', this.searchForm);
console.log('Pagination:', this.pagination);
```

### CSS Debug
```css
/* Element sınırlarını göster */
* { border: 1px solid red !important; }
```

## 📞 Destek

Sorunlar için:
1. Console hatalarını kontrol edin
2. CSS yüklenip yüklenmediğini kontrol edin
3. Vue.js delimiters'ı kontrol edin
4. Component parametrelerini kontrol edin

---

**Bu framework ile tüm tablo sayfalarınız aynı profesyonel görünüme sahip olacak!** 🎉
