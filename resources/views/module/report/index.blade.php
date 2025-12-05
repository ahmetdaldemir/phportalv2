@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/css/list-page-base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/daterangepicker/daterangepicker.css') }}"/>
    <style>
        .report-page__title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .report-page__subtitle {
            color: var(--bs-secondary-color);
        }
        .filter-card {
            border: 1px solid var(--bs-border-color);
            border-radius: 1rem;
        }
        .filter-card .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--bs-secondary-color);
        }
        .summary-card {
            border-radius: 1rem;
            padding: 1.25rem;
            background: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            height: 100%;
        }
        .summary-card__label {
            font-size: 0.85rem;
            color: var(--bs-secondary-color);
        }
        .summary-card__value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .summary-card__trend {
            font-size: 0.8rem;
        }
        .report-table thead th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: .05em;
            color: var(--bs-secondary-color);
            border-bottom-width: 1px;
        }
        .report-table tbody td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }
        .brand-stack span {
            display: block;
            font-size: 0.8rem;
        }
        .table-meta {
            font-size: 0.75rem;
            color: var(--bs-secondary-color);
        }
        [v-cloak] {
            display: none;
        }
        @media (max-width: 992px) {
            .summary-card__value {
                font-size: 1.2rem;
            }
        }
    </style>
@endsection

@php
    $defaultFilters = [
        'date1' => $sendData->date1 ?? null,
        'date2' => $sendData->date2 ?? null,
        'brand' => $sendData->brand ?? '',
        'version' => $sendData->version ?? '',
        'seller' => $sendData->seller ?? '',
        'category' => $sendData->category ?? '',
        'sales_person' => $sendData->sales_person ?? '',
        'technical_person' => $sendData->technical_person ?? '',
    ];
@endphp

@section('content')
    <div id="reportApp"
         data-brands='@json($brands)'
         data-sellers='@json($sellers)'
         data-users='@json($users)'
         data-types='@json($types)'
         data-defaults='@json($defaultFilters)'
         data-routes='@json([
            "data" => route('report.data'),
            "export" => route('report.export'),
         ])'
         class="container-xxl flex-grow-1 container-p-y"
         v-cloak>
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-3">
            <div>
                <div class="text-muted text-uppercase fw-semibold mb-1">Raporlar</div>
                <div class="report-page__title">Satış Performans Raporu</div>
                <div class="report-page__subtitle">Tüm şubeler, markalar ve personeller için detaylı satış verileri</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="resetFilters" :disabled="loading">
                    <i class="bx bx-reset me-1"></i> Temizle
                </button>
                <button type="button" class="btn btn-primary btn-sm" @click="fetchReport" :disabled="loading">
                    <i class="bx bx-search me-1"></i> Filtrele
                </button>
                <button type="button" class="btn btn-success btn-sm" @click="downloadExcel" :disabled="loading || report.length === 0">
                    <i class="bx bx-download me-1"></i> Excel
                </button>
            </div>
        </div>

        <div class="card filter-card shadow-sm mb-4">
            <div class="card-body">
                <form class="row gy-3" @submit.prevent="fetchReport">
                    <div class="col-lg-4">
                        <label class="form-label">Başlangıç / Bitiş Tarihi</label>
                        <div class="d-flex gap-2">
                                <input type="date" class="filter-input single-datepicker" v-model="filters.date1"
                                    @change="fetchReport()">
                                <input type="date" class="filter-input single-datepicker" v-model="filters.date2"
                                    @change="fetchReport()">
                            </div>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Marka</label>
                        <select name="brand" class="form-select" v-model="filters.brand" @change="fetchReport()">
                            <option value="">Tümü</option>
                            <option v-for="brand in options.brands"
                                    :key="`brand-${brand.id}`"
                                    :value="brand.id"
                                    v-text="brand.name"></option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Model</label>
                        <select name="version" id="version_id" @change="fetchReport()" class="form-select" v-model="filters.version">
                            <option value="">Tümü</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Şube</label>
                        <select name="seller" class="form-select"  @change="fetchReport()" v-model="filters.seller">
                            <option value="">Tümü</option>
                            <option v-for="seller in options.sellers"
                                    :key="`seller-${seller.id}`"
                                    :value="seller.id"
                                    v-text="seller.name"></option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select" @change="fetchReport()" v-model="filters.category">
                            <option value="">Tümü</option>
                            <option v-for="(typeLabel, typeKey) in options.types"
                                    :key="`category-${typeKey}`"
                                    :value="typeKey"
                                    v-text="typeLabel"></option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Satışı Yapan Personel</label>
                        <select name="sales_person" class="form-select" @change="fetchReport()" v-model="filters.sales_person">
                            <option value="">Tümü</option>
                            <option v-for="user in options.users"
                                    :key="`sales-${user.id}`"
                                    :value="user.id"
                                    v-text="user.name"></option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Teknisyen</label>
                        <select name="technical_person" class="form-select" @change="fetchReport()" v-model="filters.technical_person">
                            <option value="">Tümü</option>
                            <option v-for="user in options.users"
                                    :key="`technical-${user.id}`"
                                    :value="user.id"
                                    v-text="user.name"></option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4" v-for="card in summaryCards" :key="card.key">
                <div class="summary-card">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="summary-card__label" v-text="card.label"></span>
                        <span :class="card.badgeClasses" v-text="card.badgeText"></span>
                    </div>
                    <div class="summary-card__value">
                        [[ formatCurrency(card.value) ]] <small class="fs-6">TL</small>
                    </div>
                    <div class="summary-card__trend" :class="card.trendClasses" v-text="card.trendText"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">Satış Kalemleri</div>
                    <small class="text-muted">Filtre kriterlerine göre listelenmiş [[ formatNumber(totals.itemsCount) ]] kayıt</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" type="button" @click="fetchReport" :disabled="loading">
                        <i class="bx bx-refresh me-1"></i> Yenile
                    </button>
                </div>
            </div>

            <div class="table-responsive position-relative">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>

                <div v-if="errorMessage" class="alert alert-danger mb-0" role="alert" v-text="errorMessage"></div>

                <table v-if="!loading && report.length > 0" class="table table-hover align-middle report-table mb-0">
                    <thead>
                    <tr>
                        <th  style="width: 15%">Personel</th>
                        <th style="width: 25%">Tip</th>
                        <th style="width: 20%">Marka / Model</th>
                        <th style="width: 10%" class="text-end">Alış</th>
                        <th style="width: 10%" class="text-end">Satış</th>
                        <th style="width: 10%" class="text-end">Kar</th>
                        <th style="width: 10%">Tarih</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in report" :key="item.id" style="    border-bottom: 1px solid #ccc;">
                        <td>
                            <div class="fw-semibold" v-text="item.person || 'Personel Yok'"></div>
                            <div class="table-meta">
                                Seri: [[ item.serial || '—' ]]
                            </div>
                            <div class="table-meta">
                                # [[ item.id ]]
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold" v-text="item.product_name || 'Personel Yok'"></div>
                            <span class="status-badge bg-light text-dark border" v-text="item.type_label"></span>
                            <div class="table-meta">
                                # [[ item.category_path || '—' ]]
                            </div>
                        </td>
                        <td>
                            <div class="brand-stack fw-semibold" v-text="item.brand"></div>
                            <span class="brand-stack text-muted" style="font-size: 0.8rem;" v-text="item.model"></span>
                        </td>
                        <td class="text-end">[[ formatCurrency(item.cost_price) ]] TL</td>
                        <td class="text-end fw-semibold">[[ formatCurrency(item.sale_price) ]] TL</td>
                        <td class="text-end">
                            <span class="fw-semibold" :class="item.profit >= 0 ? 'text-success' : 'text-danger'">
                                [[ formatCurrency(item.profit) ]] TL
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold" v-text="item.created_at_date || '-'"></div>
                            <div class="table-meta" v-text="item.created_at_time || ''"></div>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr class="bg-primary text-white">
                        <td colspan="4" class="fw-semibold">Toplamlar</td>
                        <td class="text-end fw-semibold">[[ formatCurrency(totals.totalCostPrice) ]] TL</td>
                        <td class="text-end fw-semibold">[[ formatCurrency(totals.totalBaseCostPrice) ]] TL</td>
                        <td class="text-end fw-semibold">[[ formatCurrency(totals.totalSale) ]] TL</td>
                        <td class="text-end fw-semibold">[[ formatCurrency(totals.totalProfit) ]] TL</td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>

                <div v-if="!loading && report.length === 0" class="text-center py-5">
                    <div class="text-muted">Seçilen filtrelere göre kayıt bulunamadı.</div>
                    <small class="text-muted">Filtreleri değiştirip tekrar deneyebilirsiniz.</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script>
    <script>
        const { createApp } = Vue;

        createApp({
            delimiters: ['[[', ']]'],
            data() {
                const host = document.getElementById('reportApp');
                const defaults = JSON.parse(host.dataset.defaults || '{}');
                const startOfMonth = moment().startOf('month').format('YYYY-MM-DD');
                const endOfMonth = moment().endOf('month').format('YYYY-MM-DD');
                const normalizeDate = (value, fallback) => {
                    if (!value) {
                        return fallback;
                    }
                    const parsed = moment(value, ['DD-MM-YYYY', 'YYYY-MM-DD'], true);
                    return parsed.isValid() ? parsed.format('YYYY-MM-DD') : fallback;
                };

                const baseFilters = {
                    date1: normalizeDate(defaults.date1, startOfMonth),
                    date2: normalizeDate(defaults.date2, endOfMonth),
                    brand: defaults.brand || '',
                    version: defaults.version || '',
                    seller: defaults.seller || '',
                    category: defaults.category || '',
                    sales_person: defaults.sales_person || '',
                    technical_person: defaults.technical_person || '',
                };

                return {
                    filters: { ...baseFilters },
                    defaultFilters: { ...baseFilters },
                    options: {
                        brands: JSON.parse(host.dataset.brands || '[]'),
                        sellers: JSON.parse(host.dataset.sellers || '[]'),
                        users: JSON.parse(host.dataset.users || '[]'),
                        types: JSON.parse(host.dataset.types || '{}'),
                    },
                    report: [],
                    totals: {
                        totalSale: 0,
                        totalCostPrice: 0,
                        totalBaseCostPrice: 0,
                        totalProfit: 0,
                        itemsCount: 0,
                    },
                    loading: false,
                    errorMessage: '',
                    routes: JSON.parse(host.dataset.routes || '{}'),
                };
            },
            computed: {
                summaryCards() {
                    return [
                        {
                            key: 'sale',
                            label: 'Toplam Satış',
                            badgeText: 'TL',
                            badgeClasses: 'badge bg-primary-subtle text-primary',
                            value: this.totals.totalSale,
                            trendText: `Satılan kalem: ${this.formatNumber(this.totals.itemsCount)}`,
                            trendClasses: 'text-success',
                        },
                     
                        {
                            key: 'purchase',
                            label: 'Alış Fiyatı',
                            badgeText: 'TL',
                            badgeClasses: 'badge bg-warning-subtle text-warning',
                            value: this.totals.totalCostPrice,
                            trendText: 'Toplam alış maliyeti',
                            trendClasses: 'text-muted',
                        },
                        {
                            key: 'profit',
                            label: 'Net Kar',
                            badgeText: 'TL',
                            badgeClasses: 'badge bg-success-subtle text-success',
                            value: this.totals.totalProfit,
                            trendText: this.totals.totalProfit >= 0 ? 'Pozitif satış performansı' : 'Negatif satış performansı',
                            trendClasses: this.totals.totalProfit >= 0 ? 'text-success' : 'text-danger',
                        },
                    ];
                },
            },
            mounted() {
                this.fetchReport();
                this.bindDatePickerEvents();
            },
            methods: {
                fetchReport() {
                    this.loading = true;
                    this.errorMessage = '';
                    const params = this.buildRequestParams();

                    axios.get(this.routes.data, { params })
                        .then(response => {
                            if (response.data && response.data.success) {
                                this.report = response.data.items || [];
                                this.totals = Object.assign({
                                    totalSale: 0,
                                    totalCostPrice: 0,
                                    totalBaseCostPrice: 0,
                                    totalProfit: 0,
                                    itemsCount: 0,
                                }, response.data.totals || {});
                            } else {
                                this.errorMessage = response.data.message || 'Veriler yüklenemedi.';
                            }
                        })
                        .catch(() => {
                            this.errorMessage = 'Veriler yüklenirken bir hata oluştu.';
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                resetFilters() {
                    this.filters = { ...this.defaultFilters };
                    this.fetchReport();
                },
                buildRequestParams() {
                    const params = { ...this.filters };
                    if (params.date1) {
                        params.date1 = moment(params.date1, 'YYYY-MM-DD').format('DD-MM-YYYY');
                    }
                    if (params.date2) {
                        params.date2 = moment(params.date2, 'YYYY-MM-DD').format('DD-MM-YYYY');
                    }
                    return params;
                },
                formatCurrency(value) {
                    const number = Number(value) || 0;
                    return new Intl.NumberFormat('tr-TR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(number);
                },
                formatNumber(value) {
                    return new Intl.NumberFormat('tr-TR').format(Number(value) || 0);
                },
                downloadExcel() {
                    const params = new URLSearchParams(this.filters).toString();
                    window.open(`${this.routes.export}?${params}`, '_blank');
                },
                bindDatePickerEvents() {
                    // Native date inputs handle their own changes
                },
            },
        }).mount('#reportApp');
    </script>
@endsection
