@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bx bx-receipt display-4"></i>
                </div>
                <div>
                    <h1 class="mb-0">
                        <i class="bx bx-receipt me-2"></i>
                        FATURA LİSTESİ
                    </h1>
                    <p class="mb-0">Tüm faturaları görüntüleyin ve yönetin</p>
                </div>
            </div>
        </div>

        <div class="card professional-card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2"></i>
                        Fatura Listesi
                    </h5>
                    <div class="btn-group demo-inline-spacing">
                        <a href="{{route('invoice.create.fast')}}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>
                            Hızlı Fiş Fatura Ekle
                        </a>
                        <a href="{{route('invoice.create')}}" class="btn btn-danger">
                            <i class="bx bx-plus me-1"></i>
                            Yeni Fatura Ekle
                        </a>
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-vertical me-1"></i>
                                Diğer
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('invoice.create.personal')}}">
                                    <i class="bx bx-user me-2"></i>Personel Gideri Ekle
                                </a></li>
                                <li><a class="dropdown-item" href="{{route('invoice.create.bank')}}">
                                    <i class="bx bx-credit-card me-2"></i>Banka Gideri Ekle
                                </a></li>
                                <li><a class="dropdown-item" href="{{route('invoice.create.tax')}}">
                                    <i class="bx bx-calculator me-2"></i>Vergi / SGK Gideri Ekle
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="bx bx-hash me-1"></i>Fatura No / Tarih</th>
                                <th class="text-center"><i class="bx bx-user me-1"></i>Cari</th>
                                <th class="text-center"><i class="bx bx-category me-1"></i>Tipi</th>
                                <th class="text-center"><i class="bx bx-info-circle me-1"></i>Status</th>
                                <th><i class="bx bx-cog me-1"></i>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($invoices as $invoice)
                                <tr class="invoice-row">
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="{{route('invoice.stockcardmovementform',['id' => $invoice->id])}}" class="fw-bold text-primary">
                                                #{{$invoice->number??"Numara Girilmedi"}}
                                            </a>
                                            <small class="text-muted">
                                                {{\Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i')}}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-danger">
                                            @if($invoice->customer_id == 0) 
                                                Genel Cari 
                                            @else 
                                                {{$invoice->account->fullname ?? "Genel Cari"}}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: {{$invoice->invoice_type_color($invoice->type)}}; color: white;">
                                            {{$invoice->invoice_type($invoice->type)}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($invoice->is_status == 1)
                                            <span class="badge bg-warning" data-bs-toggle="tooltip" data-bs-html="true"
                                                  data-bs-original-title="<span>Gönderilmedi<br> Fiyat: {{$invoice->total_price}}<br>
                                                   Fatura Tarihi: {{\Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y')}}</span>">
                                                <i class="bx bx-paper-plane me-1"></i>Gönderilmedi
                                            </span>
                                        @endif
                                        @if($invoice->is_status == 2)
                                            <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-html="true"
                                                  data-bs-original-title="<span>Kısmi Ödeme<br> Bakiye: 0<br> Vade: 09/25/2020</span>">
                                                <i class="bx bx-adjust me-1"></i>Kısmi Ödeme
                                            </span>
                                        @endif
                                        @if($invoice->is_status == 3)
                                            <span class="badge bg-danger" data-bs-toggle="tooltip" data-bs-html="true"
                                                  data-bs-original-title="<span>Vadesi Geçmiş<br> Bakiye: 0<br> Vade: 08/01/2020</span>">
                                                <i class="bx bx-info-circle me-1"></i>Vadesi Geçmiş
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a title="Seri Numarası Yazdır" target="_blank" href="{{route('invoice.serialprint',['id' => $invoice->id])}}" class="btn btn-sm btn-primary">
                                                <i class="bx bx-barcode-reader"></i>
                                            </a>
                                            <a title="Düzenle" href="{{route('invoice.stockcardmovementform',['id' => $invoice->id])}}" class="btn btn-sm btn-warning">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $invoices->appends(['type' => $type])->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="transferForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">
                        <i class="bx bx-transfer me-2"></i>
                        Sevk İşlemi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="serialBackdrop" class="form-label">
                                <i class="bx bx-barcode me-1"></i>
                                Seri Numarası
                            </label>
                            <input type="text" id="serialBackdrop" class="form-control" placeholder="Seri Numarası" name="serial_number" />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="invoiceBackdrop" class="form-label">
                                <i class="bx bx-store me-1"></i>
                                Şube
                            </label>
                            <select class="form-control" name="invoice_id" id="invoiceBackdrop">
                                @foreach($invoices as $invoice)
                                    <option value="{{$invoice->id}}">{{$invoice->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check me-1"></i>
                        Sevk İşlemi Başlat
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('custom-js')
    <script>
        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#stockCardId").val(id);
        }
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
