/**
 * Dashboard JavaScript
 * Ana sayfa Vue.js uygulaması
 */

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
            chartKey: 0, // Unique key to force re-render
            
            // Stock turnover data
            stockTurnover: [],
            turnoverFilters: {
                seller_id: ''
            },
            turnoverPagination: {
                currentPage: 1,
                perPage: 10,
                totalPages: 1,
                pages: []
            },
            
            // AI Analysis
            aiAnalysis: null,
            
            // ML Model Status
            mlStatus: {
                isTrained: false,
                isTraining: false,
                trainingProgress: 0,
                message: 'Model henüz eğitilmedi'
            },
            
            // Sale Search Modal
            saleSearch: {
                input: '',
                loading: false,
                error: null,
                success: null
            },
            
            // Sellers list (will be injected from backend)
            sellers: window.dashboardSellers || [],
            
            // Loading states
            loading: {
                chart: true,
                turnover: true,
                ai: false,
                refresh: false
            }
        }
    },
    
    computed: {
        paginatedTurnover() {
            const start = (this.turnoverPagination.currentPage - 1) * this.turnoverPagination.perPage;
            const end = start + this.turnoverPagination.perPage;
            return this.stockTurnover.slice(start, end);
        }
    },
    
    async mounted() {
        console.log('Dashboard app mounted');
        console.log('Chart.js available:', typeof Chart !== 'undefined');
        console.log('Sellers loaded from backend:', this.sellers.length);
        
        this.setupAxios();
        this.handleSaleFromQuery();
        await Promise.all([
            this.loadSalesChart(),
            this.loadStockTurnover(),
            this.loadAIAnalysis()
        ]);
    },
    
    methods: {
        setupAxios() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
            // Session cookies için gerekli
            axios.defaults.withCredentials = true;
            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        },
        
        async refreshDashboard() {
            this.loading.refresh = true;
            await Promise.all([
                this.loadSalesChart(),
                this.loadStockTurnover(),
                this.loadAIAnalysis()
            ]);
            this.loading.refresh = false;
            this.showNotification('Dashboard yenilendi', 'success');
        },
        
        async loadAIAnalysis() {
            try {
                this.loading.ai = true;
                console.log('Loading AI analysis...');
                
                const params = {};
                // Sadece şube seçilmişse gönder
                if (this.turnoverFilters.seller_id) {
                    params.seller_id = this.turnoverFilters.seller_id;
                }
                
                const response = await axios.get('/api/dashboard/stock-turnover-ai', { params });
                console.log('AI Analysis response:', response.data);
                
                if (response.data && response.data.success) {
                    this.aiAnalysis = response.data.data;
                    console.log('✅ AI Analysis loaded successfully');
                }
            } catch (error) {
                console.error('AI analizi yüklenirken hata:', error);
                this.showNotification('AI analizi yüklenemedi', 'error');
            } finally {
                this.loading.ai = false;
            }
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
                    
                    // DOM'un render olmasını bekle
                    this.$nextTick(() => {
                        this.renderSalesChart();
                    });
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
                
                const params = {
                    seller_id: this.turnoverFilters.seller_id
                };
                
                const response = await axios.get('/api/dashboard/stock-turnover', { params });
                
                console.log('Stock turnover response:', response.data);
                
                if (response.data && response.data.success) {
                    this.stockTurnover = response.data.data || [];
                    this.updateTurnoverPagination();
                    
                    // ML Model eğitimi (arka planda)
                    if (typeof window.stockMLPredictor !== 'undefined' && this.stockTurnover.length > 0) {
                        console.log('🤖 ML Model eğitimi başlatılıyor...');
                        this.mlStatus.isTraining = true;
                        this.mlStatus.trainingProgress = 0;
                        this.mlStatus.message = 'Model eğitiliyor...';
                        
                        window.stockMLPredictor.trainModel(this.stockTurnover)
                            .then(success => {
                                if (success) {
                                    console.log('✅ ML Model başarıyla eğitildi!');
                                    this.mlStatus.isTrained = true;
                                    this.mlStatus.isTraining = false;
                                    this.mlStatus.trainingProgress = 100;
                                    this.mlStatus.message = 'Model aktif ve tahmin yapıyor';
                                    this.showNotification('ML Model başarıyla eğitildi! 🤖', 'success');
                                } else {
                                    this.mlStatus.isTraining = false;
                                    this.mlStatus.message = 'Eğitim başarısız';
                                }
                            })
                            .catch(err => {
                                console.error('ML eğitim hatası:', err);
                                this.mlStatus.isTraining = false;
                                this.mlStatus.message = 'Eğitim hatası: ' + err.message;
                            });
                    }
                    
                    // Bayi filtresi değiştiğinde AI analizini de güncelle
                    this.loadAIAnalysis();
                }
                
            } catch (error) {
                console.error('Stok devir hızı yüklenirken hata:', error);
                this.showNotification('Stok devir hızı yüklenemedi', 'error');
            } finally {
                this.loading.turnover = false;
            }
        },
        
        updateTurnoverPagination() {
            const total = this.stockTurnover.length;
            this.turnoverPagination.totalPages = Math.ceil(total / this.turnoverPagination.perPage) || 1;
            
            // Sayfa numaralarını oluştur
            const pages = [];
            const maxPages = 5; // Maksimum gösterilecek sayfa sayısı
            let startPage = Math.max(1, this.turnoverPagination.currentPage - Math.floor(maxPages / 2));
            let endPage = Math.min(this.turnoverPagination.totalPages, startPage + maxPages - 1);
            
            // Eğer son sayfa sınırındaysak, başlangıç sayfasını ayarla
            if (endPage - startPage < maxPages - 1) {
                startPage = Math.max(1, endPage - maxPages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            
            this.turnoverPagination.pages = pages;
        },
        
        changeTurnoverPage(page) {
            if (page < 1 || page > this.turnoverPagination.totalPages) return;
            this.turnoverPagination.currentPage = page;
            this.updateTurnoverPagination();
        },
        
        changeChartPeriod(period) {
            this.chartPeriod = period;
            this.loadSalesChart();
        },
        
        renderSalesChart() {
            console.log('Rendering chart with data:', this.salesData);
            
            if (this.salesData.length === 0) {
                console.warn('No sales data to render');
                return;
            }
            
            // Eski chart'ı yok et
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
            
            // Chart key'i artır (canvas'ı yeniden oluştur)
            this.chartKey++;
            
            // DOM'un güncellemesini bekle
            this.$nextTick(() => {
                const ctx = document.getElementById('salesChart');
                if (!ctx) {
                    console.error('Chart canvas not found');
                    return;
                }
                
                console.log('Chart canvas ready');
                
                const labels = this.salesData.map(item => item.staff_name || 'Bilinmeyen');
                const data = this.salesData.map(item => parseFloat(item.total_sales) || 0);
                
                console.log('Chart data prepared - labels:', labels.length, 'data:', data.length);
                
                try {
                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Satış Tutarı (₺)',
                                data: data,
                                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                                borderColor: 'rgba(102, 126, 234, 1)',
                                borderWidth: 1,
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return new Intl.NumberFormat('tr-TR').format(context.parsed.y) + ' ₺';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return new Intl.NumberFormat('tr-TR', { 
                                                notation: 'compact',
                                                compactDisplay: 'short'
                                            }).format(value) + '₺';
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    console.log('✅ Chart.js rendered successfully!');
                    
                } catch (error) {
                    console.error('❌ Chart.js creation error:', error);
                    this.showNotification('Grafik oluşturulamadı: ' + error.message, 'error');
                }
            });
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
        },
        
        /**
         * AI Raporu Export
         */
        exportAIReport(format) {
            const sellerId = this.turnoverFilters.seller_id || '';
            let url = '';
            
            switch(format) {
                case 'pdf':
                    url = `/api/dashboard/ai-analysis-export-pdf?seller_id=${sellerId}`;
                    break;
                case 'excel':
                    url = `/api/dashboard/ai-analysis-export-excel?seller_id=${sellerId}`;
                    break;
                case 'json':
                    url = `/api/dashboard/ai-analysis-export-json?seller_id=${sellerId}`;
                    break;
                default:
                    this.showNotification('Geçersiz export formatı', 'error');
                    return;
            }
            
            // Yeni sekmede aç veya indir
            window.open(url, '_blank');
            this.showNotification(`${format.toUpperCase()} raporu hazırlanıyor...`, 'success');
        },
        
        /**
         * Satış modalını aç
         */
        openSaleModal(options = {}) {
            const { preserveInput = false } = options;
            if (!preserveInput) {
                this.saleSearch.input = '';
            }
            this.saleSearch.error = null;
            this.saleSearch.success = null;
            this.saleSearch.loading = false;
            
            const modal = new bootstrap.Modal(document.getElementById('saleModal'));
            modal.show();
            
            // Modal açıldıktan sonra input'a focus yap
            setTimeout(() => {
                document.getElementById('saleSearchInput')?.focus();
            }, 500);
        },

        /**
         * URL parametresi üzerinden hızlı satış başlat
         */
        handleSaleFromQuery() {
            if (typeof window === 'undefined') {
                return;
            }

            const params = new URLSearchParams(window.location.search);
            const serial = params.get('sale_serial');
            const stockId = params.get('sale_stock');

            if (!serial) {
                return;
            }

            const cleanUrl = () => {
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', window.location.pathname);
                }
            };

            const redirectToSalePage = () => {
                const url = `/invoice/sales?serial=${encodeURIComponent(serial)}${stockId ? `&stock_id=${encodeURIComponent(stockId)}` : ''}`;
                cleanUrl();
                window.location.href = url;
            };

            this.saleSearch.input = serial;

            if (stockId) {
                redirectToSalePage();
                return;
            }

            this.openSaleModal({ preserveInput: true });
            this.$nextTick(() => {
                this.checkStockAndRedirect();
            });
            cleanUrl();
        },
        
        /**
         * Stok kontrolü yap ve satış sayfasına yönlendir
         */
        async checkStockAndRedirect() {
            if (!this.saleSearch.input || this.saleSearch.input.trim().length === 0) {
                this.saleSearch.error = 'Lütfen seri numarası veya barkod giriniz';
                return;
            }
            
            this.saleSearch.loading = true;
            this.saleSearch.error = null;
            this.saleSearch.success = null;
            
            try {
                const searchValue = this.saleSearch.input.trim();
                console.log('🔍 Stok aranıyor:', searchValue);
                
                // API'ye istek at
                const response = await axios.get('/api/stock/check', {
                    params: {
                        search: searchValue
                    }
                });
                
                console.log('📦 Stok kontrolü sonucu:', response.data);
                
                if (response.data && response.data.success) {
                    if (response.data.exists) {
                        // Stok bulundu
                        this.saleSearch.success = 'Stok bulundu! Satış sayfasına yönlendiriliyorsunuz...';
                        
                        // Kısa bir süre bekle ve yönlendir
                        setTimeout(() => {
                            const stockId = response.data.stock_id;
                            const serialNumber = response.data.serial_number || searchValue;
                            
                            // Satış sayfasına yönlendir
                            window.location.href = `/invoice/sales?stock_id=${stockId}&serial=${encodeURIComponent(serialNumber)}`;
                        }, 1000);
                    } else {
                        // Stok bulunamadı
                        this.saleSearch.error = 'Bu seri numarası veya barkod stoklarınızda bulunamadı. Lütfen kontrol ediniz.';
                    }
                } else {
                    this.saleSearch.error = response.data?.message || 'Stok kontrolü yapılamadı';
                }
                
            } catch (error) {
                console.error('❌ Stok kontrol hatası:', error);
                
                if (error.response) {
                    // Sunucu hatası
                    this.saleSearch.error = error.response.data?.message || 'Stok kontrolü sırasında bir hata oluştu';
                } else if (error.request) {
                    // İstek gönderildi ama cevap alınamadı
                    this.saleSearch.error = 'Sunucuya bağlanılamadı. Lütfen internet bağlantınızı kontrol edin.';
                } else {
                    // İstek oluşturulurken hata
                    this.saleSearch.error = 'Beklenmeyen bir hata oluştu: ' + error.message;
                }
            } finally {
                this.saleSearch.loading = false;
            }
        }
    }
}).mount('#dashboard-app');
