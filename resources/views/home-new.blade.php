@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/table-page-framework.css')}}">
    <style>
        /* Dashboard specific styles */
        .dashboard-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .quick-action-btn {
            padding: 1.5rem;
            text-align: center;
            border-radius: 1rem;
            border: 2px solid #e9ecef;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .quick-action-btn:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .quick-action-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .quick-action-text {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
        }
        
        .stock-turnover-table {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
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
            <a href="{{ route('invoice.sales') }}" class="quick-action-btn">
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
            
            <a href="{{ route('refund.create') }}" class="quick-action-btn">
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
                        <div v-if="loading.chart" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Grafik yükleniyor...</p>
                        </div>
                        <div v-else class="chart-container">
                            <div id="salesChart"></div>
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
                                <small class="text-muted">Ürünlerin satış hızı analizi</small>
                            </div>
                            <button @click="loadStockTurnover" class="btn btn-sm btn-outline-primary" :disabled="loading.turnover">
                                <span v-if="loading.turnover" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bx bx-refresh me-1"></i>
                                Yenile
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
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
                                        <th style="width: 40%;"><i class="bx bx-package me-1"></i>Stok Adı</th>
                                        <th style="width: 20%;" class="text-center"><i class="bx bx-tachometer me-1"></i>Devir Hızı</th>
                                        <th style="width: 15%;" class="text-center"><i class="bx bx-box me-1"></i>Mevcut Stok</th>
                                        <th style="width: 20%;" class="text-center"><i class="bx bx-trending-up me-1"></i>Performans</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in stockTurnover" :key="item.id">
                                        <td v-text="index + 1"></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong v-text="item.stock_name"></strong>
                                                <small class="text-muted" v-text="item.category"></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" :class="getTurnoverBadgeClass(item.turnover_rate)">
                                                <span v-text="item.turnover_rate.toFixed(2)"></span> gün
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" :class="item.current_stock > 0 ? 'bg-success' : 'bg-danger'" v-text="item.current_stock"></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar" 
                                                     :class="getPerformanceClass(item.turnover_rate)"
                                                     :style="{width: getPerformanceWidth(item.turnover_rate) + '%'}"
                                                     role="progressbar">
                                                    <span v-text="getPerformanceLabel(item.turnover_rate)"></span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    // Chart data
                    chartPeriod: 'daily', // 'daily' or 'monthly'
                    chartFilters: {
                        date: new Date().toISOString().split('T')[0],
                        month: new Date().toISOString().slice(0, 7)
                    },
                    salesData: [],
                    chart: null,
                    
                    // Stock turnover data
                    stockTurnover: [],
                    
                    // Loading states
                    loading: {
                        chart: true,
                        turnover: true,
                        refresh: false
                    }
                }
            },
            
            async mounted() {
                console.log('Dashboard app mounted');
                this.setupAxios();
                await Promise.all([
                    this.loadSalesChart(),
                    this.loadStockTurnover()
                ]);
            },
            
            methods: {
                setupAxios() {
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                    }
                },
                
                async refreshDashboard() {
                    this.loading.refresh = true;
                    await Promise.all([
                        this.loadSalesChart(),
                        this.loadStockTurnover()
                    ]);
                    this.loading.refresh = false;
                    this.showNotification('Dashboard yenilendi', 'success');
                },
                
                async loadSalesChart() {
                    try {
                        this.loading.chart = true;
                        
                        const params = {
                            period: this.chartPeriod
                        };
                        
                        if (this.chartPeriod === 'daily') {
                            params.date = this.chartFilters.date;
                        } else {
                            params.month = this.chartFilters.month;
                        }
                        
                        console.log('Loading sales chart with params:', params);
                        
                        const response = await axios.get('/api/dashboard/sales-by-staff', {
                            params: params
                        });
                        
                        console.log('Sales chart response:', response.data);
                        
                        if (response.data && response.data.success) {
                            this.salesData = response.data.data || [];
                            this.renderSalesChart();
                        }
                        
                    } catch (error) {
                        console.error('Satış grafiği yüklenirken hata:', error);
                        this.showNotification('Satış grafiği yüklenemedi', 'error');
                    } finally {
                        this.loading.chart = false;
                    }
                },
                
                async loadStockTurnover() {
                    try {
                        this.loading.turnover = true;
                        
                        console.log('Loading stock turnover...');
                        
                        const response = await axios.get('/api/dashboard/stock-turnover');
                        
                        console.log('Stock turnover response:', response.data);
                        
                        if (response.data && response.data.success) {
                            this.stockTurnover = response.data.data || [];
                        }
                        
                    } catch (error) {
                        console.error('Stok devir hızı yüklenirken hata:', error);
                        this.showNotification('Stok devir hızı yüklenemedi', 'error');
                    } finally {
                        this.loading.turnover = false;
                    }
                },
                
                changeChartPeriod(period) {
                    this.chartPeriod = period;
                    this.loadSalesChart();
                },
                
                renderSalesChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    
                    const categories = this.salesData.map(item => item.staff_name);
                    const sales = this.salesData.map(item => item.total_sales);
                    
                    const options = {
                        series: [{
                            name: 'Satış Tutarı',
                            data: sales
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded',
                                dataLabels: {
                                    position: 'top'
                                }
                            },
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) {
                                return new Intl.NumberFormat('tr-TR').format(val) + ' ₺';
                            },
                            offsetY: -20,
                            style: {
                                fontSize: '12px',
                                colors: ["#304758"]
                            }
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: categories,
                        },
                        yaxis: {
                            title: {
                                text: 'Satış Tutarı (₺)'
                            },
                            labels: {
                                formatter: function (val) {
                                    return new Intl.NumberFormat('tr-TR').format(val) + ' ₺';
                                }
                            }
                        },
                        fill: {
                            opacity: 1,
                            type: 'gradient',
                            gradient: {
                                shade: 'light',
                                type: "vertical",
                                shadeIntensity: 0.25,
                                gradientToColors: ['#667eea'],
                                inverseColors: true,
                                opacityFrom: 0.85,
                                opacityTo: 0.85,
                                stops: [50, 0, 100]
                            },
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return new Intl.NumberFormat('tr-TR').format(val) + ' ₺';
                                }
                            }
                        },
                        colors: ['#667eea']
                    };
                    
                    this.chart = new ApexCharts(document.querySelector("#salesChart"), options);
                    this.chart.render();
                },
                
                getTurnoverBadgeClass(rate) {
                    if (rate <= 7) return 'bg-success';      // Çok hızlı
                    if (rate <= 15) return 'bg-info';        // Hızlı
                    if (rate <= 30) return 'bg-warning';     // Orta
                    return 'bg-danger';                       // Yavaş
                },
                
                getPerformanceClass(rate) {
                    if (rate <= 7) return 'bg-success';
                    if (rate <= 15) return 'bg-info';
                    if (rate <= 30) return 'bg-warning';
                    return 'bg-danger';
                },
                
                getPerformanceWidth(rate) {
                    // 1 gün = 100%, 60 gün = 0%
                    return Math.max(0, Math.min(100, 100 - (rate / 60 * 100)));
                },
                
                getPerformanceLabel(rate) {
                    if (rate <= 7) return 'Çok Hızlı';
                    if (rate <= 15) return 'Hızlı';
                    if (rate <= 30) return 'Orta';
                    return 'Yavaş';
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
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                }
            }
        }).mount('#dashboard-app');
    </script>
@endsection

