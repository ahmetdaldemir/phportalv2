@extends('layouts.admin')

@section('content')
    <div id="app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Sayfa yüklenirken loading overlay -->
        <div v-if="loading.initialData" class="page-loading-overlay">
            <div class="loading-content">
                <div class="loading-spinner-large"></div>
                <div class="loading-text">Sayfa yükleniyor...</div>
                <div class="loading-progress">
                    <div class="progress-step" :class="{ 'active': loading.stocks, 'completed': !loading.stocks && stocks.length > 0 }">
                        <i class="bx bx-package"></i>
                        <span>Stoklar yükleniyor...</span>
                    </div>
                    <div class="progress-step" :class="{ 'active': loading.colors, 'completed': !loading.colors && colors.length > 0 }">
                        <i class="bx bx-palette"></i>
                        <span>Renkler yükleniyor...</span>
                    </div>
                    <div class="progress-step" :class="{ 'active': loading.reasons, 'completed': !loading.reasons && reasons.length > 0 }">
                        <i class="bx bx-list-ul"></i>
                        <span>İade sebepleri yükleniyor...</span>
                    </div>
                    <div class="progress-step" :class="{ 'active': loading.charts, 'completed': !loading.charts && !loading.salesChart && !loading.transferChart && !loading.refundChart }">
                        <i class="bx bx-bar-chart"></i>
                        <span>Grafik verileri yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Professional Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bx bx-home display-4"></i>
                </div>
                <div>
                    <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600;">
                        <i class="bx bx-home me-2"></i>
                        ANA SAYFA
                    </h2>
                    <p class="mb-0" style="font-size: 0.9rem;">Sistem genel bakış ve hızlı işlemler</p>
                </div>
            </div>
        </div>
        <div class="row">
            @role(['Satış Sorumlusu','super-admin','Bayi Yetkilisi'])
            <div class="col-lg-6 mb-4 order-0">
                <div class="card professional-card sales-card" :class="{ 'form-loading': loading.stockSearch }">
                    <div v-if="loading.stockSearch" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Stok aranıyor...</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="bx bx-cart-add display-4 text-primary" :class="{ 'pulse-animation': loading.stockSearch }"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-primary mb-0" style="font-size: 1rem; font-weight: 600;">SATIŞ İŞLEMİ</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">Hızlı satış yapın</small>
                                    </div>
                                </div>
                                <form @submit.prevent="searchStock" id="stockSearch" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="serialNumberSale">Seri
                                                    Numarası</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" 
                                                           v-model="stockSearchForm.serialNumber" 
                                                           id="serialNumberSale"
                                                           class="form-control"
                                                           @keypress.enter="searchStock"
                                                           placeholder="Seri numarası girin">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="width: 0; margin: 7px; height: 0; font-size: 0;" for="serialbuttun" class="label">.</label>
                                            <button style="width: 100%" 
                                                    type="button"
                                                    @click="searchStock"
                                                    :disabled="loading.stockSearch"
                                                    :class="['btn', 'btn-md', 'btn-outline-primary', { 'btn-loading': loading.stockSearch }]">
                                                <span v-if="loading.stockSearch" class="spinner-border spinner-border-sm me-2"></span>
                                                @{{ loading.stockSearch ? 'Aranıyor...' : 'Ara' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div id="nameText"></div>
                        </div>
                        <!-- div class="card-footer">
                            <table class="table table-responsive">
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Marka</th>
                                    <th>Model</th>
                                    <th>Adet</th>
                                    <th>İşlemler</th>
                                </tr>
                                <tr ng-repeat="item in stockSearchLists">
                                    <td>@{{item.name}}</td>
                                    <td>@{{item.category}}</td>
                                    <td>@{{item.brand}}</td>
                                    <td>@{{item.version}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary text-nowrap"
                                                ng-click="getStockSeller(item.id)">
                                            @{{item.quantity}}
                                        </button>
                                    </td>
                                    <td><a ng-if="item.quantity > 0" data-id="@{{item.id}}"
                                           href="{{route('invoice.sales')}}?id=@{{item.id}}">Satış</a>
                                    </td>
                                </tr>
                            </table>
                        </div -->
                    </div>
                </div>
            </div>
            @endrole
            <div class="col-lg-6 mb-4 order-0">
                <div class="card professional-card transfer-card" :class="{ 'form-loading': loading.transferSearch }">
                    <div v-if="loading.transferSearch" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Sevk kontrol ediliyor...</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="bx bx-transfer display-4 text-success" :class="{ 'pulse-animation': loading.transferSearch }"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title text-success mb-0" style="font-size: 1rem; font-weight: 600;">SEVK İŞLEMİ</h6>
                                        <small class="text-muted">Hızlı sevk yapın</small>
                                    </div>
                                </div>
                                <form @submit.prevent="searchTransfer" id="transferForm" method="post">
                                    @csrf
                                    <input type="hidden" id="sellerID" class="form-control" name="sellerID"
                                           value="{{auth()->user()->seller_id}}">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="serialBackdrop">Seri
                                                    Numarası</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" 
                                                           v-model="transferForm.serialNumber" 
                                                           id="serialBackdrop" 
                                                           class="form-control"
                                                           @keypress.enter="searchTransfer"
                                                           @paste="handlePaste"
                                                           placeholder="Seri Numarası">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="width: 0; margin: 7px; height: 0; font-size: 0;" for="serialbuttun" class="label">.</label>
                                            <button style="width: 100%" 
                                                    type="button" 
                                                    @click="searchTransfer"
                                                    :disabled="loading.transferSearch"
                                                    :class="['btn', 'btn-md', 'btn-secondary', { 'btn-loading': loading.transferSearch }]">
                                                <span v-if="loading.transferSearch" class="spinner-border spinner-border-sm me-2"></span>
                                                @{{ loading.transferSearch ? 'Aranıyor...' : 'Ara' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card professional-card chart-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-bar-chart me-2"></i>
                            Satış Grafikleri
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="newChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card professional-card chart-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-trending-up me-2"></i>
                            Aylık Satış Grafikleri
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="newMonthChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card professional-card chart-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-pie-chart me-2"></i>
                            Toplam Aylık Analiz
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="totalAylik"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>

        <div class="row">
            <div class="col-md-6 col-lg-6 order-2 mb-4">
                <div class="card h-100 professional-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="bx bx-trending-down me-2 text-warning"></i>
                            Azalan Ürünler
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th><i class="bx bx-package me-1"></i>Ürün Adı</th>
                                    <th><i class="bx bx-hash me-1"></i>Kalan Stok</th>
                                    <th><i class="bx bx-dollar me-1"></i>Son Maliyet</th>
                                    <th><i class="bx bx-tag me-1"></i>Son Satış Fiyatı</th>
                                    <th><i class="bx bx-cog me-1"></i>İşlem</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('Depo Sorumlusu'))
                                    @foreach($stockTracks as $stockTrack)
                                        @if($stockTrack['quantity'] < $stockTrack['tracking_quantity'])
                                            <tr>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>{{$stockTrack['quantity']}}</td>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>
                                                    <button
                                                        onclick="demandModal({{$stockTrack['id']}},'{{$stockTrack['name']}}')"
                                                        type="button" class="btn btn-danger">
                                                        <i class="bx bx-radar"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 order-3 order-md-2 mb-4">
                <div class="card professional-card refund-card" :class="{ 'form-loading': loading.refund }">
                    <div v-if="loading.refund" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">İade işlemi yapılıyor...</div>
                        </div>
                    </div>
                    <form @submit.prevent="submitRefund" class="modal-content" id="refundForm">
                        @csrf
                        <div class="card-header">
                            <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                                <i class="bx bx-undo me-2 text-danger"></i>
                                İade İşlemi
                            </h6>
                            <small class="text-muted">*(Seri numarası girilirse ise stock seçimine gerek yok)</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="stockBackdrop" class="form-label">Stok</label>
                                    <select class="form-control select2" 
                                            v-model="refundForm.stock_id" 
                                            name="stock_id" 
                                            id="stockBackdrop">
                                        <option value="">Stok seçiniz...</option>
                                        <option v-for="stock in stocks" :key="stock.id" :value="stock.id">
                                            @{{ stock.name }} / @{{ stock.brand?.name || 'Bulunamadı' }} / @{{ stock.version || 'Bulunamadı' }} / @{{ stock.category?.name || 'Kategori Yok' }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="reasonBackdrop" class="form-label">Neden</label>
                                    <select class="form-control" 
                                            v-model="refundForm.reason_id" 
                                            name="reason_id" 
                                            id="reasonBackdrop">
                                        <option value="">Neden seçiniz...</option>
                                        <option v-for="reason in reasons" :key="reason.id" :value="reason.id">
                                            @{{ reason.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="colorBackdrop" class="form-label">Renk</label>
                                    <select class="form-control" 
                                            v-model="refundForm.color_id" 
                                            name="color_id" 
                                            id="colorBackdrop">
                                        <option value="">Renk seçiniz...</option>
                                        <option v-for="color in colors" :key="color.id" :value="color.id">
                                            @{{ color.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="serialNumberRefund" class="form-label">Seri No</label>
                                    <input v-model="refundForm.serial_number" 
                                           name="serial_number" 
                                           type="text" 
                                           class="form-control"
                                           id="serialNumberRefund">
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="descriptionRefund" class="form-label">Açıklama</label>
                                    <input type="text" 
                                           v-model="refundForm.description" 
                                           name="description" 
                                           class="form-control" 
                                           id="descriptionRefund">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="padding: 10px;">
                            <button type="submit" 
                                    :disabled="loading.refund"
                                    :class="['btn', 'btn-primary', { 'btn-loading': loading.refund }]">
                                <span v-if="loading.refund" class="spinner-border spinner-border-sm me-2"></span>
                                @{{ loading.refund ? 'İşleniyor...' : 'İADE İŞLEMİ BAŞLAT' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12 order-3 order-md-2 mb-4">
                <div class="card professional-card delete-card" :class="{ 'form-loading': loading.delete }">
                    <div v-if="loading.delete" class="loading-overlay">
                        <div class="text-center">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">Seri numarası siliniyor...</div>
                        </div>
                    </div>
                    <form @submit.prevent="submitDeleteSerial" id="deleted_at_serial_number_storeForm" method="post">
                        @csrf
                        <div class="card-header">
                            <h6 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">
                                <i class="bx bx-trash me-2 text-danger"></i>
                                Silinecek Seri Numaraları
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="serial_number_delete" class="form-label">Seri Numarası</label>
                                    <input type="text" 
                                           v-model="deleteForm.serial_number" 
                                           name="serial_number" 
                                           class="form-control" 
                                           id="serial_number_delete"
                                           placeholder="Silinecek seri numarası">
                                </div>
                                <div class="col mb-0">
                                    <label for="deleteButton" class="form-label"></label>
                                    <button type="submit" 
                                            :disabled="loading.delete"
                                            :class="['btn', 'btn-primary', { 'btn-loading': loading.delete }]" 
                                            style="display: flex;">
                                        <span v-if="loading.delete" class="spinner-border spinner-border-sm me-2"></span>
                                        @{{ loading.delete ? 'Siliniyor...' : 'Kaydet' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    <div class="modal fade" id="getCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <table class="table table-bordered">
                            <tr>
                                <td>Bayi</td>
                                <td>Adet</td>
                            </tr>
                            <tr ng-repeat="item in data">
                                <td>@{{item.sellerName}}</td>
                                <td>@{{item.quantity}}</td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
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
                                <select class="form-select" name="color_id">
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
@endsection

@section('custom-css')
    <style>
        /* Professional Home Page Stilleri */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            pointer-events: none;
        }
        
        .page-header h1 {
            color: white !important;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 3px 6px rgba(0,0,0,0.5);
            letter-spacing: 1px;
        }
        
        .page-header p {
            color: white !important;
            font-size: 1.2rem;
            margin: 0.5rem 0 0 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.4);
            font-weight: 500;
        }
        
        /* Professional Card Styling */
        .professional-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .professional-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .professional-card .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            border-radius: 20px 20px 0 0;
        }
        
        .professional-card .card-header .card-title {
            color: #495057 !important;
            font-weight: 700;
            font-size: 1.1rem;
            text-shadow: none;
            margin-bottom: 0;
        }
        
        .professional-card .card-header .text-muted {
            color: #6c757d !important;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .professional-card .card-body {
            padding: 2rem;
        }
        
        /* Sales Card Specific */
        .sales-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-left: 5px solid #667eea;
        }
        
        .sales-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .sales-card .card-header .card-title {
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .sales-card .card-header .text-muted {
            color: rgba(255,255,255,0.9) !important;
        }
        
        /* Transfer Card Specific */
        .transfer-card {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, rgba(32, 201, 151, 0.05) 100%);
            border-left: 5px solid #28a745;
        }
        
        .transfer-card .card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .transfer-card .card-header .card-title {
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .transfer-card .card-header .text-muted {
            color: rgba(255,255,255,0.9) !important;
        }
        
        /* Chart Card Specific */
        .chart-card {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(253, 126, 20, 0.05) 100%);
            border-left: 5px solid #ffc107;
        }
        
        .chart-card .card-header {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        
        .chart-card .card-header .card-title {
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .chart-card .card-header .text-muted {
            color: rgba(255,255,255,0.9) !important;
        }
        
        /* Refund Card Specific */
        .refund-card {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(253, 126, 20, 0.05) 100%);
            border-left: 5px solid #dc3545;
        }
        
        .refund-card .card-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }
        
        .refund-card .card-header .card-title {
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .refund-card .card-header .text-muted {
            color: rgba(255,255,255,0.9) !important;
        }
        
        /* Delete Card Specific */
        .delete-card {
            background: linear-gradient(135deg, rgba(108, 117, 125, 0.05) 0%, rgba(73, 80, 87, 0.05) 100%);
            border-left: 5px solid #6c757d;
        }
        
        .delete-card .card-header {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }
        
        .delete-card .card-header .card-title {
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .delete-card .card-header .text-muted {
            color: rgba(255,255,255,0.9) !important;
        }
        
        /* Professional Table Styling */
        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: none;
            background: white;
        }
        
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border: none;
            font-weight: 700;
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }
        
        .table thead th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .table tbody td {
            padding: 0.5rem;
            border: none;
            vertical-align: middle;
            font-size: 0.75rem;
            color: #495057;
        }
        
        .table tbody tr:nth-child(even) {
            background: rgba(248, 249, 250, 0.5);
        }
        
        /* Professional Buttons */
        .btn {
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.6);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.6);
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
        }
        
        /* Modal Styling */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
        }
        
        .modal-title {
            font-weight: 700;
            color: white;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        /* Card Footer */
        .card-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
            border-radius: 0 0 20px 20px;
        }
        
        /* Icon Styling */
        .display-4 {
            font-size: 3rem;
            opacity: 0.8;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .professional-card .card-body {
                padding: 1.5rem;
            }
        }
        
        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .professional-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .professional-card:nth-child(1) { animation-delay: 0.1s; }
        .professional-card:nth-child(2) { animation-delay: 0.2s; }
        .professional-card:nth-child(3) { animation-delay: 0.3s; }
        .professional-card:nth-child(4) { animation-delay: 0.4s; }
        
        /* Enhanced Loading States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 20px;
        }
        
        .loading-spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .loading-text {
            margin-top: 1rem;
            font-weight: 600;
            color: #667eea;
            font-size: 1.1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .form-loading {
            position: relative;
        }
        
        .form-loading::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            z-index: 1;
        }
        
        .form-loading input,
        .form-loading select,
        .form-loading button {
            position: relative;
            z-index: 2;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        /* Sayfa yüklenirken loading overlay */
        .page-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }
        
        .loading-content {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }
        
        .loading-spinner-large {
            width: 4rem;
            height: 4rem;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }
        
        .loading-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 2rem;
        }
        
        .loading-progress {
            margin-top: 1rem;
        }
        
        .progress-step {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin: 0.5rem 0;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
            opacity: 0.6;
        }
        
        .progress-step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            opacity: 1;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .progress-step.completed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            opacity: 1;
        }
        
        .progress-step i {
            font-size: 1.2rem;
            margin-right: 0.75rem;
            width: 20px;
        }
        
        .progress-step span {
            font-weight: 500;
        }
        
        .progress-step.active i {
            animation: pulse 1.5s infinite;
        }
    </style>
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
                    stockSearchForm: {
                        serialNumber: ''
                    },
                    transferForm: {
                        serialNumber: ''
                    },
                    refundForm: {
                        stock_id: '',
                        reason_id: '',
                        color_id: '',
                        serial_number: '',
                        description: ''
                    },
                    deleteForm: {
                        serial_number: ''
                    },
                    
                    // Data arrays
                    stocks: [],
                    colors: [],
                    reasons: [],
                    stockSearchResults: [],
                    
                    // Loading states
                    loading: {
                        stockSearch: false,
                        transferSearch: false,
                        refund: false,
                        delete: false,
                        initialData: true,
                        stocks: false,
                        colors: false,
                        reasons: false,
                        charts: false,
                        salesChart: false,
                        transferChart: false,
                        refundChart: false
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
                    this.loading.initialData = true;
                    
                    try {
                        // Load stocks
                        console.log('Loading stocks...');
                        this.loading.stocks = true;
                        const stocksResponse = await axios.get('/api/stocks');
                        this.stocks = stocksResponse.data;
                        this.loading.stocks = false;
                        console.log('Stocks loaded:', this.stocks.length);
                        
                        // Load colors
                        console.log('Loading colors...');
                        this.loading.colors = true;
                        const colorsResponse = await axios.get('/api/colors');
                        this.colors = colorsResponse.data;
                        this.loading.colors = false;
                        console.log('Colors loaded:', this.colors.length);
                        
                        // Load reasons
                        console.log('Loading reasons...');
                        this.loading.reasons = true;
                        const reasonsResponse = await axios.get('/api/reasons');
                        this.reasons = reasonsResponse.data.filter(reason => reason.type == 2);
                        this.loading.reasons = false;
                        console.log('Reasons loaded:', this.reasons.length);
                        
                        // Load chart data
                        console.log('Loading chart data...');
                        this.loading.charts = true;
                        await this.loadChartData();
                        this.loading.charts = false;
                        console.log('Chart data loaded');
                        
                        this.loading.initialData = false;
                        console.log('All initial data loaded successfully');
                    } catch (error) {
                        console.error('Veri yüklenirken hata:', error);
                        this.loading.initialData = false;
                        this.loading.stocks = false;
                        this.loading.colors = false;
                        this.loading.reasons = false;
                        this.loading.charts = false;
                        this.showNotification('Hata', 'Veriler yüklenirken bir hata oluştu', 'error');
                    }
                },
                
                async loadChartData() {
                    try {
                        // Sales chart data
                        this.loading.salesChart = true;
                        console.log('Loading sales chart data...');
                        // Burada sales chart verilerini yükleyebilirsiniz
                        await new Promise(resolve => setTimeout(resolve, 1000)); // Simulated loading
                        this.loading.salesChart = false;
                        console.log('Sales chart data loaded');
                        
                        // Transfer chart data
                        this.loading.transferChart = true;
                        console.log('Loading transfer chart data...');
                        // Burada transfer chart verilerini yükleyebilirsiniz
                        await new Promise(resolve => setTimeout(resolve, 800)); // Simulated loading
                        this.loading.transferChart = false;
                        console.log('Transfer chart data loaded');
                        
                        // Refund chart data
                        this.loading.refundChart = true;
                        console.log('Loading refund chart data...');
                        // Burada refund chart verilerini yükleyebilirsiniz
                        await new Promise(resolve => setTimeout(resolve, 600)); // Simulated loading
                        this.loading.refundChart = false;
                        console.log('Refund chart data loaded');
                        
                    } catch (error) {
                        console.error('Chart data loading error:', error);
                        this.loading.salesChart = false;
                        this.loading.transferChart = false;
                        this.loading.refundChart = false;
                    }
                },
                
                async searchStock() {
                    if (!this.stockSearchForm.serialNumber.trim()) {
                        this.showNotification('Uyarı', 'Lütfen seri numarası girin', 'warning');
                        return;
                    }
                    
                    console.log('Starting stock search...');
                    this.loading.stockSearch = true;
                    console.log('Loading state set to true:', this.loading.stockSearch);
                    
            
                    
                      try {
                          const response = await axios.post('/stockSearch', {
                              serialNumber: this.stockSearchForm.serialNumber
                          });
                   
                          if (response.data.autoredirect === false) {
                              this.showNotification('Hata', 'Satışı Yapılamaz', 'error');
                              this.stockSearchResults = [];
                          } else if (response.data.autoredirect === true) {
                              window.location.href = `/invoice/sales?id=${response.data.id}&serial=${response.data.serial}`;
                          } else {
                              this.stockSearchResults = response.data;
                          }
                      } catch (error) {
                          console.error('Stok arama hatası:', error);
                          this.showNotification('Hata', 'Stok aranırken bir hata oluştu', 'error');
                      } finally {
                          console.log('Setting loading to false...');
                          this.loading.stockSearch = false;
                          console.log('Loading state set to false:', this.loading.stockSearch);
                      }
                },
                
                async searchTransfer() {
                    if (!this.transferForm.serialNumber.trim()) {
                        this.showNotification('Uyarı', 'Lütfen seri numarası girin', 'warning');
                        return;
                    }
                    
                    this.loading.transferSearch = true;
                    
                    // Minimum loading süresi (1.5 saniye)
                    const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1500));
                    
                    try {
                        const response = await axios.get(`/getTransferSerialCheck?serial_number=${this.transferForm.serialNumber}&seller_id={{auth()->user()->seller_id}}`);
                        
                        // Hem API response hem de minimum loading süresini bekle
                        await Promise.all([minLoadingTime]);
                        
                        if (response.data !== 'Yes') {
                            this.showNotification('Hata', 'Seri numarası transfer edilemez. Bulunamamakta veya başka bayiye ait.', 'error');
                        } else {
                            window.location.href = `transfer/create?serial_number=${this.transferForm.serialNumber}&type=other`;
                        }
                    } catch (error) {
                        console.error('Transfer arama hatası:', error);
                        this.showNotification('Hata', 'Transfer aranırken bir hata oluştu', 'error');
                    } finally {
                        this.loading.transferSearch = false;
                    }
                },
                
                handlePaste(event) {
                    setTimeout(() => {
                        if (this.transferForm.serialNumber.trim()) {
                            this.searchTransfer();
                        }
                    }, 1000);
                },
                
                async submitRefund() {
                    if (!this.refundForm.stock_id && !this.refundForm.serial_number) {
                        this.showNotification('Uyarı', 'Lütfen stok seçin veya seri numarası girin', 'warning');
                        return;
                    }
                    
                    this.loading.refund = true;
                    
                    // Minimum loading süresi (2.5 saniye)
                    const minLoadingTime = new Promise(resolve => setTimeout(resolve, 2500));
                    
                    try {
                        const response = await axios.post('{{route("stockcard.refund")}}', this.refundForm);
                        
                        // Hem API response hem de minimum loading süresini bekle
                        await Promise.all([minLoadingTime]);
                        
                        this.showNotification('Başarılı', response.data, 'success');
                        this.resetRefundForm();
                    } catch (error) {
                        console.error('İade işlemi hatası:', error);
                        this.showNotification('Hata', 'İade işlemi sırasında bir hata oluştu', 'error');
                    } finally {
                        this.loading.refund = false;
                    }
                },
                
                async submitDeleteSerial() {
                    if (!this.deleteForm.serial_number.trim()) {
                        this.showNotification('Uyarı', 'Lütfen seri numarası girin', 'warning');
                        return;
                    }
                    
                    this.loading.delete = true;
                    
                    // Minimum loading süresi (1 saniye)
                    const minLoadingTime = new Promise(resolve => setTimeout(resolve, 1000));
                    
                    try {
                        const response = await axios.post('{{route("deleted_at_serial_number_store")}}', this.deleteForm);
                        
                        // Hem API response hem de minimum loading süresini bekle
                        await Promise.all([minLoadingTime]);
                        
                        this.showNotification('Başarılı', 'Seri numarası silindi', 'success');
                        this.deleteForm.serial_number = '';
                    } catch (error) {
                        console.error('Seri numarası silme hatası:', error);
                        this.showNotification('Hata', 'Seri numarası silinirken bir hata oluştu', 'error');
                    } finally {
                        this.loading.delete = false;
                    }
                },
                
                resetRefundForm() {
                    this.refundForm = {
                        stock_id: '',
                        reason_id: '',
                        color_id: '',
                        serial_number: '',
                        description: ''
                    };
                },
                
                showNotification(title, message, type = 'info') {
                    // SweetAlert2 kullanarak bildirim göster
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: title,
                            text: message,
                            icon: type,
                            confirmButtonText: 'Tamam'
                        });
                    } else {
                        alert(`${title}: ${message}`);
                    }
                },
                
                getStockSeller(id) {
                    // Bu fonksiyon modal açmak için kullanılabilir
                    console.log('Stock seller ID:', id);
                }
            }
        }).mount('#app');
        
        // Debug için loading state'lerini kontrol et
        console.log('Vue app mounted successfully');
    </script>
    <script>
        function demandModal(id, name) {
            $("#demandModal").modal('show');
            $("#demandModal").find('.modal-header span').html(name);
            $("#demandModal").find('input#id').val(id);
        }
    </script>


@endsection
