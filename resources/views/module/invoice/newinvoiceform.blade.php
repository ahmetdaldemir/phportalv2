@extends('layouts.admin')

@section('content')
    <div id="invoice-app" class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-add">
            <div class="col-12">
                <div class="card invoice-preview-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bx bx-receipt me-2"></i>
                            Yeni Fatura Oluştur
                        </h4>
                        <div class="d-flex gap-2">
                            <button @click="resetForm" class="btn btn-outline-secondary">
                                <i class="bx bx-refresh me-1"></i>Sıfırla
                            </button>
                            <button @click="saveAsDraft" class="btn btn-outline-primary">
                                <i class="bx bx-save me-1"></i>Taslak Kaydet
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" class="invoice-form">
                        <input type="hidden" name="type" value="1" />

                        <div class="card-body">
                            <!-- Header Section -->
                            <div class="row p-3">
                                <div class="col-md-6 mb-4">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">
                                            <i class="bx bx-user me-1"></i>Cari Seçiniz
                                        </label>
                                        <div class="input-group">
                                            <div class="position-relative flex-grow-1">
                                                <input 
                                                    v-model="customer_search" 
                                                    @input="filterCustomers"
                                                    @focus="showCustomerDropdown"
                                                    @blur="hideCustomerDropdown"
                                                    type="text" 
                                                    class="form-control" 
                                                    placeholder="Cari ara..."
                                                    autocomplete="off">
                                                <div v-show="show_customer_dropdown && customer_search && customer_search.length >= 1" class="dropdown-menu show position-absolute w-100" style="z-index: 99999 !important; max-height: 200px; overflow-y: auto; display: block !important; visibility: visible !important; opacity: 1 !important;">
                                                    <div @click="selectCustomer({id: '0', fullname: 'Genel Cari'})" 
                                                         class="dropdown-item" 
                                                         style="cursor: pointer;"
                                                         v-text="'Genel Cari'">
                                                    </div>
                                                    <div v-for="customer in filtered_customers" :key="customer.id" 
                                                         @click="selectCustomer(customer)"
                                                         class="dropdown-item" 
                                                         style="cursor: pointer;"
                                                         v-text="customer.fullname + (customer.phone1 ? ' - ' + customer.phone1 : '')">
                                                    </div>
                                                    <div v-if="filtered_customers.length === 0 && customer_search.length >= 1" class="dropdown-item text-muted">
                                                        Cari bulunamadı
                                                    </div>
                                                </div>
                                        </div>
                                            <button @click="openCustomerModal" class="btn btn-primary" type="button">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Müşteri bilgilerini seçin veya yeni müşteri
                                            ekleyin</small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-semibold">Fatura No</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bx bx-hash"></i>
                                                </span>
                                                <input v-model="form.number" type="text" class="form-control"
                                                    placeholder="Otomatik oluşturulacak">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Fatura Tarihi</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bx bx-calendar"></i>
                                                </span>
                                                <input v-model="form.create_date" type="date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mx-n4">
                        </div>

                        <!-- Items Section -->
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">
                                    <i class="bx bx-list-ul me-2"></i>Fatura Kalemleri
                                </h5>
                                <button @click="addItem" type="button" class="btn btn-success">
                                    <i class="bx bx-plus me-1"></i>Kalem Ekle
                                </button>
                            </div>

                            <div class="table-responsive" style="overflow: visible !important;">
                                <table class="table table-hover" style="overflow: visible !important;">
                                    <thead class="table-header-modern">
                                        <tr>
                                            <th class="compact-header">
                                                <i class="bx bx-package me-1"></i>
                                                <span class="header-text">Stok</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-barcode me-1"></i>
                                                <span class="header-text">Seri No</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-palette me-1"></i>
                                                <span class="header-text">Renk</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-hash me-1"></i>
                                                <span class="header-text">Adet</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-code me-1"></i>
                                                <span class="header-text">Prefix</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-money me-1"></i>
                                                <span class="header-text">G.Maliyet</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-calculator me-1"></i>
                                                <span class="header-text">Maliyet</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-credit-card me-1"></i>
                                                <span class="header-text">Satış<br><small>Fiyatı</small></span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-store me-1"></i>
                                                <span class="header-text">Şube</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-building me-1"></i>
                                                <span class="header-text">Depo</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-qr me-1"></i>
                                                <span class="header-text">Barkod</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-cog me-1"></i>
                                                <span class="header-text">İşlem</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="overflow: visible !important;">
                                        <tr v-for="(item, index) in form.items" :key="index"
                                            class="invoice-item-row" style="overflow: visible !important; position: relative !important;">
                                            <!-- Stok -->
                                            <td style="overflow: visible !important; position: relative !important;">
                                                <div class="position-relative">
                                                    <input 
                                                        v-model="item.stock_search" 
                                                        @input="filterStocks(index)"
                                                        @focus="showStockDropdown(index)"
                                                        @blur="hideStockDropdown(index)"
                                                        @keydown.enter.prevent="selectFirstStock(index)"
                                                        @keydown.escape="hideStockDropdown(index)"
                                                        @keydown.down.prevent="navigateDropdown(index, 1)"
                                                        @keydown.up.prevent="navigateDropdown(index, -1)"
                                                        type="text" 
                                                        class="form-control form-control-sm" 
                                                        :class="{'is-invalid': item.stock_error, 'is-valid': item.stock_card_id}"
                                                        placeholder="Stok ara... (en az 2 karakter)"
                                                        autocomplete="off"
                                                        :disabled="item.loading_stocks">
                                                    <div v-show="item.show_stock_dropdown && item.stock_search && item.stock_search.length >= 2" 
                                                         :id="'stock-dropdown-' + index"
                                                         class="dropdown-menu show position-absolute w-100" 
                                                         style="z-index: 99999 !important; max-height: 300px; overflow-y: auto; display: block !important; visibility: visible !important; opacity: 1 !important; border: 1px solid #dee2e6; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);"
                                                         @mouseenter="cancelHideDropdown()"
                                                         @mouseleave="hideStockDropdown(index)">
                                                        
                                                        <!-- Loading state -->
                                                        <div v-if="item.loading_stocks" class="dropdown-item text-center text-muted">
                                                            <i class="fas fa-spinner fa-spin me-2"></i>Yükleniyor...
                                                        </div>
                                                        
                                                        <!-- Stock results -->
                                                        <div v-else-if="item.filtered_stocks.length > 0">
                                                            <div v-for="(stock, stockIndex) in item.filtered_stocks" :key="stock.id" 
                                                                 @click="selectStock(index, stock)"
                                                                 class="dropdown-item d-flex justify-content-between align-items-center" 
                                                                 :class="{'active': item.selected_stock_index === stockIndex}"
                                                                 style="cursor: pointer; padding: 0.5rem 1rem; border-bottom: 1px solid #f8f9fa;"
                                                                 @mouseenter="item.selected_stock_index = stockIndex; $event.target.style.backgroundColor='#f8f9fa'"
                                                                 @mouseleave="$event.target.style.backgroundColor='transparent'">
                                                                
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-bold text-dark">@{{ stock.name }}</div>
                                                                    <div class="text-muted small">
                                                                        <div v-if="stock.barcode">@{{ stock.barcode }}</div>
                                                                        <div v-if="stock.brand_name || stock.version_names">
                                                                            <span v-if="stock.brand_name">@{{ stock.brand_name }}</span>
                                                                            <span v-if="stock.version_names" class="ms-1">- @{{ stock.version_names }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- No results -->
                                                        <div v-else-if="item.filtered_stocks.length === 0 && item.stock_search.length >= 2" class="dropdown-item text-center text-muted py-3">
                                                            <i class="fas fa-search me-2"></i>
                                                            "@{{ item.stock_search }}" için stok bulunamadı
                                                        </div>
                                                        
                                                        <!-- Error state -->
                                                        <div v-if="item.stock_error" class="dropdown-item text-center text-danger py-2">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            @{{ item.stock_error }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Seri No -->
                                            <td>
                                                <input v-model="item.serial" type="text"
                                                    class="form-control form-control-sm" placeholder="Seri No">
                                            </td>

                                            <!-- Renk -->
                                            <td>
                                                <div class="position-relative">
                                                    <input 
                                                        v-model="item.color_search" 
                                                        @input="filterColors(index)"
                                                        @focus="showColorDropdown(index)"
                                                        @blur="hideColorDropdown(index)"
                                                        type="text" 
                                                        class="form-control form-control-sm" 
                                                        placeholder="Renk ara..."
                                                        autocomplete="off" >
                                                    <div v-show="item.show_color_dropdown && item.color_search && item.color_search.length >= 1" 
                                                         :id="'color-dropdown-' + index"
                                                         class="dropdown-menu show position-absolute w-100" 
                                                         style="z-index: 99999 !important; max-height: 200px; overflow-y: auto; display: block !important; visibility: visible !important; opacity: 1 !important;">
                                                        <div v-for="color in item.filtered_colors" :key="color.id" 
                                                             @click="selectColor(index, color)"
                                                             class="dropdown-item" 
                                                             style="cursor: pointer;"
                                                             v-text="color.name">
                                                        </div>
                                                        <div v-if="item.filtered_colors.length === 0 && item.color_search.length >= 1" class="dropdown-item text-muted">
                                                            Renk bulunamadı
                                                        </div>
                                            </div>
                                            </div>
                                            </td>

                                            <!-- Adet -->
                                            <td>
                                                <input v-model.number="item.quantity" type="number"
                                                    class="form-control form-control-sm" min="1" max="5000"
                                                    @input="calculateItemTotal(index)">
                                            </td>

                                            <!-- Prefix -->
                                            <td>
                                                <input v-model="item.prefix" type="text"
                                                    class="form-control form-control-sm" maxlength="3"
                                                    @input="item.prefix = item.prefix.toUpperCase()" pattern="[A-Z]+">
                                            </td>

                                            <!-- Gerçek Maliyet -->
                                            <td>
                                                <input v-model.number="item.cost_price" type="number" step="0.01"
                                                    class="form-control form-control-sm"
                                                    @input="calculateItemTotal(index)" required>
                                            </td>

                                            <!-- Maliyet -->
                                            <td>
                                                <input v-model.number="item.base_cost_price" type="number"
                                                    step="0.01" class="form-control form-control-sm"
                                                    @input="calculateItemTotal(index)" required>
                                            </td>

                                            <!-- Satış Fiyatı -->
                                            <td>
                                                <input v-model.number="item.sale_price" type="number" step="0.01"
                                                    class="form-control form-control-sm"
                                                    @input="calculateItemTotal(index)" required>
                                            </td>

                                            <!-- Şube -->
                                            <td>
                                                <select v-model="item.seller_id" class="form-select form-select-sm"
                                                        required>
                                                    <option value="">Şube</option>
                                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id" v-text="seller.name"></option>
                                                </select>
                                            </td>

                                            <!-- Depo -->
                                            <td>
                                                <select v-model="item.warehouse_id" class="form-select form-select-sm">
                                                    <option value="">Depo</option>
                                                    <option v-for="warehouse in warehouses" :key="warehouse.id"
                                                        :value="warehouse.id" v-text="warehouse.name">
                                                    </option>
                                                </select>
                                            </td>

                                            <!-- Barkod -->
                                            <td>
                                                <input v-model="item.barcode" type="text"
                                                    class="form-control form-control-sm" placeholder="Barkod">
                                            </td>

                                            <!-- İşlem -->
                                            <td>
                                                <button @click="removeItem(index)" type="button"
                                                    class="btn btn-sm btn-outline-danger" v-if="form.items.length > 1">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                            </div>

                            <!-- Summary -->
                            <div class="row mt-4">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Toplam Maliyet:</span>
                                                <strong v-text="formatCurrency(totals.cost)"></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Toplam Base Maliyet:</span>
                                                <strong v-text="formatCurrency(totals.baseCost)"></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Toplam Satış:</span>
                                                <strong class="text-primary" v-text="formatCurrency(totals.sale)"></strong>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Kar:</span>
                                                <strong class="text-success" v-text="formatCurrency(totals.profit)"></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button :disabled="submitting || !isFormValid" type="submit"
                                        class="btn btn-primary btn-lg w-100">
                                        <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                                        <i v-else class="bx bx-save me-2"></i>
                                        <span v-text="submitting ? 'Kaydediliyor...' : 'Faturayı Kaydet'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@include('components.customermodal')

        @section('custom-js')
            <script>
        // Disable AngularJS for this specific div to avoid conflicts
        const { createApp } = Vue;

        createApp({
            mixins: [VueGlobalMixin],
            data() {
                return {
                    form: {
                        customer_id: '0',
                        number: '',
                        create_date: new Date().toISOString().substr(0, 10),
                        items: [{
                            stock_card_id: '',
                            stock_search: '',
                            show_stock_dropdown: false,
                            filtered_stocks: [],
                            color_id: '',
                            color_search: '',
                            show_color_dropdown: false,
                            filtered_colors: [],
                            serial: '',
                            quantity: 1,
                            prefix: '',
                            cost_price: 0,
                            base_cost_price: 0,
                            sale_price: 0,
                            seller_id: 1,
                            warehouse_id: '',
                            barcode: '',
                            reason_id: 9,
                            tracking_quantity: 0,
                            discount: 0,
                            tax: 20,
                            description: ''
                        }]
                    },
                    stocks: @json($stocks ?? []),
                    customers: [],
                    sellers: [],
                    colors: [],
                    warehouses: [],
                    customer_search: 'Genel Cari',
                    show_customer_dropdown: false,
                    filtered_customers: [],
                    submitting: false,
                    lastInvoiceId: null
                }
            },
            computed: {
                totals() {
                    return this.form.items.reduce((acc, item) => {
                        const qty = item.quantity || 0;
                        acc.cost += (item.cost_price || 0) * qty;
                        acc.baseCost += (item.base_cost_price || 0) * qty;
                        acc.sale += (item.sale_price || 0) * qty;
                        return acc;
                    }, {
                        cost: 0,
                        baseCost: 0,
                        sale: 0,
                        profit: 0
                    });
                },
                isFormValid() {
                    return this.form.items.every(item =>
                        item.stock_card_id &&
                        item.seller_id &&
                        item.quantity > 0
                    );
                }
            },
            watch: {
                'totals.sale'(newVal) {
                    this.totals.profit = newVal - this.totals.cost;
                }
            },
            async mounted() {
                
                try {
                    // Load common data from global store
                    await this.loadGlobalData();
                    
          
                } catch (error) {
                    console.error('Error loading global data:', error);
                }

                // Pre-select stock if passed from URL
                try {
                    const urlParams = new URLSearchParams(window.location.search);
                    const stockId = urlParams.get('id');
                    if (stockId && this.form.items[0]) {
                        this.form.items[0].stock_card_id = stockId;
                        this.onStockChange(0);
                    }
                } catch (error) {
                    console.error('Error handling URL params:', error);
                }

                // Set initial customer search value
                try {
                    if (this.form.customer_id === '0') {
                        this.customer_search = 'Genel Cari';
                    }
                } catch (error) {
                    console.error('Error setting initial customer:', error);
                }

                // Listen for customerSaved event from modal component
                window.addEventListener('customerSaved', (event) => {
                    const customer = event.detail;
                    if (customer && customer.id) {
                        // Add to customers list if not exists
                        const exists = this.customers.find(c => c.id === customer.id);
                        if (!exists) {
                            this.customers.push(customer);
                        }
                        
                        // Add to global cache if not exists
                        const cacheExists = this.globalStore.cache.customers.find(c => c.id === customer.id);
                        if (!cacheExists) {
                            this.globalStore.cache.customers.push(customer);
                        }
                        
                        // Set as selected customer
                        this.form.customer_id = customer.id;
                        this.customer_search = customer.fullname || (customer.firstname + ' ' + customer.lastname);
                        
                        console.log('New customer added and selected:', customer);
                    }
                });

                // Ensure all dropdowns are closed on mount
                this.closeAllDropdowns();
            },
            methods: {
                closeAllDropdowns() {
                    try {
                        // Close customer dropdown
                        this.show_customer_dropdown = false;
                        this.filtered_customers = [];
                        
                        // Close all item dropdowns
                        this.form.items.forEach(item => {
                            item.show_stock_dropdown = false;
                            item.show_color_dropdown = false;
                            item.filtered_stocks = [];
                            item.filtered_colors = [];
                        });
                        
                    } catch (error) {
                        console.error('Error closing dropdowns:', error);
                    }
                },
                closeOtherDropdowns(currentIndex) {
                    try {
                        // Close other item dropdowns except current
                        this.form.items.forEach((item, index) => {
                            if (index !== currentIndex) {
                                item.show_stock_dropdown = false;
                                item.show_color_dropdown = false;
                                item.filtered_stocks = [];
                                item.filtered_colors = [];
                            }
                        });
                        
                    } catch (error) {
                        console.error('Error closing other dropdowns:', error);
                    }
                },
                async loadGlobalData() {
                    try {
                        // Check if globalStore exists
                        if (typeof this.globalStore === 'undefined' || !this.globalStore) {
                            console.warn('GlobalStore not available, using empty arrays');
                            this.customers = [];
                            this.sellers = [];
                            this.colors = [];
                            this.warehouses = [];
                            return;
                        }
                        
                        // Load from global store with error handling
                        const customersData = this.globalStore.getCustomers();
                        const sellersData = this.globalStore.getSellers();
                        const colorsData = this.globalStore.getColors();
                        const warehousesData = this.globalStore.getWarehouses();
                        
                        // Handle Promise responses
                        this.customers = Array.isArray(customersData) ? customersData : [];
                        this.sellers = Array.isArray(sellersData) ? sellersData : [];
                        this.colors = Array.isArray(colorsData) ? colorsData : [];
                        this.warehouses = Array.isArray(warehousesData) ? warehousesData : [];
                        
                        // If data is Promise, wait for it
                        if (customersData && typeof customersData.then === 'function') {
                            this.customers = await customersData || [];
                        }
                        if (sellersData && typeof sellersData.then === 'function') {
                            this.sellers = await sellersData || [];
                        }
                        if (colorsData && typeof colorsData.then === 'function') {
                            this.colors = await colorsData || [];
                        }
                        if (warehousesData && typeof warehousesData.then === 'function') {
                            this.warehouses = await warehousesData || [];
                        }
                        
                        // Debug data lengths
              
                        
                        // Debug customer data
                        if (this.customers.length > 0) {
                        }
                        
                    } catch (error) {
                        console.error('Error loading global data:', error);
                        // Fallback to empty arrays
                        this.customers = [];
                        this.sellers = [];
                        this.colors = [];
                        this.warehouses = [];
                    }
                },
                addItem() {
                    this.form.items.push({
                        stock_card_id: '',
                        stock_search: '',
                        show_stock_dropdown: false,
                        filtered_stocks: [],
                        color_id: '',
                        color_search: '',
                        show_color_dropdown: false,
                        filtered_colors: [],
                        serial: '',
                        quantity: 1,
                        prefix: '',
                        cost_price: 0,
                        base_cost_price: 0,
                        sale_price: 0,
                        seller_id: 1,
                        warehouse_id: '',
                        barcode: '',
                        reason_id: 9,
                        tracking_quantity: 0,
                        discount: 0,
                        tax: 20,
                        description: ''
                    });
                },
                removeItem(index) {
                    if (this.form.items.length > 1) {
                        this.form.items.splice(index, 1);
                    }
                },
                onStockChange(index) {
                    const stockId = this.form.items[index].stock_card_id;
                    if (stockId) {
                        // Fetch stock price data
                        this.fetchStockPrice(stockId, index);
                    }
                },
                async fetchStockPrice(stockId, index) {
                    try {
                        const response = await fetch(`/api/stock-price/${stockId}`);
                        const data = await response.json();
                        if (data) {
                            this.form.items[index].cost_price = data.cost_price || 0;
                            this.form.items[index].base_cost_price = data.base_cost_price || 0;
                            this.form.items[index].sale_price = data.sale_price || 0;
                        }
                    } catch (error) {
                    }
                },
                onCustomerChange() {
                    // Customer specific logic can be added here
                },
                calculateItemTotal(index) {
                    // Real-time calculation if needed
                },
                // Stock Autocomplete Methods - Improved
                filterStocks(index) {
                    try {
                        const item = this.form.items[index];
                        const searchTerm = item.stock_search ? item.stock_search.trim().toLowerCase() : '';
                        
                        // Clear previous results
                        item.filtered_stocks = [];
                        item.show_stock_dropdown = false;
                        
                        // Minimum search length
                        if (searchTerm.length < 2) {
                            return;
                        }
                        console.log(this.stocks);
                        // Ensure stocks is an array
                        if (!Array.isArray(this.stocks) || this.stocks.length === 0) {
                            console.warn('Stocks data not available or empty');
                            return;
                        }
                        // Advanced filtering with multiple criteria
                        const filtered = this.stocks.filter(stock => {
                            if (!stock || !stock.name) return false;
                            
                            const stockName = stock.name.toLowerCase();
                            const brandName = stock.brand_name?.toLowerCase() || '';
                            const sku = stock.sku?.toLowerCase() || '';
                            const barcode = stock.barcode?.toLowerCase() || '';
                    
                            // Multiple search criteria
                            return stockName.includes(searchTerm) ||
                                   brandName.includes(searchTerm) ||
                                   sku.includes(searchTerm) ||
                                   barcode.includes(searchTerm);
                        })
                        .sort((a, b) => {
                            // Prioritize exact matches
                            const aName = a.name.toLowerCase();
                            const bName = b.name.toLowerCase();
                            
                            if (aName.startsWith(searchTerm) && !bName.startsWith(searchTerm)) return -1;
                            if (!aName.startsWith(searchTerm) && bName.startsWith(searchTerm)) return 1;
                            
                            // Then by name similarity
                            return aName.localeCompare(bName);
                        })
                        .slice(0, 15); // Increased limit for better UX
                        
                        // Update results
                        item.filtered_stocks = filtered;
                        item.show_stock_dropdown = filtered.length > 0;
                        
                        
                    } catch (error) {
                        console.error('Error filtering stocks:', error);
                        this.form.items[index].filtered_stocks = [];
                        this.form.items[index].show_stock_dropdown = false;
                    }
                },
                showStockDropdown(index) {
                    try {
                        // Close other dropdowns first
                        this.closeOtherDropdowns(index);
                        
                        const item = this.form.items[index];
                        
                        // Only show if there's a search term
                        if (item.stock_search && item.stock_search.trim().length >= 2) {
                            item.show_stock_dropdown = true;
                            this.filterStocks(index);
                        } else {
                            item.show_stock_dropdown = false;
                        }
                        
                    } catch (error) {
                        console.error('Error showing stock dropdown:', error);
                    }
                },
                hideStockDropdown(index) {
                    // Use a more reliable delay mechanism
                    this.hideTimeout = setTimeout(() => {
                        if (this.form.items[index]) {
                            this.form.items[index].show_stock_dropdown = false;
                        }
                    }, 300);
                },
                cancelHideDropdown() {
                    if (this.hideTimeout) {
                        clearTimeout(this.hideTimeout);
                        this.hideTimeout = null;
                    }
                },
                // Keyboard navigation
                navigateDropdown(index, direction) {
                    const item = this.form.items[index];
                    if (!item.filtered_stocks || item.filtered_stocks.length === 0) return;
                    
                    // Initialize selected index if not exists
                    if (item.selected_stock_index === undefined) {
                        item.selected_stock_index = -1;
                    }
                    
                    // Navigate
                    item.selected_stock_index += direction;
                    
                    // Keep within bounds
                    if (item.selected_stock_index < 0) {
                        item.selected_stock_index = item.filtered_stocks.length - 1;
                    } else if (item.selected_stock_index >= item.filtered_stocks.length) {
                        item.selected_stock_index = 0;
                    }
                },
                selectFirstStock(index) {
                    const item = this.form.items[index];
                    if (item.filtered_stocks && item.filtered_stocks.length > 0) {
                        this.selectStock(index, item.filtered_stocks[0]);
                    }
                },
                selectStock(index, stock) {
                    try {
                        // Cancel any pending hide operations
                        this.cancelHideDropdown();
                        
                        const item = this.form.items[index];
                        
                        // Validate stock data
                        if (!stock || !stock.id) {
                            console.error('Invalid stock data:', stock);
                            return;
                        }
                        
                        // Update item with selected stock
                        item.stock_card_id = stock.id;
                        item.stock_search = `${stock.name}${stock.brand?.name ? ' - ' + stock.brand.name : ''}${stock.version_names ? ' - ' + stock.version_names : ''}`;
                        item.show_stock_dropdown = false;
                        item.filtered_stocks = [];
                        
                        // Clear any previous error states
                        item.stock_error = null;
                        
                        // Trigger stock change handler
                        this.onStockChange(index);
                        
                        // Force Vue.js to update
                        this.$forceUpdate();
                        
                        
                    } catch (error) {
                        console.error('Error selecting stock:', error);
                        // Show error to user
                        this.form.items[index].stock_error = 'Stok seçimi sırasında hata oluştu';
                    }
                },
                // Color Autocomplete Methods
                filterColors(index) {
                    const item = this.form.items[index];
                    const searchTerm = item.color_search.toLowerCase();
                    
                    if (searchTerm.length < 1) {
                        item.filtered_colors = [];
                        return;
                    }
                    
                    item.filtered_colors = this.colors.filter(color => 
                        color.name.toLowerCase().includes(searchTerm)
                    ).slice(0, 10); // Limit to 10 results
                },
                showColorDropdown(index) {
                    try {
                        // Close other dropdowns first
                        this.closeOtherDropdowns(index);
                        
                        const item = this.form.items[index];
                        item.show_color_dropdown = true;
                        
                        // If there's already a search term, filter immediately
                        if (item.color_search && item.color_search.length >= 1) {
                            this.filterColors(index);
                        }
                        
                    } catch (error) {
                        console.error('Error showing color dropdown:', error);
                    }
                },
                hideColorDropdown(index) {
                    // Delay to allow click events
                    setTimeout(() => {
                        this.form.items[index].show_color_dropdown = false;
                    }, 200);
                },
                selectColor(index, color) {
                    try {
                        const item = this.form.items[index];
                        item.color_id = color.id;
                        item.color_search = color.name;
                        item.show_color_dropdown = false;
                        item.filtered_colors = [];
                        
                        // Force Vue.js to update
                        this.$forceUpdate();
                        
                    } catch (error) {
                        console.error('Error selecting color:', error);
                    }
                },
                // Customer Autocomplete Methods
                filterCustomers() {
                    try {
                        const searchTerm = this.customer_search.toLowerCase();
                        
                        if (searchTerm.length < 1) {
                            this.filtered_customers = [];
                            return;
                        }
                        
                        // Ensure customers is an array
                        if (!Array.isArray(this.customers)) {
                            console.warn('Customers is not an array:', this.customers);
                            this.filtered_customers = [];
                            return;
                        }
                        
                        
                        this.filtered_customers = this.customers.filter(customer => {
                            if (!customer || !customer.fullname) return false;
                            
                            return customer.type === 'account' && (
                                customer.fullname.toLowerCase().includes(searchTerm) ||
                                (customer.phone1 && customer.phone1.includes(searchTerm)) ||
                                (customer.email && customer.email.toLowerCase().includes(searchTerm))
                            );
                        }).slice(0, 10); // Limit to 10 results
                        
                    } catch (error) {
                        console.error('Error filtering customers:', error);
                        this.filtered_customers = [];
                    }
                },
                showCustomerDropdown() {
                    try {
                        this.show_customer_dropdown = true;
                        this.filterCustomers();
                    } catch (error) {
                        console.error('Error showing customer dropdown:', error);
                    }
                },
                hideCustomerDropdown() {
                    try {
                        // Delay to allow click events
                        setTimeout(() => {
                            this.show_customer_dropdown = false;
                        }, 200);
                    } catch (error) {
                        console.error('Error hiding customer dropdown:', error);
                    }
                },
                selectCustomer(customer) {
                    try {
                        if (!customer) {
                            console.error('No customer provided');
                            return;
                        }
                        
                        this.form.customer_id = customer.id;
                        this.customer_search = customer.fullname;
                        this.show_customer_dropdown = false;
                        this.filtered_customers = [];
                        this.onCustomerChange();
                        
                        // Force Vue.js to update
                        this.$forceUpdate();
                        
                    } catch (error) {
                        console.error('Error selecting customer:', error);
                    }
                },
                async submitForm() {
                    
                    if (!this.isFormValid) {
                        alert('Lütfen tüm gerekli alanları doldurun!');
                        return;
                    }

                    this.submitting = true;

                    try {
                        const formData = new FormData();
                        formData.append('type', '1');
                        formData.append('customer_id', this.form.customer_id);
                        formData.append('number', this.form.number);
                        formData.append('create_date', this.form.create_date);

                        // Add items as arrays
                        this.form.items.forEach((item, index) => {
                            Object.keys(item).forEach(key => {
                                formData.append(`${key}[]`, item[key] || '');
                            });
                        });


                        const response = await fetch('{{ route('invoice.stockcardmovementstore') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        });


                        if (response.ok) {
                            // JSON response'dan verileri al
                            const result = await response.json();
                            alert(result.message || 'Fatura başarıyla kaydedildi!');
                            
                            // Invoice ID'sini sakla ve modal'ı aç
                            if (result.id) {
                                this.lastInvoiceId = result.id;
                                this.openSerialPrintModal();
                            } else {
                                alert('Invoice ID alınamadı, barkod yazdırma sayfası açılamadı!');
                            }
                        } else {
                            const errorText = await response.text();
                            console.error('Response error:', errorText);
                            throw new Error('Form gönderimi başarısız: ' + response.status);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Kaydetme sırasında hata oluştu: ' + error.message);
                    } finally {
                        this.submitting = false;
                        this.resetForm();
                    }
                },
                resetForm() {
                    if (confirm('Formu sıfırlamak istediğinizden emin misiniz?')) {
                        this.form = {
                            customer_id: '0',
                            number: '',
                            create_date: new Date().toISOString().substr(0, 10),
                            items: [{
                                stock_card_id: '',
                                serial: '',
                                color_id: '',
                                quantity: 1,
                                prefix: '',
                                cost_price: 0,
                                base_cost_price: 0,
                                sale_price: 0,
                                seller_id: 1,
                                warehouse_id: '',
                                barcode: '',
                                reason_id: 9,
                                tracking_quantity: 0,
                                discount: 0,
                                tax: 20,
                                description: ''
                            }]
                        };
                    }
                },
                saveAsDraft() {
                    // Draft save functionality
                    localStorage.setItem('invoice_draft', JSON.stringify(this.form));
                    alert('Taslak kaydedildi!');
                },
                openCustomerModal() {
                    const modal = new bootstrap.Modal(document.getElementById('editUser'));
                    modal.show();
                },
                openSerialPrintModal() {
                    // Modal HTML'ini oluştur
                    const modalHtml = `
                        <div class="modal fade" id="serialPrintModal" tabindex="-1" aria-labelledby="serialPrintModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="serialPrintModalLabel">Barkod Yazdırma</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe id="serialPrintFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                        <button type="button" class="btn btn-primary" onclick="printSerialFrame()">Yazdır</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Modal'ı DOM'a ekle
                    if (!document.getElementById('serialPrintModal')) {
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        
                        // Global print fonksiyonu ekle
                        window.printSerialFrame = () => {
                            const iframe = document.getElementById('serialPrintFrame');
                            if (iframe && iframe.contentWindow) {
                                try {
                                    iframe.contentWindow.print();
                                } catch (e) {
                                    console.error('Print error:', e);
                                    alert('Yazdırma işlemi başarısız!');
                                }
                            } else {
                                alert('Sayfa henüz yüklenmedi, lütfen bekleyin!');
                            }
                        };
                    }
                    
                    // Invoice ID'yi al (response'dan gelen ID'yi kullan)
                    const invoiceId = this.getLastInvoiceId();
                    const iframe = document.getElementById('serialPrintFrame');
                    
                    if (iframe && invoiceId) {
                        iframe.src = `/invoice/serialprint?id=${invoiceId}`;
                        
                        // Iframe yüklendiğinde print butonunu aktif et
                        iframe.onload = () => {
                            console.log('Serial print page loaded successfully');
                        };
                        
                        // Modal'ı göster
                        const modal = new bootstrap.Modal(document.getElementById('serialPrintModal'));
                        modal.show();
                    } else {
                        console.error('Invoice ID bulunamadı veya iframe yüklenemedi');
                        alert('Barkod yazdırma sayfası açılamadı!');
                    }
                },
                getLastInvoiceId() {
                    // Son kaydedilen invoice ID'sini döndür
                    return this.lastInvoiceId || null;
                }
            }
        }).mount('#invoice-app');
            </script>


    <style>
        /* Invoice Form - Uses project-base.css unified styles */
        /* All styles inherited from base CSS, no custom styles needed */

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
@endsection
