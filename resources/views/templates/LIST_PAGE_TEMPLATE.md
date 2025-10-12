# Professional List Page Template
## Modern, Compact UI/UX Design for All List Pages

### 📦 CSS Import
```blade
@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection
```

### 🎨 HTML Structure

#### 1. Page Header
```blade
<div class="page-header mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="bx bx-[ICON] display-4 text-primary"></i>
        </div>
        <div>
            <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600;">
                <i class="bx bx-[ICON] me-2"></i>
                [PAGE TITLE]
            </h2>
            <p class="mb-0" style="font-size: 0.9rem;">[PAGE DESCRIPTION]</p>
        </div>
    </div>
</div>
```

#### 2. Card with Filters
```blade
<div class="card professional-card">
    <div class="card-header professional-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                    <i class="bx bx-filter me-2"></i>
                    Filtreler
                </h6>
                <small class="text-muted">[FILTER DESCRIPTION]</small>
            </div>
            <a href="[CREATE_ROUTE]" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i>
                [ADD_BUTTON_TEXT]
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <form @submit.prevent="searchItems" class="compact-filter-form">
            <!-- ROW 1: Main Filters (use col-lg-3, col-lg-2 for 4-6 items) -->
            <div class="row g-2 mb-2">
                <div class="col-lg-3 col-md-4">
                    <div class="compact-filter-group">
                        <label class="compact-label">
                            <i class="bx bx-[ICON]"></i> [LABEL]
                        </label>
                        <select v-model="searchForm.field1" class="form-select form-select-sm compact-select">
                            <option value="">[ALL_OPTION]</option>
                        </select>
                    </div>
                </div>
                <!-- Repeat for other filters -->
            </div>
            
            <!-- ROW 2: Secondary Filters + Buttons -->
            <div class="row g-2 mb-3">
                <div class="col-lg-3 col-md-4">
                    <div class="compact-filter-group">
                        <label class="compact-label">
                            <i class="bx bx-[ICON]"></i> [LABEL]
                        </label>
                        <input 
                            type="text" 
                            v-model="searchForm.field2" 
                            class="form-control form-control-sm compact-input" 
                            placeholder="[PLACEHOLDER]..."
                        >
                    </div>
                </div>
                
                <!-- ACTION BUTTONS (Last Column) -->
                <div class="col-lg-3 col-md-12">
                    <div class="compact-filter-group">
                        <label class="compact-label d-none d-md-block invisible">Aksiyon</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bx bx-search me-1"></i>
                                @{{ loading ? 'Aranıyor...' : 'Ara' }}
                            </button>
                            <button type="button" @click="clearFilters" class="btn btn-outline-secondary btn-sm" title="Filtreleri Temizle">
                                <i class="bx bx-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="table-loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner-large"></div>
            <div class="loading-text">[ITEMS] yükleniyor...</div>
        </div>
    </div>
    
    <!-- Empty State -->
    <div v-if="!loading && items.length === 0" class="empty-state">
        <div class="empty-content">
            <i class="bx bx-[ICON] display-1 text-muted"></i>
            <h5 class="mt-3">[ITEM] bulunamadı</h5>
            <p class="text-muted">Arama kriterlerinize uygun [item] bulunamadı.</p>
        </div>
    </div>
    
    <!-- Table -->
    <div v-if="!loading && items.length > 0" class="table-responsive text-nowrap">
        <table class="table professional-table">
            <thead>
                <tr>
                    <th><i class="bx bx-[ICON] me-1"></i>[COLUMN 1]</th>
                    <th><i class="bx bx-[ICON] me-1"></i>[COLUMN 2]</th>
                    <!-- Add more columns -->
                    <th><i class="bx bx-cog me-1"></i>İşlemler</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0 professional-tbody">
                <tr v-for="item in items" :key="item.id">
                    <td>@{{ item.field1 }}</td>
                    <td>@{{ item.field2 }}</td>
                    <!-- Add more columns -->
                    <td>
                        <a :href="`/[route]/edit?id=${item.id}`" class="btn btn-sm btn-info" title="Düzenle">
                            <i class="bx bx-edit"></i>
                        </a>
                        <button @click="deleteItem(item.id)" class="btn btn-sm btn-danger" title="Sil">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### 🎯 Column Sizes Guide

**For 2-3 filters:** use `col-lg-4` or `col-lg-6`
**For 4-5 filters:** use `col-lg-3` or mix `col-lg-3` + `col-lg-2`  
**For 6+ filters:** use `col-lg-2` or distribute across 2 rows

### ✅ Required Classes

- **Page Container**: `container-xxl flex-grow-1 container-p-y`
- **Page Header**: `page-header mb-4`
- **Main Card**: `card professional-card`
- **Card Header**: `card-header professional-header`
- **Form**: `compact-filter-form`
- **Filter Group**: `compact-filter-group`
- **Label**: `compact-label`
- **Select**: `form-select form-select-sm compact-select`
- **Input**: `form-control form-control-sm compact-input`
- **Table**: `table professional-table`
- **Table Body**: `professional-tbody`

### 📋 Common Icons

- Shopping/Sale: `bx-shopping-bag`, `bx-cart`
- Invoice: `bx-receipt`, `bx-file`
- Stock: `bx-package`, `bx-box`
- Customer: `bx-user`, `bx-group`
- Phone: `bx-mobile`, `bx-mobile-alt`
- Technical: `bx-wrench`, `bx-cog`
- Transfer: `bx-transfer`, `bx-shuffle`
- Report: `bx-line-chart`, `bx-bar-chart`
- Settings: `bx-cog`, `bx-slider`
- Calendar: `bx-calendar`
- Money: `bx-money`, `bx-dollar`
- Search: `bx-search`
- Filter: `bx-filter`
- Edit: `bx-edit`
- Delete: `bx-trash`
- View: `bx-show`
- Plus: `bx-plus`
- Check: `bx-check`
- Close: `bx-x`

