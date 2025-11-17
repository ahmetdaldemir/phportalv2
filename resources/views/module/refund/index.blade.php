@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/list-page-base.css') }}">
    <style>
        .card-vue {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(30, 60, 114, 0.06);
        }

        /* Autocomplete - stok arama (Yeni İade Oluştur) */
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

        .autocomplete-no-results i {
            font-size: 1rem;
        }
    </style>
@endsection

@section('content')
    <div id="refundApp" class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">İade /</span> İade Listesi</h4>

        <div class="card card-vue mb-4">
            <div class="card-body">
                <form class="row g-3" @submit.prevent="fetchRefunds">
                    <div class="col-md-2">
                        <label class="form-label">Marka</label>
                        <select v-model="filters.brand" class="form-select">
                            <option value="">Tümü</option>
                            <option v-for="brand in options.brands" :key="`brand-${brand.id}`"
                                    :value="String(brand.id)">@{{ brand.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Model</label>
                        <select v-model="filters.version" class="form-select">
                            <option value="">Tümü</option>
                            <option v-for="version in versions" :key="`version-${version.id}`"
                                    :value="String(version.id)">@{{ version.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Renk</label>
                        <select v-model="filters.color" class="form-select">
                            <option value="">Tümü</option>
                            <option v-for="color in options.colors" :key="`color-${color.id}`"
                                    :value="String(color.id)">@{{ color.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Şube</label>
                        <select v-model="filters.seller" class="form-select">
                            <option value="">Tümü</option>
                            <option v-for="seller in options.sellers" :key="`seller-${seller.id}`"
                                    :value="String(seller.id)">@{{ seller.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">İade Nedeni</label>
                        <select v-model="filters.reason" class="form-select">
                            <option value="">Tümü</option>
                            <option v-for="reason in options.reasons" :key="`reason-${reason.id}`"
                                    :value="String(reason.id)">@{{ reason.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Seri Numarası</label>
                        <input v-model.trim="filters.serial_number" type="text" class="form-control"
                               placeholder="Seri / Barkod">
                    </div>
                    <div class="col-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-sm btn-outline-primary" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                            Ara
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" :disabled="loading"
                                @click="resetFilters">
                            Sıfırla
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" :disabled="loading"
                                @click="openCreateModal">
                            Yeni İade Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-vue">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table" style="font-size: 13px;">
                        <thead>
                        <tr>
                            <th>Stok Adı</th>
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Renk</th>
                            <th>İade Nedeni</th>
                            <th>Seri No</th>
                            <th>Açıklama</th>
                            <th>Detay</th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        <tr v-if="loading">
                            <td colspan="9" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Yükleniyor...</span>
                                </div>
                            </td>
                        </tr>
                        <template v-else>
                            <tr v-if="refunds.length === 0">
                                <td colspan="9" class="text-center text-muted py-4">Kayıt bulunamadı</td>
                            </tr>
                            <tr v-for="refund in refunds" :key="`refund-${refund.id}`">
                                <td><strong>@{{ refund.stock && refund.stock.name ? refund.stock.name : 'Bulunamadı'
                                        }}</strong></td>
                                <td><strong>@{{ refund.stock && refund.stock.brand && refund.stock.brand.name ?
                                        refund.stock.brand.name : 'Bulunamadı' }}</strong></td>
                                <td><strong>@{{ refund.stock && refund.stock.name ? refund.stock.name : 'Bulunamadı'
                                        }}</strong></td>
                                <td><strong>@{{ refund.color && refund.color.name ? refund.color.name : 'Bulunamadı'
                                        }}</strong></td>
                                <td><strong>@{{ refund.reason && refund.reason.name ? refund.reason.name : 'Bulunamadı'
                                        }}</strong></td>
                                <td><strong>@{{ refund.serial_number ? refund.serial_number : 'Bulunamadı' }}</strong>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm text-nowrap"
                                            @click="openDescriptionModal(refund)">
                                        <i class="bx bx-text"></i>
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm text-nowrap"
                                            @click="openDetailModal(refund)">
                                        <i class="bx bx-text"></i>
                                    </button>
                                </td>
                                <td>
                                    <template v-if="refund.status === 1">
                                        Satışa Alındı
                                    </template>
                                    <template v-else-if="refund.status === 3">
                                        Hasarlı İade Alındı
                                    </template>
                                    <template v-else-if="refund.status === 4">
                                        Müşteriye Teslim Edildi
                                    </template>
                                    <template v-else-if="refund.status === 5">
                                        <button type="button" class="btn btn-sm btn-warning"
                                                :disabled="actionLoading === refund.id"
                                                @click="updateRefundStatus(refund.id, 'service_return')">
                                            <span v-if="actionLoading === refund.id"
                                                  class="spinner-border spinner-border-sm me-1"></span>
                                            Servisten Geldi
                                        </button>
                                    </template>
                                    <template v-else-if="refund.status === 0">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button
                                                    v-for="action in getPendingActions(refund)"
                                                    :key="action.key"
                                                    type="button"
                                                    class="btn btn-sm"
                                                    :class="action.class"
                                                    :disabled="action.loading"
                                                    @click="action.onClick()"
                                            >
                                                <span v-if="action.loading"
                                                      class="spinner-border spinner-border-sm me-1"></span>
                                                @{{ action.label }}
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else-if="refund.status === 6">
                                        <button type="button" class="btn btn-sm btn-warning"
                                                :disabled="actionLoading === refund.id"
                                                @click="updateRefundStatus(refund.id, 'delivered')">
                                            <span v-if="actionLoading === refund.id"
                                                  class="spinner-border spinner-border-sm me-1"></span>
                                            Teslim Edildi
                                        </button>
                                    </template>
                                    <template v-else>
                                        Beklemede
                                    </template>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-if="descriptionModal.visible">
            <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog"
                 @click.self="closeDescriptionModal">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Açıklama</h5>
                            <button type="button" class="btn-close" @click="closeDescriptionModal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">@{{ descriptionModal.text || 'Açıklama bulunamadı.' }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" @click="closeDescriptionModal">Kapat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <div v-if="detailModal.visible">
            <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog"
                 @click.self="closeDetailModal">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form @submit.prevent="saveDetail">
                            <div class="modal-header">
                                <h5 class="modal-title">Açıklama Düzenle</h5>
                                <button type="button" class="btn-close" @click="closeDetailModal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Açıklama</label>
                                    <textarea v-model="detailModal.description" class="form-control"
                                              rows="4"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" @click="closeDetailModal"
                                        :disabled="detailModal.loading">Kapat
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="detailModal.loading">
                                    <span v-if="detailModal.loading"
                                          class="spinner-border spinner-border-sm me-1"></span>
                                    Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <div v-if="createModal.visible">
            <div id="createRefundModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog"
                 @click.self="closeCreateModal">
                <div class="modal-dialog modal-dialog-centered" role="document">
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
                                                <!-- Loading State -->
                                                <div v-if="searchingStock" class="autocomplete-loading">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                        <span class="visually-hidden">Aranıyor...</span>
                                                    </div>
                                                    <span class="ms-2">Aranıyor...</span>
                                                </div>

                                                <!-- Results -->
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
                                                                    <i class="bx bx-category-alt"></i> @{{ stock.category_name
                                                                    }}
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

                                                <!-- No Results -->
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
                                        :disabled="createModal.loading || createModal.fetchingStock">Kapat
                                </button>
                                <button type="submit" class="btn btn-primary"
                                        :disabled="createModal.loading || createModal.fetchingStock">
                                    <span v-if="createModal.loading"
                                          class="spinner-border spinner-border-sm me-1"></span>
                                    Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <div v-if="newSaleModal.visible">
            <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog"
                 @click.self="closeNewSaleModal">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                                        :disabled="newSaleModal.loading">Kapat
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="newSaleModal.loading">
                                    <span v-if="newSaleModal.loading"
                                          class="spinner-border spinner-border-sm me-1"></span>
                                    Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </div>
@endsection

@section('custom-js')
    {{-- jQuery & Select2 (for stok arama) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Vue & Axios --}}
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Daterangepicker dependencies (global forms-pickers.js bunu bekliyor) --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script>
    <script>
        (function () {
            const {createApp, ref, reactive, computed, onMounted, onBeforeUnmount, watch, nextTick} = Vue;

            createApp({
                setup() {
                    const loading = ref(false);
                    const refunds = ref([]);
                    const actionLoading = ref(null);
                    const createStockSelect = ref(null);
                    const createSerialInput = ref(null);
                    const select2Initialized = ref(false);

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

                    // Autocomplete state for "Yeni İade Oluştur" stok arama inputu
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
                        destroyCreateStockSelect();
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
                            if (Array.isArray(data)) {
                                versions.value = data.map((item) => ({id: item.id, name: item.name}));
                            } else {
                                versions.value = [];
                            }
                        } catch (error) {
                            console.error('Versiyonlar yüklenemedi', error);
                            versions.value = [];
                        }
                    };

                    watch(() => filters.brand, (brandId) => {
                        filters.version = '';
                        loadVersions(brandId);
                    });

                    watch(() => createModal.visible, (visible) => {
                        if (visible) {
                            nextTick(() => {
                                initCreateStockSelect();
                                focusCreateSerialInput();
                            });
                        } else {
                            destroyCreateStockSelect();
                        }
                    });

                    watch(() => options.stocks, () => {
                        if (createModal.visible) {
                            nextTick(() => {
                                initCreateStockSelect();
                            });
                        }
                    }, {deep: true});

                    const fetchRefunds = async () => {
                        loading.value = true;
                        try {
                            const {data} = await axios.get('/stockcard/refunds/data', {
                                params: sanitizeFilters()
                            });

                            refunds.value = Array.isArray(data.refunds) ? data.refunds : [];

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
                        } finally {
                            loading.value = false;
                        }
                    };

                    // Autocomplete helpers
                    const mapAutocompleteItem = (stock) => {
                        if (!stock) return null;
                        return {
                            id: String(stock.id),
                            name: stock.text || stock.name || '',
                            brand_name: stock.brand_name || '',
                            version_name: stock.version_names || '',
                            category_name: stock.category_name || '',
                            quantity: typeof stock.quantity !== 'undefined' ? Number(stock.quantity) : 0
                        };
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
                            const { data } = await axios.get('/stockcard/stocks-search', {
                                params: { q: term }
                            });

                            const rows = Array.isArray(data) ? data : (Array.isArray(data?.data) ? data.data : []);
                            filteredStocks.value = rows
                                .map(mapAutocompleteItem)
                                .filter(Boolean);
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
                        // Click seçimlerinin tamamlanabilmesi için küçük bir gecikme
                        setTimeout(() => {
                            showStockDropdown.value = false;
                        }, 150);
                    };

                    const selectStock = (stock) => {
                        const item = mapAutocompleteItem(stock);
                        if (!item) return;

                        createModal.form.stock_card_id = item.id;
                        searchForm.stockName = item.name;
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
                        if (detailModal.loading) {
                            return;
                        }
                        detailModal.visible = false;
                        detailModal.id = null;
                        detailModal.description = '';
                    };

                    const saveDetail = async () => {
                        if (!detailModal.id) {
                            return;
                        }

                        detailModal.loading = true;
                        try {
                            const payload = new URLSearchParams();
                            payload.append('id', String(detailModal.id));
                            payload.append('description', detailModal.description || '');

                            await axios.post('/stockcard/refunddetailStore', payload);
                            showToast('Açıklama güncellendi', 'success');
                            detailModal.visible = false;
                            detailModal.id = null;
                            detailModal.description = '';
                            fetchRefunds();
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
                        if (newSaleModal.loading && !force) {
                            return;
                        }
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
                            fetchRefunds();
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
                            fetchRefunds();
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
                                loading: newSaleModal.loading,
                                onClick: () => openNewSaleModal(refund, 'seller')
                            });
                        }

                        const isUpdating = actionLoading.value === refund.id;

                        actions.push({
                            key: `service-send-${refund.id}`,
                            label: 'Servise Gönder',
                            class: 'btn-warning',
                            loading: isUpdating,
                            onClick: () => updateRefundStatus(refund.id, 'service_send')
                        });


                        actions.push({
                            key: `normal-refund-${refund.id}`,
                            label: 'Normal Iade Gönder',
                            class: 'btn-primary',
                            loading: isUpdating,
                            onClick: () => updateRefundStatus(refund.id, 'normal_refund')
                        });

                        actions.push({
                            key: `refund-${refund.id}`,
                            label: 'Hasarlı İade',
                            class: 'btn-danger',
                            loading: isUpdating,
                            onClick: () => updateRefundStatus(refund.id, 'refund')
                        });

                        return actions;
                    };

                    const mapStockToSelectItem = (stock) => {
                        if (!stock) {
                            return null;
                        }

                        const brandName = stock.brand_name ?? (stock.brand?.name ?? '');
                        const categoryName = stock.category_name ?? (stock.category?.name ?? '');
                        let versionNames = '';

                        if (Array.isArray(stock.version_names)) {
                            versionNames = stock.version_names.filter(Boolean).join(', ');
                        } else if (typeof stock.version_names === 'string') {
                            versionNames = stock.version_names;
                        }

                        return {
                            id: String(stock.id),
                            text: stock.text || stock.name || '',
                            brand_name: brandName,
                            category_name: categoryName,
                            version_names: versionNames,
                            sku: stock.sku || '',
                            barcode: stock.barcode || ''
                        };
                    };

                    const buildStockOptionMarkup = (item) => {
                        const categoryLine = item.category_name
                            ? `<div class="text-muted small">${item.category_name}</div>`
                            : '';
                        const brandLine = item.brand_name
                            ? `<div class="text-muted small">${item.brand_name}${item.version_names ? ' · ' + item.version_names : ''}</div>`
                            : (item.version_names ? `<div class="text-muted small">${item.version_names}</div>` : '');

                        const metaLineParts = [];
                        if (item.sku) {
                            metaLineParts.push(`SKU: ${item.sku}`);
                        }
                        if (item.barcode) {
                            metaLineParts.push(`Barkod: ${item.barcode}`);
                        }
                        const metaLine = metaLineParts.length
                            ? `<div class="text-muted small">${metaLineParts.join(' · ')}</div>`
                            : '';

                        return `
                            <div class="select2-result-stock">
                                <div class="fw-bold">${item.text}</div>
                                ${categoryLine || ''}
                                ${brandLine || ''}
                                ${metaLine}
                            </div>
                        `;
                    };

                    const formatStockResult = (item) => {
                        if (!item || !item.id) {
                            return item?.text ?? '';
                        }

                        return buildStockOptionMarkup(item);
                    };

                    const formatStockSelection = (item) => {
                        if (!item || !item.id) {
                            return item?.text ?? '';
                        }

                        const brand = item.brand_name || (item.element ? item.element.getAttribute('data-brand-name') : '');
                        const versions = item.version_names || (item.element ? item.element.getAttribute('data-version-names') : '');
                        const category = item.category_name || (item.element ? item.element.getAttribute('data-category-name') : '');
                        const summaryParts = [category, brand, versions].filter(Boolean);
                        const summary = summaryParts.length ? ` — ${summaryParts.join(' · ')}` : '';

                        return `${item.text}${summary}`;
                    };

                    const destroyCreateStockSelect = () => {
                        if (select2Initialized.value && createStockSelect.value && typeof window.jQuery !== 'undefined' && window.jQuery.fn.select2) {
                            const $element = window.jQuery(createStockSelect.value);
                            $element.off('.select2Refund');
                            $element.select2('destroy');
                        }
                        select2Initialized.value = false;
                    };

                    const setCreateStockSelection = (stockItem) => {
                        if (!stockItem || !stockItem.id) {
                            return;
                        }

                        createModal.form.stock_card_id = String(stockItem.id);

                        if (!createStockSelect.value) {
                            return;
                        }

                        if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) {
                            createStockSelect.value.value = String(stockItem.id);
                            return;
                        }

                        if (!select2Initialized.value) {
                            initCreateStockSelect();
                        }

                        const $element = window.jQuery(createStockSelect.value);
                        if (!select2Initialized.value) {
                            return;
                        }

                        let $option = $element.find(`option[value="${stockItem.id}"]`);
                        if (!$option.length) {
                            $option = window.jQuery(new Option(stockItem.text, stockItem.id, true, true));
                            $element.append($option);
                        }

                        $option.attr('data-brand-name', stockItem.brand_name || '');
                        $option.attr('data-version-names', stockItem.version_names || '');
                        $option.attr('data-category-name', stockItem.category_name || '');
                        $option.attr('data-barcode', stockItem.barcode || '');
                        $option.attr('data-sku', stockItem.sku || '');

                        $element.val(String(stockItem.id)).trigger('change.select2Refund');
                    };

                    const initCreateStockSelect = () => {
                        if (!createStockSelect.value || typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) {
                            return;
                        }

                        destroyCreateStockSelect();

                        const $element = window.jQuery(createStockSelect.value);
                        const dropdownParent = window.jQuery('#createRefundModal');

                        $element.select2({
                            dropdownParent: dropdownParent.length ? dropdownParent : undefined,
                            width: '100%',
                            placeholder: 'Stok arayın (en az 2 karakter)',
                            allowClear: true,
                            minimumInputLength: 2,
                            language: {
                                searching: () => 'Aranıyor...',
                                noResults: () => 'Sonuç bulunamadı'
                            },
                            ajax: {
                                url: '/stockcard/stocks-search',
                                dataType: 'json',
                                delay: 250,
                                data: params => ({q: params.term}),
                                processResults: data => ({
                                    results: (Array.isArray(data) ? data : []).map(item => ({
                                        id: String(item.id),
                                        text: item.text,
                                        brand_name: item.brand_name || '',
                                        category_name: item.category_name || '',
                                        version_names: item.version_names || '',
                                        sku: item.sku || '',
                                        barcode: item.barcode || ''
                                    }))
                                })
                            },
                            templateResult: formatStockResult,
                            templateSelection: formatStockSelection,
                            escapeMarkup: markup => markup
                        });

                        $element.on('change.select2Refund', (event) => {
                            createModal.form.stock_card_id = event.target.value || '';
                        });

                        if (createModal.form.stock_card_id) {
                            const cached = options.stocks.find(stock => String(stock.id) === String(createModal.form.stock_card_id));
                            if (cached) {
                                const formatted = mapStockToSelectItem(cached);
                                if (formatted) {
                                    setCreateStockSelection(formatted);
                                }
                            }
                        }

                        select2Initialized.value = true;
                    };

                    const focusCreateSerialInput = () => {
                        if (createSerialInput.value && typeof createSerialInput.value.focus === 'function') {
                            createSerialInput.value.focus();
                            if (typeof createSerialInput.value.select === 'function') {
                                createSerialInput.value.select();
                            }
                        }
                    };

                    const handleCreateStockChange = (event) => {
                        createModal.form.stock_card_id = event?.target?.value || '';
                    };

                    const handleCreateSerialEnter = async () => {
                        const rawSerial = createModal.form.serial_number ? String(createModal.form.serial_number).trim() : '';
                        if (!rawSerial) {
                            return;
                        }

                        createModal.fetchingStock = true;
                        try {
                            const {data} = await axios.get('/stockcard/stocks-search', {
                                params: {barcode: rawSerial}
                            });

                            const stockData = data && data.stock ? data.stock : null;
                            if (stockData && stockData.id) {
                                const formatted = mapStockToSelectItem(stockData);
                                if (formatted) {
                                    setCreateStockSelection(formatted);
                                }
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

                        if (typeof window.jQuery !== 'undefined' && createStockSelect.value && window.jQuery.fn.select2 && select2Initialized.value) {
                            nextTick(() => {
                                const $element = window.jQuery(createStockSelect.value);
                                $element.val(null).trigger('change.select2Refund');
                            });
                        }
                    };

                    const openCreateModal = () => {
                        resetCreateForm();
                        createModal.visible = true;
                        nextTick(() => {
                            initCreateStockSelect();
                            focusCreateSerialInput();
                        });
                    };

                    const closeCreateModal = () => {
                        if (createModal.loading) {
                            return;
                        }
                        destroyCreateStockSelect();
                        createModal.visible = false;
                        resetCreateForm();
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
                            createModal.visible = false;
                            destroyCreateStockSelect();
                            resetCreateForm();
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
                        // autocomplete state & methods
                        searchForm,
                        showStockDropdown,
                        searchingStock,
                        filteredStocks,
                        searchStock,
                        onStockInputFocus,
                        hideStockDropdown,
                        selectStock,
                        // other handlers
                        fetchRefunds,
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
                        handleCreateStockChange
                    };
                }
            }).mount('#refundApp');
        })();
    </script>
@endsection
