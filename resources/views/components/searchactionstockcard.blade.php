<div id="stockcard-filter-app">
    <form @submit.prevent="searchStockCards" class="filter-form">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bx bx-search me-1"></i>Stok Adı
                </label>
                <input 
                    type="text" 
                    class="form-control modern-input" 
                    placeholder="Stok adı giriniz..." 
                    v-model="filters.stockName"
                    @input="debouncedSearch"
                >
            </div>
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bx bx-package me-1"></i>Marka
                </label>
                <select 
                    v-model="filters.brand" 
                    class="form-select modern-select" 
                    @change="loadVersions"
                >
                    <option value="">Tüm Markalar</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                        @{{ brand.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bx bx-mobile me-1"></i>Model
                </label>
                <select 
                    v-model="filters.version" 
                    class="form-select modern-select"
                    :disabled="!filters.brand"
                >
                    <option value="">Tüm Modeller</option>
                    <option v-for="version in versions" :key="version.id" :value="version.id">
                        @{{ version.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bx bx-category me-1"></i>Kategori
                </label>
                <select 
                    v-model="filters.category" 
                    class="form-select modern-select"
                >
                    <option value="">Tüm Kategoriler</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        @{{ category.path }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bx bx-filter me-1"></i>İşlemler
                </label>
                <div class="d-flex gap-2">
                    <button 
                        type="submit" 
                        class="btn btn-primary btn-sm"
                        :disabled="loading.search"
                    >
                        <i class="bx bx-search me-1" v-if="!loading.search"></i>
                        <span class="spinner-border spinner-border-sm me-1" v-if="loading.search"></span>
                        @{{ loading.search ? 'Aranıyor...' : 'Ara' }}
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-outline-secondary btn-sm"
                        @click="clearFilters"
                    >
                        <i class="bx bx-x me-1"></i>Temizle
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.filter-form {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.modern-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.modern-input:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
    outline: none;
}

.modern-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    background: white;
}

.modern-select:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
    outline: none;
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #2c3e50;
    border-color: #2c3e50;
}

.btn-primary:hover {
    background: #34495e;
    border-color: #34495e;
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    transform: translateY(-1px);
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
</style>

<!-- Vue.js CDN -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<!-- Axios CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>
<script>
// Vue.js uygulaması ana sayfada tanımlanacak
// Bu dosyada sadece HTML ve CSS var
</script>
