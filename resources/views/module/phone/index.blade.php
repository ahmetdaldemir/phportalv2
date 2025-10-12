@extends('layouts.admin')

@section('content')
    <div id="app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bx bx-mobile display-4 text-primary"></i>
                </div>
                <div>
                    <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600;">
                        <i class="bx bx-mobile me-2"></i>
                        TELEFON LİSTESİ
                    </h2>
                    <p class="mb-0" style="font-size: 0.9rem;">Telefon envanteri ve yönetimi</p>
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
                        <small class="text-muted">Telefon arama ve filtreleme</small>
                    </div>
                    @role(['Depo Sorumlusu','super-admin','Bayi Yetkilisi'])
                    <a href="{{route('phone.create')}}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Yeni Telefon Ekle
                    </a>
                    @endrole
                </div>
            </div>
            <div class="card-body p-4">
                <form @submit.prevent="searchPhones" class="compact-filter-form">
                    <!-- Row 1: Main Filters -->
                    <div class="row g-2 mb-2">
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-package"></i> Marka
                                </label>
                                <select v-model="searchForm.brand" @change="getVersions" class="form-select form-select-sm compact-select">
                                    <option value="">Tüm Markalar</option>
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">@{{ brand.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-mobile-alt"></i> Model
                                </label>
                                <select v-model="searchForm.version" class="form-select form-select-sm compact-select">
                                    <option value="">Tüm Modeller</option>
                                    <option v-for="version in versions" :key="version.id" :value="version.id">@{{ version.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-palette"></i> Renk
                                </label>
                                <select v-model="searchForm.color" class="form-select form-select-sm compact-select">
                                    <option value="">Tümü</option>
                                    <option v-for="color in colors" :key="color.id" :value="color.id">@{{ color.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-check-circle"></i> Durum
                                </label>
                                <select v-model="searchForm.status" class="form-select form-select-sm compact-select">
                                    <option value="">Tümü</option>
                                    <option value="1">Satıldı</option>
                                    <option value="0">Beklemede</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-2 col-md-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-category"></i> Tip
                                </label>
                                <select v-model="searchForm.type" class="form-select form-select-sm compact-select">
                                    <option value="">Tümü</option>
                                    <option value="old">İkinci El</option>
                                    <option value="new">Sıfır</option>
                                    <option value="assigned_device">Teminatlı</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        
                    <!-- Row 2: Secondary Filters -->
                    <div class="row g-2 mb-3">
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-store"></i> Şube
                                </label>
                                <select v-model="searchForm.seller" class="form-select form-select-sm compact-select">
                                    <option value="all">Tüm Şubeler</option>
                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id">@{{ seller.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-barcode"></i> Barkod
                                </label>
                                <input 
                                    type="text" 
                                    v-model="searchForm.barcode" 
                                    class="form-control form-control-sm compact-input" 
                                    placeholder="Barkod numarası..."
                                >
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-4">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-mobile"></i> IMEI
                                </label>
                                <input 
                                    type="text" 
                                    v-model="searchForm.imei" 
                                    class="form-control form-control-sm compact-input" 
                                    placeholder="IMEI numarası..."
                                >
                        </div>
                    </div>
                    
                        <div class="col-lg-3 col-md-12">
                            <div class="compact-filter-group">
                                <label class="compact-label d-none d-md-block invisible">Aksiyon</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill" :disabled="loading.search">
                                        <span v-if="loading.search" class="spinner-border spinner-border-sm me-1"></span>
                                        <i v-else class="bx bx-search me-1"></i>
                                    @{{ loading.search ? 'Aranıyor...' : 'Ara' }}
                                </button>
                                    <button type="button" @click="clearFilters" class="btn btn-outline-secondary btn-sm" title="Filtreleri Temizle">
                                        <i class="bx bx-refresh"></i>
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Loading Overlay -->
            <div v-if="loading.table" class="table-loading-overlay">
                <div class="loading-content">
                    <div class="loading-spinner-large"></div>
                    <div class="loading-text">Telefonlar yükleniyor...</div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-if="!loading.table && phones.length === 0" class="empty-state">
                <div class="empty-content">
                    <i class="bx bx-mobile display-1 text-muted"></i>
                    <h5 class="mt-3">Telefon bulunamadı</h5>
                    <p class="text-muted">Arama kriterlerinize uygun telefon bulunamadı.</p>
                </div>
            </div>
            
            <!-- Table -->
            <div v-if="!loading.table && phones.length > 0" class="table-responsive text-nowrap">
                <table class="table professional-table">
                    <thead>
                    <tr>
                        <th><i class="bx bx-mobile me-1"></i>IMEI</th>
                        <th><i class="bx bx-barcode me-1"></i>Barkod</th>
                        <th><i class="bx bx-package me-1"></i>Marka</th>
                        <th><i class="bx bx-mobile-alt me-1"></i>Model</th>
                        <th><i class="bx bx-category me-1"></i>Tipi</th>
                        <th><i class="bx bx-memory-card me-1"></i>Hafıza</th>
                        <th><i class="bx bx-palette me-1"></i>Renk</th>
                        <th><i class="bx bx-battery me-1"></i>Pil</th>
                        <th><i class="bx bx-shield me-1"></i>Garanti</th>
                        <th><i class="bx bx-store me-1"></i>Bayi</th>
                        <th><i class="bx bx-money me-1"></i>Alış F</th>
                        <th><i class="bx bx-money me-1"></i>Satış F</th>
                        <th><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 professional-tbody">
                    <tr v-for="phone in phones" :key="phone.id">
                        <td>@{{ phone.imei }}</td>
                        <td>@{{ phone.barcode }}</td>
                        <td>@{{ phone.brand.name }}</td>
                        <td>@{{ phone.version ? phone.version.name : 'Bulunamadı' }}</td>
                        <td>@{{ phone.type_text }}</td>
                        <td>@{{ phone.memory }} GB</td>
                        <td>@{{ phone.color.name }}</td>
                        <td>@{{ phone.battery_text }}</td>
                        <td>
                            <span :style="{ color: '#f00' }">@{{ phone.warranty_text }}</span>
                        </td>
                        <td>@{{ phone.seller.name }}</td>
                        <td>
                            @role('Depo Sorumlusu|super-admin')
                            @{{ phone.cost_price_formatted }} <b>₺</b>
                            @endrole
                        </td>
                        <td>@{{ phone.sale_price_formatted }} <b>₺</b></td>
                        <td>
                            <div class="d-flex gap-1 align-items-center">
                                <span v-if="phone.status == 2" class="badge bg-primary">Transfer Sürecinde</span>
                                
                                <template v-if="phone.status == 0 && phone.is_confirm == 1">
                                    <a :href="`/transfer/create?serial_number=${phone.barcode}&type=phone`"
                                       class="btn btn-sm btn-success" title="Sevk Et">
                                        <i class="bx bx-transfer"></i>
                                    </a>
                                    <a :href="`/phone/sale/${phone.id}`"
                                       class="btn btn-sm btn-primary" title="Satış Yap">
                                        <i class="bx bx-shopping-bag"></i>
                                    </a>
                                    @role('Depo Sorumlusu|super-admin')
                                    <a :href="`/phone/edit/${phone.id}`"
                                       class="btn btn-sm btn-warning" title="Düzenle">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    @endrole
                                </template>
                                
                                <template v-if="phone.is_confirm == 0">
                                    @role(['Depo Sorumlusu','super-admin'])
                                    <a @click="confirmPhone(phone.id)"
                                       class="btn btn-sm btn-success" title="Onayla">
                                        <i class="bx bx-check"></i>
                                    </a>
                                    @endrole
                                </template>
                                
                                @role(['super-admin'])
                                <a @click="deletePhone(phone.id)"
                                   class="btn btn-sm btn-danger" title="Sil">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endrole
                                
                                <a :href="`/phone/barcode/${phone.id}`" target="_blank"
                                   class="btn btn-sm btn-info" title="Barkod">
                                    <i class="bx bx-barcode"></i>
                                </a>
                                <a :href="`/phone/show/${phone.id}`"
                                   class="btn btn-sm btn-dark" title="Görüntüle">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a :href="`/phone/printconfirm/${phone.id}`"
                                   class="btn btn-sm btn-secondary" title="Yazdır">
                                    <i class="bx bx-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body mt-4 p-4 box has-text-centered"
                 style="padding-top: 0 !important; padding-bottom: 0 !important;">
                {!! $phones->links() !!}
            </div>
        </div>
        <hr class="my-5">
    </div>
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('phone.delete')}}" id="deleteModalForm">
                @csrf
                <input id="stockCardMovementIdDelete" name="id" type="hidden">
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
                <input id="type" name="type" type="hidden" value="phone">
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
@endsection

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
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
                        brand: '',
                        version: '',
                        color: '',
                        status: '',
                        type: '',
                        seller: 'all',
                        barcode: '',
                        imei: ''
                    },
                    
                    // Data arrays
                    phones: [],
                    brands: @json($brands),
                    colors: @json($colors),
                    sellers: @json($sellers),
                    versions: [],
                    
                    // Loading states
                    loading: {
                        search: false,
                        table: true,
                        initial: true
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
                    this.loading.initial = true;
                    
                    try {
                        // Load initial phones
                        await this.searchPhones();
                        
                        this.loading.initial = false;
                        console.log('Initial data loaded successfully');
                    } catch (error) {
                        console.error('Veri yüklenirken hata:', error);
                        this.loading.initial = false;
                        this.showNotification('Hata', 'Veriler yüklenirken bir hata oluştu', 'error');
                    }
                },
                
                async searchPhones() {
                    console.log('Searching phones...');
                    this.loading.search = true;
                    this.loading.table = true;
                    
                    try {
                        const response = await axios.get('/phone/ajax', {
                            params: this.searchForm
                        });
                        
                        this.phones = response.data.phones || [];
                        console.log('Phones loaded:', this.phones.length);
                        
                    } catch (error) {
                        console.error('Telefon arama hatası:', error);
                        this.showNotification('Hata', 'Telefonlar yüklenirken bir hata oluştu', 'error');
                        this.phones = [];
                    } finally {
                        this.loading.search = false;
                        this.loading.table = false;
                    }
                },
                
                async getVersions() {
                    if (!this.searchForm.brand) {
                        this.versions = [];
                        return;
                    }
                    
                    try {
                        const response = await axios.get('/phone/versions-ajax', {
                            params: { brand_id: this.searchForm.brand }
                        });
                        
                        this.versions = response.data.versions || [];
                        this.searchForm.version = ''; // Reset version when brand changes
                        
                    } catch (error) {
                        console.error('Versions loading error:', error);
                        this.versions = [];
                    }
                },
                
                clearFilters() {
                    this.searchForm = {
                        brand: '',
                        version: '',
                        color: '',
                        status: '',
                        type: '',
                        seller: 'all',
                        barcode: '',
                        imei: ''
                    };
                    this.versions = [];
                    this.searchPhones();
                },
                
                async confirmPhone(phoneId) {
                    if (!confirm('Onaylamak istediğinizden emin misiniz?')) {
                        return;
                    }
                    
                    try {
                        const response = await axios.post(`/phone/confirm/${phoneId}`);
                        this.showNotification('Başarılı', 'Telefon onaylandı', 'success');
                        this.searchPhones(); // Refresh the list
                    } catch (error) {
                        console.error('Phone confirmation error:', error);
                        this.showNotification('Hata', 'Telefon onaylanırken bir hata oluştu', 'error');
                    }
                },
                
                async deletePhone(phoneId) {
                    if (!confirm('Silmek istediğinizden emin misiniz?')) {
                        return;
                    }
                    
                    try {
                        const response = await axios.post('/phone/delete', {
                            id: phoneId
                        });
                        this.showNotification('Başarılı', 'Telefon silindi', 'success');
                        this.searchPhones(); // Refresh the list
                    } catch (error) {
                        console.error('Phone deletion error:', error);
                        this.showNotification('Hata', 'Telefon silinirken bir hata oluştu', 'error');
                    }
                },
                
                showNotification(title, message, type = 'info') {
                    // Using SweetAlert2 for notifications
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
    
    @if($errors->any())
        <script>
            Swal.fire('Satış yapılamaz');
        </script>
    @endif
    <script>
        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#serialBackdrop").val(id);
            $("#stockCardId").val(id);
        }

        $("#transferForm").submit(function (e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

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
    </script>


    <script>
        app.directive('loading', function () {
            return {
                restrict: 'E',
                replace: true,
                template: '<p><img src="img/loading.gif"/></p>', // Define a template where the image will be initially loaded while waiting for the ajax request to complete
                link: function (scope, element, attr) {
                    scope.$watch('loading', function (val) {
                        val = val ? $(element).show() : $(element).hide();  // Show or Hide the loading image
                    });
                }
            }
        }).directive('ngConfirmClick', [
            function () {
                return {
                    link: function (scope, element, attr) {
                        var msg = attr.ngConfirmClick || "Are you sure?";
                        var clickAction = attr.confirmedClick;
                        element.bind('click', function (event) {
                            if (window.confirm(msg)) {
                                scope.$eval(clickAction)
                            }
                        });
                    }
                };
            }]).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {


            $scope.deleteMovement = function (id) {
                Swal.fire({
                    title: "Silmek istediginizden eminmisiniz?",
                    text: "Silme islemi yapilirken kesinlikle not girmelisiniz!",
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
            }
        });
    </script>
@endsection
