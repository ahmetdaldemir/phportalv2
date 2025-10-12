@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
    <style>
        /* Base CSS'den gelen tüm stiller kullanılıyor */
        /* Accordion Styles - Specific to stockcard index */
        .accordion-toggle {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .accordion-toggle:hover {
            background: #f8f9fa !important;
        }
        
        .accordion-toggle i {
            transition: transform 0.3s ease;
        }
        
        .accordion-toggle[aria-expanded="true"] i {
            transform: rotate(180deg);
        }
        
        .hiddenRow {
            padding: 0 !important;
        }
        
        .accordian-body {
            background: #f8f9fa;
            border-radius: 8px;
            margin: 0.5rem;
            padding: 1rem;
        }
    </style>
@endsection

@section('content')
    
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="me-3">
                        <i class="bx bx-package display-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600; color: white;">
                            <i class="bx bx-package me-2"></i>
                            STOK KART LİSTESİ
                        </h2>
                        <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Stok kartları ve ürün yönetimi</p>
                    </div>
                </div>
                <div>
                    <a href="{{route('stockcard.create')}}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Yeni Stok Ekle
                    </a>
                </div>
            </div>
        </div>

        <div class="card professional-card">
            <div class="card-header professional-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-filter me-2"></i>
                            Filtreler
                        </h6>
                        <small class="text-muted">Stok kartı arama ve filtreleme</small>
                    </div>
                </div>
            </div>
            
            <!-- Vue.js Stock Cards List -->
            <div id="stockcard-list-app" class="card-body p-4">
                <form @submit.prevent="searchStockCards" class="compact-filter-form">
                    <!-- Row 1: Main Filters -->
                    <div class="row g-2 mb-2">
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-search"></i> Stok Adı
                                </label>
                                <input 
                                    type="text" 
                                    v-model="filters.stockName"
                                    @input="debouncedSearch"
                                    class="form-control form-control-sm compact-input" 
                                    placeholder="Stok adı..."
                                >
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-package"></i> Marka
                                </label>
                                <select v-model="filters.brand" @change="loadVersions" class="form-select form-select-sm compact-select">
                                    <option value="">Tüm Markalar</option>
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">@{{ brand.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-mobile-alt"></i> Model
                                </label>
                                <select v-model="filters.version" class="form-select form-select-sm compact-select" :disabled="!filters.brand">
                                    <option value="">Tüm Modeller</option>
                                    <option v-for="version in versions" :key="version.id" :value="version.id">@{{ version.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-category"></i> Kategori
                                </label>
                                <select v-model="filters.category" class="form-select form-select-sm compact-select">
                                    <option value="">Tüm Kategoriler</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">@{{ category.path || category.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="compact-filter-group">
                                <label class="compact-label d-none d-md-block invisible">Aksiyon</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill" :disabled="loading.search">
                                        <span v-if="loading.search" class="spinner-border spinner-border-sm me-1"></span>
                                        <i v-else class="bx bx-search me-1"></i>
                                        @{{ loading.search ? 'Aranıyor...' : 'Ara' }}
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
            <div v-if="loading.stockcards" class="table-loading-overlay">
                <div class="loading-content">
                    <div class="loading-spinner-large"></div>
                    <div class="loading-text">Stok kartları yükleniyor...</div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-else-if="stockcards.length === 0" class="empty-state">
                <div class="empty-content">
                    <i class="bx bx-package display-1 text-muted"></i>
                    <h5 class="mt-3">Stok kartı bulunamadı</h5>
                    <p class="text-muted">Arama kriterlerinize uygun stok kartı bulunamadı.</p>
                </div>
            </div>
            
            <!-- Table -->
            <div v-else class="table-responsive text-nowrap">
                    <table class="table professional-table">
                        <thead>
                            <tr>
                                <th><i class="bx bx-package me-1"></i>#</th>
                                <th><i class="bx bx-package me-1"></i>Stok Adı</th>
                                <th><i class="bx bx-barcode me-1"></i>Barkod</th>
                                <th><i class="bx bx-box me-1"></i>Adet</th>
                                <th><i class="bx bx-category me-1"></i>Kategori</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 professional-tbody">
                            <template v-for="stockcard in stockcards" :key="stockcard.id">
                                <tr 
                                    class="accordion-toggle" 
                                    style="cursor: pointer;"
                                    @click="showStockMovements(stockcard.id)"
                                >
                                <td>
                                        <i class="bx bx-down-arrow me-2"></i>
                                        <strong>@{{ stockcard.id }}</strong>
                                    </td>
                                    <td>
                                        <i class="bx bx-down-arrow me-2"></i>
                                        <strong>@{{ stockcard.name }}</strong>
                                    </td>
                                    <td>
                                        <i class="bx bx-barcode me-2"></i>
                                        <strong>@{{ stockcard.barcode }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">@{{ stockcard.quantity }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">@{{ stockcard.category_sperator_name }}@{{ stockcard.category }}</span>
                                    </td>
                                </tr>
                                <!-- tr>
                                    <td colspan="2" class="hiddenRow">
                                        <div class="accordian-body collapse" :id="'l' + stockcard.id">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="info">
                                                        <th style="width: 20%">
                                                            <i class="bx bx-package me-1"></i>Ürün Adı
                                                        </th>
                                                        <th style="width: 5%">
                                                            <i class="bx bx-box me-1"></i>Adet
                                                        </th>
                                                        <th style="width: 30%">
                                                            <i class="bx bx-category me-1"></i>Kategori
                                                        </th>
                                                        <th style="width: 5%">
                                                            <i class="bx bx-package me-1"></i>Marka
                                                        </th>
                                                        <th style="width: 10%">
                                                            <i class="bx bx-mobile me-1"></i>Model
                                                        </th>
                                                        <th style="width: 5%">
                                                            <i class="bx bx-dollar me-1"></i>Alış F
                                                        </th>
                                                        <th style="width: 5%">
                                                            <i class="bx bx-dollar me-1"></i>Destek. F.
                                                        </th>
                                                        <th style="width: 5%">
                                                            <i class="bx bx-dollar me-1"></i>Satış F.
                                                        </th>
                                                        <th style="width: 20%">
                                                            <i class="bx bx-cog me-1"></i>İşlemler
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="stockData in stockcard.stockData" :key="stockData.id" class="info">
                                                        <td>
                                                            <strong>@{{ stockData.name }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning">@{{ stockData.quantity }}</span>
                                                        </td>
                                                        <td>
                                                            <strong>@{{ stockData.category_sperator_name }}@{{ stockData.category || "Belirtilmedi" }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary">@{{ stockData.brand }}</span>
                                                        </td>
                                                        <td>
                                                            <div v-for="version in stockData.version" :key="version" class="mb-1">
                                                                <span class="badge bg-secondary">@{{ version }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @role('Depo Sorumlusu|super-admin')
                                                            <strong>@{{ formatPrice(stockData.cost_price) }}</strong>
                                                            @endrole
                                                        </td>
                                                        <td>
                                                            @role('Depo Sorumlusu|super-admin')
                                                            <strong>@{{ formatPrice(stockData.base_cost_price) }}</strong>
                                                            @endrole
                                                        </td>
                                                        <td>
                                                            <strong class="text-success">@{{ formatPrice(stockData.sale_price) }}</strong>
                                                        </td>
                                                        <td>
                                                            @role(['Depo Sorumlusu','super-admin'])
                                                            <a :href="'{{route('invoice.create')}}?id=' + stockData.id" 
                                                               title="Fatura Ekle" 
                                                               class="btn btn-danger btn-sm">
                                                                <i class="bx bx-list-plus"></i>
                                                            </a>
                                                            <a :href="'{{route('stockcard.edit')}}?id=' + stockData.id" 
                                                               title="Düzenle" 
                                                               class="btn btn-primary btn-sm">
                                                                <i class="bx bx-edit-alt"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    @click="priceModal(stockData.id)"
                                                                    class="btn btn-success btn-sm">
                                                                <i class="bx bxs-dollar-circle"></i>
                                                            </button>
                                                            @endrole
                                                            @role(['super-admin'])
                                                            <button type="button" 
                                                                    @click="deleteMovement(stockData.id)"
                                                                    class="btn btn-danger btn-sm">
                                                                <i class="bx bxs-trash"></i>
                                                            </button>
                                                            @endrole
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr -->
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="pagination && pagination.total > 0" class="mt-4">
                    <nav aria-label="Stok kartları sayfalama">
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                                    <i class="bx bx-chevron-left"></i>
                                </a>
                            </li>
                            
                            <li v-for="page in getPageNumbers()" :key="page" 
                                class="page-item" 
                                :class="{ active: page === pagination.current_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(page)">
                                    @{{ page }}
                                </a>
                            </li>
                            
                            <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                                <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="pagination-info text-center mt-2">
                            <small class="text-muted">
                                @{{ pagination.from }} - @{{ pagination.to }} / @{{ pagination.total }} kayıt
                                (Sayfa @{{ pagination.current_page }} / @{{ pagination.last_page }})
                            </small>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <hr class="my-5">
    </div>
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('stockcard.delete')}}" id="deleteModalForm">
                @csrf
                <input id="stockCardMovementIdDelete" name="stock_card_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Silmek icin not girmelisiniz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Not</label>
                            <input type="text" id="note" class="form-control" name="note" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Sil</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="transferForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                <input id="id" name="id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Sevk İşlemi</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Serial Number</label>
                            <input
                                type="text"
                                id="serialBackdrop"
                                class="form-control"
                                placeholder="Seri Numarası"
                                name="serial_number"
                            />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="sellerBackdrop" class="form-label">Şube</label>
                            <select class="form-control" name="seller_id" id="sellerBackdrop">
                                @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}">{{$seller->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="sellerBackdrop" class="form-label">Neden</label>
                            <select class="form-control" name="reason_id" id="sellerBackdrop">
                                @foreach($reasons as $reason)
                                    <option value="{{$reason->id}}">{{$reason->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Sevk İşlemi Başlat</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="priceModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="priceForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                 <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Fiyat Değiştir</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Stok Hareketleri Modal -->
    <div class="modal fade" id="stockMovementsModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-list-ul me-2"></i>
                        Stok Hareketleri - @{{ selectedStockCard.name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="loading.movements" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p class="mt-2">Hareketler yükleniyor...</p>
                    </div>
                    
                    <div v-else-if="stockMovements.length === 0" class="text-center py-4">
                        <i class="bx bx-package text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Bu stok kartına ait hareket bulunamadı.</p>
                    </div>
                    
                    <div v-else>
                        <!-- Hareket Grupları -->
                        <div v-for="(group, groupName) in groupedMovements" :key="groupName" class="mb-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bx bx-group me-2"></i>
                                        @{{ groupName }} (@{{ group.length }} hareket)
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><i class="bx bx-hash me-1"></i>#</th>
                                                    <th><i class="bx bx-calendar me-1"></i>Tarih</th>
                                                    <th><i class="bx bx-barcode me-1"></i>Seri No</th>
                                                    <th><i class="bx bx-box me-1"></i>Miktar</th>
                                                    <th><i class="bx bx-user me-1"></i>Kullanıcı</th>
                                                    <th><i class="bx bx-note me-1"></i>Açıklama</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(movement, index) in group" :key="movement.id">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            @{{ formatDate(movement.created_at) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <code>@{{ movement.serial_number }}</code>
                                                    </td>
                                                    <td>
                                                        <span class="badge" :class="getQuantityBadgeClass(movement.type)">
                                                            @{{ movement.quantity > 0 ? '+' : '' }}@{{ movement.quantity }}
                                                        </span>
                                                    </td>
                                                    <td>@{{ movement.user_name || 'Sistem' }}</td>
                                                    <td>@{{ movement.description || '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
 @endsection

@section('custom-js')
    <!-- Vue.js CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Axios CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <!-- Vue.js Stock Cards List Application -->
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    stockcards: [],
                    pagination: {
                        from: 0,
                        to: 0,
                        total: 0,
                        current_page: 1,
                        last_page: 1
                    },
                    loading: {
                        stockcards: true,
                        search: false,
                        movements: false
                    },
                    expandedCards: [],
                    selectedStockCard: {},
                    stockMovements: [],
                    filters: {
                        stockName: '',
                        brand: '',
                        version: '',
                        category: ''
                    },
                    brands: [],
                    versions: [],
                    categories: @json($categories ?? []),
                    colors: [],
                    sellers: [],
                    reasons: @json($reasons ?? [])
                }
            },
            computed: {
                groupedMovements() {
                    if (!this.stockMovements.length) return {};
                    
                    const groups = {};
                    this.stockMovements.forEach(movement => {
                        const groupKey = movement.type_name || 'Diğer';
                        if (!groups[groupKey]) {
                            groups[groupKey] = [];
                        }
                        groups[groupKey].push(movement);
                    });
                    
                    return groups;
                }
            },
            async mounted() {
                console.log('StockCard Index Vue app mounted');
                
                // Global API'lerden verileri yükle
                await this.loadGlobalData();
                // İlk yüklemede veri çekmiyoruz - kullanıcı arama yaptığında yüklenecek
                this.loading.stockcards = false;
            },
            methods: {
                // Global verileri yükle
                async loadGlobalData() {
                    try {
                        // Paralel olarak tüm verileri yükle
                        const [brandsRes, colorsRes, sellersRes] = await Promise.all([
                            axios.get('/api/common/brands'),
                            axios.get('/api/common/colors'),
                            axios.get('/api/common/sellers')
                        ]);
                        
                        this.brands = brandsRes.data || [];
                        this.colors = colorsRes.data || [];
                        this.sellers = sellersRes.data || [];
          
                    } catch (error) {
                        console.error('Error loading global data:', error);
                        this.showNotification('Genel veriler yüklenemedi', 'error');
                    }
                },
                
                initializeBootstrapCollapse() {
                    // Bootstrap collapse initialization
                    const collapseElements = document.querySelectorAll('.collapse');
                    collapseElements.forEach(element => {
                        new bootstrap.Collapse(element, {
                            toggle: false
                        });
                    });
                },
                
                toggleCard(cardId) {
                    const index = this.expandedCards.indexOf(cardId);
                    if (index > -1) {
                        this.expandedCards.splice(index, 1);
                    } else {
                        this.expandedCards.push(cardId);
                    }
                },
                
                formatPrice(price) {
                    return new Intl.NumberFormat('tr-TR', {
                        style: 'currency',
                        currency: 'TRY',
                        minimumFractionDigits: 2
                    }).format(price);
                },
                
                priceModal(id) {
                    $("#priceModal").modal('show');
                    $("#priceModal #stockCardId").val(id);
                },
                
                openModal(id) {
                    $("#backDropModal").modal('show');
                    $("#stockCardId").val(id);
                },
                
                deleteMovement(id) {
                    Swal.fire({
                        title: "Silmek istediğinizden emin misiniz?",
                        text: "Silme işlemi yapılırken kesinlikle not girmelisiniz!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "EVET!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#stockCardMovementIdDelete').val(id);
                            $('#deleteModal').modal('show');
                        }
                    });
                },
                
                showNotification(message, type = 'info') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: message,
                            icon: type,
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        alert(message);
                    }
                },
                
                // Pagination metodları
                getPageNumbers() {
                    if (!this.pagination || !this.pagination.last_page) return [];
                    
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const pages = [];
                    
                    // İlk sayfa
                    if (current > 1) {
                        pages.push(1);
                    }
                    
                    // Mevcut sayfa etrafındaki sayfalar
                    const start = Math.max(1, current - 2);
                    const end = Math.min(last, current + 2);
                    
                    for (let i = start; i <= end; i++) {
                        if (!pages.includes(i)) {
                            pages.push(i);
                        }
                    }
                    
                    // Son sayfa
                    if (current < last && !pages.includes(last)) {
                        pages.push(last);
                    }
                    
                    return pages;
                },
                
                changePage(page) {
                    if (page < 1 || page > this.pagination.last_page || page === this.pagination.current_page) {
                        return;
                    }
                    
                    // AJAX ile sayfa değiştir
                    this.loadStockCards(page);
                },
                
                // AJAX ile stok kartlarını yükle
                async loadStockCards(page = 1) {
                    try {
                        this.loading.stockcards = true;
                        
                        const params = new URLSearchParams();
                        params.append('page', page);
                        if (this.filters.stockName) params.append('stockName', this.filters.stockName);
                        if (this.filters.brand) params.append('brand', this.filters.brand);
                        if (this.filters.version) params.append('version', this.filters.version);
                        if (this.filters.category) params.append('category', this.filters.category);
                        
                        const response = await axios.get(`{{route('stockcard.index')}}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        // Debug: Response'u logla
                        console.log('Response:', response.data);
                        
                        // Response'dan veri al
                        if (response.data && response.data.success && response.data.data) {
                            this.stockcards = response.data.data;
                            this.pagination = response.data.pagination || {
                                from: 0,
                                to: 0,
                                total: 0,
                                current_page: 1,
                                last_page: 1
                            };
                            console.log('Stockcards loaded:', this.stockcards.length);
                        } else {
                            console.error('API response error:', response.data);
                            this.showNotification('Stok kartları yüklenemedi', 'error');
                        }
                        
                    } catch (error) {
                        console.error('Stok kartları yüklenirken hata:', error);
                        this.showNotification('Stok kartları yüklenemedi', 'error');
                    } finally {
                        this.loading.stockcards = false;
                    }
                },
                
                // Filtreleme verilerini yükle
                // Marka değiştiğinde versiyonları yükle
                async loadVersions() {
                    if (!this.filters.brand) {
                        this.versions = [];
                        this.filters.version = '';
                        return;
                    }
                    
                    try {
                        console.log('Loading versions for brand:', this.filters.brand);
                        const response = await axios.get(`/api/common/versions?brand_id=${this.filters.brand}`);
                        this.versions = response.data || [];
                        this.filters.version = '';
                        console.log('Versions loaded:', this.versions.length);
                    } catch (error) {
                        console.error('Versiyonlar yüklenemedi:', error);
                        this.versions = [];
                    }
                },
                
                // Filtreleme yap
                async searchStockCards() {
                    this.loading.search = true;
                    await this.loadStockCards(1);
                    this.loading.search = false;
                },
                
                // Filtreleri temizle
                clearFilters() {
                    this.filters = {
                        stockName: '',
                        brand: '',
                        version: '',
                        category: ''
                    };
                    this.versions = [];
                    this.stockcards = [];
                    this.pagination = {
                        from: 0,
                        to: 0,
                        total: 0,
                        current_page: 1,
                        last_page: 1
                    };
                },
                
                // Debounced search
                debouncedSearch() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.searchStockCards();
                    }, 500);
                },
                
                // Stok hareketlerini göster
                async showStockMovements(stockCardId) {
                    try {
                        // Seçilen stok kartını bul
                        this.selectedStockCard = this.stockcards.find(card => card.id === stockCardId);
                        
                        // Modal'ı aç
                        const modal = new bootstrap.Modal(document.getElementById('stockMovementsModal'));
                        modal.show();
                        
                        // Hareketleri yükle
                        await this.loadStockMovements(stockCardId);
                        
                    } catch (error) {
                        console.error('Modal açılırken hata:', error);
                        this.showNotification('Modal açılamadı', 'error');
                    }
                },
                
                // Stok hareketlerini yükle
                async loadStockMovements(stockCardId) {
                    try {
                        this.loading.movements = true;
                        
                        const response = await axios.get(`{{route('stockcard.movements.ajax')}}?stock_card_id=${stockCardId}`);
                        
                        if (response.data && response.data.movements) {
                            this.stockMovements = response.data.movements;
                        } else {
                            this.stockMovements = [];
                        }
                        
                    } catch (error) {
                        console.error('Stok hareketleri yüklenirken hata:', error);
                        this.showNotification('Hareketler yüklenemedi', 'error');
                        this.stockMovements = [];
                    } finally {
                        this.loading.movements = false;
                    }
                },
                
                // Tarih formatla
                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('tr-TR', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },
                
                // Miktar badge class'ı
                getQuantityBadgeClass(type) {
                    switch(type) {
                        case 1: return 'bg-success'; // Giriş
                        case 2: return 'bg-danger';  // Çıkış
                        case 3: return 'bg-warning'; // Transfer
                        default: return 'bg-secondary';
                    }
                }
            }
        }).mount('#stockcard-list-app');
    </script>
    
    <!-- Legacy jQuery Functions -->
    <script>
        function priceModal(id) {
            $("#priceModal").modal('show');
            $("#priceModal #stockCardId").val(id);
        }
        
        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#stockCardId").val(id);
        }

        $("#transferForm").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = '{{route('stockcard.sevk')}}';
            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardId").val() + '',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                }
            });
        });
        
        $("#priceForm").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = '{{route('stockcard.priceupdate')}}';
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#priceModal").modal('hide');
                    window.location.reload();
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#priceModal").modal('hide');
                }
            });
        });
    </script>

@endsection
