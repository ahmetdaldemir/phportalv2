@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/table-page-framework.css')}}">
@endsection

@section('content')
    <div id="invoice-app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Table Page Header -->
        <div class="table-page-header table-page-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="bx bx-receipt"></i>
                    </div>
                    <div class="header-text">
                        <h2>
                            <i class="bx bx-receipt me-2"></i>
                            FATURA LİSTESİ
                        </h2>
                        <p>Tüm faturaları görüntüleyin ve yönetin</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{route('invoice.create.fast')}}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Hızlı Fiş Fatura
                    </a>
                    <a href="{{route('invoice.create')}}" class="btn btn-danger btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Yeni Fatura
                    </a>
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-dots-vertical me-1"></i>
                            Diğer
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('invoice.create.personal')}}">
                                <i class="bx bx-user me-2"></i>Personel Gideri
                            </a></li>
                            <li><a class="dropdown-item" href="{{route('invoice.create.bank')}}">
                                <i class="bx bx-credit-card me-2"></i>Banka Gideri
                            </a></li>
                            <li><a class="dropdown-item" href="{{route('invoice.create.tax')}}">
                                <i class="bx bx-calculator me-2"></i>Vergi / SGK Gideri
                            </a></li>
                        </ul>
                    </div>
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
                <small>Fatura arama ve filtreleme</small>
            </div>
            <div class="filter-body">
                <form @submit.prevent="searchInvoices">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-search"></i> Genel Arama
                            </label>
                            <input type="text" v-model="filters.search" class="filter-input" placeholder="Fatura no, cari adı...">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-hash"></i> Fatura ID
                            </label>
                            <input type="number" v-model="filters.invoice_id" class="filter-input" placeholder="Fatura ID...">
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
                            <button type="button" @click="clearFilters" class="filter-button secondary">
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
            <div v-if="loading.invoices" class="table-page-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-primary mt-2">Faturalar yükleniyor...</p>
            </div>
            
            <!-- Empty State -->
            <div v-else-if="invoices.length === 0" class="table-page-empty">
                <i class="bx bx-receipt"></i>
                <h4 class="mt-3">Fatura bulunamadı</h4>
                <p class="text-muted">Arama kriterlerinize uygun fatura bulunamadı.</p>
            </div>
            
            <!-- Table -->
            <div v-else class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 25%;"><i class="bx bx-hash me-1"></i>Fatura No / Tarih</th>
                            <th style="width: 25%; text-align: right;"><i class="bx bx-hash me-1"></i>Tutar</th>
                            <th style="width: 25%;" class="text-center"><i class="bx bx-user me-1"></i>Cari</th>
                            <th style="width: 15%;" class="text-center"><i class="bx bx-category me-1"></i>Tipi</th>
                            <th style="width: 20%;" class="text-center"><i class="bx bx-info-circle me-1"></i>Durum</th>
                            <th style="width: 15%;" class="text-center"><i class="bx bx-cog me-1"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="invoice in invoices" :key="invoice.id" class="invoice-row">
                            <td>
                                <div class="d-flex flex-column">
                                    <a :href="`{{route('invoice.stockcardmovementform', ['id' => ''])}}${invoice.id}`" class="fw-bold text-primary" v-text="'#' + invoice.number"></a>
                                    <small class="text-muted" v-text="invoice.created_at"></small>
                                </div>
                            </td>
                            <td class="text-right" style="text-align: right">
                                <span class="fw-bold text-danger" v-text="invoice.total_price"></span> ₺
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-danger" v-text="invoice.customer_name"></span>
                            </td>
                            <td class="text-center">
                                <span class="badge" :style="{background: invoice.type_color, color: '#000'}" v-text="invoice.type_name"></span>
                            </td>
                            <td class="text-center">
                                <span v-if="invoice.is_status == 1" 
                                      class="badge bg-warning" 
                                      data-bs-toggle="tooltip" 
                                      data-bs-html="true" 
                                      :data-bs-original-title="`Gönderilmedi<br>Fiyat: ${invoice.total_price} ₺<br>Fatura Tarihi: ${invoice.create_date || '-'}`">
                                    <i class="bx bx-paper-plane me-1"></i>
                                    <span>Gönderilmedi</span>
                                </span>
                                <span v-else-if="invoice.is_status == 2" 
                                      class="badge bg-success"
                                      data-bs-toggle="tooltip" 
                                      data-bs-html="true" 
                                      :data-bs-original-title="`Kısmi Ödeme<br>Fiyat: ${invoice.total_price} ₺`">
                                    <i class="bx bx-adjust me-1"></i>
                                    <span>Kısmi Ödeme</span>
                                </span>
                                <span v-else-if="invoice.is_status == 3" 
                                      class="badge bg-danger"
                                      data-bs-toggle="tooltip" 
                                      data-bs-html="true" 
                                      :data-bs-original-title="`Vadesi Geçmiş<br>Fiyat: ${invoice.total_price} ₺`">
                                    <i class="bx bx-info-circle me-1"></i>
                                    <span>Vadesi Geçmiş</span>
                                </span>
                                <span v-else class="badge bg-secondary">
                                    <i class="bx bx-time me-1"></i>
                                    <span>Beklemede</span>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a :href="`{{route('invoice.serialprint', ['id' => ''])}}${invoice.id}`" target="_blank" title="Seri Numarası Yazdır" class="btn btn-sm btn-primary">
                                        <i class="bx bx-barcode-reader"></i>
                                    </a>
                                    <a :href="`{{route('invoice.qrprint', ['id' => ''])}}${invoice.id}`" target="_blank" title="QR Kod Yazdır" class="btn btn-sm btn-primary">
                                        <i class="bx bx-qr"></i>
                                    </a>
                                    <a :href="`{{route('invoice.stockcardmovementform', ['id' => ''])}}${invoice.id}`" title="Düzenle" class="btn btn-sm btn-warning">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="table-page-pagination table-page-fade-in-delay-3">
            <nav aria-label="Faturalar sayfalama">
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
    </div>
@endsection

@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    invoices: [],
                    pagination: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 15,
                        total: 0,
                        from: 0,
                        to: 0
                    },
                    filters: {
                        search: '',
                        invoice_id: '',
                        customer_id: ''
                    },
                    loading: {
                        invoices: true,
                        search: false
                    },
                    invoiceType: @json($type ?? 1),
                }
            },
            
            async mounted() {
                console.log('Invoice Index Vue app mounted');
                this.setupAxios();
                await this.loadInvoices();
            },
            
            methods: {
                setupAxios() {
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                    }
                },
                
                async loadInvoices(page = 1) {
                    try {
                        this.loading.invoices = true;
                        
                        const params = {
                            type: this.invoiceType,
                            page: page,
                            per_page: 15
                        };
                        
                        if (this.filters.search) params.search = this.filters.search;
                        if (this.filters.invoice_id) params.invoice_id = this.filters.invoice_id;
                        if (this.filters.customer_id) params.customer_id = this.filters.customer_id;
                        
                        
                        const response = await axios.get('{{ route("invoice.invoices.data") }}', {
                            params: params
                        });
                        
                        
                        if (response.data && response.data.success) {
                            this.invoices = response.data.data || [];
                            this.pagination = response.data.pagination || {};
                            
                            // Tooltip'leri yeniden başlat
                            this.$nextTick(() => {
                                this.initializeTooltips();
                            });
                        } else {
                            console.error('API response error:', response.data);
                            this.invoices = [];
                        }
                        
                    } catch (error) {
                        console.error('Faturalar yüklenirken hata:', error);
                        console.error('Error details:', error.response?.data);
                        this.showNotification('Faturalar yüklenemedi', 'error');
                        this.invoices = [];
                    } finally {
                        this.loading.invoices = false;
                    }
                },
                
                async searchInvoices() {
                    this.loading.search = true;
                    await this.loadInvoices(1);
                    this.loading.search = false;
                },
                
                clearFilters() {
                    this.filters = {
                        search: '',
                        invoice_id: '',
                        customer_id: ''
                    };
                    this.loadInvoices(1);
                },
                
                changePage(page) {
                    if (page < 1 || page > this.pagination.last_page) {
                        return;
                    }
                    this.loadInvoices(page);
                },
                
                getPageNumbers() {
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const pages = [];
                    
                    const maxPages = 7;
                    let start = Math.max(1, current - Math.floor(maxPages / 2));
                    let end = Math.min(last, start + maxPages - 1);
                    
                    if (end - start < maxPages - 1) {
                        start = Math.max(1, end - maxPages + 1);
                    }
                    
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    
                    return pages;
                },
                
                initializeTooltips() {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
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
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                }
            }
        }).mount('#invoice-app');
    </script>
@endsection
