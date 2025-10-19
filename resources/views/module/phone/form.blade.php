@extends('layouts.admin')

@section('content')
    <div id="phone-form-app" class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Telefon /</span> Yeni Telefon Ekle</h4>
        
        <form action="{{route('phone.store')}}" id="PhoneinsertForm" method="post" class="needs-validation" novalidate>
            @csrf
            <input name="company_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->company_id}}">
            <input name="user_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
            <input type="hidden" name="quantity" value="1">

            <div class="row g-4">
                <!-- Cari ve Bayi Bilgileri -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-user me-2"></i>Cari ve Bayi Bilgileri
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="selectCustomer" class="form-label fw-semibold">
                                        <i class="bx bx-user-circle me-1 text-primary"></i>Cari Seçiniz
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select 
                                            v-model="form.customer_id" 
                                            @change="onCustomerChange"
                                            class="form-select form-select-lg" 
                                            name="customer_id"
                                            required>
                                            <option value="1">Genel Cari</option>
                                            <option 
                                                v-for="customer in customers" 
                                                :key="customer.id"
                                                :value="customer.id">
                                                @{{ customer.fullname }}
                                            </option>
                                        </select>
                                        <button class="btn btn-outline-primary btn-lg" tabindex="0" data-bs-toggle="modal"
                                                data-bs-target="#editUser" type="button">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-store me-2"></i>Bayi Bilgileri
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="seller_id" class="form-label fw-semibold">
                                        <i class="bx bx-store me-1 text-info"></i>Bayi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="seller_id" id="seller_id" class="form-select form-select-lg"
                                            @role(['Depo Sorumlusu','super-admin'])
                                            @else
                                            disabled
                                            @endrole
                                        required>
                                        <option value="">Seçiniz</option>
                                        @foreach($sellers as $seller)
                                            <option
                                                @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected
                                                @endif value="{{$seller->id}}">{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Telefon Temel Bilgileri -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-mobile me-2"></i>Telefon Temel Bilgileri
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="imei" class="form-label fw-semibold">
                                        <i class="bx bx-barcode me-1 text-primary"></i>IMEI
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="imei" name="imei" maxlength="15" 
                                           placeholder="IMEI numarasını giriniz..." required>
                                </div>
                                <div class="col-12">
                                    <label for="type" class="form-label fw-semibold">
                                        <i class="bx bx-category me-1 text-primary"></i>Tipi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="type" id="type" class="form-select form-select-lg" required>
                                        <option value="">Seçiniz</option>
                                        @foreach(\App\Models\Phone::TYPE as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Marka, Model ve Renk Bilgileri -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-palette me-2"></i>Marka, Model ve Renk
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="brand_id" class="form-label fw-semibold">
                                        <i class="bx bx-tag me-1 text-info"></i>Marka
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="brand_id" id="brand_id" v-model="form.brand_id" @change="onBrandChange"
                                            class="form-select form-select-lg" required>
                                        <option value="">Seçiniz</option>
                                        <option v-for="brand in computedBrands" :key="brand.id" :value="brand.id">
                                            @{{ brand.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="version_id" class="form-label fw-semibold">
                                        <i class="bx bx-mobile me-1 text-info"></i>Model
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="version_id" id="version_id" v-model="form.version_id" 
                                            class="form-select form-select-lg select2" required>
                                        <option value="">@{{ form.brand_id ? 'Model seçiniz' : 'Önce marka seçiniz' }}</option>
                                        <option v-for="version in versions" :key="version.id" :value="version.id">
                                            @{{ version.name }}
                                        </option>
                                    </select>
                                    <div v-if="loading.versions" class="form-text">
                                        <i class="bx bx-loader-alt bx-spin me-1"></i>Modeller yükleniyor...
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="color_id" class="form-label fw-semibold">
                                        <i class="bx bx-palette me-1 text-info"></i>Renk
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="color_id" id="color_id" v-model="form.color_id" 
                                            class="form-select form-select-lg" required>
                                        <option value="">Seçiniz</option>
                                        <option v-for="color in computedColors" :key="color.id" :value="color.id">
                                            @{{ color.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fiyat Bilgileri -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-money me-2"></i>Fiyat Bilgileri
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="cost_price" class="form-label fw-semibold">
                                        <i class="bx bx-purchase-tag me-1 text-primary"></i>Alış Fiyatı
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₺</span>
                                        <input type="text" class="form-control form-control-lg" id="cost_price" name="cost_price" 
                                               placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="sale_price" class="form-label fw-semibold">
                                        <i class="bx bx-tag me-1 text-primary"></i>Satış Fiyatı
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₺</span>
                                        <input type="text" class="form-control form-control-lg" id="sale_price" name="sale_price" 
                                               placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Telefon Detay Bilgileri -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-cog me-2"></i>Telefon Detay Bilgileri
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="memory" class="form-label fw-semibold">
                                        <i class="bx bx-memory-card me-1 text-info"></i>Hafıza (GB)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control form-control-lg" id="memory" name="memory" 
                                           placeholder="128" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="batery" class="form-label fw-semibold">
                                        <i class="bx bx-battery me-1 text-info"></i>Pil Durumu (%)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control form-control-lg" id="batery" name="batery" 
                                           value="0" min="0" max="100" placeholder="85" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="warranty" class="form-label fw-semibold">
                                        <i class="bx bx-calendar me-1 text-info"></i>Garanti Süresi
                                    </label>
                                    <input type="date" class="form-control form-control-lg" id="warranty" 
                                           value="{{date('Y-m-d')}}" name="warranty">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-x-circle me-1 text-info"></i>Garantisiz Mi?
                                    </label>
                                    <div class="form-check form-switch form-switch-lg mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_warranty" 
                                               value="0" name="is_warranty">
                                        <label class="form-check-label" for="is_warranty">
                                            Garantisiz
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="physical_condition" class="form-label fw-semibold">
                                        <i class="bx bx-info-circle me-1 text-info"></i>Fiziksel Durum
                                    </label>
                                    <textarea class="form-control form-control-lg" id="physical_condition"
                                              name="physical_condition" rows="3" 
                                              placeholder="Telefonun fiziksel durumunu açıklayınız..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="altered_parts" class="form-label fw-semibold">
                                        <i class="bx bx-wrench me-1 text-info"></i>Değişmiş Parçalar
                                    </label>
                                    <textarea class="form-control form-control-lg" id="altered_parts"
                                              name="altered_parts" rows="3" 
                                              placeholder="Değiştirilen parçaları belirtiniz..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Açıklama ve Kaydet -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-edit me-2"></i>Açıklama ve Kaydet
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="bx bx-message-square-detail me-1 text-primary"></i>Açıklama
                                    </label>
                                    <textarea name="description" class="form-control form-control-lg" rows="4" 
                                              placeholder="Telefon hakkında ek bilgiler..."></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-3 pt-3">
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" 
                                                onclick="window.history.back()">
                                            <i class="bx bx-arrow-back me-2"></i>Geri Dön
                                        </button>
                                        <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg px-5">
                                            <i class="bx bx-save me-2"></i>Kaydet
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@include('components.customermodal')

@section('custom-css')
    <style>
        /* Modern Form Stilleri */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }

        .form-control-lg, .form-select-lg {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 16px;
            padding: 12px 16px;
        }

        .form-control-lg:focus, .form-select-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .form-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border: none;
            padding: 20px 24px;
        }

        .card-body {
            padding: 24px;
        }

        .btn-lg {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        .form-switch-lg .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }

        .form-switch-lg .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Input Group Styling */
        .input-group-text {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #e9ecef;
            border-right: none;
            font-weight: 600;
            color: #667eea;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 16px;
            }
            
            .form-control-lg, .form-select-lg {
                font-size: 14px;
                padding: 10px 12px;
            }
            
            .btn-lg {
                padding: 10px 20px;
                font-size: 14px;
            }
        }

        /* Animation for form elements */
        .form-control, .form-select {
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            transform: translateY(-1px);
        }

        /* Icon styling */
        .bx {
            font-size: 1.1em;
        }

        /* Page title styling */
        .fw-bold.py-3.mb-4 {
            color: #2d3748;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem !important;
        }

        /* Form validation styling */
        .needs-validation .form-control:invalid,
        .needs-validation .form-select:invalid {
            border-color: #dc3545;
        }

        .needs-validation .form-control:valid,
        .needs-validation .form-select:valid {
            border-color: #28a745;
        }
    </style>
@endsection

@section('custom-js')
    <script>
        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('input[id=TagifyBasic]');
        var input1 = document.querySelector('input[id=TagifyBasic1]');

        // initialize Tagify on the above input node reference
        new Tagify(input);
        new Tagify(input1);

    </script>

    <!-- Vue.js App for Phone Form -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Vue === 'undefined') {
            console.error('Vue.js is not loaded.');
            return;
        }

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    form: {
                        customer_id: '1',
                        brand_id: '',
                        version_id: '',
                        color_id: ''
                    },
                    customers: @json($customers ?? []),
                    brands: @json($brands ?? []),
                    versions: [],
                    colors: @json($colors ?? []),
                    globalStore: window.globalStore || { cache: { brands: [], colors: [], versions: [], customers: [] } },
                    loading: {
                        versions: false
                    }
                }
            },
            computed: {
                // Use global store if available, otherwise use local data
                computedBrands() {
                    return this.globalStore.cache.brands.length > 0 
                        ? this.globalStore.cache.brands 
                        : this.brands;
                },
                computedColors() {
                    return this.globalStore.cache.colors.length > 0 
                        ? this.globalStore.cache.colors 
                        : this.colors;
                }
            },
            methods: {
                onCustomerChange() {
                    console.log('Customer changed:', this.form.customer_id);
                },
                
                // Marka değiştiğinde modelleri yükle
                async onBrandChange() {
                    console.log('Brand changed:', this.form.brand_id);
                    
                    if (!this.form.brand_id) {
                        this.versions = [];
                        this.form.version_id = '';
                        return;
                    }
                    
                    this.loading.versions = true;
                    
                    try {
                        const response = await fetch(`/phone/versions-ajax?brand_id=${this.form.brand_id}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        
                        const data = await response.json();
                        this.versions = data.versions || [];
                        this.form.version_id = ''; // Reset version selection
                        
                        console.log('Versions loaded:', this.versions.length);
                        
                    } catch (error) {
                        console.error('Error loading versions:', error);
                        this.versions = [];
                        this.showNotification('Modeller yüklenemedi', 'error');
                    } finally {
                        this.loading.versions = false;
                    }
                },
                
                // Global store'dan verileri yükle
                async loadGlobalData() {
                    try {
                        // Paralel olarak tüm verileri yükle
                        const [brandsRes, colorsRes] = await Promise.all([
                            fetch('/api/common/brands', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            }),
                            fetch('/api/common/colors', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                        ]);
                        
                        if (brandsRes.ok && colorsRes.ok) {
                            const brandsData = await brandsRes.json();
                            const colorsData = await colorsRes.json();
                            
                            // Global store'u güncelle
                            if (window.globalStore) {
                                window.globalStore.cache.brands = brandsData;
                                window.globalStore.cache.colors = colorsData;
                            }
                            
                            console.log('Global data loaded:', {
                                brands: brandsData.length,
                                colors: colorsData.length
                            });
                        }
                        
                    } catch (error) {
                        console.error('Error loading global data:', error);
                    }
                },
                
                // Bildirim göster
                showNotification(message, type = 'info') {
                    // Bootstrap toast veya alert kullanabilirsiniz
                    console.log(`${type.toUpperCase()}: ${message}`);
                },
                
                handleTypeChange() {
                    const typeSelect = document.getElementById('type');
                    const value = typeSelect.value;
                    const warrantyInput = document.getElementById('warranty');
                    const isWarrantyCheckbox = document.getElementById('is_warranty');
                    
                    if(value == 'old') {
                        isWarrantyCheckbox.removeAttribute('disabled');
                        
                        if (isWarrantyCheckbox.checked) {
                            warrantyInput.setAttribute('disabled', 'disabled');
                            warrantyInput.removeAttribute('required');
                            warrantyInput.value = '';
                        } else {
                            warrantyInput.removeAttribute('disabled');
                            warrantyInput.setAttribute('required', 'required');
                        }
                    } else {
                        isWarrantyCheckbox.checked = false;
                        isWarrantyCheckbox.setAttribute('disabled', 'disabled');
                        warrantyInput.setAttribute('disabled', 'disabled');
                    }
                },
                
                handleWarrantyChange() {
                    const typeSelect = document.getElementById('type');
                    const value = typeSelect.value;
                    const warrantyInput = document.getElementById('warranty');
                    const isWarrantyCheckbox = document.getElementById('is_warranty');
                    
                    if(value == 'old') {
                        if (isWarrantyCheckbox.checked) {
                            warrantyInput.setAttribute('disabled', 'disabled');
                            warrantyInput.removeAttribute('required');
                        } else {
                            warrantyInput.removeAttribute('disabled');
                            warrantyInput.setAttribute('required', 'required');
                        }
                    } else {
                        warrantyInput.removeAttribute('disabled');
                        warrantyInput.removeAttribute('required');
                    }
                }
            },
            mounted() {
                // Global verileri yükle
                this.loadGlobalData();
                
                // Listen for customer save events
                window.addEventListener('customerSaved', (event) => {
                    const customer = event.detail;
                    if (customer && customer.id) {
                        // Check if customer already exists
                        const exists = this.customers.find(c => c.id === customer.id);
                        if (!exists) {
                            this.customers.push(customer);
                        }
                        this.form.customer_id = customer.id;
                        
                        console.log('New customer selected:', customer);
                    }
                });
                
                // Setup event listeners for warranty logic
                const typeSelect = document.getElementById('type');
                const isWarrantyCheckbox = document.getElementById('is_warranty');
                
                if (typeSelect) {
                    typeSelect.addEventListener('change', this.handleTypeChange);
                }
                
                if (isWarrantyCheckbox) {
                    isWarrantyCheckbox.addEventListener('click', this.handleWarrantyChange);
                }
            }
        }).mount('#phone-form-app');
    });
    </script>
@endsection
