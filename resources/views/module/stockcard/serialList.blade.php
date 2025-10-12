@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div id="app" class="container-xxl flex-grow-1 container-p-y">
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
                            SERİ NUMARASI LİSTESİ
                        </h2>
                        <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Stok seri numaraları ve hareket yönetimi</p>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{route('stockcard.deleted')}}" class="btn btn-success btn-sm">
                        <i class="bx bx-trash me-1"></i>
                        Silinen Seriler
                    </a>
                    
                    <button id="barcode" type="button" 
                            onclick="document.getElementById('itemFrom').submit();" 
                            disabled="disabled" 
                            class="btn btn-danger btn-sm">
                        <i class="bx bx-printer me-1"></i>
                        Barkod Yazdır
                    </button>

                    @role(['Depo Sorumlusu','super-admin'])
                    <button id="multiplepriceUpdate" type="button" class="btn btn-warning btn-sm">
                        <i class="bx bx-dollar me-1"></i>
                        Fiyat Güncelle
                    </button>

                    <a href="{{route('stockcard.create',['category'=>$category])}}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Yeni Stok Ekle
                    </a>
                    @endrole
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card professional-card mb-4">
            <div class="card-header professional-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-filter me-2"></i>
                            Filtreler
                        </h6>
                        <small class="text-muted">Seri numarası arama</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form @submit.prevent="searchStockCards" class="compact-filter-form">
                    <div class="row g-2 mb-2">
                        <div class="col-lg-9 col-md-8">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-barcode"></i> Seri Numarası
                                </label>
                                <input 
                                    type="text" 
                                    v-model="searchForm.serialNumber" 
                                    @keyup.enter="searchStockCards"
                                    class="form-control form-control-sm compact-input" 
                                    placeholder="Seri numarasını giriniz..."
                                    autofocus
                                >
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label d-none d-md-block invisible">Aksiyon</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill" :disabled="loading.search">
                                        <span v-if="loading.search" class="spinner-border spinner-border-sm me-1"></span>
                                        <i v-else class="bx bx-search me-1"></i>
                                        @{{ loading.search ? 'Aranıyor...' : 'Ara' }}
                                    </button>
                                    <button type="button" @click="clearSearch" class="btn btn-outline-secondary btn-sm" title="Temizle">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card professional-card">
            <div class="card-header professional-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-list-ul me-2"></i>
                            Seri Numaraları
                        </h6>
                        <small class="text-muted">
                            <span v-if="stockCards.length > 0">@{{ stockCards.length }} kayıt bulundu</span>
                            <span v-else>Arama yapın</span>
                        </small>
                    </div>
                </div>
            </div>
            <!-- Loading State -->
            <div v-if="loading.stockCards" class="table-loading-overlay">
                <div class="loading-content">
                    <div class="loading-spinner-large"></div>
                    <div class="loading-text">Seri numaraları yükleniyor...</div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="stockCards.length === 0" class="empty-state">
                <div class="empty-content">
                    <i class="bx bx-barcode display-1 text-muted"></i>
                    <h5 class="mt-3">Seri numarası bulunamadı</h5>
                    <p class="text-muted">Aramak için yukarıdaki filtreyi kullanın</p>
                </div>
            </div>

            <!-- Table -->
            <form v-else id="itemFrom" role="form" method="POST" action="{{route('stockcard.barcodes')}}">
                @csrf
                <div class="table-responsive text-nowrap">
                    <table class="table professional-table">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" 
                                       v-model="selectAll" 
                                       @change="toggleSelectAll"
                                       class="form-check-input">
                            </th>
                            <th><i class="bx bx-hash me-1"></i>ID</th>
                            <th><i class="bx bx-barcode me-1"></i>Seri No</th>
                            @role(['Depo Sorumlusu','super-admin'])
                            <th><i class="bx bx-dollar me-1"></i>Maliyet</th>
                            <th><i class="bx bx-dollar me-1"></i>D. Maliyet</th>
                            @endrole
                            <th><i class="bx bx-dollar me-1"></i>Satış F.</th>
                            <th><i class="bx bx-palette me-1"></i>Renk</th>
                            <th><i class="bx bx-purchase-tag me-1"></i>Marka</th>
                            <th><i class="bx bx-mobile me-1"></i>Model</th>
                            <th><i class="bx bx-category me-1"></i>Kategori</th>
                            <th><i class="bx bx-store me-1"></i>Şube</th>
                            <th><i class="bx bx-cog me-1"></i>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 professional-tbody">
                        <tr v-for="stockData in stockCards" 
                            :key="stockData.id"
                            :data-type="stockData.type" 
                            :data-quantity="stockData.quantity"
                            v-show="stockData.quantity > 0">
                            <td class="text-center">
                                <input v-if="stockData.type == 1"
                                       type="checkbox" 
                                       v-model="selectedItems"
                                       :value="stockData.id"
                                       class="form-check-input">
                            </td>
                            <td>@{{ stockData.id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>@{{ stockData.serial_number }}</strong>
                                    <a :href="'{{route('invoice.stockcardmovementform', ['id' => ''])}}/' + stockData.invoice_id" class="text-muted small">
                                        <i class="bx bx-receipt me-1"></i>#@{{ stockData.invoice_id }}
                                    </a>
                                </div>
                            </td>
                            @role(['Depo Sorumlusu','super-admin'])
                            <td><strong>@{{ stockData.cost_price }} ₺</strong></td>
                            <td><strong>@{{ stockData.base_cost_price }} ₺</strong></td>
                            @endrole
                            <td><strong class="text-success">@{{ stockData.sale_price }} ₺</strong></td>
                            <td>@{{ stockData.color?.name || '-' }}</td>
                            <td>@{{ stockData.stock?.brand?.name || '-' }}</td>
                            <td v-html="stockData.stock?.version || '-'"></td>
                            <td>@{{ stockData.categoryPath || '-' }}</td>
                            <td>@{{ stockData.seller?.name || '-' }}</td>
                            <td>
                                <!-- Status Badges -->
                                <span v-if="stockData.type == 4" class="badge badge-info">TRANSFER</span>
                                <span v-else-if="stockData.type == 3" class="badge badge-warning">HASARLI</span>
                                <span v-else-if="stockData.type == 5" class="badge badge-secondary">TEKNİK SERVİS</span>
                                <div v-else-if="stockData.type == 2" class="text-success small">
                                    <div><i class="bx bx-check-circle me-1"></i>SATILDI</div>
                                    <div class="text-muted">@{{ stockData.sale?.user?.name || '-' }}</div>
                                    <div class="text-muted">@{{ stockData.sale?.created_at }}</div>
                                </div>
                                <!-- Action Buttons -->
                                <div v-else class="d-flex gap-1">
                                    <a :href="'{{route('transfer.create', ['serial_number' => '', 'type' => 'other'])}}' + stockData.serial_number"
                                       title="Sevk Et"
                                       class="btn btn-sm btn-success">
                                        <i class="bx bx-transfer"></i>
                                    </a>
                                    @role('Depo Sorumlusu|super-admin')
                                    <button type="button"
                                            @click="openPriceModal(stockData.id)"
                                            class="btn btn-sm btn-danger"
                                            title="Fiyat Güncelle">
                                        <i class="bx bxs-dollar-circle"></i>
                                    </button>
                                    <button type="button"
                                            @click="deleteMovement(stockData.id)"
                                            class="btn btn-sm btn-warning"
                                            title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                    @endrole
                                    <button type="button"
                                            @click="openDemandModal(stockData.id, stockData.stock?.name, stockData.color_id)"
                                            class="btn btn-sm btn-info"
                                            title="Talep Oluştur">
                                        <i class="bx bx-radar"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
         <div class="card mt-4" v-if="pagination && pagination.total > pagination.per_page">
            <div class="card-body mt-4 p-4 box has-text-centered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                            <button class="page-link" @click="loadPage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1">
                                <i class="bx bx-chevron-left"></i>
                            </button>
                        </li>
                        <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                            <button class="page-link" @click="loadPage(page)">@{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                            <button class="page-link" @click="loadPage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page">
                                <i class="bx bx-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <hr class="my-5">
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
                            <input type="text" id="serialBackdrop" class="form-control" name="serial_number[]"/>
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
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
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
                <form action="{{route('demand.store')}}" method="post">
                    <input type="hidden" name="id" id="id" value="">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Renk</label>
                                <select class="form-select" id="color" name="color_id">
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

    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('stockcard.movementdelete')}}" id="deleteModalForm">
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
                            <label for="nameBackdrop" class="form-label">Maliyet</label>
                            <input type="text" id="cost_price" class="form-control" name="cost_price"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Destekli Maliyet</label>
                            <input type="text" id="base_cost_price" class="form-control" name="base_cost_price"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
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
@endsection

@section('custom-js')
    <!-- Vue.js CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    
    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    // Form data
                    searchForm: {
                        serialNumber: ''
                    },
                    
                    // Stock cards data
                    stockCards: [],
                    pagination: null,
                    
                    // Selection
                    selectedItems: [],
                    selectAll: false,
                    
                    // Loading states
                    loading: {
                        search: false,
                        stockCards: false
                    },
                    
                    // Modal data
                    currentStockCardId: null,
                    currentStockCardName: '',
                    currentColorId: null
                }
            },
            
            computed: {
                visiblePages() {
                    if (!this.pagination) return [];
                    
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const delta = 2;
                    
                    let start = Math.max(1, current - delta);
                    let end = Math.min(last, current + delta);
                    
                    const pages = [];
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                }
            },
            
            mounted() {
                console.log('SerialList Vue app mounted');
                // İlk yüklemede veri çekmiyoruz - kullanıcı arama yaptığında yüklenecek
            },
            
            methods: {
                // Load stock cards
                async loadStockCards(page = 1) {
                    this.loading.stockCards = true;
                    try {
                        const response = await axios.get('/stockcard/serialList', {
                            params: {
                                page: page,
                                serialNumber: this.searchForm.serialNumber
                            }
                        });
                        
                        // Parse the response data
                        this.stockCards = response.data.stockcards || [];
                        this.pagination = response.data.pagination || null;
                        
                    } catch (error) {
                        console.error('Stok kartları yüklenemedi:', error);
                        this.stockCards = [];
                    } finally {
                        this.loading.stockCards = false;
                    }
                },
                
                // Search stock cards
                async searchStockCards() {
                    this.loading.search = true;
                    await this.loadStockCards(1);
                    this.loading.search = false;
                },
                
                // Clear search
                clearSearch() {
                    this.searchForm.serialNumber = '';
                    this.stockCards = [];
                    this.pagination = null;
                },
                
                // Load specific page
                async loadPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    await this.loadStockCards(page);
                },
                
                // Toggle select all
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedItems = this.stockCards
                            .filter(item => item.type === 1)
                            .map(item => item.id);
                    } else {
                        this.selectedItems = [];
                    }
                },
                
                // Open price modal
                openPriceModal(id) {
                    this.currentStockCardId = id;
                    $('#priceModal').modal('show');
                },
                
                // Open demand modal
                openDemandModal(id, name, colorId) {
                    this.currentStockCardId = id;
                    this.currentStockCardName = name;
                    this.currentColorId = colorId;
                    $('#demandModal').modal('show');
                },
                
                // Delete movement
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
                
                // Multiple price update
                multiplePriceUpdate() {
                    if (this.selectedItems.length === 0) {
                        Swal.fire('Seçim Yapınız');
                        return;
                    }
                    
                    $('#stockCardMovementIdArray').val(this.selectedItems.join(','));
                    $('#multiplepriceModal').modal('show');
                }
            },
            
            watch: {
                selectedItems: {
                    handler(newVal) {
                        // Update barcode button state
                        const barcodeBtn = document.getElementById('barcode');
                        if (barcodeBtn) {
                            barcodeBtn.disabled = newVal.length === 0;
                        }
                        
                        // Update select all state
                        const selectableItems = this.stockCards.filter(item => item.type === 1);
                        this.selectAll = selectableItems.length > 0 && 
                                       selectableItems.every(item => newVal.includes(item.id));
                    },
                    deep: true
                }
            }
        }).mount('#app');
        
        // Global functions for modals
        function priceModal(id) {
            app.openPriceModal(id);
        }
        
        function demandModal(id, name, color) {
            app.openDemandModal(id, name, color);
        }
        
        function deleteMovement(id) {
            app.deleteMovement(id);
        }
        
        // Multiple price update button
        document.getElementById('multiplepriceUpdate').addEventListener('click', function() {
            app.multiplePriceUpdate();
        });
        
        // Form submissions
        document.getElementById('multiplepriceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const selectedIds = document.getElementById('stockCardMovementIdArray').value.split(',');
            
            try {
                const response = await axios.post('{{route('stockcard.multiplepriceupdate')}}', {
                    stock_card_id_multiple: selectedIds,
                    cost_price: formData.get('cost_price'),
                    base_cost_price: formData.get('base_cost_price'),
                    sale_price: formData.get('sale_price')
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: 'Fiyatlar güncellendi',
                    customClass: {
                        confirmButton: "btn btn-success"
                    }
                });
                
                $('#multiplepriceModal').modal('hide');
                app.loadStockCards();
                
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: error.response?.data?.message || 'Bir hata oluştu',
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
        
        document.getElementById('priceForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const stockCardId = document.getElementById('stockCardMovementId').value;
            
            try {
                const response = await axios.post(`{{route('stockcard.singlepriceupdate')}}?id=${stockCardId}`, {
                    sale_price: formData.get('sale_price')
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: 'Fiyat güncellendi',
                    customClass: {
                        confirmButton: "btn btn-success"
                    }
                });
                
                $('#priceModal').modal('hide');
                app.loadStockCards();
                
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: error.response?.data?.message || 'Bir hata oluştu',
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
        
        document.getElementById('transferForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const stockCardId = document.getElementById('stockCardId').value;
            
            try {
                const response = await axios.post(`{{route('stockcard.sevk')}}?id=${stockCardId}`, {
                    serial_number: formData.get('serial_number'),
                    seller_id: formData.get('seller_id'),
                    reason_id: formData.get('reason_id')
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: 'Sevk işlemi başlatıldı',
                    customClass: {
                        confirmButton: "btn btn-success"
                    }
                });
                
                $('#backDropModal').modal('hide');
                app.loadStockCards();
                
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: error.response?.data?.message || 'Bir hata oluştu',
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    </script>
@endsection

