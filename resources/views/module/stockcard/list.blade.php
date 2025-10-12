@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/list-page-base.css') }}">
    <style>
        /* Text truncation with tooltip styles */
        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: help;
        }

        .text-truncate:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 3px;
            padding: 2px 4px;
            margin: -2px -4px;
        }

        /* Ensure table cells have proper width constraints */
        .professional-table td {
            vertical-align: middle;
        }

        /* Table width optimization */
        .professional-table {
            table-layout: fixed;
            width: 100%;
        }

        .professional-table th,
        .professional-table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Compact button styles */
        .btn-xs {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }

        /* Action buttons container */
        .d-flex.gap-1 {
            gap: 2px !important;
        }

        /* Table row height optimization */
        .professional-table tr {
            height: auto;
        }

        .professional-table td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        /* Modal table styles */
        .modal-xl {
            max-width: 95%;
        }

        .modal .table-responsive {
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal .table th,
        .modal .table td {
            padding: 0.5rem 0.3rem;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .modal .btn-xs {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
            line-height: 1;
        }

        /* Compact header styles */
        .compact-header {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.5rem 0.3rem;
        }

        .header-text {
            display: inline-block;
            margin-left: 0.25rem;
        }

        /* Autocomplete dropdown styles */
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2px;
        }

        .autocomplete-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
        }

        .autocomplete-item:hover {
            background-color: #f8f9fa;
            border-left: 3px solid #696cff;
            padding-left: 9px;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item strong {
            color: #333;
            font-size: 0.9rem;
        }

        .autocomplete-item small {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .autocomplete-item .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .autocomplete-loading {
            padding: 15px 12px;
            text-align: center;
            color: #6c757d;
        }

        .autocomplete-no-results {
            padding: 15px 12px;
            text-align: center;
            color: #999;
            font-size: 0.85rem;
        }

        /* Modal pagination styles */
        .modal .pagination {
            margin-bottom: 0;
        }

        .modal .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .modal .pagination-sm .page-link {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }

        /* Transfer Modal Styles */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        #transferModal .modal-header {
            border-radius: 0.5rem 0.5rem 0 0;
        }

        #transferModal .serial-list-container {
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        #transferModal .serial-list-container:hover {
            border-color: #667eea;
        }

        #transferModal .form-select:focus,
        #transferModal .form-control:focus,
        #transferModal textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        #transferModal .input-group .btn-danger {
            padding: 0.375rem 0.75rem;
        }

        #transferModal .input-group .btn-primary {
            padding: 0.375rem 0.75rem;
        }

        #transferModal .alert-info {
            background: linear-gradient(145deg, #e7f3ff 0%, #d4e9ff 100%);
            border: 1px solid #667eea;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
@endsection

@section('content')
    <div id="app">
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
                            <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Stok kartları ve seri
                                numaraları yönetimi</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('stockcard.deleted') }}" type="button" formtarget="_blank"
                            class="btn btn-success btn-sm">
                            <i class="bx bx-trash me-1"></i>
                            Silinen Seriler
                        </a>
                        <button id="barcode" type="button" formtarget="_blank"
                            onclick="document.getElementById('itemFrom').submit();" disabled="disabled"
                            class="btn btn-danger btn-sm">
                            <i class="bx bx-barcode me-1"></i>
                            Barkod Yazdır
                        </button>
                        @role(['Depo Sorumlusu', 'super-admin'])
                            <button id="multiplepriceUpdate" type="button" class="btn btn-warning btn-sm">
                                <i class="bx bx-dollar me-1"></i>
                                Fiyat Güncelle
                            </button>
                            <a href="{{ route('stockcard.create', ['category' => $category]) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>
                                Yeni Stok Ekle
                            </a>
                        @endrole
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
                <div class="card-body p-4">
                    <form @submit.prevent="searchStockCards" class="compact-filter-form">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category }}" />

                        <!-- Row 1: Main Filters -->
                        <div class="row g-2 mb-2">
                            <div class="col-lg-3 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-package"></i> Stok Adı
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" v-model="searchForm.stockName" @input="searchStock"
                                            @focus="onStockInputFocus" @blur="hideStockDropdown"
                                            class="form-control form-control-sm compact-input" placeholder="Stok adı ara..."
                                            autocomplete="off">
                                        <div v-if="showStockDropdown" class="autocomplete-dropdown">
                                            <!-- Loading State -->
                                            <div v-if="searchingStock" class="autocomplete-loading">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Aranıyor...</span>
                                                </div>
                                                <span class="ms-2">Aranıyor...</span>
                                            </div>

                                            <!-- Results -->
                                            <div v-else-if="filteredStocks.length > 0">
                                                <div v-for="stock in filteredStocks" :key="stock.id"
                                                    @mousedown.prevent="selectStock(stock)" class="autocomplete-item">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <strong>@{{ stock.name }}</strong>
                                                            <span class="text-muted"> - @{{ stock.brand_name }}</span>
                                                            <span class="text-muted" v-if="stock.version_name"
                                                                v-html="stock.version_name"></span>
                                                            <small class="text-muted d-block">
                                                                <i class="bx bx-category-alt"></i> @{{ stock.category_name }}
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge"
                                                                :class="stock.quantity > 0 ? 'bg-success' : 'bg-secondary'">
                                                                @{{ stock.quantity }} Adet
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- No Results -->
                                            <div v-else class="autocomplete-no-results">
                                                <i class="bx bx-search-alt"></i>
                                                <span class="ms-2">Sonuç bulunamadı</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-package"></i> Marka
                                    </label>
                                    <select v-model="searchForm.brand" @change="loadVersions"
                                        class="form-select form-select-sm compact-select">
                                        <option value="">Tüm Markalar</option>
                                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                            @{{ brand.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-mobile-alt"></i> Model
                                    </label>
                                    <select v-model="searchForm.version" class="form-select form-select-sm compact-select"
                                        :disabled="!searchForm.brand">
                                        <option value="">Tüm Modeller</option>
                                        <option v-for="version in versions" :key="version.id" :value="version.id">
                                            @{{ version.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-category"></i> Kategori
                                    </label>
                                    <select v-model="searchForm.category"
                                        class="form-select form-select-sm compact-select">
                                        <option value="">Tüm Kategoriler</option>
                                        <option v-for="category in categories" :key="category.id"
                                            :value="category.id">@{{ category.path }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-palette"></i> Renk
                                    </label>
                                    <select v-model="searchForm.color" class="form-select form-select-sm compact-select">
                                        <option value="">Tümü</option>
                                        <option v-for="color in colors" :key="color.id" :value="color.id">
                                            @{{ color.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Secondary Filters + Buttons -->
                        <div class="row g-2 mb-3">
                            <div class="col-lg-2 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-store"></i> Şube
                                    </label>
                                    <select v-model="searchForm.seller" class="form-select form-select-sm compact-select">
                                        <option value="all">Tüm Şubeler</option>
                                        <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                                            @{{ seller.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4">
                                <div class="compact-filter-group">
                                    <label class="compact-label">
                                        <i class="bx bx-barcode"></i> Seri Numarası
                                    </label>
                                    <input type="text" v-model="searchForm.serialNumber"
                                        class="form-control form-control-sm compact-input" placeholder="Seri numarası...">
                                </div>
                            </div>

                            <div class="col-lg-7 col-md-12">
                                <div class="compact-filter-group">
                                    <label class="compact-label d-none d-md-block invisible">Aksiyon</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" @click="searchStockCards"
                                            class="btn btn-primary btn-sm flex-fill" :disabled="loading.search">
                                            <span v-if="loading.search"
                                                class="spinner-border spinner-border-sm me-1"></span>
                                            <i v-else class="bx bx-search me-1"></i>
                                            @{{ loading.search ? 'Aranıyor...' : 'Ara' }}
                                        </button>
                                        <button type="button" @click="clearFilters"
                                            class="btn btn-outline-secondary btn-sm" title="Filtreleri Temizle">
                                            <i class="bx bx-refresh"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="itemFrom" role="form" method="POST" action="{{ route('stockcard.barcodes') }}">
                    @csrf
                    <div class="table-responsive text-nowrap">
                        <table class="table professional-table">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">
                                        <input type="checkbox" class="form-check-input" @change="toggleAllStockCards"
                                            :checked="allStockCardsSelected">
                                    </th>
                                    <th style="width: 40%;"><i class="bx bx-package me-1"></i>Stok Adı</th>
                                    <th style="width: 30%;"><i class="bx bx-category me-1"></i>Kategori</th>
                                    <th style="width: 10%;"><i class="bx bx-package me-1"></i>Marka</th>
                                    <th style="width: 50px;"><i class="bx bx-box me-1"></i>Adet</th>
                                    <th style="width: 120px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 professional-tbody">
                                <!-- Empty State - İlk açılışta gösterilecek -->
                                <tr v-if="stockcards.length === 0 && !loading.stockcards" class="text-center">
                                    <td colspan="6" class="py-4">
                                        <div class="empty-state">
                                            <i class="bx bx-search display-1 text-muted"></i>
                                            <h4 class="mt-3">Stok kartı aramak için filtreleri kullanın</h4>
                                            <p>Yukarıdaki filtreleri doldurarak arama yapabilirsiniz.</p>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Loading State -->
                                <tr v-if="loading.stockcards" class="text-center">
                                    <td colspan="6" class="py-4">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <p class="text-primary mt-2">Stok kartları yükleniyor...</p>
                                    </td>
                                </tr>

                                <!-- Stock Cards -->
                                <tr v-else v-for="stockcard in stockcards" :key="stockcard.id" class="clickable-row"
                                    @click="openStockModal(stockcard.ids, stockcard.id, stockcard.stock_name)">
                                    <td style="width: 20px;text-align: center;" @click.stop>
                                        <input type="checkbox" class="form-check-input" name="item[]"
                                            :value="stockcard.ids" v-model="selectedStockCards"
                                            @change="updateBarcodeButton">
                                    </td>
                                    <td style="width:40%;">
                                        <span class="text-truncate d-inline-block" :title="stockcard.stock_name"
                                            style="max-width: calc(100% - 35px);">
                                            @{{ stockcard.stock_name }}
                                        </span>
                                    </td>

                                    <td class="text-truncate" :title="stockcard.category_separator_name"
                                        style="width: 30%;">
                                        @{{ stockcard.category_separator_name }}
                                    </td>
                                    <td class="text-truncate" :title="stockcard.brand_name" style="width: 10%;">
                                        @{{ stockcard.brand_name }}
                                    </td>
                                    <td v-text="stockcard.quantity" style="width: 50px; text-align: center;"></td>
                                    <td style="white-space: nowrap;">
                                        <div class="d-flex gap-1 align-items-center">
                                            <a target="_blank" class="btn btn-xs btn-primary"
                                                :href="`/stockcard/barcode?ids=${stockcard.ids}`" title="Barkodlar">
                                                <i class="bx bx-barcode"></i>
                                            </a>
                                            @role('Depo Sorumlusu|super-admin')
                                                <button type="button" class="btn btn-xs btn-success" title="Fiyat Güncelle"
                                                    @click.stop="multipleAllPriceUpdate(stockcard.ids)">
                                                    <i class="bx bx-dollar"></i>
                                                </button>
                                              
                                            @endrole
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
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

            <hr class="my-5">


            <!-- Stock Details Modal -->
            <div class="modal fade" id="stockDetailsModal" tabindex="-1" aria-labelledby="stockDetailsModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" style="max-width: 95%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="stockDetailsModalLabel">
                                <i class="bx bx-package me-2"></i>
                                <span v-text="'Stok Detayları - ' + selectedStock.name"></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Loading State -->
                            <div v-show="loading.stockDetails" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-primary mt-2">Stok detayları yükleniyor...</p>
                            </div>

                            <!-- Content -->
                            <div v-show="!loading.stockDetails">
                                <!-- Summary Cards -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="stat-card stat-card-primary">
                                            <div class="stat-value" v-text="stockDetails.length"></div>
                                            <div class="stat-label">Toplam Seri</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card stat-card-success">
                                            <div class="stat-value" v-text="getAvailableCount()"></div>
                                            <div class="stat-label">Mevcut</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card stat-card-warning">
                                            <div class="stat-value" v-text="getDamagedCount()"></div>
                                            <div class="stat-label">Hasarlı</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-card stat-card-info">
                                            <div class="stat-value" v-text="getTransferCount()"></div>
                                            <div class="stat-label">Transfer</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Details Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-header-modern">
                                            <tr>
                                                <th class="compact-header" style="width: 70px;">
                                                    <i class="bx bx-check me-1"></i>
                                                    <span class="header-text">Seç</span>
                                                </th>
                                                <th class="compact-header" style="width: 100px;">
                                                    <i class="bx bx-hash me-1"></i>
                                                    <span class="header-text">#</span>
                                                </th>
                                                <th class="compact-header">
                                                    <i class="bx bx-barcode me-1"></i>
                                                    <span class="header-text">Seri No</span>
                                                </th>
                                                @role(['Depo Sorumlusu', 'super-admin'])
                                                    <th class="compact-header text-end"
                                                        style="width: 100px; text-align: center;">
                                                        <i class="bx bx-dollar me-1"></i>
                                                        <span class="header-text">Maliyet</span>
                                                    </th>
                                                    <th class="compact-header text-end"
                                                        style="width: 100px; text-align: center;">
                                                        <i class="bx bx-dollar me-1"></i>
                                                        <span class="header-text">D. Maliyet</span>
                                                    </th>
                                                @endrole
                                                <th class="compact-header text-end"
                                                    style="width: 100px; text-align: center;">
                                                    <i class="bx bx-dollar me-1"></i>
                                                    <span class="header-text">Satış F.</span>
                                                </th>
                                                <th class="compact-header" style="width: 100px;">
                                                    <i class="bx bx-palette me-1"></i>
                                                    <span class="header-text">Renk</span>
                                                </th>
                                                <th class="compact-header" style="width: 100px;">
                                                    <i class="bx bx-package me-1"></i>
                                                    <span class="header-text">Marka</span>
                                                </th>
                                                <th class="compact-header">
                                                    <i class="bx bx-mobile-alt me-1"></i>
                                                    <span class="header-text">Model</span>
                                                </th>
                                                <th class="compact-header">
                                                    <i class="bx bx-category me-1"></i>
                                                    <span class="header-text">Kategori</span>
                                                </th>
                                                <th class="compact-header" style="width: 200px;">
                                                    <i class="bx bx-store me-1"></i>
                                                    <span class="header-text">Şube</span>
                                                </th>
                                                <th class="compact-header">
                                                    <i class="bx bx-cog me-1"></i>
                                                    <span class="header-text">İşlemler</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in stockDetails" :key="item.id"
                                                :class="getRowClass(item)" v-show="item.quantity > 0">
                                                <td class="text-center">
                                                    <input type="checkbox" v-model="selectedItems" :value="item.id"
                                                        class="form-check-input">
                                                </td>
                                                <td v-text="item.id"></td>
                                                <td>
                                                    <code v-text="item.serial_number"></code> / <code
                                                        v-text="item.barcode"></code>
                                                </td>
                                                @role(['Depo Sorumlusu', 'super-admin'])
                                                    <td class="text-end">
                                                        <span class="price-display fw-bold"
                                                            v-text="formatCurrency(item.cost_price)"></span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="price-display fw-bold"
                                                            v-text="formatCurrency(item.base_cost_price)"></span>
                                                    </td>
                                                @endrole
                                                <td class="text-end">
                                                    <span class="price-display fw-bold"
                                                        v-text="formatCurrency(item.sale_price)"></span>
                                                </td>
                                                <td v-text="item.color_name"></td>
                                                <td v-text="item.brand_name"></td>
                                                <td v-text="item.versions"></td>
                                                <td>
                                                    <span
                                                        v-text="item.category_sperator_name + ' ' + item.category_name"></span>
                                                </td>
                                                <td v-text="item.seller_name"></td>
                                                <td>
                                                    <span v-if="item.type == 4" class="badge bg-primary">TRANSFER
                                                        SÜRECİNDE</span>
                                                    <span v-if="item.type == 3" class="badge bg-danger">HASARLI
                                                        ÜRÜN</span>
                                                    <span v-if="item.type == 5" class="badge bg-warning">TEKNİK SERVİS
                                                        SÜRECİNDE</span>

                                                    <span v-if="item.type == 1">
                                                        <!-- Action Buttons -->
                                                        <div class="d-flex gap-1 align-items-center">
                                                            <!-- Sevk Et -->
                                                            <button type="button" @click="openTransferModal(item)"
                                                                class="btn btn-xs btn-success" title="Sevk Et">
                                                                <i class="bx bx-transfer"></i>
                                                            </button>

                                                            <!-- Fiyat Güncelle (Depo Sorumlusu|super-admin) -->
                                                            @role('Depo Sorumlusu|super-admin')
                                                                <button type="button" @click="openPriceModal(item.id)"
                                                                    class="btn btn-xs btn-warning" title="Fiyat Güncelle">
                                                                    <i class="bx bxs-dollar-circle"></i>
                                                                </button>
                                                            @endrole

                                                            <!-- Sil (super-admin) -->
                                                            @role('super-admin')
                                                                <button type="button" @click="deleteMovement(item.id)"
                                                                    class="btn btn-xs btn-danger" title="Sil">
                                                                    <i class="bx bx-trash"></i>
                                                                </button>
                                                            @endrole

                                                            <!-- Talep Oluştur -->
                                                            <button type="button"
                                                                @click="openDemandModal(selectedStock.id, selectedStock.name, item.color_id)"
                                                                class="btn btn-xs btn-info" title="Talep Oluştur">
                                                                <i class="bx bx-radar"></i>
                                                            </button>
                                                        </div>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Modal Pagination -->
                                <nav v-if="stockDetailsPagination && stockDetailsPagination.last_page > 1" class="mt-3">
                                    <ul class="pagination pagination-sm justify-content-center">
                                        <li class="page-item"
                                            :class="{ disabled: stockDetailsPagination.current_page <= 1 }">
                                            <button class="page-link"
                                                @click="changeStockDetailsPage(stockDetailsPagination.current_page - 1)"
                                                :disabled="stockDetailsPagination.current_page <= 1">
                                                <i class="bx bx-chevron-left"></i>
                                            </button>
                                        </li>

                                        <li v-for="page in getStockDetailsPageNumbers()" :key="page"
                                            class="page-item"
                                            :class="{ active: page === stockDetailsPagination.current_page }">
                                            <button class="page-link" @click="changeStockDetailsPage(page)">
                                                @{{ page }}
                                            </button>
                                        </li>

                                        <li class="page-item"
                                            :class="{ disabled: stockDetailsPagination.current_page >= stockDetailsPagination
                                                    .last_page }">
                                            <button class="page-link"
                                                @click="changeStockDetailsPage(stockDetailsPagination.current_page + 1)"
                                                :disabled="stockDetailsPagination.current_page >= stockDetailsPagination.last_page">
                                                <i class="bx bx-chevron-right"></i>
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            @{{ stockDetailsPagination.from }} - @{{ stockDetailsPagination.to }} / @{{ stockDetailsPagination.total }}
                                            kayıt
                                            (Sayfa @{{ stockDetailsPagination.current_page }} / @{{ stockDetailsPagination.last_page }})
                                        </small>
                                    </div>
                                </nav>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            <button v-if="selectedItems.length > 0" type="button" class="btn btn-primary"
                                @click="enableBarcodePrint">
                                Barkod Yazdır (@{{ selectedItems.length }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="transferForm">
                    @csrf
                    <input id="stockCardId" name="stock_card_id" type="hidden">
                    <input id="id" name="id" type="hidden">
                    <input id="type" name="type" value="other" type="hidden">
                    <div class="modal-header">
                        <h5 class="modal-title" id="backDropModalTitle">Sevk İşlemi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBackdrop" class="form-label">Serial Number</label>
                                <input type="text" id="serialBackdrop" class="form-control" name="serial_number[]" />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="sellerBackdrop" class="form-label">Şube</label>
                                <select class="form-control" name="seller_id" id="sellerBackdrop">
                                    <option value="">Şube Seçin</option>
                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                                        @{{ seller.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="sellerBackdrop" class="form-label">Neden</label>
                                <select class="form-control" name="reason_id" id="sellerBackdrop">
                                    <option value="4">SATIŞ</option>
                                    <option value="5">SIFIR</option>
                                    <option value="6">İKİNCİ El SATIŞ</option>
                                    <option value="7">SATIŞ İADE</option>
                                    <option value="8">HASARLI İADE</option>
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
                    <input id="stockCardMovementId" name="stock_card_id" type="hidden">
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
        <div class="modal fade" id="demandModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
                <div class="modal-content" style="padding: 1px">
                    <div class="modal-header">Ürün Adı : <span></span></div>
                    <form action="{{ route('demand.store') }}" method="post">
                        <input type="hidden" name="id" id="id" value="">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="nameSmall" class="form-label">Renk</label>
                                    <select class="form-select" id="color" name="color_id">
                                        <option value="">Renk Seçin</option>
                                        <option v-for="color in colors" :key="color.id" :value="color.id">
                                            @{{ color.name }}</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="nameSmall" class="form-label">Açıklama</label>
                                    <input type="text" id="nameSmall" name="description" class="form-control"
                                        placeholder="Açıklama">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="multiplepriceModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="multiplepriceForm">
                    @csrf
                    <input id="stockCardMovementIdArray" name="stock_card_id_multiple" type="hidden">
                    <div class="modal-header">
                        <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBackdrop" class="form-label">Barkod</label>
                                <input type="text" id="barcode" class="form-control" name="barcode" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBackdrop" class="form-label">Maliyet</label>
                                <input type="text" id="cost_price" class="form-control" name="cost_price" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBackdrop" class="form-label">Destekli Maliyet</label>
                                <input type="text" id="base_cost_price" class="form-control"
                                    name="base_cost_price" />
                            </div>
                        </div>
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
  
        <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" action="{{ route('stockcard.movementdelete') }}" id="deleteModalForm">
                    @csrf
                    <input id="stockCardMovementIdDelete" name="stock_card_movement_id" type="hidden">
                    <div class="modal-header">
                        <h5 class="modal-title" id="backDropModalTitle">Silmek icin not girmelisiniz</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nameBackdrop" class="form-label">Not</label>
                                <input type="text" id="note" class="form-control" name="note" required />
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
 

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-transfer me-2"></i>
                        Transfer İşlemi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="transferForm" @submit.prevent="submitTransfer">
                        <!-- Transfer Bilgileri -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"></label>
                                    <i class="bx bx-user me-1"></i>
                                    Gönderici Bayi
                                </label>

                                <select v-model="transferForm.main_seller_id" class="form-select" required>
                                    <option value="">Bayi Seçiniz</option>
                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id"
                                        v-text="seller.name"></option>
                                </select>
                                <!-- Debug -->
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="bx bx-building me-1"></i>
                                    Alıcı Bayi
                                </label>
                                <select v-model="transferForm.delivery_seller_id" class="form-select" required>
                                    <option value="">Bayi Seçiniz</option>
                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id"
                                        v-text="seller.name"></option>
                                </select>
                                <!-- Debug -->
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">
                                    <i class="bx bx-hash me-1"></i>
                                    Sevk Numarası
                                </label>
                                <input type="text" v-model="transferForm.number" class="form-control"
                                    placeholder="Otomatik oluşturulacak" readonly>
                            </div>
                        </div>

                        <!-- Seri Numaraları -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bx bx-barcode me-1"></i>
                                Seri Numaraları
                            </label>
                            <div class="serial-list-container p-3 bg-light rounded">
                                <!-- Debug info -->


                                <div v-if="transferForm.sevkList.length === 0" class="text-muted text-center py-3">
                                    Seri numarası eklemek için aşağıdaki alana girin
                                </div>
                                <div v-for="(serial, index) in transferForm.sevkList" :key="index"
                                    class="input-group mb-2">
                                    <input type="text" :value="serial" class="form-control" readonly>
                                    <button type="button" @click="removeSerial(index)" class="btn btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>

                                <!-- Yeni Seri Ekle -->
                                <div class="input-group mt-3">
                                    <input type="text" v-model="newSerial" @keyup.enter="addSerial"
                                        class="form-control" placeholder="Seri numarası girin ve Enter tuşuna basın">
                                    <button type="button" @click="addSerial" class="btn btn-primary">
                                        <i class="bx bx-plus"></i> Ekle
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Not -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bx bx-note me-1"></i>
                                Açıklama
                            </label>
                            <textarea v-model="transferForm.description" class="form-control" rows="3"
                                placeholder="Transfer hakkında notlarınızı yazabilirsiniz..."></textarea>
                        </div>

                        <!-- Ürün Bilgileri -->
                        <div v-if="transferForm.selectedItem" class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="bx bx-info-circle me-1"></i>
                                Transfer Edilecek Ürün
                            </h6>
                            <p class="mb-1"><strong>Stok Adı:</strong> <span
                                    v-text="transferForm.selectedItem.stock_name"></span></p>
                            <p class="mb-1"><strong>Seri No:</strong> <span
                                    v-text="transferForm.selectedItem.serial_number"></span></p>
                            <p class="mb-1"><strong>Marka:</strong> <span
                                    v-text="transferForm.selectedItem.brand_name"></span></p>
                            <p class="mb-1"><strong>Renk:</strong> <span
                                    v-text="transferForm.selectedItem.color_name"></span></p>
                            <p class="mb-0"><strong>Bayi:</strong> <span
                                    v-text="transferForm.selectedItem.seller_name"></span></p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        İptal
                    </button>
                    <button type="button" @click="submitTransfer" class="btn btn-primary"
                        :disabled="loading.transfer || transferForm.sevkList.length === 0">
                        <span v-if="loading.transfer">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            İşleniyor...
                        </span>
                        <span v-else>
                            <i class="bx bx-paper-plane me-1"></i>
                            Transfer Oluştur
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Axios CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>
    <script>
        const {
            createApp
        } = Vue;

        // Axios CSRF token setup
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

        // Initial data from backend
        window.initialData = {
            brands: @json($brands ?? []),
            colors: @json($colors ?? [])
        };

        createApp({
            data() {
                return {
                    // Main data
                    stockcards: [],
                    pagination: {},

                    // Filter data (will be loaded dynamically)
                    brands: [],
                    versions: [],
                    categories: @json($categories ?? []),
                    colors: [],
                    sellers: [],

                    // Autocomplete data
                    filteredStocks: [],
                    showStockDropdown: false,
                    stockSearchTimeout: null,
                    searchingStock: false,

                    // Search form
                    searchForm: {
                        stockName: '',
                        brand: '',
                        version: '',
                        category: '',
                        color: '',
                        seller: '',
                        serialNumber: '',
                        page: 1
                    },

                    // Stock details modal data
                    selectedStock: {
                        id: null,
                        name: '',
                        ids: ''
                    },
                    stockDetails: [],
                    selectedItems: [],
                    selectedStockCards: [], // For main table barcode printing
                    stockDetailsPagination: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 10,
                        total: 0,
                        from: 0,
                        to: 0
                    },

                    // Loading states
                    loading: {
                        search: false,
                        stockcards: false,
                        stockDetails: false,
                        transfer: false
                    },

                    // Transfer form data
                    transferForm: {
                        main_seller_id: '',
                        delivery_seller_id: '',
                        number: Math.floor(Math.random() * 999999989) + 111,
                        sevkList: [],
                        description: '',
                        type: 'other',
                        selectedItem: null
                    },
                    newSerial: '',
                    userRoles: @json(auth()->user()->getRoleNames() ?? [])
                }
            },

            async mounted() {
                console.log('Vue.js StockCard List mounted');

                // Filter verilerini yükle
                await this.loadFilterData();
                // Sayfa açıldığında verileri yükle
                await this.loadStockCards();
            },

            computed: {
                allStockCardsSelected() {
                    return this.stockcards.length > 0 &&
                        this.selectedStockCards.length === this.stockcards.length;
                }
            },

            methods: {

                // Filter verilerini yükle
                async loadFilterData() {

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

            // Load versions by brand
            async loadVersions() {
                try {
                    if (this.searchForm.brand) {
                        console.log('Loading versions for brand:', this.searchForm.brand);
                        const response = await axios.get(
                            `/api/common/versions?brand_id=${this.searchForm.brand}`);
                        this.versions = response.data || [];
                        console.log('Versions loaded:', this.versions.length);
                    } else {
                        this.versions = [];
                        this.searchForm.version = '';
                    }
                } catch (error) {
                    console.error('Error loading versions:', error);
                    this.versions = [];
                }
            },

            // Stock autocomplete search
            searchStock() {
                clearTimeout(this.stockSearchTimeout);

                if (this.searchForm.stockName.length < 2) {
                    this.filteredStocks = [];
                    this.showStockDropdown = false;
                    this.searchingStock = false;
                    return;
                }

                // Show dropdown and loading state immediately when typing
                this.showStockDropdown = true;
                this.searchingStock = true;

                this.stockSearchTimeout = setTimeout(async () => {
                    try {
                        const response = await axios.get('/searchStockCard', {
                            params: {
                                search: this.searchForm.stockName
                            }
                        });

                        if (response.data && Array.isArray(response.data)) {
                            this.filteredStocks = response.data.slice(0, 10); // Limit to 10 results
                            this.showStockDropdown = this.filteredStocks.length > 0;
                        } else {
                            this.filteredStocks = [];
                            this.showStockDropdown = false;
                        }
                    } catch (error) {
                        console.error('Stock search error:', error);
                        this.filteredStocks = [];
                        this.showStockDropdown = false;
                    } finally {
                        this.searchingStock = false;
                    }
                }, 300); // 300ms debounce
            },

            // Focus event - show previous results if available
            onStockInputFocus() {
                if (this.searchForm.stockName.length >= 2) {
                    // Trigger search to get fresh results
                    this.searchStock();
                }
            },

            // Select stock from autocomplete
            selectStock(stock) {
                // IMPORTANT: Only store the stock name (not brand/model)
                // Backend searches by 'name' field: WHERE name LIKE '%stockName%'
                this.searchForm.stockName = stock.name;
                this.showStockDropdown = false;
                this.filteredStocks = [];
            },

            // Hide stock dropdown
            hideStockDropdown() {
                setTimeout(() => {
                    this.showStockDropdown = false;
                }, 200);
            },

            async loadStockCards() {
                try {
                    console.log('loadStockCards called');
                    this.loading.stockcards = true;

                    const params = new URLSearchParams();
                    Object.keys(this.searchForm).forEach(key => {
                        if (this.searchForm[key]) {
                            params.append(key, this.searchForm[key]);
                        }
                    });

                    // Category ID'yi URL'den al
                    const urlParams = new URLSearchParams(window.location.search);
                    const categoryId = urlParams.get('category_id') || this.searchForm.category;
                    if (categoryId) {
                        params.append('category_id', categoryId);
                    }

                    console.log('Request params:', params.toString());
                    const url = `{{ route('stockcard.getListData') }}?${params.toString()}`;
                    console.log('Request URL:', url);

                    const response = await axios.get(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });


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
                        this.stockcards = [];
                    }

                } catch (error) {
                    console.error('Error loading stock cards:', error);
                    this.stockcards = [];
                } finally {
                    this.loading.stockcards = false;
                }
            },

            async searchStockCards() {
                console.log('searchStockCards called');
                console.log('Search form:', this.searchForm);
                await this.loadStockCards();
            },

            // Checkbox methods for main table barcode printing
            toggleAllStockCards(event) {
                if (event.target.checked) {
                    this.selectedStockCards = this.stockcards.map(sc => sc.ids);
                } else {
                    this.selectedStockCards = [];
                }
                this.updateBarcodeButton();
            },

            updateBarcodeButton() {
                const barcodeButton = document.getElementById('barcode');
                if (this.selectedStockCards.length > 0) {
                    barcodeButton.removeAttribute('disabled');
                } else {
                    barcodeButton.setAttribute('disabled', 'disabled');
                }
            },

            // Pagination methods
            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.searchForm.page = page;
                    this.loadStockCards();
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
            },

            clearFilters() {
                this.searchForm = {
                    stockName: '',
                    brand: '',
                    version: '',
                    category: '',
                    color: '',
                    seller: '',
                    serialNumber: '',
                    page: 1
                };
                this.loadStockCards();
            },


            async openStockModal(ids, stockId, stockName) {
                console.log('Opening stock modal for:', {
                    ids,
                    stockId,
                    stockName
                });

                // Set selected stock
                this.selectedStock = {
                    id: stockId,
                    name: stockName,
                    ids: ids
                };

                // Reset data
                this.stockDetails = [];
                this.selectedItems = [];

                // Open modal first
                const modal = new bootstrap.Modal(document.getElementById('stockDetailsModal'), {
                    backdrop: 'static',
                    keyboard: false,
                    focus: true
                });
                modal.show();

                // Start loading
                this.loading.stockDetails = true;

                // Load stock movements with pagination
                await this.loadStockMovements(stockId, 1);
            },

            getRowClass(item) {
                if (item.quantity == 0) return 'table-warning';
                if (item.type == 3) return 'table-danger';
                return '';
            },

            getAvailableCount() {
                return this.stockDetails.filter(item => item.type == 1 && item.quantity > 0).length;
            },

            getDamagedCount() {
                return this.stockDetails.filter(item => item.type == 3).length;
            },

            getTransferCount() {
                return this.stockDetails.filter(item => item.type == 4).length;
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('tr-TR', {
                    style: 'currency',
                    currency: 'TRY'
                }).format(amount || 0);
            },

            openPriceModal(id) {
                $("#priceModal").modal('show');
                $("#priceModal #stockCardMovementId").val(id);
            },

            openDemandModal(id, name, color) {
                $("#demandModal").modal('show');
                $("#demandModal").find('.modal-header span').html(name);
                $("#demandModal").find('input#id').val(id);
                $("#demandModal").find('select#color').val(color).trigger('change');
                $("#demandModal").find('select#color').attr('data-color', color);
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

            enableBarcodePrint() {
                if (this.selectedItems.length > 0) {
                    // Enable barcode print button
                    document.getElementById('barcode').disabled = false;

                    // Set selected items in form
                    const form = document.getElementById('itemFrom');
                    const checkboxes = form.querySelectorAll('input[name="selected[]"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.selectedItems.includes(parseInt(checkbox.value));
                    });

                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('stockDetailsModal')).hide();

                    // Show success message
                    Swal.fire({
                        title: 'Başarılı!',
                        text: `${this.selectedItems.length} ürün seçildi. Barkod yazdırma aktif.`,
                        icon: 'success',
                        timer: 2000
                    });
                }
            },

            multipleAllPriceUpdate(ids) {
                if (!ids) {
                    Swal.fire('Seçim Yapılmadı');
                    return;
                }
                $("#multiplepriceModal").modal('show');
                $("#multiplepriceModal").find("#stockCardMovementIdArray").val(ids);
            },

 

            // Transfer Modal İşlemleri
            openTransferModal(item) {
                console.log('Opening transfer modal for item:', item);
                console.log('Available sellers:', this.sellers);

                // Form'u sıfırla
                this.transferForm = {
                    main_seller_id: item.seller_id || (item.seller ? item.seller.id : ''),
                    delivery_seller_id: '',
                    number: Math.floor(Math.random() * 999999989) + 111,
                    sevkList: [item.serial_number],
                    description: '',
                    type: 'other',
                    selectedItem: item
                };
                this.newSerial = '';
                console.log('Transfer form initialized:', this.transferForm);

                // Modal'ı aç
                const modal = new bootstrap.Modal(document.getElementById('transferModal'));
                modal.show();
            },

            addSerial() {
                if (!this.newSerial || this.newSerial.length < 7) {
                    this.showNotification('Geçerli bir seri numarası girin (minimum 7 karakter)', 'warning');
                    return;
                }

                // Aynı seri numarası var mı kontrol et
                if (this.transferForm.sevkList.includes(this.newSerial)) {
                    this.showNotification('Bu seri numarası zaten eklenmiş', 'warning');
                    this.newSerial = '';
                    return;
                }

                // Seri numarasını ekle
                this.transferForm.sevkList.push(this.newSerial);
                this.newSerial = '';
            },

            removeSerial(index) {
                this.transferForm.sevkList.splice(index, 1);
            },

            async submitTransfer() {
                // Validasyon
                if (!this.transferForm.main_seller_id) {
                    this.showNotification('Gönderici bayi seçiniz', 'warning');
                    return;
                }

                if (!this.transferForm.delivery_seller_id) {
                    this.showNotification('Alıcı bayi seçiniz', 'warning');
                    return;
                }

                if (this.transferForm.sevkList.length === 0) {
                    this.showNotification('En az bir seri numarası eklemelisiniz', 'warning');
                    return;
                }

                if (this.transferForm.main_seller_id === this.transferForm.delivery_seller_id) {
                    this.showNotification('Gönderici ve alıcı bayi aynı olamaz', 'warning');
                    return;
                }

                try {
                    this.loading.transfer = true;

                    const response = await axios.post('/transfer/store', {
                        main_seller_id: this.transferForm.main_seller_id,
                        delivery_seller_id: this.transferForm.delivery_seller_id,
                        number: this.transferForm.number,
                        sevkList: this.transferForm.sevkList,
                        description: this.transferForm.description,
                        type: this.transferForm.type
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    console.log('Transfer response:', response);

                    // Modal'ı kapat
                    const modal = bootstrap.Modal.getInstance(document.getElementById('transferModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Başarı mesajı
                    this.showNotification(response.data || 'Transfer başarıyla oluşturuldu', 'success');

                    // Sayfayı yenile
                    await this.loadStockCards();

                } catch (error) {
                    console.error('Transfer error:', error);
                    const errorMessage = error.response?.data?.message || error.response?.data ||
                        'Transfer oluşturulurken bir hata oluştu';
                    this.showNotification(errorMessage, 'error');
                } finally {
                    this.loading.transfer = false;
                }
            },

            hasRole(roles) {
                if (!Array.isArray(roles)) {
                    roles = [roles];
                }
                return roles.some(role => this.userRoles.includes(role));
            },

            // Bildirim göster
            showNotification(message, type = 'success') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    console.log(`${type.toUpperCase()}: ${message}`);
                }
            },

            // Modal pagination methods
            changeStockDetailsPage(page) {
                if (page >= 1 && page <= this.stockDetailsPagination.last_page) {
                    this.stockDetailsPagination.current_page = page;
                    this.loadStockMovements(this.selectedStock.id, page);
                }
            },

            getStockDetailsPageNumbers() {
                const current = this.stockDetailsPagination.current_page;
                const last = this.stockDetailsPagination.last_page;
                const pages = [];

                // Show max 5 pages
                let start = Math.max(1, current - 2);
                let end = Math.min(last, start + 4);

                // Adjust start if we're near the end
                if (end - start < 4) {
                    start = Math.max(1, end - 4);
                }

                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }

                return pages;
            },

            // Updated loadStockMovements method with pagination
            async loadStockMovements(stockCardId, page = 1) {
                try {
                    this.loading.stockDetails = true;

                    const response = await axios.get('/stockcard/movements', {
                        params: {
                            stock_card_ids: stockCardId,
                            page: page,
                            per_page: 10,
                            serialNumber: '',
                            seller: '',
                            color: ''
                        }
                    });

                    console.log('Stock movements response:', response.data);

                    // Parse response data
                    if (response.data && response.data.data) {
                        this.stockDetails = response.data.data;
                        this.stockDetailsPagination = {
                            current_page: response.data.current_page || 1,
                            last_page: response.data.last_page || 1,
                            per_page: response.data.per_page || 10,
                            total: response.data.total || 0,
                            from: response.data.from || 0,
                            to: response.data.to || 0
                        };
                        console.log('Stock details loaded:', this.stockDetails.length, 'items');
                    } else {
                        console.warn('No stock movements data found in response');
                        this.stockDetails = [];
                        this.stockDetailsPagination = {
                            current_page: 1,
                            last_page: 1,
                            per_page: 10,
                            total: 0,
                            from: 0,
                            to: 0
                        };
                    }

                } catch (error) {
                    console.error('Error loading stock movements:', error);
                    this.showNotification('Stok hareketleri yüklenemedi', 'error');
                    this.stockDetails = [];
                } finally {
                    this.loading.stockDetails = false;
                }
            }
        }
        }).mount('#app');
    </script>

    <!-- Legacy jQuery scripts for existing modals -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        var selected = [];
        $(document).ready(function() {
            $("#multiplepriceUpdate").click(function(e) {
                $("input:checkbox[name^='selected']:checked").each(function() {
                    selected.push($(this).val());
                });
                if (selected.length > 0) {
                    $("#multiplepriceModal").modal('show');
                    $("#multiplepriceModal").find("#stockCardMovementIdArray").val(selected);
                } else {
                    Swal.fire('Seçim Yapınız');
                }
            });
        });

        $("#multiplepriceForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = '{{ route('stockcard.multiplepriceupdate') }}';
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#multiplepriceModal").modal('hide');
                },
                error: function(request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#multiplepriceModal").modal('hide');
                }
            });
        });

 

        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#serialBackdrop").val(id);
            $("#stockCardId").val(id);
        }

        $("#transferForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = '{{ route('stockcard.sevk') }}';
            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardId").val() + '',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data, status) {
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
                error: function(request, status, error) {
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

        $("#priceForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = '{{ route('stockcard.singlepriceupdate') }}';
            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardMovementId").val() + '',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data, status) {
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
                error: function(request, status, error) {
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
