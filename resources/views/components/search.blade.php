<div id="search-component-app">
    <form @submit.prevent="performSearch" class="search-form">
        <div class="row g-3">
            <!-- Stok Name -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bx bx-search me-1"></i>Stok Adı
                </label>
                <input 
                    v-model="searchForm.stockName" 
                    type="text" 
                    class="form-control"
                    placeholder="Stok adı giriniz...">
            </div>
            
            <!-- Brand -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bx bx-tag me-1"></i>Marka
                </label>
                <select 
                    v-model="searchForm.brand" 
                    @change="onBrandChange"
                    class="form-select">
                    <option value="">Tümü</option>
                    <option 
                        v-for="brand in brands" 
                        :key="brand.id"
                        :value="brand.id">
                        @{{ brand.name }}
                    </option>
                </select>
            </div>
            
            <!-- Version/Model -->
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    <i class="bx bx-mobile me-1"></i>Model
                </label>
                <select 
                    v-model="searchForm.version" 
                    class="form-select"
                    :disabled="!searchForm.brand">
                    <option value="">Tümü</option>
                    <option 
                        v-for="version in filteredVersions" 
                        :key="version.id"
                        :value="version.id">
                        @{{ version.name }}
                    </option>
                </select>
                <small v-if="!searchForm.brand" class="text-muted">Önce marka seçiniz</small>
            </div>
            
            <!-- Category -->
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    <i class="bx bx-category me-1"></i>Kategori
                </label>
                <select 
                    v-model="searchForm.category" 
                    class="form-select">
                    <option value="">Tümü</option>
                    <option 
                        v-for="category in parentCategories" 
                        :key="category.id"
                        :value="category.id">
                        @{{ category.name }}
                    </option>
                </select>
            </div>
            
            <!-- Serial Number -->
            <div class="col-md-2">
                <label class="form-label fw-semibold">
                    <i class="bx bx-barcode me-1"></i>Seri No
                </label>
                <input 
                    v-model="searchForm.serialNumber" 
                    type="text" 
                    class="form-control"
                    placeholder="Seri numarası...">
            </div>
        </div>
        
        <!-- Search Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button 
                        :disabled="searching" 
                        type="submit" 
                        class="btn btn-primary">
                        <span v-if="searching" class="spinner-border spinner-border-sm me-2"></span>
                        <i v-else class="bx bx-search me-1"></i>
                        @{{ searching ? 'Aranıyor...' : 'Ara' }}
                    </button>
                    <button 
                        @click="resetSearch" 
                        type="button" 
                        class="btn btn-outline-secondary">
                        <i class="bx bx-refresh me-1"></i>Temizle
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const { createApp } = Vue;

window.SearchComponentApp = createApp({
    data() {
        return {
            searchForm: {
                stockName: '',
                brand: '',
                version: '',
                category: '',
                serialNumber: ''
            },
            brands: @json($brands ?? []),
            versions: @json($versions ?? []),
            categories: @json($categories ?? []),
            searching: false
        }
    },
    computed: {
        filteredVersions() {
            if (!this.searchForm.brand) return [];
            return this.versions.filter(v => v.brand_id == this.searchForm.brand);
        },
        parentCategories() {
            return this.categories.filter(c => c.parent_id == 0 || !c.parent_id);
        }
    },
    watch: {
        'searchForm.brand'(newVal) {
            if (!newVal) {
                this.searchForm.version = '';
            }
        }
    },
    methods: {
        async onBrandChange() {
            if (this.searchForm.brand) {
                await this.loadVersions(this.searchForm.brand);
            }
            this.searchForm.version = '';
        },
        
        async loadVersions(brandId) {
            try {
                const response = await fetch(`/get_version?brand_id=${brandId}`);
                const data = await response.json();
                this.versions = data.versions || [];
            } catch (error) {
                console.error('Error loading versions:', error);
            }
        },
        
        async performSearch() {
            this.searching = true;
            
            try {
                // Emit search event for parent components
                window.dispatchEvent(new CustomEvent('stockSearch', {
                    detail: this.searchForm
                }));
                
                // Optional: Direct API call
                const params = new URLSearchParams();
                Object.keys(this.searchForm).forEach(key => {
                    if (this.searchForm[key]) {
                        params.append(key, this.searchForm[key]);
                    }
                });
                
                const response = await fetch(`/stockSearch?${params.toString()}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify(this.searchForm)
                });
                
                const results = await response.json();
                
                // Emit results for parent components
                window.dispatchEvent(new CustomEvent('searchResults', {
                    detail: results
                }));
                
            } catch (error) {
                console.error('Search error:', error);
                if (window.$toast) {
                    window.$toast.error('Arama sırasında hata oluştu!');
                }
            } finally {
                this.searching = false;
            }
        },
        
        resetSearch() {
            this.searchForm = {
                stockName: '',
                brand: '',
                version: '',
                category: '',
                serialNumber: ''
            };
            
            // Emit reset event
            window.dispatchEvent(new CustomEvent('searchReset'));
        }
    },
    
    mounted() {
        // Auto-focus on first input
        this.$nextTick(() => {
            const firstInput = this.$el.querySelector('input[type="text"]');
            if (firstInput) firstInput.focus();
        });
    }
}).mount('#search-component-app');
</script>

<style scoped>
.search-form {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e7e9ed;
}

.form-label.fw-semibold {
    font-weight: 600;
    color: #566a7f;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
</style>