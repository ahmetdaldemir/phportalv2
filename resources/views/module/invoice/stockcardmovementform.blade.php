@extends('layouts.admin')

@section('content')
<div id="invoice-form-app" class="container-xxl flex-grow-1 container-p-y">
    <div v-if="!invoice" class="text-center p-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
        <p class="mt-2">Fatura bilgileri yükleniyor...</p>
    </div>
    <div v-else class="row invoice-add">
        <div class="col-12">
            <div class="card invoice-preview-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bx bx-receipt me-2"></i>
                        Fatura Düzenle - #@{{ invoice?.invoice_number || invoice?.id || 'N/A' }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Geri Dön
                        </a>
                        <button @click="printInvoice" class="btn btn-outline-primary">
                            <i class="bx bx-printer me-1"></i>Yazdır
                        </button>
                    </div>
                </div>

                <form @submit.prevent="submitForm" class="invoice-form">
                    <input type="hidden" v-model="form.id" />

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
                                            <select 
                                                v-model="form.customer_id" 
                                                @change="onCustomerChange"
                                                class="form-select">
                                                <option value="1">Genel Cari</option>
                                                <option 
                                                    v-for="customer in filteredCustomers" 
                                                    :key="customer.id"
                                                    :value="customer.id">
                                                    @{{ customer.fullname }}
                                                </option>
                                            </select>
                                        </div>
                                        <button @click="openCustomerModal" class="btn btn-primary" type="button">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Müşteri bilgilerini seçin veya yeni müşteri ekleyin</small>
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
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Sadece Renk, Barkod ve Fiyat alanları düzenlenebilir
                                </small>
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
                                            <td>
                                                <input 
                                                    v-model="item.stock_search" 
                                                    type="text" 
                                                    class="form-control form-control-sm" 
                                                    placeholder="Stok"
                                                    disabled
                                                    readonly>
                                            </td>

                                            <!-- Seri No -->
                                            <td>
                                                <input v-model="item.serial" type="text"
                                                    class="form-control form-control-sm" placeholder="Seri No" disabled readonly>
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
                                                        autocomplete="off"
                                                        required>
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
                                                    @input="calculateItemTotal(index)" disabled readonly>
                                            </td>

                                            <!-- Prefix -->
                                            <td>
                                                <input v-model="item.prefix" type="text"
                                                    class="form-control form-control-sm" maxlength="3"
                                                    @input="item.prefix = item.prefix.toUpperCase()" pattern="[A-Z]+" disabled readonly>
                                            </td>

                                            <!-- Gerçek Maliyet -->
                                            <td>
                                                <input v-model.number="item.cost_price" type="number" step="0.01"
                                                    class="form-control form-control-sm"
                                                    @input="calculateItemTotal(index)" required disabled readonly>
                                            </td>

                                            <!-- Maliyet -->
                                            <td>
                                                <input v-model.number="item.base_cost_price" type="number"
                                                    step="0.01" class="form-control form-control-sm"
                                                    @input="calculateItemTotal(index)" required disabled readonly>
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
                                                        required disabled>
                                                    <option value="">Şube</option>
                                                    <option v-for="seller in sellers" :key="seller.id" :value="seller.id" v-text="seller.name"></option>
                                                </select>
                                            </td>

                                            <!-- Depo -->
                                            <td>
                                                <select v-model="item.warehouse_id" class="form-select form-select-sm" disabled>
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
                                                <!-- Remove button disabled for existing items -->
                                                <span class="text-muted">
                                                    <i class="bx bx-lock"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary -->
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <!-- Payment Type Selection -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="bx bx-money me-2"></i>Ödeme Türü
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="form-label">Ödeme Şekli</label>
                                                    <div class="form-check">
                                                        <input 
                                                            v-model="form.payment_type" 
                                                            class="form-check-input" 
                                                            type="radio" 
                                                            value="cash" 
                                                            id="paymentCash">
                                                        <label class="form-check-label" for="paymentCash">
                                                            <i class="bx bx-money me-1"></i>Nakit
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input 
                                                            v-model="form.payment_type" 
                                                            class="form-check-input" 
                                                            type="radio" 
                                                            value="credit_card" 
                                                            id="paymentCard">
                                                        <label class="form-check-label" for="paymentCard">
                                                            <i class="bx bx-credit-card me-1"></i>Kredi Kartı
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input 
                                                            v-model="form.payment_type" 
                                                            class="form-check-input" 
                                                            type="radio" 
                                                            value="installment" 
                                                            id="paymentInstallment">
                                                        <label class="form-check-label" for="paymentInstallment">
                                                            <i class="bx bx-calendar me-1"></i>Taksit
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div v-if="form.payment_type === 'cash'">
                                                        <label class="form-label">Nakit Tutar</label>
                                                        <input 
                                                            v-model.number="form.cash" 
                                                            type="number" 
                                                            step="0.01"
                                                            class="form-control">
                                                    </div>
                                                    <div v-if="form.payment_type === 'credit_card'">
                                                        <label class="form-label">Kredi Kartı Tutar</label>
                                                        <input 
                                                            v-model.number="form.credit_card" 
                                                            type="number" 
                                                            step="0.01"
                                                            class="form-control">
                                                    </div>
                                                    <div v-if="form.payment_type === 'installment'">
                                                        <label class="form-label">Taksit Tutar</label>
                                                        <input 
                                                            v-model.number="form.installment" 
                                                            type="number" 
                                                            step="0.01"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Açıklama</label>
                                                    <textarea 
                                                        v-model="form.description" 
                                                        class="form-control" 
                                                        rows="3"
                                                        placeholder="Fatura açıklaması">
                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="bx bx-calculator me-2"></i>Özet
                                            </h5>
                                        </div>
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
                        </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <button :disabled="loading" type="submit" class="btn btn-primary btn-lg w-100">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                <i v-else class="bx bx-save me-2"></i>
                                <span v-text="loading ? 'Kaydediliyor...' : 'Faturayı Güncelle'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('components.customermodal')
@endsection

@section('custom-js')
<script>
const { createApp } = Vue;

const app = createApp({
    data() {
        const invoice = @json($invoice ?? null);
        return {
            invoice: invoice,
            stockCardMovements: @json($stock_card_movements ?? []),
            customers: Array.isArray(@json($customers ?? [])) ? @json($customers ?? []) : [],
            stocks: @json($stocks ?? []),
            sellers: @json($sellers ?? []),
            colors: @json($colors ?? []),
            warehouses: @json($warehouses ?? []),
            loading: false,
            form: {
                id: invoice ? invoice.id : null,
                customer_id: invoice ? invoice.customer_id : 1,
                number: invoice ? invoice.invoice_number : '',
                create_date: invoice ? invoice.create_date : '',
                payment_type: 'cash',
                cash: 0,
                credit_card: 0,
                installment: 0,
                description: invoice ? invoice.description : '',
                is_status: invoice ? invoice.is_status : 1,
                items: []
            }
        }
    },
    computed: {
        filteredCustomers() {
            if (!this.customers || !Array.isArray(this.customers)) {
                return [];
            }
            return this.customers.filter(customer => 
                customer && customer.type === 'account'
            );
        },
        totals() {
            try {
                // Use form.items instead of stockCardMovements
                if (!this.form.items || !Array.isArray(this.form.items)) {
                    return {
                        cost: 0,
                        baseCost: 0,
                        sale: 0,
                        profit: 0
                    };
                }
                
                const totals = this.form.items.reduce((acc, item) => {
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
                
                totals.profit = totals.sale - totals.cost;
                
                return totals;
            } catch (error) {
                console.error('Error calculating totals:', error);
                return {
                    cost: 0,
                    baseCost: 0,
                    sale: 0,
                    profit: 0
                };
            }
        }
    },
    methods: {
        initializeItems() {
            // Eğer stockCardMovements varsa, onları items olarak kullan
            if (this.stockCardMovements && this.stockCardMovements.length > 0) {
                return this.stockCardMovements.map(movement => ({
                    id: movement.id,
                    stock_card_id: movement.stock_card_id || '',
                    stock_search: movement.stock_name || '',
                    show_stock_dropdown: false,
                    filtered_stocks: [],
                    color_id: movement.color_id || '',
                    color_search: movement.color_name || '',
                    show_color_dropdown: false,
                    filtered_colors: [],
                    serial: movement.serial || '',
                    quantity: movement.quant || 1,
                    prefix: movement.prefix || '',
                    cost_price: movement.cost_price || 0,
                    base_cost_price: movement.base_cost_price || 0,
                    sale_price: movement.sale_price || 0,
                    seller_id: movement.seller_id || 1,
                    warehouse_id: movement.warehouse_id || '',
                    barcode: movement.barcode || '',
                    reason_id: movement.reason_id || 9,
                    tracking_quantity: movement.tracking_quantity || 0,
                    discount: movement.discount || 0,
                    tax: movement.tax || 20,
                    description: movement.description || ''
                }));
            }
            
            // Yoksa boş bir item oluştur
            return [{
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
            }];
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
        
        calculateItemTotal(index) {
            // Real-time calculation if needed
        },
        
        // Stock Autocomplete Methods
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
        
        formatCurrency(amount) {
            if (!amount) return '0,00 ₺';
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY'
            }).format(amount);
        },
        
        onCustomerChange() {
            // Customer değiştiğinde yapılacak işlemler
            console.log('Customer changed:', this.form.customer_id);
        },
        
        openCustomerModal() {
            // Müşteri modal'ını aç
            console.log('Open customer modal');
        },
        
        printInvoice() {
            window.print();
        },
        
        async submitForm() {
            this.loading = true;
            
            try {
                const response = await fetch('/invoice/update-movements', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        invoice_id: this.invoice ? this.invoice.id : null,
                        invoice_data: {
                            customer_id: this.form.customer_id,
                            number: this.form.number,
                            create_date: this.form.create_date,
                            description: this.form.description,
                            payment_type: this.form.payment_type,
                            cash: this.form.cash,
                            credit_card: this.form.credit_card,
                            installment: this.form.installment
                        },
                        items: this.form.items.map(item => ({
                            stock_card_id: item.stock_card_id,
                            color_id: item.color_id,
                            sale_price: item.sale_price,
                            barcode: item.barcode
                        }))
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Fatura başarıyla güncellendi',
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });
                    } else {
                        alert('Fatura başarıyla güncellendi');
                    }
                } else {
                    throw new Error(data.message || 'Güncelleme başarısız');
                }
            } catch (error) {
                console.error('Update error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: 'Güncelleme sırasında bir hata oluştu',
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                } else {
                    alert('Güncelleme sırasında bir hata oluştu');
                }
            } finally {
                this.loading = false;
            }
        }
    },
    mounted() {
        try {
            console.log('Invoice form loaded:', this.invoice);
            console.log('Stock movements:', this.stockCardMovements);
            console.log('Customers:', this.customers);
            console.log('Filtered customers:', this.filteredCustomers);
            
            // Initialize items
            this.form.items = this.initializeItems();
            
            // Set default date if not provided
            if (!this.form.create_date) {
                this.form.create_date = new Date().toISOString().split('T')[0];
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
                    
                    // Set as selected customer
                    this.form.customer_id = customer.id;
                    
                    console.log('New customer added and selected:', customer);
                }
            });
        } catch (error) {
            console.error('Error in mounted:', error);
        }
    }
});

// Set error handler for the app
app.config.errorHandler = function (err, vm, info) {
    console.error('Vue Error:', err);
    console.error('Component:', vm);
    console.error('Info:', info);
};

// Mount the app
app.mount('#invoice-form-app');
</script>

<style>
@media print {
    .btn, .card-header .d-flex {
        display: none !important;
    }
}
</style>
@endsection