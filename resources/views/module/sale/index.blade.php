@extends('layouts.admin')

@section('content')
    <div id="app">
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Professional Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="me-3">
                            <i class="bx bx-shopping-bag display-4 text-white"></i>
                    </div>
                    <div>
                            <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600; color: white;">
                            <i class="bx bx-shopping-bag me-2"></i>
                            SATIŞ LİSTESİ
                            </h2>
                            <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Satış fiyatları ve
                                kar zarar yönetimi</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success btn-sm">
                            <i class="bx bx-printer me-1"></i>
                            Yazdır
                        </button>
                        <button class="btn btn-warning btn-sm">
                            <i class="bx bx-download me-1"></i>
                            Excel
                        </button>
                        <a href="{{ route('invoice.sales') }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>
                            YENİ SATIŞ
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
                            <small class="text-muted">Satış arama ve filtreleme</small>
                </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="searchSales" class="compact-filter-form">
                        @csrf

                        <!-- Row 1: Main Filters -->
                        <div class="row g-2 mb-2">
                            <div class="col-lg-3 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-calendar"></i> Tarih Aralığı
                            </label>
                            <div class="date-range-container">
                                        <input type="date" class="form-control form-control-sm compact-input"
                                            v-model="searchForm.startDate">
                                <span class="date-separator">-</span>
                                        <input type="date" class="form-control form-control-sm compact-input"
                                            v-model="searchForm.endDate">
                            </div>
                            </div>
                        </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-package"></i> Stok Adı
                            </label>
                                    <input type="text" class="form-control form-control-sm compact-input"
                                        v-model="searchForm.stockName" @input="debouncedSearch()"
                                        placeholder="Stok adı ara...">
                        </div>
                        </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-tag"></i> Marka
                            </label>
                                    <select v-model="searchForm.brand" @change="getVersions(); debouncedSearch()"
                                        class="form-select form-select-sm compact-select">
                                        <option value="">Tüm Markalar</option>
                                <option v-for="brand in globalBrands" :key="brand.id" :value="brand.id">
                                            @{{ brand.name }}</option>
                            </select>
                        </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-mobile"></i> Model
                            </label>
                                    <select v-model="searchForm.version" @change="debouncedSearch()"
                                        class="form-select form-select-sm compact-select">
                                        <option value="">Tüm Modeller</option>
                                        <option v-for="version in globalVersions" :key="version.id"
                                            :value="version.id">
                                            @{{ version.name }}</option>
                            </select>
                        </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-category"></i> Kategori
                            </label>
                                    <select v-model="searchForm.category" @change="debouncedSearch()"
                                        class="form-select form-select-sm compact-select">
                                        <option value="">Tüm Kategoriler</option>
                                        <option v-for="category in globalCategories" :key="category.id"
                                            :value="category.id">
                                            @{{ category.name }}</option>
                            </select>
                        </div>
                            </div>

                        
                        </div>

                        <!-- Row 2: Secondary Filters -->
                        <div class="row g-2">
                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-user"></i> Müşteri
                            </label>
                                    <input type="text" class="form-control form-control-sm compact-input"
                                        v-model="searchForm.customerName" @input="debouncedSearch"
                                        placeholder="Müşteri ara...">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-barcode"></i> Seri No
                                    </label>
                                    <input type="text" class="form-control form-control-sm compact-input"
                                        v-model="searchForm.serialNumber" @input="debouncedSearch"
                                        placeholder="Seri numarası...">
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-store"></i> Bayi
                                    </label>
                                    <select v-model="searchForm.seller" @change="debouncedSearch()"
                                        class="form-select form-select-sm compact-select">
                                        <option value="">Tüm Bayiler</option>
                                        <option v-for="seller in globalSellers" :key="seller.id"
                                            :value="seller.id">
                                            @{{ seller.name }}</option>
                            </select>
                        </div>
                            </div>
                            <div class="col-lg-1 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-search"></i> Ara
                                    </label>
                                    <button type="submit" class="btn btn-primary btn-sm w-100" :disabled="loading.search">
                                    <i class="bx bx-search me-1" v-if="!loading.search"></i>
                                    <i class="bx bx-loader-alt bx-spin me-1" v-if="loading.search"></i>
                                        Q
                                </button>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-refresh"></i> Temizle
                                    </label>
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                                        @click="clearFilters">
                                        <i class="bx bx-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <!-- Sales Table -->
            <div class="card professional-card mt-4">
                <div class="card-header professional-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-list-ul me-2"></i>
                            Satış Listesi
                            </h6>
                            <small class="text-muted" v-if="invoices.length > 0">
                                @{{ invoices.length }} fatura bulundu
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary" v-if="totals.gross_total > 0">
                                <i class="bx bx-money me-1"></i>
                                @{{ formatCurrency(totals.gross_total) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Debug Info - Remove after testing -->
                    <!-- <div class="alert alert-info mb-3">
                                    <strong>Debug:</strong> @{{ debugInfo }}
                                    <br><strong>Invoices Count:</strong> @{{ invoices.length }}
                                    <br><strong>Loading:</strong> @{{ loading.invoices }}
                                </div> -->

                    <!-- Main Content -->
                    <div v-if="loading.invoices" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-primary mt-2">Faturalar yükleniyor...</p>
                    </div>

                    <div v-else-if="invoices.length === 0" class="text-center py-5">
                        <i class="bx bx-shopping-bag display-1 text-muted"></i>
                        <h4 class="text-muted mb-3">Fatura Bulunamadı</h4>
                        <p class="text-muted">Seçilen tarihte fatura bulunmamaktadır.</p>
                    </div>

                    <!-- Invoice Table -->
                    <div v-else class="table-responsive">
                        <table class="table table-hover professional-table">
                            <thead class="table-header-modern">
                                <tr>
                                    <th class="compact-header" style="width: 5%;">
                                        <input type="checkbox" class="form-check-input">
                                    </th>
                                    <th class="compact-header" style="width: 15%;">
                                        <i class="bx bx-receipt me-1"></i>
                                        <span class="header-text">FATURA NO</span>
                                    </th>
                                    <th class="compact-header" style="width: 12%;">
                                        <i class="bx bx-calendar me-1"></i>
                                        <span class="header-text">TARİH</span>
                                    </th>
                                    <th class="compact-header" style="width: 20%;">
                                        <i class="bx bx-user me-1"></i>
                                        <span class="header-text">MÜŞTERİ</span>
                                    </th>
                                    <th class="compact-header" style="width: 15%;">
                                        <i class="bx bx-user-check me-1"></i>
                                        <span class="header-text">PERSONEL</span>
                                    </th>
                                    <th class="compact-header text-center" style="width: 8%;">
                                        <i class="bx bx-package me-1"></i>
                                        <span class="header-text">ADET</span>
                                    </th>
                                    <th class="compact-header text-end" style="width: 12%;">
                                        <i class="bx bx-money me-1"></i>
                                        <span class="header-text">TOPLAM</span>
                                    </th>
                                    <th class="compact-header text-center" style="width: 13%;">
                                        <i class="bx bx-cog me-1"></i>
                                        <span class="header-text">İŞLEMLER</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in invoices" :key="invoice.id" class="invoice-row clickable-row"
                                    @click="openInvoiceModal(invoice.id)">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input">
                                    </td>
                                    <td class="text-start">
                                        <div class="text-compact">
                                            <div class="fw-bold text-primary" v-text="'#' + invoice.number"></div>
                                            <div class="text-muted small" v-text="invoice.id"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-compact" v-text="formatDate(invoice.created_at)"></span>
                                    </td>
                                    <td class="text-start">
                                        <span class="text-truncate" :title="invoice.customer_name"
                                            v-text="invoice.customer_name">
                                        </span>
                                    </td>
                                    <td class="text-start">
                                        <span class="text-truncate" :title="invoice.staff_name"
                                            v-text="invoice.staff_name">
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info" v-text="invoice.sales_count">
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="price-display fw-bold"
                                            v-text="formatCurrency(invoice.total_price)"></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1">
                                        <button @click.stop="openInvoiceModal(invoice.id)"
                                                class="btn btn-xs btn-outline-primary" title="Detay">
                                                <i class="bx bx-list-ul"></i>
                                        </button>
                                            <button @click.stop="printInvoice(invoice.id)"
                                                class="btn btn-xs btn-outline-success" title="Yazdır">
                                                <i class="bx bx-printer"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Vue.js Pagination -->
            <div class="card mt-4" v-if="pagination && pagination.last_page > 1">
                <div class="card-body mt-4 p-4 box has-text-centered"
                    style="padding-top: 0 !important; padding-bottom: 0 !important;">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Page -->
                            <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page - 1)"
                                    :disabled="pagination.current_page <= 1">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <li v-for="page in getPageNumbers()" :key="page" class="page-item"
                                :class="{ active: page === pagination.current_page }">
                                <a v-if="page !== '...'" class="page-link" href="#"
                                    @click.prevent="changePage(page)">
                                    @{{ page }}
                                </a>
                                <span v-else class="page-link">...</span>
                            </li>

                            <!-- Next Page -->
                            <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                                <a class="page-link" href="#"
                                    @click.prevent="changePage(pagination.current_page + 1)"
                                    :disabled="pagination.current_page >= pagination.last_page">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Pagination Info -->
                    <div class="pagination-info mt-2">
                        <small class="text-muted">
                            @{{ pagination.from }}-@{{ pagination.to }} / @{{ pagination.total }} kayıt
                        </small>
                    </div>
                </div>
            </div>

        </div>

        <!-- Invoice Sales Detail Modal -->
        <div class="modal fade" id="invoiceSalesModal" tabindex="-1" aria-labelledby="invoiceSalesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="invoiceSalesModalLabel">
                            <i class="bx bx-receipt me-2"></i>
                            <span v-html="'Fatura Detayları - #' + (selectedInvoice.number || '')"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div v-show="loading.invoiceDetails" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Fatura detayları yükleniyor...</p>
                        </div>

                        <div v-show="!loading.invoiceDetails">
                            <!-- Test Content -->
                            <div class="alert alert-success mb-3">
                                <strong>Content is visible!</strong> Loading state is false.
                            </div>

                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="stat-card stat-card-primary">
                                        <div class="stat-value"
                                            v-text="(invoiceDetails.totals && invoiceDetails.totals.items_count) || 0">
                                        </div>
                                        <div class="stat-label">Toplam Ürün</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card stat-card-success">
                                        <div class="stat-value"
                                            v-text="formatCurrency((invoiceDetails.totals && invoiceDetails.totals.total_sale_price) || 0)">
                                        </div>
                                        <div class="stat-label">Satış Toplamı</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card stat-card-warning">
                                        <div class="stat-value"
                                            v-text="formatCurrency((invoiceDetails.totals && invoiceDetails.totals.total_cost_price) || 0)">
                                        </div>
                                        <div class="stat-label">Maliyet Toplamı</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-card stat-card-primary">
                                        <div class="stat-value"
                                            :class="{
                                                'text-success': (invoiceDetails.totals && invoiceDetails.totals
                                                    .total_profit) > 0,
                                                'text-danger': (invoiceDetails.totals && invoiceDetails.totals
                                                    .total_profit) < 0
                                            }">
                                            <span
                                                v-text="formatCurrency((invoiceDetails.totals && invoiceDetails.totals.total_profit) || 0)"></span>
                                        </div>
                                        <div class="stat-label">Net Kar</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Detail Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-header-modern">
                                        <tr>
                                            <th class="compact-header">
                                                <i class="bx bx-package me-1"></i>
                                                <span class="header-text">Ürün</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-tag me-1"></i>
                                                <span class="header-text">Marka</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-barcode me-1"></i>
                                                <span class="header-text">Seri No</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-category me-1"></i>
                                                <span class="header-text">Tip</span>
                                            </th>
                                            <th class="compact-header text-end">
                                                <i class="bx bx-money me-1"></i>
                                                <span class="header-text">Satış<br><small>Fiyatı</small></span>
                                            </th>
                                            <th class="compact-header text-end">
                                                <i class="bx bx-calculator me-1"></i>
                                                <span class="header-text">Maliyet</span>
                                            </th>
                                            <th class="compact-header text-end">
                                                <i class="bx bx-trending-up me-1"></i>
                                                <span class="header-text">Kar</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-user me-1"></i>
                                                <span class="header-text">Satışçı</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="sale in (invoiceDetails.sales || [])" :key="sale.id">
                                            <td class="text-truncate-custom">
                                                <span class="text-compact fw-semibold" v-text="sale.stock_name"></span>
                                            </td>
                                            <td>
                                                <span class="text-compact" v-text="sale.brand_name"></span>
                                            </td>
                                            <td>
                                                <code class="text-compact" v-text="sale.serial_number"></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary text-compact"
                                                    v-text="sale.type_name"></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="price-display text-compact fw-bold"
                                                    v-text="formatCurrency(sale.sale_price)"></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="price-display text-compact"
                                                    v-text="formatCurrency(sale.base_cost_price)"></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="price-display text-compact"
                                                    :class="{
                                                        'text-success': sale.profit > 0,
                                                        'text-danger': sale.profit < 0
                                                    }"
                                                    v-text="formatCurrency(sale.profit)">
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-compact" v-text="sale.seller_name"></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-css')
    <!-- Base List Page Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/list-page-base.css') }}">
    <!-- Sale Index Specific Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/sale-index.css') }}">
@endsection

@section('custom-js')
    <!-- Vue.js CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const {
            createApp
        } = Vue;

        // Axios CSRF token setup
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        createApp({
            delimiters: ['[[', ']]'], // Blade ile çakışmaması için

            mixins: [VueGlobalMixin],
            data() {
                return {
                    // Search form
                    searchForm: {
                        startDate: '',
                        endDate: '',
                        stockName: '',
                        serialNumber: '',
                        brand: '',
                        version: '',
                        category: '',
                        seller: '',
                        customerName: ''
                    },

                    // Main data
                    invoices: [], // Changed from sales to invoices
                    pagination: {},
                    debugInfo: 'Starting...',

                    // Invoice details for modal
                    selectedInvoice: {},
                    invoiceDetails: {
                        sales: [],
                        totals: {
                            items_count: 0,
                            total_sale_price: 0,
                            total_cost_price: 0,
                            total_profit: 0
                        }
                    },

                    // Loading states
                    loading: {
                        search: false,
                        invoices: false,
                        invoiceDetails: false,
                        totals: false,
                        datePicker: false,
                        versions: false
                    },

                    // Totals
                    totals: {
                        credit_card: 0,
                        cash: 0,
                        installment: 0,
                        gross_total: 0,
                        total_price: 0,
                        discount: 0,
                        profit: 0
                    }
                }
            },

            async mounted() {
                try {
                    console.log('Sale Index - Starting mount...');

                    // Filter verilerini yükle
                    await this.loadFilterData();
                    console.log('Filter data loaded');

                    await this.loadInitialData();
                    console.log('Initial data loaded successfully');

                    this.setupDebouncedSearch();
                    console.log('Sale Index - Mount completed');
                } catch (error) {
                    console.error('Error during mount:', error);
                }
            },

            methods: {
                // Filter verilerini yükle
                async loadFilterData() {
                    try {
                        // Backend'den gelen data'yı kullan
                        this.globalBrands = @json($brands ?? []);
                        this.globalCategories = @json($categories ?? []);
                        this.globalSellers = @json($sellers ?? []);

                        console.log('Filter data loaded from backend:', {
                            brands: this.globalBrands.length,
                            categories: this.globalCategories.length,
                            sellers: this.globalSellers.length
                        });

                    } catch (error) {
                        console.error('Error loading filter data:', error);
                        this.showNotification('Genel veriler yüklenemedi', 'error');
                    }
                },

                async loadInitialData() {
                    try {
                        this.debugInfo = 'Starting loadInitialData...';
                        this.loading.invoices = true;

                        const today = new Date().toISOString().split('T')[0];
                        this.debugInfo = 'Making API call...';

                        const response = await axios.get('/sale/ajax', {
                            params: {
                                daterange: `${today} to ${today}`
                            },
                            timeout: 15000
                        });

                        this.debugInfo = 'API call successful';
                        console.log('API Response:', response.data);

                        if (response.data && response.data.invoices) {
                            this.invoices = response.data.invoices;
                            this.pagination = response.data.pagination || {};
                            this.debugInfo = `${this.invoices.length} invoices loaded`;
                        } else {
                            this.debugInfo = 'No invoices data in response';
                            this.invoices = [];
                        }

                        // Load totals async - don't wait for it
                        setTimeout(() => {
                            this.loadTotalsAsync();
                        }, 100);

                    } catch (error) {
                        this.debugInfo = `Error: ${error.message}`;
                        console.error('Faturalar yüklenemedi:', error);
                        if (error.response) {
                            console.error('Response error:', error.response.data);
                            this.debugInfo += ` | Response: ${error.response.status}`;
                        }
                        this.invoices = [];
                    } finally {
                        this.loading.invoices = false;
                    }
                },

                async loadTotalsAsync() {
                    try {
                        this.loading.totals = true;

                        const searchParams = {
                            ...this.searchForm
                        };
                        if (searchParams.startDate && searchParams.endDate) {
                            searchParams.daterange = `${searchParams.startDate} to ${searchParams.endDate}`;
                        }

                        const response = await axios.get('/sale/totals-async', {
                            params: searchParams
                        });

                        this.totals = response.data.totals || {};

                    } catch (error) {
                        console.error('Toplamlar hesaplanamadı:', error);
                    } finally {
                        this.loading.totals = false;
                    }
                },

                async openInvoiceModal(invoiceId) {
                    console.log('Opening modal for invoice:', invoiceId);

                    // Set selected invoice
                    this.selectedInvoice = this.invoices.find(inv => inv.id === invoiceId) || {};
                    console.log('Selected invoice:', this.selectedInvoice);

                    // Reset modal data
                    this.invoiceDetails = {
                        sales: [],
                        totals: {
                            items_count: 0,
                            total_sale_price: 0,
                            total_cost_price: 0,
                            total_profit: 0
                        }
                    };

                    // Open modal first, then load data
                    const modal = new bootstrap.Modal(document.getElementById('invoiceSalesModal'), {
                        backdrop: 'static',
                        keyboard: false,
                        focus: true
                    });
                    modal.show();

                    // Start loading
                    this.loading.invoiceDetails = true;

                    try {
                        // Load invoice sales details
                        const response = await axios.get(`/sale/invoice-details/${invoiceId}`);
                        console.log('Invoice details response:', response.data);

                        if (response.data && response.data.sales) {
                            // Direct object assignment
                            this.invoiceDetails = response.data;
                            this.loading.invoiceDetails = false;
                            console.log('Invoice details loaded successfully');
                            console.log('Updated invoiceDetails:', this.invoiceDetails);
                        } else {
                            console.warn('No sales data received');
                        }

                    } catch (error) {
                        console.error('Error loading invoice details:', error);
                        alert('Fatura detayları yüklenemedi!');
                    } finally {
                        // Simple loading state update
                        this.loading.invoiceDetails = false;
                        console.log('Loading set to false');
                        console.log('Current loading state:', this.loading.invoiceDetails);
                        console.log('Current invoiceDetails:', this.invoiceDetails);

                        // Force Vue to re-render
                        this.$forceUpdate();
                        console.log('Force update called');

                        // Manual DOM manipulation as last resort
                        setTimeout(() => {
                            const loadingDiv = document.querySelector(
                                '#invoiceSalesModal .modal-body div[v-show="loading.invoiceDetails"]'
                            );
                            const contentDiv = document.querySelector(
                                '#invoiceSalesModal .modal-body div[v-show="!loading.invoiceDetails"]'
                            );

                            if (loadingDiv) {
                                loadingDiv.style.display = 'none';
                                console.log('Loading div manually hidden');
                            }

                            if (contentDiv) {
                                contentDiv.style.display = 'block';
                                console.log('Content div manually shown');
                            }
                        }, 100);
                    }
                },

                async searchSales() {
                    try {
                        this.loading.search = true;

                        // Prepare search parameters
                        const searchParams = {
                            ...this.searchForm
                        };

                        // Convert date range format for API
                        if (searchParams.startDate && searchParams.endDate) {
                            searchParams.daterange = `${searchParams.startDate} to ${searchParams.endDate}`;
                        }

                        // Remove startDate and endDate from params as we converted them to daterange
                        delete searchParams.startDate;
                        delete searchParams.endDate;

                        const response = await axios.get('/sale/ajax', {
                            params: searchParams,
                            timeout: 10000 // 10 second timeout
                        });
                        this.invoices = response.data.invoices || [];
                        this.pagination = response.data.pagination || {};

                        // Load totals async
                        this.loadTotalsAsync();
                    } catch (error) {
                        console.error('Arama yapılamadı:', error);
                        let errorMessage = 'Arama yapılamadı';
                        if (error.response && error.response.data && error.response.data.error) {
                            errorMessage = error.response.data.error;
                        } else if (error.message) {
                            errorMessage = error.message;
                        }
                        this.showNotification('Hata', errorMessage, 'error');
                    } finally {
                        this.loading.search = false;
                    }
                },

                async getVersions() {
                    try {
                        if (this.searchForm.brand) {
                            console.log('Loading versions for brand:', this.searchForm.brand);
                            const response = await axios.get(
                                `/sale/versions-ajax?brand_id=${this.searchForm.brand}`);
                            this.globalVersions = response.data.versions || [];
                            console.log('Versions loaded:', this.globalVersions.length);
                        } else {
                            this.globalVersions = [];
                            this.searchForm.version = '';
                        }
                    } catch (error) {
                        console.error('Error loading versions:', error);
                        this.globalVersions = [];
                    }
                },

                clearFilters() {
                    this.searchForm = {
                        startDate: '',
                        endDate: '',
                        stockName: '',
                        serialNumber: '',
                        brand: '',
                        version: '',
                        category: '',
                        seller: '',
                        customerName: ''
                    };
                    this.versions = [];
                    this.loadInitialData();
                },

                setDateRange(type) {
                    const today = new Date();
                    let startDate, endDate;

                    switch (type) {
                        case 'today':
                            startDate = endDate = today;
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(yesterday.getDate() - 1);
                            startDate = endDate = yesterday;
                            break;
                        case 'last7days':
                            endDate = today;
                            startDate = new Date(today);
                            startDate.setDate(startDate.getDate() - 6);
                            break;
                        case 'last30days':
                            endDate = today;
                            startDate = new Date(today);
                            startDate.setDate(startDate.getDate() - 29);
                            break;
                        case 'thismonth':
                            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                            endDate = today;
                            break;
                        case 'lastmonth':
                            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                            break;
                    }

                    this.searchForm.startDate = this.formatDateForInput(startDate);
                    this.searchForm.endDate = this.formatDateForInput(endDate);

                    // Auto search when date range is set
                    this.searchSales();
                },

                formatDateForInput(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('tr-TR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                formatDateForAPI(date) {
                    // Convert Date object to YYYY-MM-DD for API
                    if (date instanceof Date) {
                        return date.toISOString().split('T')[0];
                    }
                    // If it's a string, try to parse it
                    if (typeof date === 'string') {
                        const parsedDate = new Date(date);
                        if (!isNaN(parsedDate.getTime())) {
                            return parsedDate.toISOString().split('T')[0];
                        }
                    }
                    return date;
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('tr-TR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(amount || 0);
                },

                getTypeBadgeClass(type) {
                    const classes = {
                        1: 'bg-primary',
                        2: 'bg-success',
                        3: 'bg-info',
                        4: 'bg-warning'
                    };
                    return classes[type] || 'bg-secondary';
                },

                showNotification(title, message, type) {
                    // SweetAlert2 or custom notification
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: title,
                            text: message,
                            icon: type,
                            confirmButtonText: 'Tamam'
                        });
                    } else {
                        alert(`${title}: ${message}`);
                    }
                },

                setupDebouncedSearch() {
                    // Debounce search to prevent too many API calls
                    this.debounceTimer = null;
                },

                debouncedSearch() {
                    if (this.debounceTimer) {
                        clearTimeout(this.debounceTimer);
                    }
                    this.debounceTimer = setTimeout(() => {
                        this.searchSales();
                    }, 500); // 500ms delay
                },

                printInvoice(invoiceId) {
                    // Print invoice functionality
                    window.open(`/invoice/print/${invoiceId}`, '_blank');
                },

                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.searchForm.page = page;
                        this.searchSales();
                    }
                },

                getPageNumbers() {
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const delta = 2;
                    const range = [];
                    const rangeWithDots = [];

                    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                        range.push(i);
                    }

                    if (current - delta > 2) {
                        rangeWithDots.push(1, '...');
                    } else {
                        rangeWithDots.push(1);
                    }

                    rangeWithDots.push(...range);

                    if (current + delta < last - 1) {
                        rangeWithDots.push('...', last);
                    } else {
                        rangeWithDots.push(last);
                    }

                    return rangeWithDots;
                }
            }
        }).mount('#app');
    </script>
@endsection
