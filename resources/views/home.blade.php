@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/table-page-framework.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/dashboard.css')}}">
@endsection

@section('content')
    <div id="dashboard-app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Table Page Header -->
        <div class="table-page-header table-page-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="bx bx-home"></i>
                    </div>
                    <div class="header-text">
                        <h2>
                            <i class="bx bx-home me-2"></i>
                            ANA SAYFA
                        </h2>
                        <p>Sistem genel bakış ve hızlı işlemler</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button @click="refreshDashboard" class="btn btn-primary btn-sm" :disabled="loading.refresh">
                        <span v-if="loading.refresh" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="bx bx-refresh me-1"></i>
                        Yenile
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-grid table-page-fade-in-delay-1">
            <a href="javascript:void(0)" @click="openSaleModal" class="quick-action-btn">
                <div class="quick-action-icon text-success">
                    <i class="bx bx-cart-add"></i>
                </div>
                <div class="quick-action-text">Satış Yap</div>
            </a>
            
            <a href="{{ route('stockcard.create') }}" class="quick-action-btn">
                <div class="quick-action-icon text-primary">
                    <i class="bx bx-package"></i>
                </div>
                <div class="quick-action-text">Stok Ekle</div>
            </a>
            
            <a href="{{ route('stockcard.refundlist') }}" class="quick-action-btn">
                <div class="quick-action-icon text-warning">
                    <i class="bx bx-undo"></i>
                </div>
                <div class="quick-action-text">İade İşlemi</div>
            </a>
            
            <a href="{{ route('transfer.create') }}" class="quick-action-btn">
                <div class="quick-action-icon text-info">
                    <i class="bx bx-transfer"></i>
                </div>
                <div class="quick-action-text">Sevk İşlemi</div>
            </a>
        </div>

        <!-- Satış Modal -->
        <div class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title" id="saleModalLabel">
                            <i class="bx bx-cart-add me-2"></i>
                            Hızlı Satış
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="saleSearchInput" class="form-label fw-semibold">
                                <i class="bx bx-search me-1"></i>
                                Seri Numarası veya Barkod
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="saleSearchInput"
                                v-model="saleSearch.input"
                                @keyup.enter="checkStockAndRedirect"
                                placeholder="Seri numarası veya barkod giriniz..."
                                autofocus>
                            <div class="form-text">
                                <i class="bx bx-info-circle me-1"></i>
                                Enter tuşuna basarak arama yapabilirsiniz
                            </div>
                        </div>

                        <!-- Loading -->
                        <div v-if="saleSearch.loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Stok kontrol ediliyor...</p>
                        </div>

                        <!-- Error Message -->
                        <div v-if="saleSearch.error" class="alert alert-danger" role="alert">
                            <i class="bx bx-error-circle me-2"></i>
                            <span v-text="saleSearch.error"></span>
                        </div>

                        <!-- Success Message -->
                        <div v-if="saleSearch.success" class="alert alert-success" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            <span v-text="saleSearch.success"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>
                            İptal
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-primary" 
                            @click="checkStockAndRedirect"
                            :disabled="!saleSearch.input || saleSearch.loading">
                            <span v-if="saleSearch.loading" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bx bx-search me-1"></i>
                            Kontrol Et ve Satış Yap
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card table-page-fade-in-delay-2">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="bx bx-bar-chart me-2"></i>
                                    Personele Göre Satış Grafikleri
                                </h5>
                                <small class="text-muted">Personellerin satış performansı</small>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="button" 
                                        class="btn btn-sm" 
                                        :class="chartPeriod === 'daily' ? 'btn-primary' : 'btn-outline-primary'"
                                        @click="changeChartPeriod('daily')">
                                    Günlük
                                </button>
                                <button type="button" 
                                        class="btn btn-sm" 
                                        :class="chartPeriod === 'monthly' ? 'btn-primary' : 'btn-outline-primary'"
                                        @click="changeChartPeriod('monthly')">
                                    Aylık
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Date Filter -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="bx bx-calendar me-1"></i>
                                    <span v-if="chartPeriod === 'daily'">Tarih Seçin</span>
                                    <span v-else>Ay Seçin</span>
                                </label>
                                <input v-if="chartPeriod === 'daily'" 
                                       type="date" 
                                       v-model="chartFilters.date" 
                                       @change="loadSalesChart"
                                       class="form-control">
                                <input v-else 
                                       type="month" 
                                       v-model="chartFilters.month" 
                                       @change="loadSalesChart"
                                       class="form-control">
                            </div>
                        </div>
                        
                        <!-- Chart -->
                        <div v-show="loading.chart" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Grafik yükleniyor...</p>
                        </div>
                        <div v-show="!loading.chart" class="chart-container" style="position: relative; height: 400px;">
                            <canvas :key="chartKey" id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row mb-4" v-if="aiAnalysis && aiAnalysis.insights && aiAnalysis.insights.length > 0">
            <div class="col-12">
                <div class="card dashboard-card border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0 text-white">
                                    <i class="bx bx-brain me-2"></i>
                                    AI Analiz & Öneriler
                                </h5>
                                <small class="text-white-50">Yapay zeka destekli içgörüler</small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <!-- ML Training Status -->
                                <div class="d-flex align-items-center" style="margin-right: 1rem;">
                                    <span v-if="mlStatus.isTraining" class="badge bg-warning text-dark">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        🤖 ML Eğitiliyor... @{{ mlStatus.trainingProgress }}%
                                    </span>
                                    <span v-else-if="mlStatus.isTrained" class="badge bg-success">
                                        <i class="bx bx-check-circle me-1"></i>
                                        🤖 ML Aktif
                                    </span>
                                    <span v-else class="badge bg-secondary">
                                        <i class="bx bx-info-circle me-1"></i>
                                        🤖 ML Beklemede
                                    </span>
                                </div>
                                
                                <div class="text-end" v-if="aiAnalysis.score !== undefined">
                                    <div class="badge" :class="{
                                        'bg-success': aiAnalysis.score >= 80,
                                        'bg-info': aiAnalysis.score >= 60 && aiAnalysis.score < 80,
                                        'bg-warning': aiAnalysis.score >= 40 && aiAnalysis.score < 60,
                                        'bg-danger': aiAnalysis.score < 40
                                    }" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        Sağlık Skoru: <strong v-text="aiAnalysis.score"></strong>/100
                                    </div>
                                </div>
                                
                                <!-- Export Buttons -->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-success" @click="exportAIReport('pdf')" title="PDF İndir">
                                        <i class="bx bxs-file-pdf"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" @click="exportAIReport('excel')" title="Excel İndir">
                                        <i class="bx bxs-file"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" @click="exportAIReport('json')" title="JSON İndir">
                                        <i class="bx bx-data"></i>
                                    </button>
                                </div>
                                
                                <button @click="loadAIAnalysis" class="btn btn-sm btn-light" :disabled="loading.ai">
                                    <span v-if="loading.ai" class="spinner-border spinner-border-sm me-1"></span>
                                    <i v-else class="bx bx-refresh me-1"></i>
                                    Yenile
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" v-for="(insight, index) in aiAnalysis.insights" :key="index">
                                <div class="alert" :class="{
                                    'alert-success': insight.type === 'success',
                                    'alert-warning': insight.type === 'warning',
                                    'alert-danger': insight.type === 'danger',
                                    'alert-info': insight.type === 'info'
                                }" role="alert">
                                    <h6 class="alert-heading">
                                        <i class="bx" :class="insight.icon"></i>
                                        <span v-text="insight.title"></span>
                                    </h6>
                                    <p class="mb-2" v-text="insight.message"></p>
                                    <hr>
                                    <p class="mb-0">
                                        <strong>💡 Öneri:</strong> <span v-text="insight.action"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı ve Yavaş Hareket Eden Ürünler -->
        <div class="row mb-4" v-if="aiAnalysis && (aiAnalysis.fast_movers?.length > 0 || aiAnalysis.slow_movers?.length > 0)">
            <!-- Hızlı Hareket Eden Ürünler -->
            <div class="col-md-6" v-if="aiAnalysis.fast_movers && aiAnalysis.fast_movers.length > 0">
                <div class="card dashboard-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-trending-up me-2"></i>
                            🚀 En Hızlı Satan 10 Ürün
                        </h5>
                        <small class="text-white-50">En iyi performans gösteren ürünler</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 25%;">Ürün Adı</th>
                                        <th style="width: 15%;">Kategori</th>
                                        <th style="width: 15%;">Devir Süresi</th>
                                        <th style="width: 10%;">Satış</th>
                                        <th style="width: 15%;">Gelir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in aiAnalysis.fast_movers" :key="'fast-' + index">
                                        <td><span class="badge bg-success">@{{ index + 1 }}</span></td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="@{{ product.stock_name }}">
                                            <strong>@{{ product.stock_name }}</strong>
                                        </td>
                                        <td><span class="badge bg-info">@{{ product.category }}</span></td>
                                        <td>
                                            <span class="badge bg-success">
                                                @{{ product.avg_days_to_sell }} gün
                                            </span>
                                        </td>
                                        <td>@{{ product.total_sold }} adet</td>
                                        <td><strong>@{{ Number(product.total_revenue).toLocaleString('tr-TR') }} ₺</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yavaş Hareket Eden Ürünler -->
            <div class="col-md-6" v-if="aiAnalysis.slow_movers && aiAnalysis.slow_movers.length > 0">
                <div class="card dashboard-card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-trending-down me-2"></i>
                            🐌 En Yavaş Satan 10 Ürün
                        </h5>
                        <small>Aksiyon gerektirebilir</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 30%;">Ürün Adı</th>
                                        <th style="width: 15%;">Devir Süresi</th>
                                        <th style="width: 10%;">Stok</th>
                                        <th style="width: 40%;">Öneri</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(product, index) in aiAnalysis.slow_movers" :key="'slow-' + index">
                                        <td><span class="badge bg-warning">@{{ index + 1 }}</span></td>
                                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="@{{ product.stock_name }}">
                                            <strong>@{{ product.stock_name }}</strong>
                                            <br>
                                            <small class="text-muted">@{{ product.category }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">
                                                @{{ product.avg_days_to_sell }} gün
                                            </span>
                                        </td>
                                        <td>@{{ product.current_stock }} adet</td>
                                        <td style="max-width: 300px;">
                                            <small class="text-warning" style="word-wrap: break-word; white-space: normal;">
                                                <i class="bx bx-bulb"></i> @{{ product.recommendation }}
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mağaza Bazlı Performans Analizi -->
        <div class="row mb-4" v-if="aiAnalysis && aiAnalysis.warehouse_performance?.has_data">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-store me-2"></i>
                            🏪 Mağaza Bazlı Performans Analizi
                        </h5>
                        <small class="text-white-50">Her mağazada hangi ürünler hızlı/yavaş satıyor</small>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="warehouseAccordion">
                            <div class="accordion-item" v-for="(warehouse, index) in aiAnalysis.warehouse_performance.warehouses" :key="'warehouse-' + index">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" :class="{ 'collapsed': index !== 0 }" type="button" 
                                            :data-bs-toggle="'collapse'" 
                                            :data-bs-target="'#collapse-warehouse-' + index"
                                            :aria-expanded="index === 0 ? 'true' : 'false'">
                                        <strong>@{{ warehouse.warehouse_name }}</strong>
                                        <span class="ms-3 badge bg-info">@{{ warehouse.total_products }} ürün</span>
                                        <span class="ms-2 badge bg-secondary">Ort: @{{ warehouse.avg_turnover }} gün</span>
                                        <span class="ms-2 badge bg-success">@{{ warehouse.total_sales }} satış</span>
                                    </button>
                                </h2>
                                <div :id="'collapse-warehouse-' + index" 
                                     class="accordion-collapse collapse" 
                                     :class="{ 'show': index === 0 }"
                                     data-bs-parent="#warehouseAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <!-- Hızlı Satan Ürünler -->
                                            <div class="col-md-6">
                                                <h6 class="text-success">
                                                    <i class="bx bx-trending-up"></i> En Hızlı Satan 5 Ürün
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Ürün</th>
                                                                <th>Kategori</th>
                                                                <th>Süre</th>
                                                                <th>Satış</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(product, pIndex) in warehouse.fastest_products" :key="'fast-w-' + pIndex">
                                                                <td><strong>@{{ product.stock_name }}</strong></td>
                                                                <td><span class="badge bg-info">@{{ product.category }}</span></td>
                                                                <td><span class="badge bg-success">@{{ product.avg_days }} gün</span></td>
                                                                <td>@{{ product.total_sold }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Yavaş Satan Ürünler -->
                                            <div class="col-md-6">
                                                <h6 class="text-warning">
                                                    <i class="bx bx-trending-down"></i> En Yavaş Satan 5 Ürün
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Ürün</th>
                                                                <th>Kategori</th>
                                                                <th>Süre</th>
                                                                <th>Stok</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(product, pIndex) in warehouse.slowest_products" :key="'slow-w-' + pIndex">
                                                                <td><strong>@{{ product.stock_name }}</strong></td>
                                                                <td><span class="badge bg-secondary">@{{ product.category }}</span></td>
                                                                <td><span class="badge bg-danger">@{{ product.avg_days }} gün</span></td>
                                                                <td>@{{ product.current_stock }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Turnover Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card table-page-fade-in-delay-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="bx bx-tachometer me-2"></i>
                                    Stok Devir Hızı
                                </h5>
                                <small class="text-muted">Ürünlerin satış hızı analizi (Son 90 gün)</small>
                            </div>
                            <button @click="loadStockTurnover" class="btn btn-sm btn-outline-primary" :disabled="loading.turnover">
                                <span v-if="loading.turnover" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bx bx-refresh me-1"></i>
                                Yenile
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Bayi Filtresi -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="bx bx-user me-1"></i>Bayi
                                </label>
                                <select v-model="turnoverFilters.seller_id" @change="loadStockTurnover" class="form-select">
                                    <option value="">Tüm Bayiler</option>
                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id" v-text="seller.name"></option>
                                </select>
                            </div>
                        </div>
                        
                        <div v-if="loading.turnover" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Stok devir hızı hesaplanıyor...</p>
                        </div>
                        
                        <div v-else-if="stockTurnover.length === 0" class="text-center py-5">
                            <i class="bx bx-package text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Stok devir hızı verisi bulunamadı</p>
                        </div>
                        
                        <div v-else class="stock-turnover-table">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;"><i class="bx bx-hash me-1"></i>#</th>
                                        <th style="width: 35%;"><i class="bx bx-package me-1"></i>Stok Adı</th>
                                        <th style="width: 15%;" class="text-center"><i class="bx bx-user me-1"></i>Bayi</th>
                                        <th style="width: 15%;" class="text-center"><i class="bx bx-tachometer me-1"></i>Devir Hızı</th>
                                        <th style="width: 10%;" class="text-center"><i class="bx bx-box me-1"></i>Mevcut Stok</th>
                                        <th style="width: 20%;" class="text-center"><i class="bx bx-trending-up me-1"></i>Performans</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in paginatedTurnover" :key="item.id">
                                        <td v-text="(turnoverPagination.currentPage - 1) * turnoverPagination.perPage + index + 1"></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong v-text="item.stock_name"></strong>
                                                <small class="text-muted" v-text="item.category"></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary" v-text="item.seller_name || 'Belirsiz'"></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" :class="getTurnoverBadgeClass(item.turnover_rate)">
                                                <span v-text="item.turnover_rate.toFixed(1)"></span> gün
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" :class="item.current_stock > 0 ? 'bg-success' : 'bg-danger'" v-text="item.current_stock"></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar" 
                                                     :class="getPerformanceClass(item.turnover_rate || 0)"
                                                     :style="{width: getPerformanceWidth(item.turnover_rate || 0) + '%'}"
                                                     role="progressbar">
                                                    <span v-text="getPerformanceLabel(item.turnover_rate || 0)"></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Toplam <strong v-text="stockTurnover.length"></strong> kayıt - 
                                    Sayfa <strong v-text="turnoverPagination.currentPage"></strong> / <strong v-text="turnoverPagination.totalPages"></strong>
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item" :class="{disabled: turnoverPagination.currentPage === 1}">
                                            <a class="page-link" href="javascript:void(0)" @click="changeTurnoverPage(turnoverPagination.currentPage - 1)">
                                                <i class="bx bx-chevron-left"></i>
                                            </a>
                                        </li>
                                        <li v-for="page in turnoverPagination.pages" :key="page" 
                                            class="page-item" 
                                            :class="{active: page === turnoverPagination.currentPage}">
                                            <a class="page-link" href="javascript:void(0)" @click="changeTurnoverPage(page)" v-text="page"></a>
                                        </li>
                                        <li class="page-item" :class="{disabled: turnoverPagination.currentPage === turnoverPagination.totalPages}">
                                            <a class="page-link" href="javascript:void(0)" @click="changeTurnoverPage(turnoverPagination.currentPage + 1)">
                                                <i class="bx bx-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.11.0/dist/tf.min.js"></script>
    <script src="{{ asset('assets/js/ml-predictor.js') }}?v={{ time() }}"></script>
    
    <script>
        // Backend data'yı JavaScript'e aktar
        window.dashboardSellers = @json($sellers ?? []);
    </script>
    
    <script src="{{ asset('assets/js/dashboard.js') }}?v={{ time() }}"></script>
@endsection

