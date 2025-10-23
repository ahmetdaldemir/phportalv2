@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/table-page-framework.css')}}">
@endsection

@section('content')
    <div id="stockcard-list-app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Table Page Header -->
        <div class="table-page-header table-page-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="bx bx-package"></i>
                    </div>
                    <div class="header-text">
                        <h2>
                            <i class="bx bx-package me-2"></i>
                            STOK KART LİSTESİ
                        </h2>
                        <p>Stok kartları ve ürün yönetimi</p>
                    </div>
                </div>
                <div class="header-actions">
                    
                        <a href="{{route('stockcard.create')}}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            Yeni Stok Ekle
                        </a>
                        <button class="btn btn-success btn-sm" @click="exportToExcel">
                            <i class="bx bx-download me-1"></i>
                            Excel
                        </button>

                </div>
            </div>
        </div>

        <!-- Table Page Filters -->
        <div class="table-page-filters table-page-fade-in-delay-1">
            <div class="filter-header">
                <h6>
                    <i class="bx bx-filter me-2"></i>
                    Filtreler
                </h6>
                <small>Stok kartı arama ve filtreleme</small>
            </div>
            <div class="filter-body">
                <form @submit.prevent="searchStockCards">
                    <!-- Filter Row -->
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-search"></i> Stok Adı
                            </label>
                            <input type="text" v-model="filters.stockName" @input="debouncedSearch" class="filter-input" placeholder="Stok adı...">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-package"></i> Marka
                            </label>
                            <select v-model="filters.brand" @change="loadVersions" class="filter-select">
                                <option value="">Tüm Markalar</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id" v-text="brand.name"></option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-mobile-alt"></i> Model
                            </label>
                            <select v-model="filters.version" class="filter-select" :disabled="!filters.brand">
                                <option value="">Tüm Modeller</option>
                                <option v-for="version in versions" :key="version.id" :value="version.id" v-text="version.name"></option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-category"></i> Kategori
                            </label>
                            <select v-model="filters.category" class="filter-select">
                                <option value="">Tüm Kategoriler</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id" v-text="category.path || category.name"></option>
                            </select>
                        </div>
                        
                        <div class="filter-group auto">
                            <label class="filter-label">
                                <i class="bx bx-search"></i> Ara
                            </label>
                            <button type="submit" class="filter-button primary" :disabled="loading.search">
                                <span v-if="loading.search" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bx bx-search me-1"></i>
                                <span v-text="loading.search ? 'Aranıyor...' : 'Ara'"></span>
                            </button>
                        </div>
                        
                        <div class="filter-group auto">
                            <label class="filter-label">
                                <i class="bx bx-refresh"></i> Temizle
                            </label>
                            <button type="button" @click="clearFilters" class="filter-button secondary" title="Filtreleri Temizle">
                                <i class="bx bx-refresh me-1"></i>
                                Temizle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="table-page-table table-page-fade-in-delay-2">
            <!-- Loading State -->
            <div v-if="loading.stockcards" class="table-page-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-primary mt-2">Stok kartları yükleniyor...</p>
            </div>
            
            <!-- Empty State -->
            <div v-else-if="stockcards.length === 0" class="table-page-empty">
                <i class="bx bx-package"></i>
                <h4 class="mt-3">Stok kartı bulunamadı</h4>
                <p class="text-muted">Arama kriterlerinize uygun stok kartı bulunamadı.</p>
            </div>
            
            <!-- Table -->
            <div v-else class="table-responsive">
                <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;"><i class="bx bx-hash me-1"></i>#</th>
                                <th style="width: 25%;"><i class="bx bx-package me-1"></i>Stok Adı</th>
                                <th style="width: 12%;"><i class="bx bx-tag me-1"></i>Marka</th>
                                <th style="width: 12%;"><i class="bx bx-mobile me-1"></i>Model</th>
                                <th style="width: 20%;"><i class="bx bx-category me-1"></i>Kategori</th>
                                <th style="width: 10%;"><i class="bx bx-tachometer me-1"></i>Devir Hızı</th>
                                <th style="width: 8%;" class="text-center"><i class="bx bx-box me-1"></i>Adet</th>
                                @role('super-admin')
                                <th style="width: 8%;" class="text-center"><i class="bx bx-cog me-1"></i>İşlemler</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stockcard in stockcards" :key="stockcard.id" style="cursor: pointer;" @click="showStockMovements(stockcard.id)">
                                <td>
                                    <span class="badge bg-primary" v-text="stockcard.id"></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-package me-2 text-primary"></i>
                                        <strong v-text="stockcard.name || stockcard.stock_name"></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary" v-text="stockcard.brand || stockcard.brand_name"></span>
                                </td>
                                <td>
                                    <div v-if="Array.isArray(stockcard.version) && stockcard.version.length > 0">
                                        <span v-for="(ver, idx) in stockcard.version.slice(0, 3)" :key="idx" class="badge bg-info me-1 mb-1" v-text="ver"></span>
                                        <span v-if="stockcard.version.length > 3" class="badge bg-dark" v-text="'+' + (stockcard.version.length - 3)"></span>
                                    </div>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>
                                    <span v-text="stockcard.category_separator_name || stockcard.category_sperator_name || ''"></span>
                                    <span v-text="stockcard.category_name || stockcard.category || ''"></span>
                                </td>
                                <td class="text-center">
                                    <span v-if="stockcard.turnover_rate && stockcard.turnover_rate > 0" 
                                          class="badge" 
                                          :class="stockcard.turnover_status?.class || 'bg-secondary'"
                                          :title="stockcard.turnover_status?.description || ''"
                                          v-text="stockcard.turnover_rate + ' gün'">
                                    </span>
                                    <span v-else class="badge bg-secondary" title="Son 90 günde satış yok">-</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge" :class="stockcard.quantity > 0 ? 'bg-success' : 'bg-warning'" v-text="stockcard.quantity"></span>
                                </td>
                                @role('super-admin')
                                <td class="text-center" @click.stop>
                                    <a :href="`{{route('stockcard.edit')}}?id=${stockcard.id}`" 
                                       class="btn btn-sm btn-warning" 
                                       title="Düzenle">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                </td>
                                @endrole
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
            </div>
            
            <!-- Pagination -->
            <div v-if="pagination && pagination.total > 0" class="table-page-pagination table-page-fade-in-delay-3">
                <nav aria-label="Stok kartları sayfalama">
                    <ul class="pagination">
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                                <i class="bx bx-chevron-left"></i>
                            </a>
                        </li>
                        
                        <li v-for="page in getPageNumbers()" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                            <a class="page-link" href="#" @click.prevent="changePage(page)" v-text="page"></a>
                        </li>
                        
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="pagination-info">
                        <small class="text-muted">
                            <span v-text="pagination.from"></span> - <span v-text="pagination.to"></span> / <span v-text="pagination.total"></span> kayıt
                            (Sayfa <span v-text="pagination.current_page"></span> / <span v-text="pagination.last_page"></span>)
                        </small>
                    </div>
                </nav>
            </div>
        <hr class="my-5">

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
                        Stok Hareketleri - <span v-text="selectedStockCard.name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-show="loading.movements" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p class="mt-2">Hareketler yükleniyor...</p>
                    </div>
                    
                    <div v-show="!loading.movements && stockMovements.length === 0" class="text-center py-4">
                        <i class="bx bx-package text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">Bu stok kartına ait hareket bulunamadı.</p>
                    </div>
                    
                    <div v-show="!loading.movements && stockMovements.length > 0">
                        <!-- Toplam Bilgi -->
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            Toplam <strong v-text="stockMovements.length"></strong> hareket bulundu. 
                            Sayfa <strong v-text="movementsPagination.currentPage"></strong> / <strong v-text="totalMovementsPages"></strong>
                            (Her sayfada 10 kayıt gösteriliyor)
                        </div>
                        
                        <!-- Hareket Grupları -->
                        <div v-for="(group, groupName) in paginatedGroupedMovements" :key="groupName" class="mb-4">
                            <div class="card" v-if="group.length > 0">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bx bx-group me-2"></i>
                                        <span v-text="groupName"></span> (<span v-text="group.length"></span> hareket gösteriliyor)
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
                                                    <td v-text="index + 1"></td>
                                                    <td>
                                                        <span class="badge bg-info" v-text="formatDate(movement.created_at)"></span>
                                                    </td>
                                                    <td>
                                                        <code v-text="movement.serial_number"></code>
                                                    </td>
                                                    <td>
                                                        <span class="badge" :class="getQuantityBadgeClass(movement.type)">
                                                            <span v-text="(movement.quantity > 0 ? '+' : '') + movement.quantity"></span>
                                                        </span>
                                                    </td>
                                                    <td v-text="movement.user_name || 'Sistem'"></td>
                                                    <td v-text="movement.description || '-'"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Movements Pagination -->
                        <div class="mt-4" v-if="totalMovementsPages > 1">
                            <nav aria-label="Hareketler sayfalama">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: movementsPagination.currentPage === 1 }">
                                        <a class="page-link" href="#" @click.prevent="changeMovementsPage(movementsPagination.currentPage - 1)">
                                            <i class="bx bx-chevron-left"></i>
                                        </a>
                                    </li>
                                    
                                    <li v-for="page in getMovementsPageNumbers()" :key="page" 
                                        class="page-item" 
                                        :class="{ active: page === movementsPagination.currentPage }">
                                        <a class="page-link" href="#" @click.prevent="changeMovementsPage(page)" v-text="page"></a>
                                    </li>
                                    
                                    <li class="page-item" :class="{ disabled: movementsPagination.currentPage === totalMovementsPages }">
                                        <a class="page-link" href="#" @click.prevent="changeMovementsPage(movementsPagination.currentPage + 1)">
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
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
                    movementsPagination: {
                        currentPage: 1,
                        perPage: 10
                    },
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
                },
                
                paginatedGroupedMovements() {
                    const groups = this.groupedMovements;
                    const paginatedGroups = {};
                    
                    Object.keys(groups).forEach(groupKey => {
                        const start = (this.movementsPagination.currentPage - 1) * this.movementsPagination.perPage;
                        const end = start + this.movementsPagination.perPage;
                        paginatedGroups[groupKey] = groups[groupKey].slice(start, end);
                    });
                    
                    return paginatedGroups;
                },
                
                totalMovementsPages() {
                    if (!this.stockMovements.length) return 1;
                    return Math.ceil(this.stockMovements.length / this.movementsPagination.perPage);
                }
            },
            async mounted() {
                console.log('StockCard Index Vue app mounted');
                
                // Global API'lerden verileri yükle
                await this.loadGlobalData();
                
                // İlk yüklemede stok kartlarını yükle
                await this.loadStockCards(1);
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
                        
                        const params = {
                            page: page,
                            per_page: 15
                        };
                        
                        if (this.filters.stockName) params.stockName = this.filters.stockName;
                        if (this.filters.brand) params.brand = this.filters.brand;
                        if (this.filters.version) params.version = this.filters.version;
                        if (this.filters.category) params.category = this.filters.category;
                        
                        console.log('Loading stockcards with params:', params);
                        
                        const response = await axios.get('{{ route("stockcard.getStockCardsData") }}', {
                            params: params
                        });
                        
                        console.log('API Response:', response.data);
                        
                        if (response.data && response.data.success) {
                            this.stockcards = response.data.data || [];
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
                            this.stockcards = [];
                        }
                        
                    } catch (error) {
                        console.error('Stok kartları yüklenirken hata:', error);
                        console.error('Error details:', error.response?.data);
                        this.showNotification('Stok kartları yüklenemedi', 'error');
                        this.stockcards = [];
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
                        
                        // Pagination'ı sıfırla
                        this.movementsPagination.currentPage = 1;
                        
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
                        
                        const url = '{{ route("stockcard.movements.ajax") }}';
                        console.log('Loading movements for stock card:', stockCardId);
                        console.log('Request URL:', url);
                        
                        const response = await axios.get(url, {
                            params: {
                                stock_card_id: stockCardId
                            }
                        });
                        
                        console.log('Movements API Response:', response.data);
                        
                        if (response.data && response.data.movements) {
                            this.stockMovements = response.data.movements;
                            console.log('Movements loaded:', this.stockMovements.length);
                            console.log('Sample movement:', this.stockMovements[0]);
                            console.log('Grouped movements:', this.groupedMovements);
                        } else if (response.data && response.data.error) {
                            console.error('API Error:', response.data.error);
                            this.showNotification(response.data.error, 'error');
                            this.stockMovements = [];
                        } else {
                            console.warn('Unexpected response format:', response.data);
                            this.stockMovements = [];
                        }
                        
                    } catch (error) {
                        console.error('Stok hareketleri yüklenirken hata:', error);
                        console.error('Error details:', error.response?.data);
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
                },
                
                // Movements pagination metodları
                changeMovementsPage(page) {
                    if (page < 1 || page > this.totalMovementsPages) {
                        return;
                    }
                    this.movementsPagination.currentPage = page;
                    console.log('Movements page changed to:', page);
                },
                
                getMovementsPageNumbers() {
                    const total = this.totalMovementsPages;
                    const current = this.movementsPagination.currentPage;
                    const pages = [];
                    
                    // Basit sayfalama: max 7 sayfa göster
                    const maxPages = 7;
                    let start = Math.max(1, current - Math.floor(maxPages / 2));
                    let end = Math.min(total, start + maxPages - 1);
                    
                    if (end - start < maxPages - 1) {
                        start = Math.max(1, end - maxPages + 1);
                    }
                    
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    
                    return pages;
                },
                
                // Excel export method
                async exportToExcel() {
                    try {
                        // Show loading
                        Swal.fire({
                            title: 'Excel İndiriliyor...',
                            text: 'Lütfen bekleyin',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Prepare search form data
                        const searchForm = {
                            page: 1,
                            export: 'excel',
                            per_page: 10000
                        };

                        // Add current filters to search form
                        if (this.filters.stockName) searchForm.search = this.filters.stockName;
                        if (this.filters.brand) searchForm.brand = this.filters.brand;
                        if (this.filters.category) searchForm.category = this.filters.category;
                        if (this.filters.version) searchForm.version = this.filters.version;

                        // Create download URL
                        const params = new URLSearchParams(searchForm);
                        const downloadUrl = `/stockcard/export?${params.toString()}`;

                        // Test URL accessibility
                        try {
                            await fetch(downloadUrl, { method: 'HEAD' });
                        } catch (error) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: 'Export URL\'sine ulaşılamadı!'
                            });
                            return;
                        }

                        // Create temporary link and trigger download
                        const link = document.createElement('a');
                        link.href = downloadUrl;
                        link.download = 'stok_kartlari_' + new Date().toISOString().slice(0, 10) + '.csv';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        // Close loading and show success
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Excel dosyası indirildi.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                    } catch (error) {
                        console.error('Excel export error:', error);
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Excel dosyası indirilirken bir hata oluştu.'
                        });
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
