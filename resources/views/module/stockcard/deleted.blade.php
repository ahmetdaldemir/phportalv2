@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/table-page-framework.css') }}">
@endsection

@section('content')
    <div id="app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Table Page Header -->
        <div class="table-page-header table-page-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="bx bx-trash"></i>
                    </div>
                    <div class="header-text">
                        <h2>
                            <i class="bx bx-trash me-2"></i>
                            SİLİNEN STOK KART HAREKETLERİ
                        </h2>
                        <p>Silinen stok kartları ve hareketleri yönetimi</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button @click="loadDeletedMovements" class="btn btn-primary btn-sm">
                        <i class="bx bx-refresh me-1"></i>
                        Yenile
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
                <small>Silinen stok arama ve filtreleme</small>
            </div>
            <div class="filter-body">
                <form @submit.prevent="searchDeletedMovements">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-search"></i> Arama
                            </label>
                            <input type="text" v-model="searchForm.search" class="filter-input" placeholder="Stok adı, seri no, barkod...">
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
            <div v-if="loading.table" class="table-page-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-primary mt-2">Silinen hareketler yükleniyor...</p>
            </div>
            
            <!-- Empty State -->
            <div v-else-if="movements.length === 0" class="table-page-empty">
                <i class="bx bx-trash"></i>
                <h4 class="mt-3">Silinen hareket bulunamadı</h4>
                <p class="text-muted">Arama kriterlerinize uygun silinen hareket bulunamadı.</p>
            </div>
            
            <!-- Table -->
            <div v-else class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;"><i class="bx bx-hash me-1"></i>#</th>
                            <th style="width: 20%;"><i class="bx bx-package me-1"></i>Stok Adı</th>
                            <th style="width: 12%;"><i class="bx bx-barcode me-1"></i>Barkod</th>
                            <th style="width: 12%;"><i class="bx bx-barcode me-1"></i>Seri No</th>
                            <th style="width: 8%;"><i class="bx bx-box me-1"></i>Adet</th>
                            <th style="width: 15%;"><i class="bx bx-store me-1"></i>Şube</th>
                            <th style="width: 15%;"><i class="bx bx-time me-1"></i>Silinme Tarihi</th>
                            <th style="width: 13%;" class="text-center"><i class="bx bx-cog me-1"></i>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="movement in movements" :key="movement.id">
                            <td>
                                <span class="badge bg-danger" v-text="movement.id"></span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong v-text="movement.stock && movement.stock.name ? movement.stock.name : 'Stok Kaydı Silinmiş'"></strong>
                                    <span v-if="!movement.stock || !movement.stock.name" class="badge bg-danger mt-1">Silinmiş</span>
                                </div>
                            </td>
                            <td>
                                <code v-if="movement.barcode" v-text="movement.barcode"></code>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <code v-if="movement.serial_number" v-text="movement.serial_number"></code>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <span class="badge bg-warning" v-text="movement.quantity"></span>
                            </td>
                            <td>
                                <span v-if="movement.seller && movement.seller.name" v-text="movement.seller.name"></span>
                                <span v-else class="text-muted">Şube Kaydı Silinmiş</span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bx bx-time me-1"></i>
                                    <span v-text="formatDate(movement.deleted_at)"></span>
                                </small>
                            </td>
                            <td class="text-center">
                                <button @click="restoreMovement(movement.id)" class="btn btn-sm btn-success" title="Geri Al">
                                    <i class="bx bx-undo"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <hr class="my-5">
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
                    // Form data
                    searchForm: {
                        search: ''
                    },
                    
                    // Data arrays
                    movements: [],
                    
                    // Loading states
                    loading: {
                        search: false,
                        table: true,
                        initial: true
                    }
                }
            },
            
            mounted() {
                this.setupAxios();
                this.loadDeletedMovements();
            },
            
            methods: {
                setupAxios() {
                    // CSRF token setup
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                    }
                },
                
                async loadDeletedMovements() {
                    console.log('Loading deleted movements...');
                    this.loading.table = true;
                    this.loading.search = true;
                    
                    try {
                        const url = '{{ route("stockcard.deleted.data") }}';
                        console.log('Request URL:', url);
                        console.log('Request params:', this.searchForm);
                        
                        const response = await axios.get(url, {
                            params: this.searchForm
                        });
                        
                        console.log('API Response:', response.data);
                        
                        if (response.data && response.data.success) {
                            this.movements = response.data.data || [];
                            console.log('Deleted movements loaded:', this.movements.length);
                        } else {
                            console.error('Unexpected response format:', response.data);
                            this.movements = [];
                        }
                        
                    } catch (error) {
                        console.error('Error loading deleted movements:', error);
                        console.error('Error response:', error.response?.data);
                        this.showNotification('Hata', 'Silinen hareketler yüklenirken bir hata oluştu', 'error');
                        this.movements = [];
                    } finally {
                        this.loading.search = false;
                        this.loading.table = false;
                        this.loading.initial = false;
                    }
                },
                
                async searchDeletedMovements() {
                    await this.loadDeletedMovements();
                },
                
                clearFilters() {
                    this.searchForm = {
                        search: ''
                    };
                    this.loadDeletedMovements();
                },
                
                async restoreMovement(movementId) {
                    if (!confirm('Bu hareketi geri almak istediğinizden emin misiniz?')) {
                        return;
                    }
                    
                    try {
                        const response = await axios.post('/stockcard/restore', {
                            id: movementId
                        });
                        
                        this.showNotification('Başarılı', 'Hareket geri alındı', 'success');
                        this.loadDeletedMovements(); // Refresh the list
                    } catch (error) {
                        console.error('Restore error:', error);
                        this.showNotification('Hata', 'Hareket geri alınırken bir hata oluştu', 'error');
                    }
                },
                
                formatDate(date) {
                    if (!date) return '-';
                    
                    try {
                        const dateObj = new Date(date);
                        if (isNaN(dateObj.getTime())) {
                            return date;
                        }
                        
                        return dateObj.toLocaleDateString('tr-TR', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch (error) {
                        return date;
                    }
                },
                
                showNotification(title, message, type = 'info') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: title,
                            text: message,
                            icon: type,
                            confirmButtonText: 'Tamam'
                        });
                    } else {
                        console.log(`${title}: ${message}`);
                    }
                }
            }
        }).mount('#app');
    </script>
@endsection
