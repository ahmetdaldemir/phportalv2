@extends('layouts.admin')

@section('content')
    <div id="invoice-sales-app" class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-add">
            <div class="col-12">
                <div class="card invoice-preview-card">
                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bx bx-receipt me-2"></i>
                            Satış Faturası
                        </h4>
                        <div class="d-flex gap-2">
                            <button id="saveButton" @click="saveInvoice()" type="button" class="btn btn-primary">
                                <i class="bx bx-paper-plane me-1"></i>Kaydet
                            </button>
                        </div>
                    </div>

                    <form id="invoiceForm" method="post" class="form-repeater source-item">
                        <input type="hidden" name="id" @if(isset($invoices)) value="{{$invoices->id}}" @endif />
                        
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
                                                    v-model="customerSearch" 
                                                    @input="filterCustomers"
                                                    @focus="showDropdown"
                                                    @blur="hideDropdown"
                                                    type="text" 
                                                    class="form-control" 
                                                    placeholder="Müşteri ara..."
                                                    autocomplete="off">
                                                
                                                <!-- Hidden input for form submission -->
                                                <input type="hidden" name="customer_id" :value="selectedCustomerId">
                                                
                                                <!-- Dropdown Menu -->
                                                <div v-show="showCustomerDropdown && customerSearch && customerSearch.length >= 1" 
                                                     class="dropdown-menu show position-absolute w-100" 
                                                     style="z-index: 99999 !important; max-height: 300px; overflow-y: auto; display: block !important;">
                                                    
                                                    <!-- Genel Cari Option -->
                                                    <div @mousedown="selectCustomer({id: '1', fullname: 'Genel Cari'})" 
                                                         class="dropdown-item" 
                                                         style="cursor: pointer;">
                                                        <strong>Genel Cari</strong>
                                                    </div>
                                                    
                                                    <!-- Customer Results -->
                                                    <div v-for="customer in filteredCustomers" 
                                                         :key="customer.id" 
                                                         @mousedown="selectCustomer(customer)"
                                                         class="dropdown-item" 
                                                         style="cursor: pointer;">
                                                        <div>
                                                            <strong>@{{ customer.fullname }}</strong>
                                                            <small class="text-muted d-block" v-if="customer.phone1">
                                                                <i class="bx bx-phone"></i> @{{ customer.phone1 }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- No Results -->
                                                    <div v-if="filteredCustomers.length === 0 && customerSearch.length >= 1" 
                                                         class="dropdown-item text-muted text-center">
                                                        <i class="bx bx-search"></i> Müşteri bulunamadı
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <button class="btn btn-primary" tabindex="0"
                                                    data-bs-toggle="modal" data-bs-target="#editUser" type="button">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bx bx-info-circle"></i> 
                                            Seçili: <strong>@{{ selectedCustomerName }}</strong>
                                        </small>
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
                                                <input type="text" class="form-control"
                                                       @if(isset($invoices)) value="{{$invoices->number}}"
                                                       @endif name="number" id="invoiceId" placeholder="Otomatik oluşturulacak">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-semibold">Fatura Tarihi</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bx bx-calendar"></i>
                                                </span>
                                                <input type="text" class="form-control single-datepicker"
                                                       name="create_date"
                                                       @if(isset($invoices)) value="{{$invoices->create_date}}"
                                                       @else value="{{date('d-m-Y')}}" @endif />
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-semibold">Fatura Tipi</label>
                                            <select class="form-select" data-style="btn-default" name="type" id="type">
                                                <option value="2">Giden Fatura</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mx-n4">

                            <!-- Items Section -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">
                                    <i class="bx bx-list-ul me-2"></i>Fatura Kalemleri
                                </h5>
                                <button type="button" @click="addInvoiceItem()" class="btn btn-success">
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
                                            @if (auth()->user()->hasRole('admin'))
                                            <th class="compact-header">
                                                <i class="bx bx-money me-1"></i>
                                                <span class="header-text">Destekli<br><small>Maliyet</small></span>
                                            </th>
                                            @endif
                                            <th class="compact-header">
                                                <i class="bx bx-credit-card me-1"></i>
                                                <span class="header-text">Satış<br><small>Fiyatı</small></span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-purchase-tag me-1"></i>
                                                <span class="header-text">İndirim<br><small>(%)</small></span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-note me-1"></i>
                                                <span class="header-text">Açıklama</span>
                                            </th>
                                            <th class="compact-header">
                                                <i class="bx bx-trash me-1"></i>
                                                <span class="header-text">Sil</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="overflow: visible !important;" id="myList1">
                                        <tr v-if="!isLoaded" class="text-center">
                                            <td colspan="7" class="py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Yükleniyor...</span>
                                                </div>
                                                <p class="mt-2">Vue.js yükleniyor...</p>
                                            </td>
                                        </tr>
                                        <tr v-for="item in (invoiceItems || [])" :key="item.id" :id="item.id" class="invoice-item-row" style="overflow: visible !important; position: relative !important;">
                                            <!-- Stok -->
                                            <td style="overflow: visible !important; position: relative !important;">
                                                <div class="position-relative flex-grow-1 stock-search-container">
                                                    <input 
                                                        type="text" 
                                                        class="form-control form-control-sm stock-search-input" 
                                                        v-model="stockSearchQueries[item.id]"
                                                        @input="filterStocks(item.id, $event.target.value)"
                                                        @focus="stockDropdowns[item.id] = stockDropdowns[item.id] || []"
                                                        placeholder="Stok ara..."
                                                        autocomplete="off">
                                                    
                                                    <!-- Hidden input for form submission -->
                                                    <input type="hidden" :name="`stock_card_id[${(invoiceItems || []).indexOf(item)}]`" :value="item.stockCardId">
                                                    
                                                    <!-- Stock Dropdown -->
                                                    <div v-show="stockDropdowns[item.id] && stockDropdowns[item.id].length > 0" 
                                                         class="stock-dropdown position-absolute w-100 bg-white border rounded shadow-lg" 
                                                         style="z-index: 1000; max-height: 300px; overflow-y: auto; top: 100%; left: 0; display: block !important;">
                                                        <div v-for="stock in stockDropdowns[item.id]" 
                                                             :key="stock.id"
                                                             @click="selectStock(item.id, stock)"
                                                             class="stock-item p-2 border-bottom" 
                                                             style="cursor: pointer;">
                                                            <div class="fw-semibold">@{{ stock.name }}</div>
                                                            <small class="text-muted">@{{ stock.brand?.name || 'Bilinmiyor' }} - @{{ stock.version_names || '' }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Seri No -->
                                            <td>
                                                <input type="text" 
                                                       class="form-control form-control-sm serialnumber"
                                                       v-model="item.serialNumber"
                                                       @blur="validateSerialNumber(item.id, item.serialNumber)"
                                                       required 
                                                       placeholder="Seri numarası" />
                                                <small class="text-danger d-none">
                                                    <i class="bx bx-error-circle"></i> Zorunlu
                                                </small>
                                            </td>

                                            @if (auth()->user()->hasRole('admin'))
                                            <!-- Destekli Maliyet -->
                                            <td>
                                                <input type="text"
                                                       class="form-control form-control-sm invoice-item-price invoice-item-cost-price"
                                                       v-model="item.costPrice"
                                                       readonly/>
                                            </td>
                                            @else
                                            <td style="display: none;">
                                                <input type="hidden"
                                                       class="form-control invoice-item-price invoice-item-cost-price"
                                                       v-model="item.costPrice"
                                                       readonly/>
                                            </td>
                                            @endif

                                            <!-- Satış Fiyatı -->
                                            <td>
                                                <input type="text"
                                                       class="form-control form-control-sm invoice-item-price invoice-item-sales-price"
                                                       v-model="item.salePrice"
                                                       readonly/>
                                                <input :name="`reason_id[${(invoiceItems || []).indexOf(item)}]`" :value="item.reasonId" type="hidden" />
                                            </td>

                                            <!-- İndirim -->
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm"
                                                       v-model="item.discount"
                                                       @change="applyDiscount(item.id, item.discount)"
                                                       min="0"
                                                       @role('admin')
                                                       max="{{setting('admin.discount_admin')}}"
                                                       @else
                                                       max="{{setting('admin.discount')}}"
                                                       @endrole
                                                       placeholder="0">
                                            </td>

                                            <!-- Açıklama -->
                                            <td>
                                                <input type="text" 
                                                       class="form-control form-control-sm"
                                                       v-model="item.description"
                                                       placeholder="Açıklama...">
                                            </td>

                                            <!-- Sil -->
                                            <td class="text-center">
                                                <button type="button" 
                                                        class="btn btn-sm btn-icon btn-outline-danger" 
                                                        @click="removeInvoiceItem(item.id)"
                                                        title="Satırı Sil">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr class="my-4">

                            <!-- Summary Section -->
                            <div class="row">
                                <div class="col-lg-8 col-12">
                                    <div class="row g-3">
                                        <!-- Staff Selection -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-user-circle me-1"></i>Personel
                                            </label>
                                            <select required id="staff_id_select" class="w-100 StaffIdClass form-select" name="staff_id">
                                                <option value="">Personel Seçiniz</option>
                                            @foreach($users as $user)
                                                @if($user->id != 1)
                                                    <option @if(isset($invoices)) {{ $invoices->hasStaff($user->id) ? 'selected' : '' }}
                                                            @endif value="{{$user->id}}" data-value="{{$user->id}}">{{$user->name}}</option>
                                                @endif
                                            @endforeach
                                            </select>
                                        </div>

                                        <!-- Payment Status -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-money me-1"></i>Ödeme Durumu
                                            </label>
                                            <select name="paymentStatus" id="paymentStatus" class="form-select">
                                                <option value="unpaid">💳 Ödenecek</option>
                                                <option value="paid">✅ Ödendi</option>
                                                <option value="paidOutOfPocket">👤 Çalışan Cebinden</option>
                                            </select>
                                        </div>

                                        <!-- Safe/Bank Area -->
                                        <div class="col-12" id="safeArea"></div>

                                        <!-- Category -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-folder-open me-1"></i>Kategori
                                            </label>
                                            <select name="accounting_category_id" class="form-select">
                                                @foreach($categories as $category)
                                                    @if($category->category == "gelir")
                                                        <option @if($category->id == '1') selected @endif value="{{$category->id}}">
                                                            {{$category->name}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Payment Date -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-calendar me-1"></i>Ödeme Tarihi
                                            </label>
                                            <input type="text" class="form-control single-datepicker" 
                                                   placeholder="DD-MM-YYYY" id="payment-date" name="payment_date" readonly="readonly">
                                        </div>

                                        <!-- Payment Types -->
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-credit-card me-1"></i>Ödeme Tipleri
                                            </label>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <label class="form-label">Kredi Kartı</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="text" name="payment_type[credit_card]" value="0" id="credit_card" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <label class="form-label">Nakit</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="text" name="payment_type[cash]" id="money_order" value="0" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <label class="form-label">Taksit</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="text" name="payment_type[installment]" value="0" id="installment" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">
                                                <i class="bx bx-note me-1"></i>Not / Açıklama
                                            </label>
                                            <textarea class="form-control" name="description" rows="3" id="note" placeholder="Fatura ile ilgili notlarınızı buraya yazabilirsiniz...">@if(isset($invoices)){{ $invoices->description}}@endif</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Section -->
                                <div class="col-lg-4 col-12">
                                    <div class="card bg-light-subtle border">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted mb-2">Genel Toplam</h6>
                                            <div class="display-4 fw-bold text-success">
                                                <span v-if="!isLoaded">0.00 ₺</span>
                                                <span v-else>@{{ (totalAmount || 0).toFixed(2) }} ₺</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="loader" class="lds-dual-ring display-none overlay"></div>
    </div>

    <!-- Stock Movement Selection Modal -->
    <div class="modal fade" id="stockMovementModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-package me-2"></i>Seri Numarası Seç
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="stockMovementLoader" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p class="mt-2">Stok hareketleri yükleniyor...</p>
                    </div>
                    
                    <div id="stockMovementContent">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Seri No</th>
                                        <th>Renk</th>
                                        <th>Satış Fiyatı</th>
                                        <th>Maliyet</th>
                                        <th>Şube</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody id="stockMovementTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noDataMessage" class="text-center py-5" style="display: none;">
                            <i class="bx bx-info-circle display-1 text-muted"></i>
                            <p class="mt-3">Bu stok için müsait seri numarası bulunamadı.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@include('components.customermodal')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/sales-page.css')}}">
    <style>
        /* Vue.js styling - müşteri arama gibi */
    </style>
@endsection

@section('custom-js')
    <!-- Vue.js CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- jQuery UI kaldırıldı - Vue.js kullanıyoruz -->
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
    
    <!-- Sales Page Data -->
    <script>
        // Global data for Vue.js and other scripts
        window.salesPageData = {
            customers: @json($customers ?? []),
            stocks: @json($stocks ?? []),
            salesIndexRoute: "{{route('sale.index')}}",
            safeAreaHTML: `<div class="mb-3">
                <label class="form-label fw-semibold"><i class="bx bx-wallet me-1"></i>Kasa / Banka</label>
                <select class="form-select" name="safe_id">
                    @foreach($safes as $safe)
                        <option @if(isset($invoices)) {{ $invoices->hasSafe($safe->id) ? 'selected' : '' }} @endif value="{{$safe->id}}">{{$safe->name}}</option>
                    @endforeach
                </select>
            </div>`,
            paidOutOfPocketHTML: `<div class="mb-3">
                <label class="form-label fw-semibold"><i class="bx bx-user me-1"></i>İsim Soyisim</label>
                <input type="text" id="pay_to" class="form-control" name="pay_to" placeholder="Ödeme yapan kişinin adı" @if(isset($invoices)) value="{{$invoices->pay_to}}" @endif />
            </div>`
        };
        
        // Alias for backward compatibility
        var salesIndexRoute = window.salesPageData.salesIndexRoute;
        var safeAreaHTML = window.salesPageData.safeAreaHTML;
        var paidOutOfPocketHTML = window.salesPageData.paidOutOfPocketHTML;
    </script>

    <!-- Sales Vue.js App -->
    <script src="{{asset('assets/js/sales-vue.js')}}"></script>
@endsection
