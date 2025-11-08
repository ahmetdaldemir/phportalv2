@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
    <style>
        /* Transfer-specific overrides */
        .btn-xs {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }
        
        .d-flex.gap-1 {
            gap: 2px !important;
        }
        
        .page-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
        }
        
        .page-header p {
            color: rgba(255, 255, 255, 0.9);
        }
        
        /* Tab styles specific to transfer */
        .nav-tabs {
            border: none;
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px 15px 0 0;
            padding: 0.5rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px;
            margin: 0 0.25rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            transform: translateY(-2px);
        }
        
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .tab-content {
            border: none;
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            min-height: 400px;
        }
        
        /* Transfer Modal Styles */
        #transferModal .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0;
        }
        
        #transferModal .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        #transferModal .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        #transferModal .form-control,
        #transferModal .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
        }
        
        #transferModal .form-control:focus,
        #transferModal .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .serial-list-container-modal {
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }
        
        .serial-list-container-modal:hover {
            border-color: #667eea;
        }
        
        #transferModal .input-group {
            border-radius: 10px;
            overflow: hidden;
        }
        
        #transferModal .input-group .form-control {
            border-radius: 10px 0 0 10px;
            border-right: none;
        }
        
        #transferModal .input-group .btn {
            border-radius: 0 10px 10px 0;
        }
    </style>
@endsection

@section('content')

    <div id="app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bx bx-transfer me-2"></i>Sevk Yönetimi
                    </h4>
                    <p class="mb-0 opacity-75">Gelen ve yapılan sevkleri yönetin</p>
                </div>
                
                @role(['Depo Sorumlusu','super-admin','Bayi Yetkilisi'])
                <button type="button" @click="openTransferModal" class="btn btn-light btn-lg shadow-sm">
                    <i class="bx bx-plus me-2"></i>Yeni Sevk Ekle
                </button>
                @endrole
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                <strong>Uyarı!</strong> {{$errors->first()}}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Professional Filter Section with Base CSS -->
        <div class="professional-card mb-4">
            <div class="professional-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-filter me-2"></i>Arama Filtreleri
                    </h5>
                </div>
            </div>
            
            <div class="card-body">
                <form @submit.prevent="searchTransfers" class="compact-filter-form">
                    <div class="row g-2">
                        <div class="col-lg-2 col-md-2 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-package"></i>No
                                </label>
                                <input type="text"
                                       v-model="searchForm.number"
                                       class="form-control compact-input"
                                       placeholder="Transfer No Giriniz...">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-package"></i>Stok Adı
                                </label>
                                <input type="text" 
                                       v-model="searchForm.stockName" 
                                       class="form-control compact-input" 
                                       placeholder="Stok adı giriniz...">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-category"></i>Kategori
                                </label>
                                <select v-model="searchForm.category" 
                                        class="form-select compact-select">
                                    <option value="">Tümü</option>
                                    <option v-for="category in categories" 
                                            :key="category.id" 
                                            :value="category.id">
                                        @{{ category.path }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-3 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-palette"></i>Renk
                                </label>
                                <select v-model="searchForm.color" 
                                        class="form-select compact-select">
                                    <option value="">Tümü</option>
                                    <option v-for="color in colors" 
                                            :key="color.id" 
                                            :value="color.id">
                                        @{{ color.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-3 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-store"></i>Şube
                                </label>
                                <select v-model="searchForm.seller" 
                                        class="form-select compact-select"
                                        :disabled="!canSelectSeller">
                                    <option value="all">Tümü</option>
                                    <option v-for="seller in sellers" 
                                            :key="seller.id" 
                                            :value="seller.id">
                                        @{{ seller.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-3 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-barcode"></i>Seri No
                                </label>
                                <input type="text" 
                                       v-model="searchForm.serialNumber" 
                                       class="form-control compact-input" 
                                       placeholder="Seri no...">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-end">
                            <button type="submit" 
                                    :disabled="loading.search"
                                    class="btn btn-primary btn-sm me-2">
                                <i v-if="loading.search" class="bx bx-loader-alt bx-spin me-1"></i>
                                <i v-else class="bx bx-search me-1"></i>
                                @{{ loading.search ? 'Aranıyor...' : 'Ara' }}
                            </button>
                            <button type="button" 
                                    @click="clearFilters"
                                    class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-x me-1"></i>Temizle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Professional Tab Navigation -->
        <div class="card shadow-sm border-0">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" 
                                class="nav-link"
                                :class="{ active: activeTab === 'incoming' }"
                                @click="activeTab = 'incoming'"
                                role="tab">
                            <i class="bx bx-down-arrow-circle me-2"></i>Gelen Sevkler
                            <span v-if="incomingTransfers.length > 0" class="badge bg-light text-dark ms-2">
                                @{{ incomingTransfers.length }}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" 
                                class="nav-link"
                                :class="{ active: activeTab === 'outgoing' }"
                                @click="activeTab = 'outgoing'"
                                role="tab">
                            <i class="bx bx-up-arrow-circle me-2"></i>Yapılan Sevkler
                            <span v-if="outgoingTransfers.length > 0" class="badge bg-light text-dark ms-2">
                                @{{ outgoingTransfers.length }}
                            </span>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <!-- Gelen Sevkler Tab -->
                    <div v-show="activeTab === 'incoming'" class="tab-pane fade show active">
                        <!-- Loading State with Base CSS -->
                        <div v-if="loading.incoming" class="table-loading-overlay">
                            <div class="loading-content">
                                <div class="loading-spinner-large"></div>
                                <p class="loading-text">Gelen sevkler yükleniyor...</p>
                            </div>
                        </div>

                        <!-- Empty State with Base CSS -->
                        <div v-else-if="incomingTransfers.length === 0" class="empty-state">
                            <div class="empty-content">
                                <i class="bx bx-package" style="font-size: 4rem;"></i>
                                <h5>Gelen Sevk Bulunamadı</h5>
                                <p>Henüz size gönderilmiş bir sevk bulunmuyor</p>
                            </div>
                        </div>

                        <!-- Professional Table with Base CSS -->
                        <div v-else class="table-responsive">
                            <table class="professional-table">
                                <thead>
                                <tr>
                                    <th style="width: 10%"><i class="bx bx-hash"></i>Sevk No</th>
                                    <th style="width: 8%"><i class="bx bx-mobile"></i>Tip</th>
                                    <th style="width: 12%"><i class="bx bx-store"></i>Gönderici</th>
                                    <th style="width: 10%"><i class="bx bx-calendar"></i>Tarih</th>
                                    <th style="width: 12%"><i class="bx bx-store"></i>Alıcı</th>
                                    <th style="width: 10%"><i class="bx bx-user"></i>Gönderen</th>
                                    <th style="width: 10%"><i class="bx bx-user-check"></i>Teslim Alan</th>
                                    <th style="width: 10%"><i class="bx bx-info-circle"></i>Durum</th>
                                    <th style="width: 10%"><i class="bx bx-calendar-check"></i>Teslim</th>
                                    <th style="width: 8%"><i class="bx bx-cog"></i>İşlemler</th>
                                </tr>
                                </thead>
                                <tbody class="professional-tbody">
                                <tr v-for="transfer in incomingTransfers" 
                                    :key="transfer.id">
                                    <td class="text-center">
                                        <a href="javascript:void(0)" 
                                           @click="viewTransfer(transfer.id)"
                                           class="fw-bold text-primary text-decoration-none"
                                           style="cursor: pointer;">
                                            @{{ transfer.number }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            @{{ transfer.type === 'phone' ? 'TELEFON' : 'DİĞER' }}
                                        </span>
                                    </td>
                                    <td class="text-center">@{{ transfer.main_seller?.name || 'N/A' }}</td>
                                    <td class="text-center">@{{ formatDate(transfer.created_at) }}</td>
                                    <td class="text-center">@{{ transfer.delivery_seller?.name || 'N/A' }}</td>
                                    <td class="text-center">@{{ transfer.user?.name || 'N/A' }}</td>
                                    <td class="text-center">@{{ transfer.confirm_user?.name || '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge" 
                                              :class="'badge-' + getStatusColor(transfer.is_status)">
                                            @{{ getStatusText(transfer.is_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">@{{ transfer.comfirm_date || '-' }}</td>
                                    <td class="text-center">
                                        <div v-if="transfer.is_status == 1 || transfer.is_status == 2" 
                                             class="d-flex gap-1 justify-content-center">
                                            <button type="button"
                                                    @click="confirmTransfer(transfer.id, 3)"
                                                    class="btn btn-success btn-xs"
                                                    title="Onayla">
                                                <i class="bx bx-check"></i>
                                            </button>
                                            <button type="button"
                                                    @click="confirmTransfer(transfer.id, 4)"
                                                    class="btn btn-danger btn-xs"
                                                    title="Reddet">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </div>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="incomingPagination && incomingPagination.total > incomingPagination.per_page" 
                             class="card mt-4">
                            <div class="card-body p-4">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item" :class="{ disabled: incomingPagination.current_page <= 1 }">
                                            <button class="page-link" 
                                                    @click="loadIncomingPage(incomingPagination.current_page - 1)" 
                                                    :disabled="incomingPagination.current_page <= 1">
                                                <i class="bx bx-chevron-left"></i>
                                            </button>
                                        </li>
                                        <li v-for="page in getVisiblePages(incomingPagination)" 
                                            :key="page" 
                                            class="page-item" 
                                            :class="{ active: page === incomingPagination.current_page }">
                                            <button class="page-link" @click="loadIncomingPage(page)">@{{ page }}</button>
                                        </li>
                                        <li class="page-item" :class="{ disabled: incomingPagination.current_page >= incomingPagination.last_page }">
                                            <button class="page-link" 
                                                    @click="loadIncomingPage(incomingPagination.current_page + 1)" 
                                                    :disabled="incomingPagination.current_page >= incomingPagination.last_page">
                                                <i class="bx bx-chevron-right"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- Yapılan Sevkler Tab -->
                    <div v-show="activeTab === 'outgoing'" class="tab-pane fade show active">
                        <!-- Loading State with Base CSS -->
                        <div v-if="loading.outgoing" class="table-loading-overlay">
                            <div class="loading-content">
                                <div class="loading-spinner-large"></div>
                                <p class="loading-text">Yapılan sevkler yükleniyor...</p>
                            </div>
                        </div>

                        <!-- Empty State with Base CSS -->
                        <div v-else-if="outgoingTransfers.length === 0" class="empty-state">
                            <div class="empty-content">
                                <i class="bx bx-package" style="font-size: 4rem;"></i>
                                <h5>Yapılan Sevk Bulunamadı</h5>
                                <p>Henüz gönderdiğiniz bir sevk bulunmuyor</p>
                            </div>
                        </div>

                        <!-- Professional Table with Base CSS -->
                        <div v-else class="table-responsive">
                            <table class="professional-table">
                                <thead>
                                <tr>
                                    <th style="width: 10%"><i class="bx bx-hash"></i>Sevk No</th>
                                    <th style="width: 8%"><i class="bx bx-mobile"></i>Tip</th>
                                    <th style="width: 12%"><i class="bx bx-store"></i>Gönderici</th>
                                    <th style="width: 10%"><i class="bx bx-calendar"></i>Tarih</th>
                                    <th style="width: 12%"><i class="bx bx-store"></i>Alıcı</th>
                                    <th style="width: 10%"><i class="bx bx-user"></i>Gönderen</th>
                                    <th style="width: 10%"><i class="bx bx-user-check"></i>Teslim Alan</th>
                                    <th style="width: 10%"><i class="bx bx-info-circle"></i>Durum</th>
                                    <th style="width: 10%"><i class="bx bx-calendar-check"></i>Teslim</th>
                                    <th style="width: 8%"><i class="bx bx-cog"></i>İşlemler</th>
                                </tr>
                                </thead>
                                <tbody class="professional-tbody">
                                <tr v-for="transfer in outgoingTransfers" 
                                    :key="transfer.id">
                                    <td class="text-center">
                                        <a href="javascript:void(0)" 
                                           @click="viewTransfer(transfer.id)"
                                           class="fw-bold text-primary text-decoration-none"
                                           style="cursor: pointer;">
                                            @{{ transfer.number }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            @{{ transfer.type === 'phone' ? 'TELEFON' : 'DİĞER' }}
                                        </span>
                                    </td>
                                    <td class="text-center">@{{ transfer.main_seller?.name || 'N/A' }}</td>
                                    <td class="text-center">@{{ formatDate(transfer.created_at) }}</td>
                                    <td class="text-center">@{{ transfer.delivery_seller?.name || 'N/A' }}</td>
                                    <td class="text-center">@{{ transfer.user?.name || 'N/A' }}</td>
                                    <td class="text-center">@{{ transfer.confirm_user?.name || '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge" 
                                              :class="'badge-' + getStatusColor(transfer.is_status)">
                                            @{{ getStatusText(transfer.is_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">@{{ transfer.comfirm_date || '-' }}</td>
                                    <td class="text-center">
                                        @role(['Depo Sorumlusu|super-admin'])
                                        <div v-if="transfer.is_status == 1" class="d-flex justify-content-center">
                                            <a :href="'{{ route('transfer.edit', ['id' => '']) }}' + transfer.id"
                                               class="btn btn-primary btn-xs"
                                               title="Düzenle">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                        </div>
                                        <span v-else class="text-muted">-</span>
                                        @endrole
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="outgoingPagination && outgoingPagination.total > outgoingPagination.per_page" 
                             class="card mt-4">
                            <div class="card-body p-4">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item" :class="{ disabled: outgoingPagination.current_page <= 1 }">
                                            <button class="page-link" 
                                                    @click="loadOutgoingPage(outgoingPagination.current_page - 1)" 
                                                    :disabled="outgoingPagination.current_page <= 1">
                                                <i class="bx bx-chevron-left"></i>
                                            </button>
                                        </li>
                                        <li v-for="page in getVisiblePages(outgoingPagination)" 
                                            :key="page" 
                                            class="page-item" 
                                            :class="{ active: page === outgoingPagination.current_page }">
                                            <button class="page-link" @click="loadOutgoingPage(page)">@{{ page }}</button>
                                        </li>
                                        <li class="page-item" :class="{ disabled: outgoingPagination.current_page >= outgoingPagination.last_page }">
                                            <button class="page-link" 
                                                    @click="loadOutgoingPage(outgoingPagination.current_page + 1)" 
                                                    :disabled="outgoingPagination.current_page >= outgoingPagination.last_page">
                                                <i class="bx bx-chevron-right"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

     
        
        <!-- Transfer Modal -->
        <div class="modal fade" id="transferModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-transfer me-2"></i>
                            Yeni Transfer Oluştur
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="transferCreateForm" @submit.prevent="submitTransfer">
                            <input type="hidden" name="type" value="other" />
                            
                            <!-- Transfer Bilgileri -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="bx bx-user me-1"></i>
                                        Gönderici Bayi
                                    </label>
                                    <select v-model="transferFormModal.main_seller_id" 
                                            class="form-select" 
                                            required
                                            :disabled="!hasRole(['Depo Sorumlusu', 'super-admin'])">
                                        <option value="">Bayi Seçiniz</option>
                                        <option v-for="seller in sellers" :key="seller.id" :value="seller.id" v-text="seller.name"></option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="bx bx-building me-1"></i>
                                        Alıcı Bayi
                                    </label>
                                    <select v-model="transferFormModal.delivery_seller_id" 
                                            class="form-select" 
                                            required>
                                        <option value="">Bayi Seçiniz</option>
                                        <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                                            @{{ seller.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="bx bx-hash me-1"></i>
                                        Sevk Numarası
                                    </label>
                                    <input type="text" 
                                           v-model="transferFormModal.number" 
                                           class="form-control" 
                                           required>
                                </div>
                            </div>

                            <!-- Transfer Tipi -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           v-model="transferFormModal.is_barcode_transfer"
                                           value="1"
                                           id="barcodeTransferCheck">
                                    <label class="form-check-label fw-bold" for="barcodeTransferCheck">
                                        <i class="bx bx-barcode me-1"></i>
                                        Barkod Transfer
                                    </label>
                                </div>
                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Barkod transfer seçilirse seri numarası yerine barkodlar girilecektir
                                </small>
                            </div>

                            <!-- Seri Numaraları / Barkodlar -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bx bx-barcode me-1"></i>
                                    <span v-if="transferFormModal.is_barcode_transfer">Barkodlar ve Adetler</span>
                                    <span v-else>Seri Numaraları</span>
                                </label>
                                <div class="serial-list-container-modal p-3 bg-light rounded">
                                    <div v-if="transferFormModal.sevkList.length === 0" class="text-muted text-center py-3">
                                        <span v-if="transferFormModal.is_barcode_transfer">
                                            Barkod ve adet eklemek için aşağıdaki alanları doldurun
                                        </span>
                                        <span v-else>
                                            Seri numarası eklemek için aşağıdaki alana girin ve Enter tuşuna basın
                                        </span>
                                    </div>
                                    
                                    <!-- Seri Numaraları Listesi (Normal Transfer) -->
                                    <div v-if="!transferFormModal.is_barcode_transfer">
                                        <div v-for="(serial, index) in transferFormModal.sevkList" 
                                             :key="index"
                                             class="input-group mb-2">
                                            <input type="text" 
                                                   :value="serial" 
                                                   class="form-control" 
                                                   readonly>
                                            <button type="button" 
                                                    @click="removeSerialModal(index)"
                                                    class="btn btn-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Barkod ve Adet Listesi (Barkod Transfer) -->
                                    <div v-else>
                                        <div v-for="(item, index) in transferFormModal.sevkList" 
                                             :key="index"
                                             class="row mb-2 align-items-center">
                                            <div class="col-md-6">
                                                <input type="text" 
                                                       :value="item.barcode || item" 
                                                       class="form-control" 
                                                       readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" 
                                                       :value="item.quantity || 1" 
                                                       class="form-control" 
                                                       readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" 
                                                        @click="removeSerialModal(index)"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Yeni Ekleme Alanı -->
                                    <div v-if="!transferFormModal.is_barcode_transfer" class="input-group mt-3">
                                        <input type="text" 
                                               v-model="newSerialModal" 
                                               @keyup.enter="addSerialModal"
                                               class="form-control" 
                                               placeholder="Seri numarası girin ve Enter tuşuna basın">
                                        <button type="button" 
                                                @click="addSerialModal"
                                                class="btn btn-primary">
                                            <i class="bx bx-plus"></i> Ekle
                                        </button>
                                    </div>
                                    
                                    <!-- Barkod ve Adet Ekleme Alanı -->
                                    <div v-else class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" 
                                                       v-model="newBarcodeModal" 
                                                       class="form-control" 
                                                       placeholder="Barkod girin">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" 
                                                       v-model="newQuantityModal" 
                                                       class="form-control" 
                                                       min="1" 
                                                       value="1"
                                                       placeholder="Adet">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" 
                                                        @click="addBarcodeModal"
                                                        class="btn btn-primary">
                                                    <i class="bx bx-plus"></i> Ekle
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Not -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bx bx-note me-1"></i>
                                    Açıklama
                                </label>
                                <textarea v-model="transferFormModal.description" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Transfer hakkında notlarınızı yazabilirsiniz..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>
                            İptal
                        </button>
                        <button type="button" 
                                @click="submitTransfer" 
                                class="btn btn-primary"
                                :disabled="loading.transfer || transferFormModal.sevkList.length === 0">
                            <span v-if="loading.transfer">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                İşleniyor...
                            </span>
                            <span v-else>
                                <i class="bx bx-paper-plane me-1"></i>
                                Transfer Oluştur
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Transfer View Modal (Read-Only) -->
        <div class="modal fade" id="transferViewModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-show me-2"></i>
                            Transfer Detayları
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Loading -->
                        <div v-if="loading.viewTransfer" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                            <p class="mt-2">Transfer detayları yükleniyor...</p>
                        </div>
                        
                        <!-- Transfer Details -->
                        <div v-else-if="viewTransferData">
                            <!-- Transfer Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold mb-3">
                                                <i class="bx bx-info-circle me-2 text-primary"></i>
                                                Transfer Bilgileri
                                            </h6>
                                            <div class="mb-2">
                                                <strong>Sevk No:</strong> 
                                                <span class="text-primary fw-bold">@{{ viewTransferData.number }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Tarih:</strong> 
                                                <span>@{{ formatDate(viewTransferData.created_at) }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Durum:</strong> 
                                                <span class="badge" :class="'badge-' + getStatusColor(viewTransferData.is_status)">
                                                    @{{ getStatusText(viewTransferData.is_status) }}
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Tip:</strong> 
                                                <span class="badge badge-info">
                                                    @{{ viewTransferData.type === 'phone' ? 'TELEFON' : 'DİĞER' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold mb-3">
                                                <i class="bx bx-user me-2 text-success"></i>
                                                Bayi Bilgileri
                                            </h6>
                                            <div class="mb-2">
                                                <strong>Gönderici Bayi:</strong> 
                                                <span>@{{ viewTransferData.main_seller?.name || 'N/A' }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Alıcı Bayi:</strong> 
                                                <span>@{{ viewTransferData.delivery_seller?.name || 'N/A' }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Gönderen:</strong> 
                                                <span>@{{ viewTransferData.user?.name || 'N/A' }}</span>
                                            </div>
                                            <div class="mb-2" v-if="viewTransferData.confirm_user">
                                                <strong>Teslim Alan:</strong> 
                                                <span>@{{ viewTransferData.confirm_user?.name || '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Serial Numbers Table -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bx bx-barcode me-2"></i>
                                        Seri Numaraları (@{{ viewTransferData.serial_list?.length || 0 }} adet)
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 20%">Seri No</th>
                                                    <th style="width: 25%">Stok Adı</th>
                                                    <th style="width: 15%">Kategori</th>
                                                    <th style="width: 10%">Marka</th>
                                                    <th style="width: 15%">Model</th>
                                                    <th style="width: 10%">Renk</th>
                                                    <th style="width: 10%">Adet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="!viewTransferData.detail || viewTransferData.detail.length === 0">
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        <i class="bx bx-info-circle me-2"></i>
                                                        Seri numarası bulunamadı
                                                    </td>
                                                </tr>
                                                <tr v-for="(item, index) in viewTransferData.detail" :key="index">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td class="fw-bold text-primary">@{{ item.serial }}</td>
                                                    <td>@{{ item.name }}</td>
                                                    <td>@{{ item.category }}</td>
                                                    <td>@{{ item.brand || 'N/A' }}</td>
                                                    <td>@{{ item.version }}</td>
                                                    <td>@{{ item.color || 'N/A' }}</td>
                                                    <td>@{{ item.quantity || 'N/A' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="card" v-if="viewTransferData.description">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bx bx-note me-2 text-warning"></i>
                                        Açıklama
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">@{{ viewTransferData.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>
                            Kapat
                        </button>
                        <button type="button" @click="printTransfer" class="btn btn-primary">
                            <i class="bx bx-printer me-1"></i>
                            Yazdır
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-5">
    </div>
@endsection

@section('custom-js')
    <!-- Vue.js CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script>
        const { createApp } = Vue;

        // Axios CSRF token setup
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        
        // Axios interceptor for CSRF token
        axios.interceptors.request.use(function (config) {
            if (config.method === 'post' || config.method === 'put' || config.method === 'patch' || config.method === 'delete') {
                config.headers['X-CSRF-TOKEN'] = token;
                if (config.data && typeof config.data === 'object') {
                    config.data._token = token;
                }
            }
            return config;
        });

        createApp({
            data() {
                return {
                    // Active tab
                    activeTab: 'incoming',
                    
                    // Search form
                    searchForm: {
                        stockName: '',
                        brand: '',
                        version: '',
                        category: '',
                        color: '',
                        seller: 'all',
                        serialNumber: ''
                    },
                    
                    // Data arrays
                    brands: @json($brands ?? []),
                    versions: [],
                    categories: @json($categories ?? []),
                    colors: @json($colors ?? []),
                    sellers: @json($sellers ?? []),
                    
                    // Transfer data
                    incomingTransfers: [],
                    outgoingTransfers: [],
                    
                    // Pagination
                    incomingPagination: null,
                    outgoingPagination: null,
                    
                    // Loading states
                    loading: {
                        search: false,
                        incoming: false,
                        outgoing: false,
                        transfer: false,
                        viewTransfer: false
                    },
                    
                    // Transfer Modal Form
                    transferFormModal: {
                        main_seller_id: '',
                        delivery_seller_id: '',
                        number: '',
                        sevkList: [],
                        description: '',
                        type: 'other',
                        is_barcode_transfer: false
                    },
                    newSerialModal: '',
                    newBarcodeModal: '',
                    newQuantityModal: 1,
                    
                    // View Transfer Data
                    viewTransferData: null,
                    
                    // User permissions
                    canSelectSeller: @json(auth()->user()->hasRole(['Depo Sorumlusu','super-admin'])),
                    
                    // Status mappings
                    statusMap: {
                        1: { text: 'Beklemede', color: 'warning' },
                        2: { text: 'Onaylandı', color: 'info' },
                        3: { text: 'Tamamlandı', color: 'success' },
                        4: { text: 'Reddedildi', color: 'danger' }
                    }
                }
            },
            
            mounted() {
                this.loadInitialData();
            },
            
            methods: {
                // Load initial data
                async loadInitialData() {
                    await Promise.all([
                        this.loadIncomingTransfers(),
                        this.loadOutgoingTransfers()
                    ]);
                },
                
                // Load incoming transfers
                async loadIncomingTransfers(page = 1) {
                    this.loading.incoming = true;
                    try {
                        const response = await axios.get('/transfer/incoming-ajax', {
                            params: {
                                page: page,
                                ...this.searchForm
                            }
                        });
                        
                        this.incomingTransfers = response.data.transfers || [];
                        this.incomingPagination = response.data.pagination || null;
                        
                    } catch (error) {
                        console.error('Gelen sevkler yüklenemedi:', error);
                        this.incomingTransfers = [];
                    } finally {
                        this.loading.incoming = false;
                    }
                },
                
                // Load outgoing transfers
                async loadOutgoingTransfers(page = 1) {
                    this.loading.outgoing = true;
                    try {
                        const response = await axios.get('/transfer/outgoing-ajax', {
                            params: {
                                page: page,
                                ...this.searchForm
                            }
                        });
                        
                        this.outgoingTransfers = response.data.transfers || [];
                        this.outgoingPagination = response.data.pagination || null;
                        
                    } catch (error) {
                        console.error('Yapılan sevkler yüklenemedi:', error);
                        this.outgoingTransfers = [];
                    } finally {
                        this.loading.outgoing = false;
                    }
                },
                
                // Search transfers
                async searchTransfers() {
                    this.loading.search = true;
                    await Promise.all([
                        this.loadIncomingTransfers(1),
                        this.loadOutgoingTransfers(1)
                    ]);
                    this.loading.search = false;
                },
                
                // Clear filters
                clearFilters() {
                    this.searchForm = {
                        stockName: '',
                        brand: '',
                        version: '',
                        category: '',
                        color: '',
                        seller: 'all',
                        serialNumber: ''
                    };
                    this.versions = [];
                    this.searchTransfers();
                },
                
                // Get versions when brand changes
                async getVersions() {
                    if (!this.searchForm.brand) {
                        this.versions = [];
                        return;
                    }
                    
                    try {
                        const response = await axios.get('/transfer/versions-ajax', {
                            params: { brand_id: this.searchForm.brand }
                        });
                        this.versions = response.data;
                    } catch (error) {
                        console.error('Versiyonlar yüklenemedi:', error);
                        this.versions = [];
                    }
                },
                
                // Load specific pages
                async loadIncomingPage(page) {
                    if (page < 1 || page > this.incomingPagination.last_page) return;
                    await this.loadIncomingTransfers(page);
                },
                
                async loadOutgoingPage(page) {
                    if (page < 1 || page > this.outgoingPagination.last_page) return;
                    await this.loadOutgoingTransfers(page);
                },
                
                // Get visible pages for pagination
                getVisiblePages(pagination) {
                    if (!pagination) return [];
                    
                    const current = pagination.current_page;
                    const last = pagination.last_page;
                    const delta = 2;
                    
                    let start = Math.max(1, current - delta);
                    let end = Math.min(last, current + delta);
                    
                    const pages = [];
                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                    return pages;
                },
                
                // Format date
                formatDate(date) {
                    if (!date) return '-';
                    return new Date(date).toLocaleDateString('tr-TR');
                },
                
                // Get status color
                getStatusColor(status) {
                    return this.statusMap[status]?.color || 'secondary';
                },
                
                // Get status text
                getStatusText(status) {
                    return this.statusMap[status]?.text || 'Bilinmiyor';
                },
                
                // Confirm transfer
                async confirmTransfer(transferId, status) {
                    const action = status === 3 ? 'onaylamak' : 'reddetmek';
                    
                    if (!confirm(`${action} istediğinizden emin misiniz?`)) {
                        return;
                    }
                    
                    try {
                        const response = await axios.post(`/transfer/update`, {
                            id: transferId,
                            is_status: status
                        });
                        
                        // Reload data
                        await this.loadIncomingTransfers();
                        
                        // Show success message
                        this.showNotification('Başarılı', 'Sevk durumu güncellendi', 'success');
                        
                    } catch (error) {
                        console.error('Sevk güncellenemedi:', error);
                        this.showNotification('Hata', 'Sevk güncellenemedi', 'error');
                    }
                },
                
                // Show notification
                showNotification(title, message, type) {
                    // You can implement a toast notification here
                    console.log(`${title}: ${message} (${type})`);
                },
                
                // Open transfer modal
                openTransferModal() {
                    // Reset form
                    this.transferFormModal = {
                        main_seller_id: @json(auth()->user()->seller_id ?? ''),
                        delivery_seller_id: '',
                        number: this.generateTransferNumber(),
                        sevkList: [],
                        description: '',
                        type: 'other',
                        is_barcode_transfer: false
                    };
                    this.newSerialModal = '';
                    this.newBarcodeModal = '';
                    this.newQuantityModal = 1;
                    
                    // Open modal
                    const modal = new bootstrap.Modal(document.getElementById('transferModal'));
                    modal.show();
                },
                
                // Generate transfer number
                generateTransferNumber() {
                    return 'TR' + Date.now() + Math.floor(Math.random() * 1000);
                },
                
                // Add serial to modal
                async addSerialModal() {
                    if (!this.newSerialModal || this.newSerialModal.length < 6) {
                        alert('Lütfen geçerli bir seri numarası girin (min 6 karakter)');
                        return;
                    }
                    
                    // Check duplicate
                    if (this.transferFormModal.sevkList.includes(this.newSerialModal)) {
                        alert('Bu seri numarası zaten eklenmiş');
                        this.newSerialModal = '';
                        return;
                    }
                    
                    // Validate serial with backend
                    try {
                        const response = await axios.get('/serialcheck', {
                            params: {
                                id: this.newSerialModal,
                                seller_id: this.transferFormModal.main_seller_id
                            }
                        });

                        const data = response.data;

                        if (!data || !data.status) {
                            alert('Seri numarası transfer edilemez. Bulunamadı veya başka bayiye ait.');
                            this.newSerialModal = '';
                            return;
                        }


                        // Add to list
                        this.transferFormModal.sevkList.push(this.newSerialModal);
                        this.newSerialModal = '';
                        
                    } catch (error) {
                        console.error('Seri numarası kontrolü başarısız:', error);
                        alert('Seri numarası kontrolü yapılamadı');
                    }
                },
                
                // Add barcode with quantity to modal
                async addBarcodeModal() {
                    if (!this.newBarcodeModal || this.newBarcodeModal.length < 6) {
                        alert('Lütfen geçerli bir barkod girin (min 6 karakter)');
                        return;
                    }
                    
                    if (!this.newQuantityModal || this.newQuantityModal < 1) {
                        alert('Lütfen geçerli bir adet girin (min 1)');
                        return;
                    }
                    
                    // Check duplicate
                    const existingItem = this.transferFormModal.sevkList.find(item => {
                        const barcode = typeof item === 'string' ? item : item.barcode;
                        return barcode === this.newBarcodeModal;
                    });
                    
                    if (existingItem) {
                        alert('Bu barkod zaten eklenmiş');
                        this.newBarcodeModal = '';
                        this.newQuantityModal = 1;
                        return;
                    }
                    
                    // Validate barcode with backend
                    try {
                        const response = await axios.get('/getTransferBarcodeCheck', {
                            params: {
                                barcode: this.newBarcodeModal,
                                seller_id: this.transferFormModal.main_seller_id
                            }
                        });
                        
                        if (response.data.success) {
                            // Check if requested quantity is available
                            if (this.newQuantityModal > response.data.available_quantity) {
                                alert(`Yetersiz stok! Mevcut adet: ${response.data.available_quantity}, Talep edilen: ${this.newQuantityModal}`);
                                return;
                            }
                            
                            // Add barcode with quantity
                            this.transferFormModal.sevkList.push({
                                barcode: this.newBarcodeModal,
                                quantity: parseInt(this.newQuantityModal)
                            });
                            
                            this.newBarcodeModal = '';
                            this.newQuantityModal = 1;
                        } else {
                            alert(response.data.message || 'Barkod doğrulanamadı');
                        }
                    } catch (error) {
                        console.error('Barkod doğrulama hatası:', error);
                        alert('Barkod doğrulanırken bir hata oluştu');
                    }
                },
                
                // Remove serial from modal
                removeSerialModal(index) {
                    this.transferFormModal.sevkList.splice(index, 1);
                },
                
                // Submit transfer
                async submitTransfer() {
                    if (!this.transferFormModal.main_seller_id) {
                        alert('Lütfen gönderici bayi seçiniz');
                        return;
                    }
                    
                    if (!this.transferFormModal.delivery_seller_id) {
                        alert('Lütfen alıcı bayi seçiniz');
                        return;
                    }
                    
                    if (this.transferFormModal.sevkList.length === 0) {
                        const itemType = this.transferFormModal.is_barcode_transfer ? 'barkod' : 'seri numarası';
                        alert(`Lütfen en az bir ${itemType} ekleyin`);
                        return;
                    }
                    
                    this.loading.transfer = true;
                    
                    try {
                        const response = await axios.post('/transfer/store', {
                            main_seller_id: this.transferFormModal.main_seller_id,
                            delivery_seller_id: this.transferFormModal.delivery_seller_id,
                            number: this.transferFormModal.number,
                            sevkList: this.transferFormModal.sevkList,
                            description: this.transferFormModal.description,
                            type: this.transferFormModal.type,
                            is_barcode_transfer: this.transferFormModal.is_barcode_transfer
                        });
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('transferModal'));
                        modal.hide();
                        
                        // Show success message
                        alert(response.data || 'Transfer başarıyla oluşturuldu');
                        
                        // Reload data
                        await Promise.all([
                            this.loadIncomingTransfers(),
                            this.loadOutgoingTransfers()
                        ]);
                        
                    } catch (error) {
                        console.error('Transfer oluşturulamadı:', error);
                        alert('Transfer oluşturulamadı: ' + (error.response?.data?.message || error.message));
                    } finally {
                        this.loading.transfer = false;
                    }
                },
                
                // Check user role
                hasRole(roles) {
                    // This would need to be passed from backend
                    return this.canSelectSeller;
                },
                
                // View transfer details
                async viewTransfer(transferId) {
                    this.loading.viewTransfer = true;
                    this.viewTransferData = null;
                    
                    try {
                        // Open modal
                        const modal = new bootstrap.Modal(document.getElementById('transferViewModal'));
                        modal.show();
                        
                        // Fetch transfer details
                        const response = await axios.get(`/transfer/${transferId}`);
                        this.viewTransferData = response.data;
                        
                    } catch (error) {
                        console.error('Transfer detayları yüklenemedi:', error);
                        alert('Transfer detayları yüklenemedi: ' + (error.response?.data?.message || error.message));
                        
                        // Close modal on error
                        const modal = bootstrap.Modal.getInstance(document.getElementById('transferViewModal'));
                        if (modal) modal.hide();
                    } finally {
                        this.loading.viewTransfer = false;
                    }
                },
                
                // Print transfer
                printTransfer() {
                    if (!this.viewTransferData) return;
                    
                    // Open print page in new window
                    window.open(`/transfer/show?id=${this.viewTransferData.id}`, '_blank');
                }
            }
        }).mount('#app');
    </script>
@endsection


