@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/table-page-framework.css')}}">
    <style>
        .invoice-detail-modal .modal-dialog {
            max-width: 1020px;
        }

        .invoice-detail-modal .modal-content {
            border: none;
            border-radius: 26px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .invoice-detail-modal .modal-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 55%, #0f172a 100%);
            border-bottom: none;
            padding: 26px 30px 22px;
            color: #fff;
        }

        .invoice-detail-modal .detail-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .invoice-detail-modal .detail-header-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .invoice-detail-modal .modal-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .invoice-detail-modal .detail-header-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .invoice-detail-modal .detail-header-meta small {
            opacity: 0.85;
        }

        .invoice-detail-modal .detail-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-soft-primary {
            background: rgba(59, 130, 246, 0.12);
            color: #e0ecff;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }

        .badge-soft-success {
            background: rgba(34, 197, 94, 0.14);
            color: #dcfce7;
            border: 1px solid rgba(34, 197, 94, 0.22);
        }

        .badge-soft-warning {
            background: rgba(250, 204, 21, 0.15);
            color: #fef3c7;
            border: 1px solid rgba(250, 204, 21, 0.22);
        }

        .badge-soft-danger {
            background: rgba(248, 113, 113, 0.1);
            color: #fee2e2;
            border: 1px solid rgba(248, 113, 113, 0.22);
        }

        .badge-soft-secondary {
            background: rgba(148, 163, 184, 0.16);
            color: #f1f5f9;
            border: 1px solid rgba(148, 163, 184, 0.24);
        }

        .invoice-detail-modal .modal-body {
            background: #f8fafc;
            padding: 28px 30px;
        }

        .invoice-detail-modal .invoice-detail-body {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .invoice-detail-modal .detail-info-card {
            background: #fff;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            height: 100%;
        }

        .invoice-detail-modal .detail-info-card .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 18px;
        }

        @media (max-width: 575.98px) {
            .invoice-detail-modal .detail-info-card .info-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }

        .invoice-detail-modal .detail-info-card .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.6px;
        }

        .invoice-detail-modal .detail-info-card .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 4px;
        }

        .invoice-detail-modal .detail-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .invoice-detail-modal .detail-summary-card {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(30, 64, 175, 0.08));
            border-radius: 20px;
            padding: 20px;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            gap: 6px;
            border: 1px solid rgba(59, 130, 246, 0.08);
        }

        .invoice-detail-modal .detail-summary-card.success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(22, 163, 74, 0.08));
            border-color: rgba(34, 197, 94, 0.12);
        }

        .invoice-detail-modal .detail-summary-card.warning {
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.12), rgba(234, 179, 8, 0.1));
            border-color: rgba(250, 204, 21, 0.16);
        }

        .invoice-detail-modal .detail-summary-card.profit {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(14, 116, 144, 0.08));
            border-color: rgba(14, 165, 233, 0.12);
        }

        .invoice-detail-modal .detail-summary-card .summary-label {
            font-size: 13px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: rgba(15, 23, 42, 0.7);
        }

        .invoice-detail-modal .detail-summary-card .summary-value {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }

        .invoice-detail-modal .detail-summary-card .summary-trend {
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(15, 23, 42, 0.7);
        }

        .invoice-detail-modal .detail-table-card {
            border-radius: 22px;
            overflow: hidden;
            border: none;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        .invoice-detail-modal .detail-table-card .card-header {
            background: #0f172a;
            color: #f8fafc;
            border-bottom: none;
            padding: 18px 22px;
        }

        .invoice-detail-modal .detail-table-card .card-header h6 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.4px;
        }

        .invoice-detail-modal .detail-table-card .table {
            margin: 0;
        }

        .invoice-detail-modal .detail-table-card thead {
            background: #f1f5f9;
        }

        .invoice-detail-modal .detail-table-card th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: none;
            color: #475569;
            padding-top: 14px;
            padding-bottom: 14px;
        }

        .invoice-detail-modal .detail-table-card td {
            border-top: 1px solid #eef2f7;
            font-size: 14px;
            vertical-align: middle;
            color: #0f172a;
        }

        .invoice-detail-modal .detail-table-card tbody tr:hover {
            background: rgba(59, 130, 246, 0.08);
        }

        .invoice-detail-modal .detail-table-card code {
            background: rgba(15, 23, 42, 0.08);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            color: #0f172a;
        }

        .invoice-detail-modal .empty-state {
            padding: 48px 24px;
        }

        .invoice-detail-modal .modal-footer {
            border-top: none;
            background: #fff;
            padding: 20px 30px;
        }

        .invoice-detail-modal .text-muted-strong {
            color: #64748b;
        }
    </style>
@endsection

@section('content')
    <div id="invoice-app" class="container-xxl flex-grow-1 container-p-y" data-invoice-type="{{ $type ?? 1 }}">
        <!-- Table Page Header -->
        <div class="table-page-header table-page-fade-in">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="bx bx-receipt"></i>
                    </div>
                    <div class="header-text">
                        <h2>
                            <i class="bx bx-receipt me-2"></i>
                            FATURA LİSTESİ
                        </h2>
                        <p>Tüm faturaları görüntüleyin ve yönetin</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{route('invoice.create.fast')}}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Hızlı Fiş Fatura
                    </a>
                    <a href="{{route('invoice.create')}}" class="btn btn-danger btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Yeni Fatura
                    </a>
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="bx bx-dots-vertical me-1"></i>
                            Diğer
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('invoice.create.personal')}}">
                                    <i class="bx bx-user me-2"></i>Personel Gideri
                                </a></li>
                            <li><a class="dropdown-item" href="{{route('invoice.create.bank')}}">
                                    <i class="bx bx-credit-card me-2"></i>Banka Gideri
                                </a></li>
                            <li><a class="dropdown-item" href="{{route('invoice.create.tax')}}">
                                    <i class="bx bx-calculator me-2"></i>Vergi / SGK Gideri
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Page Filters -->
        <div class="table-page-filters table-page-fade-in-delay-1">
            <div class="filter-header">
                <h6>
                    <i class="bx bx-filter me-2"></i>
                    Filtreler
                </h6>
                <small>Fatura arama ve filtreleme</small>
            </div>
            <div class="filter-body">
                <form @submit.prevent="searchInvoices">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-search"></i> Genel Arama
                            </label>
                            <input type="text" v-model="filters.search" class="filter-input"
                                   placeholder="Fatura no, cari adı...">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bx bx-hash"></i> Fatura ID
                            </label>
                            <input type="number" v-model="filters.invoice_id" class="filter-input"
                                   placeholder="Fatura ID...">
                        </div>

                        <div class="filter-group auto">
                            <label class="filter-label">
                                <i class="bx bx-search"></i> Ara
                            </label>
                            <button type="submit" class="filter-button primary" :disabled="loading.search">
                                <span v-if="loading.search" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bx bx-search me-1"></i>
                                <span v-text="loading.search ? 'Aranıyor...' : 'Ara'"></span>
                            </button>
                        </div>

                        <div class="filter-group auto">
                            <label class="filter-label">
                                <i class="bx bx-refresh"></i> Temizle
                            </label>
                            <button type="button" @click="clearFilters" class="filter-button secondary">
                                <i class="bx bx-refresh me-1"></i>
                                Temizle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-page-table table-page-fade-in-delay-2">
            <!-- Loading State -->
            <div v-if="loading.invoices" class="table-page-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-primary mt-2">Faturalar yükleniyor...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="invoices.length === 0" class="table-page-empty">
                <i class="bx bx-receipt"></i>
                <h4 class="mt-3">Fatura bulunamadı</h4>
                <p class="text-muted">Arama kriterlerinize uygun fatura bulunamadı.</p>
            </div>

            <!-- Table -->
            <div v-else class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style="width: 15%;"><i class="bx bx-hash me-1"></i>F. No / Tarih</th>
                        <th style="width: 15%; text-align: right;"><i class="bx bx-hash me-1"></i>Tutar</th>
                        <th style="width: 25%;" class="text-center"><i class="bx bx-user me-1"></i>Cari</th>
                        <th style="width: 10%;" class="text-center"><i class="bx bx-category me-1"></i>Tipi</th>
                        <th style="width: 20%;" class="text-center"><i class="bx bx-info-circle me-1"></i>Durum</th>
                        <th style="width: 20%;" class="text-center"><i class="bx bx-info-circle me-1"></i>O. Durum</th>
                        <th style="width: 25%;" class="text-center"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in invoices" :key="invoice.id" class="invoice-row"
                        :style="invoice.detail == null ? {'background-color': '#f2dbdb'} : {}">
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="fw-bold text-primary" @click.prevent="openInvoiceModal(invoice)"
                                   v-text="'#' + invoice.number"></a>
                                <small class="text-muted" v-text="invoice.created_at"></small>
                            </div>
                        </td>

                        <td class="text-right" style="font-size: 1.2rem; text-align: right;">
                            <span class="fw-bold text-danger" style="font-size: 1.2rem;"
                                  v-text="formatCurrency(invoice.total_price)"></span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-danger" v-text="invoice.customer_name"></span>
                        </td>
                        <td class="text-center">
                            <span class="badge" :style="{background: invoice.type_color, color: '#000'}"
                                  v-text="invoice.type_name"></span>
                        </td>
                        <td class="text-center">
                                <span v-if="invoice.is_status == 1"
                                      class="badge bg-warning"
                                      data-bs-toggle="tooltip"
                                      data-bs-html="true"
                                      :data-bs-original-title="`Gönderilmedi<br>Fiyat: ${invoice.total_price} ₺<br>Fatura Tarihi: ${invoice.create_date || '-'}`">
                                    <i class="bx bx-paper-plane me-1"></i>
                                    <span>Gönderilmedi</span>
                                </span>
                            <span v-else-if="invoice.is_status == 2"
                                  class="badge bg-success"
                                  data-bs-toggle="tooltip"
                                  data-bs-html="true"
                                  :data-bs-original-title="`Kısmi Ödeme<br>Fiyat: ${invoice.total_price} ₺`">
                                    <i class="bx bx-adjust me-1"></i>
                                    <span>Kısmi Ödeme</span>
                                </span>
                            <span v-else-if="invoice.is_status == 3"
                                  class="badge bg-danger"
                                  data-bs-toggle="tooltip"
                                  data-bs-html="true"
                                  :data-bs-original-title="`Vadesi Geçmiş<br>Fiyat: ${invoice.total_price} ₺`">
                                    <i class="bx bx-info-circle me-1"></i>
                                    <span>Vadesi Geçmiş</span>
                                </span>
                            <span v-else class="badge bg-secondary">
                                    <i class="bx bx-time me-1"></i>
                                    <span>Beklemede</span>
                                </span>
                        </td>
                        <td class="text-center">
                                <span v-if="invoice.payment_status == 'unpaid'" class="badge bg-warning">
                                    <i class="bx bx-paper-plane me-1"></i>
                                    <span>Odenmedi</span>
                                </span>
                            <span v-else-if="invoice.is_status == 2" class="badge bg-success">
                                    <i class="bx bx-adjust me-1"></i>
                                    <span>Kısmi Ödeme</span>
                                </span>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a :href="`{{route('invoice.serialprint', ['id' => ''])}}${invoice.id}`" target="_blank"
                                   title="Seri Numarası Yazdır" class="btn btn-sm btn-primary">
                                    <i class="bx bx-barcode-reader"></i>
                                </a>
                                <a :href="`{{route('invoice.qrprint', ['id' => ''])}}${invoice.id}`" target="_blank"
                                   title="QR Kod Yazdır" class="btn btn-sm btn-primary">
                                    <i class="bx bx-qr"></i>
                                </a>
                                <a :href="`{{route('invoice.stockcardmovementform', ['id' => ''])}}${invoice.id}`"
                                   title="Düzenle" class="btn btn-sm btn-warning">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                @role(['Depo Sorumlusu','super-admin'])
                                <a  :href="`{{route('invoice.delete', ['id' => ''])}}${invoice.id}`"
                                   title="Sil" class="btn btn-sm btn-danger" @click.prevent="deleteInvoice(invoice)">
                                    <i class="bx bx-trash"></i>
                                </a>
                                @endrole
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoice Detail Modal -->
        <div class="modal fade invoice-detail-modal" id="invoiceDetailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="detail-header">
                            <div class="detail-header-icon">
                                <i class="bx bx-receipt"></i>
                            </div>
                            <div>
                                <h5 class="modal-title"
                                    v-text="'Fatura Detayı - #' + ((selectedInvoice && selectedInvoice.number) || '')"></h5>
                                <div class="detail-header-meta" v-if="selectedInvoice">
                                    <small class="me-2">
                                        <i class="bx bx-user me-1"></i>
                                        <span v-text="selectedInvoice.customer_name || 'Cari Bilgisi Yok'"></span>
                                    </small>
                                    <small class="me-2">
                                        <i class="bx bx-calendar me-1"></i>
                                        <span v-text="formatDate(selectedInvoice.create_date || selectedInvoice.created_at)"></span>
                                    </small>
                                    <span v-if="detailBadges.type" class="detail-badge"
                                          :class="detailBadges.type.class">
                                        <i :class="detailBadges.type.icon"></i>
                                        <span v-text="detailBadges.type.label"></span>
                                    </span>
                                    <span v-if="detailBadges.status" class="detail-badge"
                                          :class="detailBadges.status.class">
                                        <i :class="detailBadges.status.icon"></i>
                                        <span v-text="detailBadges.status.label"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="loading.invoiceDetails" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-primary mt-2">Fatura detayları yükleniyor...</p>
                        </div>
                        <div v-else class="invoice-detail-body">
                            <div class="row g-4 align-items-stretch">
                                <div class="col-lg-5">
                                    <div class="detail-info-card">
                                        <div class="info-grid">
                                            <div>
                                                <div class="info-label">Fatura No</div>
                                                <div class="info-value"
                                                     v-text="'#' + ((selectedInvoice && selectedInvoice.number) || '-')"></div>
                                            </div>
                                            <div>
                                                <div class="info-label">Kayıt ID</div>
                                                <div class="info-value"
                                                     v-text="selectedInvoice ? selectedInvoice.id : '-'"></div>
                                            </div>
                                            <div>
                                                <div class="info-label">Oluşturma</div>
                                                <div class="info-value"
                                                     v-text="formatDate(selectedInvoice ? selectedInvoice.created_at : null)"></div>
                                            </div>
                                            <div>
                                                <div class="info-label">Fatura Tarihi</div>
                                                <div class="info-value"
                                                     v-text="formatDate(selectedInvoice ? selectedInvoice.create_date : null)"></div>
                                            </div>
                                            <div>
                                                <div class="info-label">Cari</div>
                                                <div class="info-value"
                                                     v-text="(selectedInvoice && selectedInvoice.customer_name) ? selectedInvoice.customer_name : 'Genel Cari'"></div>
                                            </div>
                                            <div>
                                                <div class="info-label">Toplam Tutar</div>
                                                <div class="info-value"
                                                     v-text="formatCurrency(selectedInvoice ? selectedInvoice.total_price : 0)"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="detail-summary">
                                        <div class="detail-summary-card">
                                            <div class="summary-label">Toplam Ürün</div>
                                            <div class="summary-value"
                                                 v-text="formatNumber(invoiceDetails.totals.items_count || 0)"></div>
                                            <div class="summary-trend">
                                                <i class="bx bx-list-ul"></i>
                                                <span v-text="'Kalem: ' + formatNumber(invoiceDetails.sales.length || 0)"></span>
                                            </div>
                                        </div>
                                        <div class="detail-summary-card success">
                                            <div class="summary-label">Satış Toplamı</div>
                                            <div class="summary-value"
                                                 v-text="formatCurrency(invoiceDetails.totals.total_sale_price)"></div>
                                            <div class="summary-trend">
                                                <i class="bx bx-receipt"></i>
                                                <span v-text="'Fatura: ' + formatCurrency(selectedInvoice ? selectedInvoice.total_price : 0)"></span>
                                            </div>
                                        </div>
                                        <div class="detail-summary-card warning">
                                            <div class="summary-label">Maliyet Toplamı</div>
                                            <div class="summary-value"
                                                 v-text="formatCurrency(invoiceDetails.totals.total_cost_price)"></div>
                                            <div class="summary-trend">
                                                <i class="bx bx-wallet"></i>
                                                <span v-text="'Ortalama: ' + formatCurrency(calculateAverageCost(invoiceDetails.totals.total_cost_price, invoiceDetails.totals.items_count))"></span>
                                            </div>
                                        </div>
                                        <div class="detail-summary-card profit">
                                            <div class="summary-label">Net Kar</div>
                                            <div class="summary-value"
                                                 :class="{
                                                    'text-success': (invoiceDetails.totals.total_profit || 0) > 0,
                                                    'text-danger': (invoiceDetails.totals.total_profit || 0) < 0
                                                 }"
                                                 v-text="formatCurrency(invoiceDetails.totals.total_profit)">
                                            </div>
                                            <div class="summary-trend">
                                                <i class="bx bx-trending-up"></i>
                                                <span v-text="'Kar Marjı: ' + formatProfitMargin(invoiceDetails.totals.total_profit, invoiceDetails.totals.total_sale_price)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card detail-table-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="bx bx-list-check me-2"></i>Fatura Kalemleri</h6>
                                    <span class="badge bg-light text-dark"
                                          v-text="'Kalem: ' + formatNumber(invoiceDetails.sales.length || 0)"></span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Marka</th>
                                            <th>Seri / Barkod</th>
                                            <th class="text-center">Adet</th>
                                            <th class="text-end">Satış (₺)</th>
                                            <th class="text-end">Maliyet (₺)</th>
                                            <th class="text-end">Kar (₺)</th>
                                            <th>Satışçı</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="item in invoiceDetails.sales" :key="item.id">
                                            <td>
                                                <div class="fw-semibold" v-text="item.stock_name || '-'"></div>
                                                <div class="text-muted small" v-if="item.description"
                                                     v-text="item.description"></div>
                                            </td>
                                            <td v-text="item.brand_name || '-'"></td>
                                            <td>
                                                <code v-text="item.serial_number || '-'"></code>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark"
                                                      v-text="formatNumber(item.quantity || 1)"></span>
                                            </td>
                                            <td class="text-end" v-text="formatCurrency(item.sale_price)"></td>
                                            <td class="text-end" v-text="formatCurrency(item.base_cost_price)"></td>
                                            <td class="text-end">
                                                    <span :class="{
                                                        'text-success fw-semibold': (item.profit || 0) > 0,
                                                        'text-danger fw-semibold': (item.profit || 0) < 0
                                                    }" v-text="formatCurrency(item.profit)">
                                                    </span>
                                            </td>
                                            <td v-text="item.seller_name || '-'"></td>
                                        </tr>
                                        <tr v-if="invoiceDetails.sales.length === 0">
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Bu faturaya ait kalem bulunamadı.
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="table-page-pagination table-page-fade-in-delay-3">
            <nav aria-label="Faturalar sayfalama">
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                            <i class="bx bx-chevron-left"></i>
                        </a>
                    </li>

                    <li v-for="page in getPageNumbers()" :key="page" class="page-item"
                        :class="{ active: page === pagination.current_page }">
                        <a class="page-link" href="#" @click.prevent="changePage(page)" v-text="page"></a>
                    </li>

                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                            <i class="bx bx-chevron-right"></i>
                        </a>
                    </li>
                </ul>

                <div class="pagination-info">
                    <small class="text-muted">
                        <span v-text="pagination.from"></span> - <span v-text="pagination.to"></span> / <span
                                v-text="pagination.total"></span> kayıt
                        (Sayfa <span v-text="pagination.current_page"></span> / <span
                                v-text="pagination.last_page"></span>)
                    </small>
                </div>
            </nav>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const {createApp} = Vue;

        createApp({
            data() {
                return {
                    invoices: [],
                    pagination: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 15,
                        total: 0,
                        from: 0,
                        to: 0
                    },
                    filters: {
                        search: '',
                        invoice_id: '',
                        customer_id: ''
                    },
                    loading: {
                        invoices: true,
                        search: false,
                        invoiceDetails: false
                    },
                    invoiceType: 1,
                    selectedInvoice: null,
                    invoiceDetails: {
                        sales: [],
                        totals: {
                            items_count: 0,
                            total_sale_price: 0,
                            total_cost_price: 0,
                            total_profit: 0
                        }
                    },
                    detailModalInstance: null,
                    detailBadges: {
                        type: null,
                        status: null
                    }
                }
            },

            async mounted() {
                const rootEl = document.getElementById('invoice-app');
                if (rootEl && rootEl.dataset && rootEl.dataset.invoiceType) {
                    const parsedType = Number(rootEl.dataset.invoiceType);
                    if (!Number.isNaN(parsedType)) {
                        this.invoiceType = parsedType;
                    }
                }

                console.log('Invoice Index Vue app mounted');
                this.setupAxios();
                await this.loadInvoices();
            },

            methods: {
                setupAxios() {
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                    }
                },

                async loadInvoices(page = 1) {
                    try {
                        this.loading.invoices = true;

                        const params = {
                            type: this.invoiceType,
                            page: page,
                            per_page: 15
                        };

                        if (this.filters.search) params.search = this.filters.search;
                        if (this.filters.invoice_id) params.invoice_id = this.filters.invoice_id;
                        if (this.filters.customer_id) params.customer_id = this.filters.customer_id;


                        const response = await axios.get('{{ route("invoice.invoices.data") }}', {
                            params: params
                        });


                        if (response.data && response.data.success) {
                            this.invoices = response.data.data || [];
                            this.pagination = response.data.pagination || {};

                            // Tooltip'leri yeniden başlat
                            this.$nextTick(() => {
                                this.initializeTooltips();
                            });
                        } else {
                            console.error('API response error:', response.data);
                            this.invoices = [];
                        }

                    } catch (error) {
                        console.error('Faturalar yüklenirken hata:', error);
                        console.error('Error details:', error.response && error.response.data ? error.response.data : null);
                        this.showNotification('Faturalar yüklenemedi', 'error');
                        this.invoices = [];
                    } finally {
                        this.loading.invoices = false;
                    }
                },

                async searchInvoices() {
                    this.loading.search = true;
                    await this.loadInvoices(1);
                    this.loading.search = false;
                },

                clearFilters() {
                    this.filters = {
                        search: '',
                        invoice_id: '',
                        customer_id: ''
                    };
                    this.loadInvoices(1);
                },

                changePage(page) {
                    if (page < 1 || page > this.pagination.last_page) {
                        return;
                    }
                    this.loadInvoices(page);
                },

                getPageNumbers() {
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const pages = [];

                    const maxPages = 7;
                    let start = Math.max(1, current - Math.floor(maxPages / 2));
                    let end = Math.min(last, start + maxPages - 1);

                    if (end - start < maxPages - 1) {
                        start = Math.max(1, end - maxPages + 1);
                    }

                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }

                    return pages;
                },

                initializeTooltips() {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                },

                showNotification(message, type = 'info') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: message,
                            icon: type,
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        console.log(`${type.toUpperCase()}: ${message}`);
                    }
                },

                formatCurrency(value) {
                    const amount = Number(value) || 0;
                    return new Intl.NumberFormat('tr-TR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(amount) + ' ₺';
                },

                async openInvoiceModal(invoice) {
                    if (!invoice || !invoice.id) {
                        return;
                    }

                    this.selectedInvoice = invoice;
                    this.detailBadges = {
                        type: this.getTypeBadge(invoice),
                        status: this.getStatusBadge(invoice)
                    };
                    this.invoiceDetails.sales = [];
                    this.invoiceDetails.totals = {
                        items_count: 0,
                        total_sale_price: 0,
                        total_cost_price: 0,
                        total_profit: 0
                    };

                    if (!this.detailModalInstance) {
                        const modalEl = document.getElementById('invoiceDetailModal');
                        if (modalEl) {
                            this.detailModalInstance = new bootstrap.Modal(modalEl, {
                                backdrop: 'static',
                                keyboard: false
                            });
                        }
                    }

                    if (this.detailModalInstance) {
                        this.detailModalInstance.show();
                    }

                    this.loading.invoiceDetails = true;

                    const hasLocalDetails = this.prepareInvoiceDetailsFromLocal(invoice);
                    if (hasLocalDetails) {
                        this.loading.invoiceDetails = false;
                        return;
                    }

                    try {
                        const response = await axios.get(`/sale/invoice-details/${invoice.id}`);
                        const sales = (response.data && Array.isArray(response.data.sales)) ? response.data.sales : [];
                        const totalsFromResponse = (response.data && response.data.totals) ? response.data.totals : {};

                        this.invoiceDetails.sales = sales;
                        this.invoiceDetails.totals = this.calculateInvoiceTotals(sales, invoice, totalsFromResponse);
                    } catch (error) {
                        console.error('Invoice detail error:', error);
                        this.showNotification('Fatura detayları yüklenemedi', 'error');
                    } finally {
                        this.loading.invoiceDetails = false;
                    }
                },

                prepareInvoiceDetailsFromLocal(invoice) {
                    const detailList = (invoice && Array.isArray(invoice.detail)) ? invoice.detail : [];
                    if (!detailList.length) {
                        return false;
                    }

                    const sales = detailList.map((detailItem, index) =>
                        this.mapInvoiceDetailItem(detailItem, index, invoice)
                    );

                    this.invoiceDetails.sales = sales;
                    this.invoiceDetails.totals = this.calculateInvoiceTotals(sales, invoice);

                    return true;
                },

                mapInvoiceDetailItem(detailItem, index, invoice) {
                    const quantity = this.sanitizeNumber(detailItem.quantity, 1);
                    const salePrice = this.sanitizeNumber(
                        detailItem.sale_price !== undefined ? detailItem.sale_price : detailItem.total_price,
                        this.sanitizeNumber(invoice.total_price, 0) / (quantity || 1)
                    );
                    const baseCost = this.sanitizeNumber(
                        detailItem.base_cost_price !== undefined ? detailItem.base_cost_price : detailItem.cost_price,
                        0
                    );
                    const costPrice = this.sanitizeNumber(detailItem.cost_price, baseCost);
                    const profit = (salePrice - baseCost) * quantity;

                    return {
                        id: detailItem.id || detailItem.invoice_detail_id || (invoice.id + '-' + index),
                        stock_name: this.resolveStockName(detailItem),
                        brand_name: this.resolveBrandName(detailItem),
                        serial_number: this.resolveSerial(detailItem),
                        description: detailItem.description || '',
                        quantity: quantity,
                        sale_price: salePrice,
                        base_cost_price: baseCost,
                        cost_price: costPrice,
                        profit: profit,
                        seller_name: this.resolveSellerName(detailItem, invoice)
                    };
                },

                calculateInvoiceTotals(sales, invoice, totalsFromResponse = {}) {
                    const totals = {
                        items_count: 0,
                        total_sale_price: 0,
                        total_cost_price: 0,
                        total_profit: 0
                    };

                    sales.forEach((saleItem) => {
                        const quantity = this.sanitizeNumber(saleItem.quantity, 1);
                        const salePrice = this.sanitizeNumber(saleItem.sale_price, 0);
                        const costPrice = this.sanitizeNumber(
                            saleItem.base_cost_price !== undefined ? saleItem.base_cost_price : saleItem.cost_price,
                            0
                        );

                        const saleTotal = salePrice * quantity;
                        const costTotal = costPrice * quantity;
                        const profitTotal = saleTotal - costTotal;

                        totals.items_count += quantity;
                        totals.total_sale_price += saleTotal;
                        totals.total_cost_price += costTotal;
                        totals.total_profit += profitTotal;
                    });

                    if (!totals.items_count && totalsFromResponse.items_count) {
                        totals.items_count = this.sanitizeNumber(totalsFromResponse.items_count, 0);
                    }

                    if (!totals.total_sale_price && totalsFromResponse.total_sale_price) {
                        totals.total_sale_price = this.sanitizeNumber(totalsFromResponse.total_sale_price, 0);
                    }

                    if (!totals.total_cost_price && totalsFromResponse.total_cost_price) {
                        totals.total_cost_price = this.sanitizeNumber(totalsFromResponse.total_cost_price, 0);
                    }

                    if (!totals.total_profit && totalsFromResponse.total_profit) {
                        totals.total_profit = this.sanitizeNumber(totalsFromResponse.total_profit, 0);
                    }

                    if (!totals.total_sale_price && invoice && invoice.total_price) {
                        totals.total_sale_price = this.sanitizeNumber(invoice.total_price, 0);
                    }

                    if (!totals.total_profit) {
                        totals.total_profit = totals.total_sale_price - totals.total_cost_price;
                    }

                    return totals;
                },

                sanitizeNumber(value, fallback = 0) {
                    const parsed = Number(value);
                    return Number.isFinite(parsed) ? parsed : fallback;
                },

                resolveStockName(detailItem) {
                    if (detailItem.stock_name) {
                        return detailItem.stock_name;
                    }
                    if (detailItem.name) {
                        return detailItem.name;
                    }
                    if (detailItem.stock && detailItem.stock.name) {
                        return detailItem.stock.name;
                    }
                    if (detailItem.product_name) {
                        return detailItem.product_name;
                    }
                    if (detailItem.stockcardid) {
                        return 'Stok #' + detailItem.stockcardid;
                    }
                    return 'Ürün';
                },

                resolveBrandName(detailItem) {
                    if (detailItem.brand_name) {
                        return detailItem.brand_name;
                    }
                    if (detailItem.brand) {
                        if (typeof detailItem.brand === 'string') {
                            return detailItem.brand;
                        }
                        if (detailItem.brand.name) {
                            return detailItem.brand.name;
                        }
                    }
                    if (detailItem.stock && detailItem.stock.brand && detailItem.stock.brand.name) {
                        return detailItem.stock.brand.name;
                    }
                    return '-';
                },

                resolveSerial(detailItem) {
                    if (detailItem.serial_number) {
                        return detailItem.serial_number;
                    }
                    if (detailItem.serial) {
                        return detailItem.serial;
                    }
                    if (detailItem.imei) {
                        return detailItem.imei;
                    }
                    if (detailItem.barcode) {
                        return detailItem.barcode;
                    }
                    return '-';
                },

                resolveSellerName(detailItem, invoice) {
                    if (detailItem.seller_name) {
                        return detailItem.seller_name;
                    }
                    if (detailItem.seller && detailItem.seller.name) {
                        return detailItem.seller.name;
                    }
                    if (invoice && invoice.customer_name) {
                        return invoice.customer_name;
                    }
                    return '-';
                },

                getTypeBadge(invoice) {
                    if (!invoice) {
                        return null;
                    }

                    return {
                        label: invoice.type_name || 'Fatura',
                        class: 'badge-soft-primary',
                        icon: 'bx bx-layer'
                    };
                },

                getStatusBadge(invoice) {
                    if (!invoice) {
                        return null;
                    }

                    const statusKey = Number(invoice.is_status);
                    const statusMap = {
                        1: {
                            label: 'Gönderilmedi',
                            class: 'badge-soft-warning',
                            icon: 'bx bx-paper-plane'
                        },
                        2: {
                            label: 'Kısmi Ödeme',
                            class: 'badge-soft-success',
                            icon: 'bx bx-adjust'
                        },
                        3: {
                            label: 'Vadesi Geçmiş',
                            class: 'badge-soft-danger',
                            icon: 'bx bx-info-circle'
                        },
                        4: {
                            label: 'Kapandı',
                            class: 'badge-soft-success',
                            icon: 'bx bx-badge-check'
                        }
                    };

                    return statusMap[statusKey] || {
                        label: 'Beklemede',
                        class: 'badge-soft-secondary',
                        icon: 'bx bx-time'
                    };
                },

                formatNumber(value) {
                    const numberValue = Number(value);
                    if (!Number.isFinite(numberValue)) {
                        return '0';
                    }
                    return new Intl.NumberFormat('tr-TR').format(numberValue);
                },

                formatDate(value) {
                    if (!value) {
                        return '-';
                    }

                    if (value instanceof Date && !Number.isNaN(value.getTime())) {
                        return value.toLocaleString('tr-TR');
                    }

                    const stringValue = String(value);

                    if (stringValue.includes('T')) {
                        const parsed = new Date(stringValue);
                        if (!Number.isNaN(parsed.getTime())) {
                            return parsed.toLocaleString('tr-TR');
                        }
                    }

                    return stringValue;
                },

                calculateAverageCost(totalCost, itemCount) {
                    const cost = this.sanitizeNumber(totalCost, 0);
                    const count = this.sanitizeNumber(itemCount, 0);
                    if (!count) {
                        return 0;
                    }
                    return cost / count;
                },

                formatProfitMargin(profit, sale) {
                    const profitValue = this.sanitizeNumber(profit, 0);
                    const saleValue = this.sanitizeNumber(sale, 0);

                    if (!saleValue) {
                        return '0 %';
                    }

                    const ratio = (profitValue / saleValue) * 100;
                    const formatted = ratio.toLocaleString('tr-TR', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 1
                    });

                    const prefix = ratio > 0 ? '+' : ratio < 0 ? '-' : '';
                    return `${prefix}${formatted} %`;
                },

                async deleteInvoice(invoice) {
                    if (!invoice || !invoice.id) {
                        return;
                    }

                    let confirmed = true;
                    if (typeof Swal !== 'undefined') {
                        const result = await Swal.fire({
                            title: 'Faturayı silmek istediğinize emin misiniz?',
                            text: `#${invoice.number || invoice.id} numaralı fatura kalıcı olarak silinecek.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Evet, Sil',
                            cancelButtonText: 'Vazgeç',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d'
                        });
                        confirmed = result.isConfirmed;
                    } else {
                        confirmed = window.confirm('Faturayı silmek istediğinize emin misiniz?');
                    }

                    if (!confirmed) {
                        return;
                    }

                    try {
                        await axios.get("{{ route('invoice.delete') }}", {
                            params: { id: invoice.id }
                        });

                        this.invoices = this.invoices.filter(item => item.id !== invoice.id);
                        this.showNotification('Fatura başarıyla silindi.', 'success');
                    } catch (error) {
                        console.error('Delete invoice error:', error);
                        const message = error?.response?.data?.message || 'Fatura silinirken bir hata oluştu.';
                        this.showNotification(message, 'error');
                    }
                }
            }
        }).mount('#invoice-app');
    </script>
@endsection
