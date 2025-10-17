@extends('layouts.admin')

@section('content')
    <div id="app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bx bx-home" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <div>
                    <h2><i class="bx bx-home me-2"></i>ANA SAYFA</h2>
                    <p>Sistem genel bakış ve hızlı işlemler</p>
                </div>
            </div>
        </div>
        <div class="row">
            @role(['Satış Sorumlusu','super-admin','Bayi Yetkilisi'])
            <div class="col-lg-6 mb-4 order-0">
                <div class="card modern-card quick-action-card" :class="{ 'form-loading': loading.stockSearch }">
                    <div v-if="loading.stockSearch" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Stok aranıyor...</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="bx bx-cart-add display-4 text-primary" :class="{ 'pulse-animation': loading.stockSearch }"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-primary mb-0" style="font-size: 1rem; font-weight: 600;">SATIŞ İŞLEMİ</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">Hızlı satış yapın</small>
                                    </div>
                                </div>
                                <form @submit.prevent="searchStock" id="stockSearch" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="serialNumberSale">Seri
                                                    Numarası</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" 
                                                           v-model="stockSearchForm.serialNumber" 
                                                           id="serialNumberSale"
                                                           class="form-control"
                                                           @keypress.enter="searchStock"
                                                           placeholder="Seri numarası girin">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="width: 0; margin: 7px; height: 0; font-size: 0;" for="serialbuttun" class="label">.</label>
                                            <button style="width: 100%" 
                                                    type="button"
                                                    @click="searchStock"
                                                    :disabled="loading.stockSearch"
                                                    :class="['btn', 'btn-md', 'btn-outline-primary', { 'btn-loading': loading.stockSearch }]">
                                                <span v-if="loading.stockSearch" class="spinner-border spinner-border-sm me-2"></span>
                                                @{{ loading.stockSearch ? 'Aranıyor...' : 'Ara' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div id="nameText"></div>
                        </div>
                        <!-- div class="card-footer">
                            <table class="table table-responsive">
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Marka</th>
                                    <th>Model</th>
                                    <th>Adet</th>
                                    <th>İşlemler</th>
                                </tr>
                                <tr ng-repeat="item in stockSearchLists">
                                    <td>@{{item.name}}</td>
                                    <td>@{{item.category}}</td>
                                    <td>@{{item.brand}}</td>
                                    <td>@{{item.version}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary text-nowrap"
                                                ng-click="getStockSeller(item.id)">
                                            @{{item.quantity}}
                                        </button>
                                    </td>
                                    <td><a ng-if="item.quantity > 0" data-id="@{{item.id}}"
                                           href="{{route('invoice.sales')}}?id=@{{item.id}}">Satış</a>
                                    </td>
                                </tr>
                            </table>
                        </div -->
                    </div>
                </div>
            </div>
            @endrole
            <div class="col-lg-6 mb-4 order-0">
                <div class="card modern-card quick-action-card transfer" :class="{ 'form-loading': loading.transferSearch }">
                    <div v-if="loading.transferSearch" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Sevk kontrol ediliyor...</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="bx bx-transfer display-4 text-success" :class="{ 'pulse-animation': loading.transferSearch }"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-success mb-0" style="font-size: 1rem; font-weight: 600;">SEVK İŞLEMİ</h6>
                                        <small class="text-muted">Hızlı sevk yapın</small>
                                    </div>
                                </div>
                                <form @submit.prevent="searchTransfer" id="transferForm" method="post">
                                    @csrf
                                    <input type="hidden" id="sellerID" class="form-control" name="sellerID"
                                           value="{{auth()->user()->seller_id}}">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="serialBackdrop">Seri
                                                    Numarası</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" 
                                                           v-model="transferForm.serialNumber" 
                                                           id="serialBackdrop" 
                                                           class="form-control"
                                                           @keypress.enter="searchTransfer"
                                                           @paste="handlePaste"
                                                           placeholder="Seri Numarası">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="width: 0; margin: 7px; height: 0; font-size: 0;" for="serialbuttun" class="label">.</label>
                                            <button style="width: 100%" 
                                                    type="button" 
                                                    @click="searchTransfer"
                                                    :disabled="loading.transferSearch"
                                                    :class="['btn', 'btn-md', 'btn-secondary', { 'btn-loading': loading.transferSearch }]">
                                                <span v-if="loading.transferSearch" class="spinner-border spinner-border-sm me-2"></span>
                                                @{{ loading.transferSearch ? 'Aranıyor...' : 'Ara' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>

        <!-- Analytics Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card modern-card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="bx bx-bar-chart me-2"></i>
                            Satış Analizi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                        <div id="newChart"></div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card modern-card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="bx bx-trending-up me-2"></i>
                            Aylık Performans
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                        <div id="newMonthChart"></div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card modern-card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="bx bx-pie-chart me-2"></i>
                            Genel Analiz
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                        <div id="totalAylik"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>

        <div class="row">
            <div class="col-md-6 col-lg-6 order-2 mb-4">
                <div class="card h-100 modern-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-package me-2 text-primary"></i>
                            Stok Uyarıları
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th><i class="bx bx-package me-1"></i>Ürün</th>
                                    <th><i class="bx bx-hash me-1"></i>Stok</th>
                                    <th><i class="bx bx-dollar me-1"></i>Maliyet</th>
                                    <th><i class="bx bx-tag me-1"></i>Satış Fiyatı</th>
                                    <th><i class="bx bx-cog me-1"></i>İşlem</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('Depo Sorumlusu'))
                                    @foreach($stockTracks as $stockTrack)
                                        @if($stockTrack['quantity'] < $stockTrack['tracking_quantity'])
                                            <tr>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>{{$stockTrack['quantity']}}</td>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>
                                                    <button
                                                        onclick="demandModal({{$stockTrack['id']}},'{{$stockTrack['name']}}')"
                                                        type="button" class="btn btn-danger">
                                                        <i class="bx bx-radar"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 order-3 order-md-2 mb-4">
                <div class="card modern-card quick-action-card refund" :class="{ 'form-loading': loading.refund }">
                    <div v-if="loading.refund" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">İade işlemi yapılıyor...</div>
                        </div>
                    </div>
                    <form @submit.prevent="submitRefund" class="modal-content" id="refundForm">
                        @csrf
                        <div class="card-header">
                            <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                                <i class="bx bx-undo me-2 text-primary"></i>
                                İade İşlemi
                            </h6>
                            <small class="text-muted">Seri numarası girilirse stok seçimine gerek yoktur</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="stockBackdrop" class="form-label">Stok</label>
                                    <select class="form-control select2" 
                                            v-model="refundForm.stock_id" 
                                            name="stock_id" 
                                            id="stockBackdrop">
                                        <option value="">Stok seçiniz...</option>
                                        <option v-for="stock in stocks" :key="stock.id" :value="stock.id">
                                            @{{ stock.name }} / @{{ stock.brand?.name || 'Bulunamadı' }} / @{{ stock.version || 'Bulunamadı' }} / @{{ stock.category?.name || 'Kategori Yok' }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="reasonBackdrop" class="form-label">Neden</label>
                                    <select class="form-control" 
                                            v-model="refundForm.reason_id" 
                                            name="reason_id" 
                                            id="reasonBackdrop">
                                        <option value="">Neden seçiniz...</option>
                                        <option v-for="reason in reasons" :key="reason.id" :value="reason.id">
                                            @{{ reason.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="colorBackdrop" class="form-label">Renk</label>
                                    <select class="form-control" 
                                            v-model="refundForm.color_id" 
                                            name="color_id" 
                                            id="colorBackdrop">
                                        <option value="">Renk seçiniz...</option>
                                        <option v-for="color in colors" :key="color.id" :value="color.id">
                                            @{{ color.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="serialNumberRefund" class="form-label">Seri No</label>
                                    <input v-model="refundForm.serial_number" 
                                           name="serial_number" 
                                           type="text" 
                                           class="form-control"
                                           id="serialNumberRefund">
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="descriptionRefund" class="form-label">Açıklama</label>
                                    <input type="text" 
                                           v-model="refundForm.description" 
                                           name="description" 
                                           class="form-control" 
                                           id="descriptionRefund">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="padding: 10px;">
                            <button type="submit" 
                                    :disabled="loading.refund"
                                    :class="['btn', 'btn-primary', { 'btn-loading': loading.refund }]">
                                <span v-if="loading.refund" class="spinner-border spinner-border-sm me-2"></span>
                                @{{ loading.refund ? 'İşleniyor...' : 'İADE İŞLEMİ BAŞLAT' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12 order-3 order-md-2 mb-4">
                <div class="card modern-card quick-action-card delete" :class="{ 'form-loading': loading.delete }">
                    <div v-if="loading.delete" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Seri numarası siliniyor...</div>
                        </div>
                    </div>
                    <form @submit.prevent="submitDeleteSerial" id="deleted_at_serial_number_storeForm" method="post">
                        @csrf
                        <div class="card-header">
                            <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                                <i class="bx bx-trash me-2 text-primary"></i>
                                Seri Numarası Silme
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="serial_number_delete" class="form-label">Seri Numarası</label>
                                    <input type="text" 
                                           v-model="deleteForm.serial_number" 
                                           name="serial_number" 
                                           class="form-control" 
                                           id="serial_number_delete"
                                           placeholder="Seri numarası girin">
                                </div>
                                <div class="col mb-0">
                                    <label for="deleteButton" class="form-label"></label>
                                    <button type="submit" 
                                            :disabled="loading.delete"
                                            :class="['btn', 'btn-primary', { 'btn-loading': loading.delete }]" 
                                            style="display: flex;">
                                        <span v-if="loading.delete" class="spinner-border spinner-border-sm me-2"></span>
                                        @{{ loading.delete ? 'Siliniyor...' : 'Kaydet' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    <div class="modal fade" id="getCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <table class="table table-bordered">
                            <tr>
                                <td>Bayi</td>
                                <td>Adet</td>
                            </tr>
                            <tr ng-repeat="item in data">
                                <td>@{{item.sellerName}}</td>
                                <td>@{{item.quantity}}</td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="demandModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content" style="padding: 1px">
                <div class="modal-header">Ürün Adı : <span></span></div>
                <form action="{{route('demand.store')}}" method="post">
                    <input type="hidden" name="id" id="id" value="">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Renk</label>
                                <select class="form-select" name="color_id">
                                    @foreach($colors as $color)
                                        <option value="{{$color->id}}">{{$color->name}}</option>
                                    @endforeach
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
@endsection

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/home-page.css') }}">
@endsection

@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    // Form data
                    stockSearchForm: {
                        serialNumber: ''
                    },
                    transferForm: {
                        serialNumber: ''
                    },
                    refundForm: {
                        stock_id: '',
                        reason_id: '',
                        color_id: '',
                        serial_number: '',
                        description: ''
                    },
                    deleteForm: {
                        serial_number: ''
                    },
                    
                    // Data arrays
                    stocks: [],
                    colors: [],
                    reasons: [],
                    stockSearchResults: [],
                    
                    // Loading states
                    loading: {
                        stockSearch: false,
                        transferSearch: false,
                        refund: false,
                        delete: false,
                        initialData: true,
                        stocks: false,
                        colors: false,
                        reasons: false,
                        charts: false,
                        salesChart: false,
                        transferChart: false,
                        refundChart: false
                    },
                    
                    // Notifications
                    notifications: []
                }
            },
            
            mounted() {
                this.loadInitialData();
                this.setupAxios();
            },
            
            methods: {
                setupAxios() {
                    // CSRF token setup
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                    }
                },
                
                async loadInitialData() {
                    console.log('Loading initial data...');
                    this.loading.initialData = true;
                    
                    try {
                        // Load stocks
                        console.log('Loading stocks...');
                        this.loading.stocks = true;
                        const stocksResponse = await axios.get('/api/stocks');
                        this.stocks = stocksResponse.data;
                        this.loading.stocks = false;
                        console.log('Stocks loaded:', this.stocks.length);
                        
                        // Load colors
                        console.log('Loading colors...');
                        this.loading.colors = true;
                        const colorsResponse = await axios.get('/api/colors');
                        this.colors = colorsResponse.data;
                        this.loading.colors = false;
                        console.log('Colors loaded:', this.colors.length);
                        
                        // Load reasons
                        console.log('Loading reasons...');
                        this.loading.reasons = true;
                        const reasonsResponse = await axios.get('/api/reasons');
                        this.reasons = reasonsResponse.data.filter(reason => reason.type == 2);
                        this.loading.reasons = false;
                        console.log('Reasons loaded:', this.reasons.length);
                        
                        // Load chart data
                        console.log('Loading chart data...');
                        this.loading.charts = true;
                        await this.loadChartData();
                        this.loading.charts = false;
                        console.log('Chart data loaded');
                        
                        this.loading.initialData = false;
                        console.log('All initial data loaded successfully');
                    } catch (error) {
                        console.error('Veri yüklenirken hata:', error);
                        this.loading.initialData = false;
                        this.loading.stocks = false;
                        this.loading.colors = false;
                        this.loading.reasons = false;
                        this.loading.charts = false;
                        this.showNotification('Hata', 'Veriler yüklenirken bir hata oluştu', 'error');
                    }
                },
                
                async loadChartData() {
                    try {
                        // Load dashboard report data
                        const response = await axios.get('/dashboardNewReport');
                        const monthResponse = await axios.get('/dashboardMounthNewReport');
                        
                        // Sales Chart
                        this.renderSalesChart(response.data);
                        
                        // Month Chart
                        this.renderMonthChart(monthResponse.data);
                        
                        // Total Aylik Chart (simple example)
                        this.renderTotalAylikChart();
                        
                    } catch (error) {
                        console.error('Chart data loading error:', error);
                    }
                },
                
                renderSalesChart(data) {
                    if (!data || !data.series || !data.categories) {
                        console.warn('Invalid sales chart data');
                        return;
                    }
                    
                    const options = {
                        series: data.series || [],
                        chart: {
                            type: 'bar',
                            height: 350,
                            stacked: true,
                            toolbar: { show: true }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true
                            }
                        },
                        xaxis: {
                            categories: data.categories || []
                        },
                        fill: {
                            opacity: 1
                        },
                        colors: ['#667eea', '#28a745', '#17a2b8', '#6c757d'],
                        legend: {
                            position: 'top',
                            horizontalAlign: 'left'
                        }
                    };
                    
                    const chart = new ApexCharts(document.querySelector("#newChart"), options);
                    chart.render();
                },
                
                renderMonthChart(data) {
                    if (!data || !data.series || !data.categories) {
                        console.warn('Invalid month chart data');
                        return;
                    }
                    
                    const options = {
                        series: data.series || [],
                        chart: {
                            type: 'bar',
                            height: 350,
                            stacked: true,
                            toolbar: { show: true }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true
                            }
                        },
                        xaxis: {
                            categories: data.categories || []
                        },
                        colors: ['#667eea', '#28a745', '#17a2b8', '#6c757d'],
                        legend: {
                            position: 'top'
                        }
                    };
                    
                    const chart = new ApexCharts(document.querySelector("#newMonthChart"), options);
                    chart.render();
                },
                
                renderTotalAylikChart() {
                    const options = {
                        series: [{
                            name: 'Satış',
                            data: [44, 55, 57, 56, 61, 58]
                        }, {
                            name: 'Transfer',
                            data: [76, 85, 101, 98, 87, 105]
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: { show: true }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz'],
                        },
                        fill: {
                            opacity: 1
                        },
                        colors: ['#667eea', '#6c757d']
                    };
                    
                    const chart = new ApexCharts(document.querySelector("#totalAylik"), options);
                    chart.render();
                },
                
                async searchStock() {
                    if (!this.stockSearchForm.serialNumber.trim()) {
                        this.showNotification('Uyarı', 'Lütfen seri numarası girin', 'warning');
                        return;
                    }
                    
                    console.log('Starting stock search...');
                    this.loading.stockSearch = true;
                    console.log('Loading state set to true:', this.loading.stockSearch);
                    
            
                    
                      try {
                          const response = await axios.post('/stockSearch', {
                              serialNumber: this.stockSearchForm.serialNumber
                          });
                   
                          if (response.data.autoredirect === false) {
                              this.showNotification('Hata', 'Satışı Yapılamaz', 'error');
                              this.stockSearchResults = [];
                          } else if (response.data.autoredirect === true) {
                              window.location.href = `/invoice/sales?id=${response.data.id}&serial=${response.data.serial}`;
                          } else {
                              this.stockSearchResults = response.data;
                          }
                      } catch (error) {
                          console.error('Stok arama hatası:', error);
                          this.showNotification('Hata', 'Stok aranırken bir hata oluştu', 'error');
                      } finally {
                          console.log('Setting loading to false...');
                          this.loading.stockSearch = false;
                          console.log('Loading state set to false:', this.loading.stockSearch);
                      }
                },
                
                async searchTransfer() {
                    if (!this.transferForm.serialNumber.trim()) {
                        this.showNotification('Uyarı', 'Lütfen seri numarası girin', 'warning');
                        return;
                    }
                    
                    this.loading.transferSearch = true;
                    
                    // Minimum loading süresi (1.5 saniye)
                    const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1500));
                    
                    try {
                        const response = await axios.get(`/getTransferSerialCheck?serial_number=${this.transferForm.serialNumber}&seller_id={{auth()->user()->seller_id}}`);
                        
                        // Hem API response hem de minimum loading süresini bekle
                        await Promise.all([minLoadingTime]);
                        
                        if (response.data !== 'Yes') {
                            this.showNotification('Hata', 'Seri numarası transfer edilemez. Bulunamamakta veya başka bayiye ait.', 'error');
                        } else {
                            window.location.href = `transfer/create?serial_number=${this.transferForm.serialNumber}&type=other`;
                        }
                    } catch (error) {
                        console.error('Transfer arama hatası:', error);
                        this.showNotification('Hata', 'Transfer aranırken bir hata oluştu', 'error');
                    } finally {
                        this.loading.transferSearch = false;
                    }
                },
                
                handlePaste(event) {
                    setTimeout(() => {
                        if (this.transferForm.serialNumber.trim()) {
                            this.searchTransfer();
                        }
                    }, 1000);
                },
                
                async submitRefund() {
                    if (!this.refundForm.stock_id && !this.refundForm.serial_number) {
                        this.showNotification('Uyarı', 'Lütfen stok seçin veya seri numarası girin', 'warning');
                        return;
                    }
                    
                    this.loading.refund = true;
                    
                    // Minimum loading süresi (2.5 saniye)
                    const minLoadingTime = new Promise(resolve => setTimeout(resolve, 2500));
                    
                    try {
                        const response = await axios.post('{{route("stockcard.refund")}}', this.refundForm);
                        
                        // Hem API response hem de minimum loading süresini bekle
                        await Promise.all([minLoadingTime]);
                        
                        this.showNotification('Başarılı', response.data, 'success');
                        this.resetRefundForm();
                    } catch (error) {
                        console.error('İade işlemi hatası:', error);
                        this.showNotification('Hata', 'İade işlemi sırasında bir hata oluştu', 'error');
                    } finally {
                        this.loading.refund = false;
                    }
                },
                
                async submitDeleteSerial() {
                    if (!this.deleteForm.serial_number.trim()) {
                        this.showNotification('Uyarı', 'Lütfen seri numarası girin', 'warning');
                        return;
                    }
                    
                    this.loading.delete = true;
                    
                    // Minimum loading süresi (1 saniye)
                    const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));
                    
                    try {
                        const response = await axios.post('{{route("deleted_at_serial_number_store")}}', this.deleteForm);
                        
                        // Hem API response hem de minimum loading süresini bekle
                        await Promise.all([minLoadingTime]);
                        
                        this.showNotification('Başarılı', 'Seri numarası silindi', 'success');
                        this.deleteForm.serial_number = '';
                    } catch (error) {
                        console.error('Seri numarası silme hatası:', error);
                        this.showNotification('Hata', 'Seri numarası silinirken bir hata oluştu', 'error');
                    } finally {
                        this.loading.delete = false;
                    }
                },
                
                resetRefundForm() {
                    this.refundForm = {
                        stock_id: '',
                        reason_id: '',
                        color_id: '',
                        serial_number: '',
                        description: ''
                    };
                },
                
                showNotification(title, message, type = 'info') {
                    // SweetAlert2 kullanarak bildirim göster
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
                
                getStockSeller(id) {
                    // Bu fonksiyon modal açmak için kullanılabilir
                    console.log('Stock seller ID:', id);
                }
            }
        }).mount('#app');
        
        // Debug için loading state'lerini kontrol et
        console.log('Vue app mounted successfully');
    </script>
    <script>
        function demandModal(id, name) {
            $("#demandModal").modal('show');
            $("#demandModal").find('.modal-header span').html(name);
            $("#demandModal").find('input#id').val(id);
        }
    </script>


@endsection
