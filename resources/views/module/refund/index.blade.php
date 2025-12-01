@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/list-page-base.css') }}">
    <style>
        /* Autocomplete - stok arama */
        .filter-input {
            width: 100%;
            padding: 0.45rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid var(--bs-border-color);
            font-size: 0.875rem;
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
        }

        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1055;
            margin-top: 2px;
            background-color: #fff;
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
            box-shadow: 0 10px 30px rgba(15, 30, 65, 0.15);
            max-height: 260px;
            overflow-y: auto;
        }

        .autocomplete-item {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
            font-size: 0.85rem;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover {
            background-color: #f5f7ff;
        }

        .autocomplete-loading,
        .autocomplete-no-results {
            padding: 0.6rem 0.75rem;
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: var(--bs-secondary-color);
        }
    </style>
@endsection

@section('content')
    <div id="refundApp" class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="İadeler"
            :createRoute="null"
            :count="0"
            icon="bx-undo"
            description="İade işlemleri yönetimi"
        >
            <button type="button" class="btn btn-sm btn-outline-success" :disabled="loading"
                    @click="openCreateModal">
                <i class="bx bx-plus me-1"></i>
                Yeni İade Oluştur
            </button>
        </x-list-page.header>

        <!-- Filter Card -->
        <x-list-page.card>
            <form class="row g-3" @submit.prevent="fetchRefunds">
                <div class="col-md-2">
                    <label class="form-label">Marka</label>
                    <select v-model="filters.brand" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option v-for="brand in options.brands" :key="`brand-${brand.id}`"
                                :value="String(brand.id)">@{{ brand.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Model</label>
                    <select v-model="filters.version" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option v-for="version in versions" :key="`version-${version.id}`"
                                :value="String(version.id)">@{{ version.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Renk</label>
                    <select v-model="filters.color" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option v-for="color in options.colors" :key="`color-${color.id}`"
                                :value="String(color.id)">@{{ color.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Şube</label>
                    <select v-model="filters.seller" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option v-for="seller in options.sellers" :key="`seller-${seller.id}`"
                                :value="String(seller.id)">@{{ seller.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">İade Nedeni</label>
                    <select v-model="filters.reason" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option v-for="reason in options.reasons" :key="`reason-${reason.id}`"
                                :value="String(reason.id)">@{{ reason.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Seri Numarası</label>
                    <input v-model.trim="filters.serial_number" type="text" class="form-control form-control-sm"
                           placeholder="Seri / Barkod">
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-sm btn-primary" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i class="bx bx-search me-1"></i>
                        Ara
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="loading"
                            @click="resetFilters">
                        <i class="bx bx-refresh me-1"></i>
                        Sıfırla
                    </button>
                </div>
            </form>
        </x-list-page.card>

        <!-- Table Card -->
        <x-list-page.card>
            <x-list-page.table>
                <thead>
                <tr>
                    <th>Stok Adı</th>
                    <th>Marka</th>
                    <th>Model</th>
                    <th>Renk</th>
                    <th>İade Nedeni</th>
                    <th>Seri No</th>
                    <th>Şube</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="loading">
                    <td colspan="9" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </td>
                </tr>
                <template v-else>
                    <tr v-if="refunds.length === 0">
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bx bx-inbox fs-4 d-block mb-2"></i>
                            Kayıt bulunamadı
                        </td>
                    </tr>
                    <tr v-for="refund in refunds" :key="`refund-${refund.id}`">
                        <td>
                            <strong>@{{ refund.stock?.name || 'Bulunamadı' }}</strong>
                        </td>
                        <td>
                            <strong>@{{ refund.stock?.brand?.name || refund.brand?.name || 'Bulunamadı' }}</strong>
                        </td>
                        <td>
                            <span class="text-muted">@{{ refund.stock?.name || 'Bulunamadı' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info">@{{ refund.color?.name || 'Bulunamadı' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">@{{ refund.reason?.name || 'Bulunamadı' }}</span>
                        </td>
                        <td>
                            <code>@{{ refund.serial_number || 'Bulunamadı' }}</code>
                        </td>
                        <td>
                            <span>@{{ refund.seller?.name || 'Bulunamadı' }}</span>
                        </td>
                        <td>
                            <span class="badge" :class="getStatusBadgeClass(refund.status)">
                                @{{ getStatusText(refund.status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-info"
                                        @click="openDescriptionModal(refund)" v-if="refund.description"
                                        title="Açıklama">
                                    <i class="bx bx-text"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        @click="openDetailModal(refund)"
                                        title="Detay">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <template v-if="refund.status === 0">
                                    <button
                                        v-for="action in getPendingActions(refund)"
                                        :key="action.key"
                                        type="button"
                                        class="btn btn-sm"
                                        :class="action.class"
                                        :disabled="action.loading"
                                        @click="action.onClick()"
                                        :title="action.label"
                                    >
                                        <span v-if="action.loading"
                                              class="spinner-border spinner-border-sm"></span>
                                        <i v-else :class="action.icon"></i>
                                    </button>
                                </template>
                                <template v-else-if="refund.status === 5">
                                    <button type="button" class="btn btn-sm btn-warning"
                                            :disabled="actionLoading === refund.id"
                                            @click="updateRefundStatus(refund.id, 'service_return')"
                                            title="Servisten Geldi">
                                        <span v-if="actionLoading === refund.id"
                                              class="spinner-border spinner-border-sm"></span>
                                        <i v-else class="bx bx-check"></i>
                                    </button>
                                </template>
                                <template v-else-if="refund.status === 6">
                                    <button type="button" class="btn btn-sm btn-success"
                                            :disabled="actionLoading === refund.id"
                                            @click="updateRefundStatus(refund.id, 'delivered')"
                                            title="Teslim Edildi">
                                        <span v-if="actionLoading === refund.id"
                                              class="spinner-border spinner-border-sm"></span>
                                        <i v-else class="bx bx-check"></i>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </x-list-page.table>

            <!-- Pagination -->
            <div v-if="pagination && pagination.last_page > 1" class="card-footer bg-white border-top p-3">
                <nav>
                    <ul class="pagination pagination-sm mb-0 justify-content-end">
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <button class="page-link" @click="loadPage(pagination.current_page - 1)" :disabled="loading">
                                <i class="bx bx-chevron-left"></i>
                            </button>
                        </li>
                        <li v-for="page in getPageNumbers()" :key="page" class="page-item"
                            :class="{ active: page === pagination.current_page }">
                            <button class="page-link" @click="loadPage(page)" :disabled="loading">
                                @{{ page }}
                            </button>
                        </li>
                        <li class="page-item"
                            :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="loadPage(pagination.current_page + 1)" :disabled="loading">
                                <i class="bx bx-chevron-right"></i>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </x-list-page.card>

        <!-- Description Modal -->
        <div v-if="descriptionModal.visible" class="modal fade show" style="display: block;" tabindex="-1"
             @click.self="closeDescriptionModal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Açıklama</h5>
                        <button type="button" class="btn-close" @click="closeDescriptionModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">@{{ descriptionModal.text || 'Açıklama bulunamadı.' }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" @click="closeDescriptionModal">Kapat</button>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <!-- Detail Modal -->
        <div v-if="detailModal.visible" class="modal fade show" style="display: block;" tabindex="-1"
             @click.self="closeDetailModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form @submit.prevent="saveDetail">
                        <div class="modal-header">
                            <h5 class="modal-title">Açıklama Düzenle</h5>
                            <button type="button" class="btn-close" @click="closeDetailModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Açıklama</label>
                                <textarea v-model="detailModal.description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" @click="closeDetailModal"
                                    :disabled="detailModal.loading">Kapat</button>
                            <button type="submit" class="btn btn-primary" :disabled="detailModal.loading">
                                <span v-if="detailModal.loading" class="spinner-border spinner-border-sm me-1"></span>
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <!-- Create Modal -->
        <div v-if="createModal.visible" class="modal fade show" style="display: block;" tabindex="-1"
             @click.self="closeCreateModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form @submit.prevent="createRefund">
                        <div class="modal-header">
                            <h5 class="modal-title">Yeni İade Oluştur</h5>
                            <button type="button" class="btn-close" @click="closeCreateModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Stok</label>
                                    <div class="position-relative">
                                        <input type="text" v-model="searchForm.stockName" @input="searchStock"
                                               @focus="onStockInputFocus" @blur="hideStockDropdown" class="filter-input"
                                               placeholder="Stok adı ara..." autocomplete="off">
                                        <div v-if="showStockDropdown" class="autocomplete-dropdown">
                                            <div v-if="searchingStock" class="autocomplete-loading">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Aranıyor...</span>
                                                </div>
                                                <span class="ms-2">Aranıyor...</span>
                                            </div>
                                            <div v-else-if="filteredStocks.length > 0">
                                                <div v-for="stock in filteredStocks" :key="stock.id"
                                                     @mousedown.prevent="selectStock(stock)" class="autocomplete-item">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <strong>@{{ stock.name }}</strong>
                                                            <span class="text-muted"> - @{{ stock.brand_name }}</span>
                                                            <span class="text-muted" v-if="stock.version_name"
                                                                  v-html="stock.version_name"></span>
                                                            <small class="text-muted d-block">
                                                                <i class="bx bx-category-alt"></i> @{{ stock.category_name }}
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="badge"
                                                                  :class="stock.quantity > 0 ? 'bg-success' : 'bg-secondary'">
                                                                @{{ stock.quantity }} Adet
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-else class="autocomplete-no-results">
                                                <i class="bx bx-search-alt"></i>
                                                <span class="ms-2">Sonuç bulunamadı</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Renk</label>
                                    <select v-model="createModal.form.color_id" class="form-select">
                                        <option value="">Seçiniz</option>
                                        <option v-for="color in options.colors" :key="`create-color-${color.id}`"
                                                :value="String(color.id)">@{{ color.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">İade Nedeni</label>
                                    <select v-model="createModal.form.reason_id" class="form-select" required>
                                        <option value="">Seçiniz</option>
                                        <option v-for="reason in options.reasons"
                                                :key="`create-reason-${reason.id}`" :value="String(reason.id)">@{{
                                            reason.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Seri / Barkod</label>
                                    <input
                                        ref="createSerialInput"
                                        v-model="createModal.form.serial_number"
                                        @keydown.enter.prevent="handleCreateSerialEnter"
                                        type="text"
                                        class="form-control"
                                        :disabled="createModal.loading || createModal.fetchingStock"
                                        placeholder="Seri numarası veya barkod"
                                    >
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Açıklama</label>
                                    <textarea v-model="createModal.form.description" class="form-control" rows="3"
                                              placeholder="İade açıklaması"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" @click="closeCreateModal"
                                    :disabled="createModal.loading || createModal.fetchingStock">Kapat</button>
                            <button type="submit" class="btn btn-primary"
                                    :disabled="createModal.loading || createModal.fetchingStock">
                                <span v-if="createModal.loading" class="spinner-border spinner-border-sm me-1"></span>
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <!-- New Sale Modal -->
        <div v-if="newSaleModal.visible" class="modal fade show" style="display: block;" tabindex="-1"
             @click.self="closeNewSaleModal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="saveNewSale">
                        <div class="modal-header">
                            <h5 class="modal-title">Satışa Çıkart</h5>
                            <button type="button" class="btn-close" @click="closeNewSaleModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Stok</label>
                                    <select v-model="newSaleModal.stock_card_id" class="form-select" required>
                                        <option value="" disabled>Seçiniz</option>
                                        <option v-for="stock in options.stocks" :key="`stock-${stock.id}`"
                                                :value="String(stock.id)">@{{ stock.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Renk</label>
                                    <select v-model="newSaleModal.color_id" class="form-select">
                                        <option value="">Seçiniz</option>
                                        <option v-for="color in options.colors" :key="`modal-color-${color.id}`"
                                                :value="String(color.id)">@{{ color.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gerçek Maliyet</label>
                                    <input v-model="newSaleModal.cost_price" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Maliyet</label>
                                    <input v-model="newSaleModal.base_cost_price" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Satış Fiyatı</label>
                                    <input v-model="newSaleModal.sale_price" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" @click="closeNewSaleModal"
                                    :disabled="newSaleModal.loading">Kapat</button>
                            <button type="submit" class="btn btn-primary" :disabled="newSaleModal.loading">
                                <span v-if="newSaleModal.loading" class="spinner-border spinner-border-sm me-1"></span>
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        (function () {
            const {createApp, ref, reactive, computed, onMounted, onBeforeUnmount, watch, nextTick} = Vue;

            createApp({
                setup() {
                    const loading = ref(false);
                    const refunds = ref([]);
                    const actionLoading = ref(null);
                    const createSerialInput = ref(null);
                    const pagination = ref(null);

                    const filters = reactive({
                        brand: '',
                        version: '',
                        color: '',
                        seller: '',
                        reason: '',
                        serial_number: ''
                    });

                    const options = reactive({
                        brands: [],
                        colors: [],
                        sellers: [],
                        reasons: [],
                        stocks: []
                    });

                    const versions = ref([]);
                    const roles = ref([]);

                    const searchForm = reactive({
                        stockName: ''
                    });
                    const showStockDropdown = ref(false);
                    const searchingStock = ref(false);
                    const filteredStocks = ref([]);

                    const descriptionModal = reactive({
                        visible: false,
                        text: ''
                    });

                    const detailModal = reactive({
                        visible: false,
                        id: null,
                        description: '',
                        loading: false
                    });

                    const newSaleModal = reactive({
                        visible: false,
                        id: null,
                        type: 'seller',
                        stock_card_id: '',
                        color_id: '',
                        cost_price: '',
                        base_cost_price: '',
                        sale_price: '',
                        loading: false
                    });

                    const createModal = reactive({
                        visible: false,
                        loading: false,
                        fetchingStock: false,
                        form: {
                            stock_card_id: '',
                            color_id: '',
                            reason_id: '',
                            serial_number: '',
                            description: ''
                        }
                    });

                    const hasSalePermission = computed(() => {
                        return roles.value.includes('super-admin') && roles.value.includes('Depo Sorumlusu');
                    });

                    const modalStates = computed(() => [descriptionModal.visible, detailModal.visible, newSaleModal.visible, createModal.visible]);

                    watch(modalStates, (states) => {
                        const isAnyModalOpen = states.some(Boolean);
                        document.body.classList.toggle('modal-open', isAnyModalOpen);
                        document.body.style.overflow = isAnyModalOpen ? 'hidden' : '';
                    });

                    onBeforeUnmount(() => {
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('overflow');
                    });

                    const showToast = (message, type = 'success') => {
                        if (window.Swal) {
                            window.Swal.fire({
                                icon: type,
                                title: message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        } else {
                            alert(message);
                        }
                    };

                    const sanitizeFilters = () => {
                        const params = {};
                        Object.entries(filters).forEach(([key, value]) => {
                            if (value) {
                                params[key] = value;
                            }
                        });
                        return params;
                    };

                    const loadVersions = async (brandId) => {
                        if (!brandId) {
                            versions.value = [];
                            return;
                        }
                        try {
                            const {data} = await axios.get('/get_version', {params: {id: brandId}});
                            versions.value = Array.isArray(data) ? data.map((item) => ({id: item.id, name: item.name})) : [];
                        } catch (error) {
                            console.error('Versiyonlar yüklenemedi', error);
                            versions.value = [];
                        }
                    };

                    watch(() => filters.brand, (brandId) => {
                        filters.version = '';
                        loadVersions(brandId);
                    });

                    const fetchRefunds = async (page = 1) => {
                        loading.value = true;
                        try {
                            const params = {...sanitizeFilters(), page, per_page: 50};
                            const {data} = await axios.get('/stockcard/refunds/data', {params});

                            refunds.value = Array.isArray(data.refunds) ? data.refunds : [];
                            pagination.value = data.pagination || null;

                            if (data.filters) {
                                options.brands = Array.isArray(data.filters.brands) ? data.filters.brands : [];
                                options.sellers = Array.isArray(data.filters.sellers) ? data.filters.sellers : [];
                                options.colors = Array.isArray(data.filters.colors) ? data.filters.colors : [];
                                options.reasons = Array.isArray(data.filters.reasons) ? data.filters.reasons : [];
                                options.stocks = Array.isArray(data.filters.stocks) ? data.filters.stocks : [];
                            }
                        } catch (error) {
                            console.error('İade listesi alınamadı', error);
                            showToast('İade listesi alınamadı', 'error');
                            refunds.value = [];
                            pagination.value = null;
                        } finally {
                            loading.value = false;
                        }
                    };

                    const loadPage = (page) => {
                        if (page >= 1 && (!pagination.value || page <= pagination.value.last_page)) {
                            fetchRefunds(page);
                        }
                    };

                    const getPageNumbers = () => {
                        if (!pagination.value) return [];
                        const current = pagination.value.current_page;
                        const last = pagination.value.last_page;
                        const pages = [];
                        const maxPages = 5;

                        let start = Math.max(1, current - Math.floor(maxPages / 2));
                        let end = Math.min(last, start + maxPages - 1);

                        if (end - start < maxPages - 1) {
                            start = Math.max(1, end - maxPages + 1);
                        }

                        for (let i = start; i <= end; i++) {
                            pages.push(i);
                        }
                        return pages;
                    };

                    const getStatusText = (status) => {
                        const statusMap = {
                            0: 'Beklemede',
                            1: 'Satışa Alındı',
                            3: 'Hasarlı İade Alındı',
                            4: 'Müşteriye Teslim Edildi',
                            5: 'Servise Gönderildi',
                            6: 'Servisten Döndü'
                        };
                        return statusMap[status] || 'Bilinmeyen';
                    };

                    const getStatusBadgeClass = (status) => {
                        const classMap = {
                            0: 'bg-secondary',
                            1: 'bg-success',
                            3: 'bg-danger',
                            4: 'bg-info',
                            5: 'bg-warning',
                            6: 'bg-primary'
                        };
                        return classMap[status] || 'bg-secondary';
                    };

                    const searchStock = async () => {
                        const term = (searchForm.stockName || '').trim();
                        if (term.length < 2) {
                            filteredStocks.value = [];
                            showStockDropdown.value = !!term.length;
                            return;
                        }

                        searchingStock.value = true;
                        showStockDropdown.value = true;

                        try {
                            const {data} = await axios.get('/stockcard/stocks-search', {params: {q: term}});
                            const rows = Array.isArray(data) ? data : (Array.isArray(data?.data) ? data.data : []);
                            filteredStocks.value = rows.map(stock => ({
                                id: String(stock.id),
                                name: stock.text || stock.name || '',
                                brand_name: stock.brand_name || '',
                                version_name: stock.version_names || '',
                                category_name: stock.category_name || '',
                                quantity: typeof stock.quantity !== 'undefined' ? Number(stock.quantity) : 0
                            })).filter(Boolean);
                        } catch (error) {
                            console.error('Stok arama hatası', error);
                            filteredStocks.value = [];
                        } finally {
                            searchingStock.value = false;
                        }
                    };

                    const onStockInputFocus = () => {
                        if (filteredStocks.value.length > 0 || (searchForm.stockName || '').length >= 2) {
                            showStockDropdown.value = true;
                        }
                    };

                    const hideStockDropdown = () => {
                        setTimeout(() => {
                            showStockDropdown.value = false;
                        }, 150);
                    };

                    const selectStock = (stock) => {
                        createModal.form.stock_card_id = stock.id;
                        searchForm.stockName = stock.name;
                        showStockDropdown.value = false;
                    };

                    const resetFilters = () => {
                        filters.brand = '';
                        filters.version = '';
                        filters.color = '';
                        filters.seller = '';
                        filters.reason = '';
                        filters.serial_number = '';
                        fetchRefunds();
                    };

                    const openDescriptionModal = (refund) => {
                        descriptionModal.text = refund.description || '';
                        descriptionModal.visible = true;
                    };

                    const closeDescriptionModal = () => {
                        descriptionModal.visible = false;
                    };

                    const openDetailModal = async (refund) => {
                        detailModal.id = refund.id;
                        detailModal.loading = true;
                        detailModal.visible = true;
                        try {
                            const {data} = await axios.get('/stockcard/refunddetail', {params: {id: refund.id}});
                            detailModal.description = data && data.description ? data.description : '';
                        } catch (error) {
                            console.error('İade detayı alınamadı', error);
                            showToast('Detay yüklenemedi', 'error');
                        } finally {
                            detailModal.loading = false;
                        }
                    };

                    const closeDetailModal = () => {
                        if (detailModal.loading) return;
                        detailModal.visible = false;
                        detailModal.id = null;
                        detailModal.description = '';
                    };

                    const saveDetail = async () => {
                        if (!detailModal.id) return;

                        detailModal.loading = true;
                        try {
                            const payload = new URLSearchParams();
                            payload.append('id', String(detailModal.id));
                            payload.append('description', detailModal.description || '');

                            await axios.post('/stockcard/refunddetailStore', payload);
                            showToast('Açıklama güncellendi', 'success');
                            closeDetailModal();
                            fetchRefunds(pagination.value?.current_page || 1);
                        } catch (error) {
                            console.error('Açıklama güncellenemedi', error);
                            showToast('Açıklama güncellenemedi', 'error');
                        } finally {
                            detailModal.loading = false;
                        }
                    };

                    const openNewSaleModal = async (refund, type) => {
                        newSaleModal.loading = true;
                        newSaleModal.type = type;
                        try {
                            const {data} = await axios.get('/stockcard/newSale', {params: {id: refund.id}});
                            if (data && data.status === false) {
                                showToast(data.data || 'İşlem gerçekleştirilemedi', 'error');
                                return;
                            }

                            const saleData = data && data.data ? data.data : {};
                            newSaleModal.id = refund.id;
                            newSaleModal.stock_card_id = saleData.stock_card_id ? String(saleData.stock_card_id) : '';
                            newSaleModal.color_id = saleData.color_id ? String(saleData.color_id) : '';
                            newSaleModal.cost_price = saleData.cost_price || '';
                            newSaleModal.base_cost_price = saleData.base_cost_price || '';
                            newSaleModal.sale_price = saleData.sale_price || '';
                            newSaleModal.visible = true;
                        } catch (error) {
                            console.error('Satış bilgileri alınamadı', error);
                            showToast('Satış bilgileri alınamadı', 'error');
                        } finally {
                            newSaleModal.loading = false;
                        }
                    };

                    const closeNewSaleModal = (force = false) => {
                        if (newSaleModal.loading && !force) return;
                        newSaleModal.visible = false;
                        newSaleModal.id = null;
                        newSaleModal.stock_card_id = '';
                        newSaleModal.color_id = '';
                        newSaleModal.cost_price = '';
                        newSaleModal.base_cost_price = '';
                        newSaleModal.sale_price = '';
                    };

                    const saveNewSale = async () => {
                        if (!newSaleModal.id || !newSaleModal.stock_card_id) {
                            showToast('Lütfen stok seçiniz', 'error');
                            return;
                        }

                        newSaleModal.loading = true;
                        try {
                            const payload = new URLSearchParams();
                            payload.append('id', String(newSaleModal.id));
                            payload.append('type', newSaleModal.type);
                            payload.append('stock_card_id[]', newSaleModal.stock_card_id);
                            payload.append('color_id[]', newSaleModal.color_id);
                            payload.append('cost_price[]', newSaleModal.cost_price);
                            payload.append('base_cost_price[]', newSaleModal.base_cost_price);
                            payload.append('sale_price[]', newSaleModal.sale_price);

                            await axios.post('/stockcard/newSaleStore', payload);
                            showToast('Satış kaydedildi', 'success');
                            closeNewSaleModal(true);
                            fetchRefunds(pagination.value?.current_page || 1);
                        } catch (error) {
                            console.error('Satış kaydedilemedi', error);
                            showToast('Satış kaydedilemedi', 'error');
                        } finally {
                            newSaleModal.loading = false;
                        }
                    };

                    const updateRefundStatus = async (id, type) => {
                        actionLoading.value = id;
                        try {
                            await axios.get('/stockcard/refundcomfirm', {params: {id, type}});
                            showToast('İşlem başarıyla tamamlandı', 'success');
                            fetchRefunds(pagination.value?.current_page || 1);
                        } catch (error) {
                            console.error('İade durumu güncellenemedi', error);
                            showToast('İşlem başarısız', 'error');
                        } finally {
                            actionLoading.value = null;
                        }
                    };

                    const getPendingActions = (refund) => {
                        const actions = [];

                        if (hasSalePermission.value) {
                            actions.push({
                                key: `sale-${refund.id}`,
                                label: 'Satışa Çıkart',
                                class: 'btn-success',
                                icon: 'bx bx-cart',
                                loading: newSaleModal.loading,
                                onClick: () => openNewSaleModal(refund, 'seller')
                            });
                        }

                        const isUpdating = actionLoading.value === refund.id;

                        actions.push({
                            key: `service-send-${refund.id}`,
                            label: 'Servise Gönder',
                            class: 'btn-warning',
                            icon: 'bx bx-package',
                            loading: isUpdating,
                            onClick: () => updateRefundStatus(refund.id, 'service_send')
                        });

                        actions.push({
                            key: `normal-refund-${refund.id}`,
                            label: 'Normal İade',
                            class: 'btn-primary',
                            icon: 'bx bx-undo',
                            loading: isUpdating,
                            onClick: () => updateRefundStatus(refund.id, 'normal_refund')
                        });

                        actions.push({
                            key: `refund-${refund.id}`,
                            label: 'Hasarlı İade',
                            class: 'btn-danger',
                            icon: 'bx bx-error',
                            loading: isUpdating,
                            onClick: () => updateRefundStatus(refund.id, 'refund')
                        });

                        return actions;
                    };

                    const handleCreateSerialEnter = async () => {
                        const rawSerial = createModal.form.serial_number ? String(createModal.form.serial_number).trim() : '';
                        if (!rawSerial) return;

                        createModal.fetchingStock = true;
                        try {
                            const {data} = await axios.get('/stockcard/stocks-search', {params: {barcode: rawSerial}});
                            const stockData = data && data.stock ? data.stock : null;
                            if (stockData && stockData.id) {
                                createModal.form.stock_card_id = String(stockData.id);
                                searchForm.stockName = stockData.text || stockData.name || '';
                            } else {
                                showToast('Barkod için stok bulunamadı', 'error');
                            }
                        } catch (error) {
                            console.error('Barkod araması başarısız', error);
                            showToast('Barkod araması sırasında hata oluştu', 'error');
                        } finally {
                            createModal.fetchingStock = false;
                        }
                    };

                    const resetCreateForm = () => {
                        createModal.loading = false;
                        createModal.form.stock_card_id = '';
                        createModal.form.color_id = '';
                        createModal.form.reason_id = '';
                        createModal.form.serial_number = '';
                        createModal.form.description = '';
                        createModal.fetchingStock = false;
                        searchForm.stockName = '';
                        filteredStocks.value = [];
                    };

                    const openCreateModal = () => {
                        resetCreateForm();
                        createModal.visible = true;
                        nextTick(() => {
                            focusCreateSerialInput();
                        });
                    };

                    const closeCreateModal = () => {
                        if (createModal.loading) return;
                        createModal.visible = false;
                        resetCreateForm();
                    };

                    const focusCreateSerialInput = () => {
                        if (createSerialInput.value && typeof createSerialInput.value.focus === 'function') {
                            createSerialInput.value.focus();
                            if (typeof createSerialInput.value.select === 'function') {
                                createSerialInput.value.select();
                            }
                        }
                    };

                    const createRefund = async () => {
                        if (!createModal.form.stock_card_id && !createModal.form.serial_number) {
                            showToast('Lütfen stok seçin veya seri numarası girin', 'error');
                            return;
                        }

                        if (!createModal.form.reason_id) {
                            showToast('Lütfen iade nedenini seçin', 'error');
                            return;
                        }

                        createModal.loading = true;

                        try {
                            const payload = new URLSearchParams();
                            if (createModal.form.stock_card_id) {
                                payload.append('stock_id', createModal.form.stock_card_id);
                            }
                            if (createModal.form.color_id) {
                                payload.append('color_id', createModal.form.color_id);
                            }
                            payload.append('reason_id', createModal.form.reason_id);
                            payload.append('serial_number', createModal.form.serial_number || '');
                            payload.append('description', createModal.form.description || '');

                            await axios.post('/stockcard/refund', payload);
                            showToast('İade kaydedildi', 'success');
                            closeCreateModal();
                            fetchRefunds();
                        } catch (error) {
                            console.error('İade kaydedilemedi', error);
                            showToast('İade kaydedilemedi', 'error');
                        } finally {
                            createModal.loading = false;
                        }
                    };

                    onMounted(() => {
                        try {
                            const storedRoles = localStorage.getItem('roles');
                            if (storedRoles) {
                                const parsed = JSON.parse(storedRoles);
                                if (Array.isArray(parsed)) {
                                    roles.value = parsed;
                                }
                            }
                        } catch (error) {
                            console.warn('Roller okunamadı', error);
                            roles.value = [];
                        }

                        fetchRefunds();
                    });

                    return {
                        loading,
                        refunds,
                        filters,
                        options,
                        versions,
                        actionLoading,
                        descriptionModal,
                        detailModal,
                        newSaleModal,
                        createModal,
                        hasSalePermission,
                        pagination,
                        searchForm,
                        showStockDropdown,
                        searchingStock,
                        filteredStocks,
                        searchStock,
                        onStockInputFocus,
                        hideStockDropdown,
                        selectStock,
                        fetchRefunds,
                        loadPage,
                        getPageNumbers,
                        getStatusText,
                        getStatusBadgeClass,
                        resetFilters,
                        openDescriptionModal,
                        closeDescriptionModal,
                        openDetailModal,
                        closeDetailModal,
                        saveDetail,
                        openNewSaleModal,
                        closeNewSaleModal,
                        saveNewSale,
                        updateRefundStatus,
                        getPendingActions,
                        openCreateModal,
                        closeCreateModal,
                        createRefund,
                        handleCreateSerialEnter,
                        createSerialInput
                    };
                }
            }).mount('#refundApp');
        })();
    </script>
@endsection
