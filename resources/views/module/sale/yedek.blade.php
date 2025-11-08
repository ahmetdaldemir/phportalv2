@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Satışlar /</span> Satış listesi</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{route('sale.index')}}" class="row" id="stockSearch" method="get">
                    @csrf
                    <div class="col-md-6 fv-plugins-icon-container">
                        <label class="form-label" for="formValidationName">Başlangıç - Bitiş Tarihi</label>
                        <input type="text" class="form-control daterangepicker-input" name="daterange"
                               placeholder="YYYY-MM-DD to YYYY-MM-DD" id="date-range" readonly="readonly">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="multicol-username">Stok</label>
                        <input type="text" class="form-control" placeholder="············" name="stockName">
                    </div>
                    <div class="col-md-3">
                        <div class="form-password-toggle">
                            <label class="form-label" for="multicol-confirm-password">Seri Numarası</label>
                            <div class="input-group input-group-merge">
                                <input type="text" name="serialNumber" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="multicol-email">Marka</label>
                        <div class="input-group input-group-merge">
                            <select type="text" name="brand" class="form-select" onchange="getVersion(this.value)"
                                    style="width: 100%">
                                <option value="">Tümü</option>
                                @foreach($brands as $brand)
                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-password-toggle">
                            <label class="form-label" for="multicol-password">Model</label>
                            <div class="input-group input-group-merge">
                                <select type="text" id="version_id" name="version" class="form-select"
                                        style="width: 100%">
                                    <option value="">Tümü</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-password-toggle">
                            <label class="form-label" for="multicol-password">Kategori</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="category" class="form-select" style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($categories as $category)
                                        @if($category->parent_id == 0)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>

            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Fatura No / Tarih</th>
                        <th style="text-align: center">Müşteri</th>
                        <th style="text-align: center">Ürün</th>
                        <th style="text-align: center">Satışı Yapan</th>
                        <th style="text-align: center">Satış Fiyatı</th>
                        <th style="text-align: center">Kredi Kartı</th>
                        <th style="text-align: center">Nakit</th>
                        <th style="text-align: center">Taksit</th>
                        <th style="text-align: center">Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($invoices as $invoice)
                        @if($invoice->type == 2)
                                <?php $detail = json_decode($invoice->detail, true); ?>
                            <tr>
                                <td>
                                    <a href="{{route('invoice.show',['id' => $invoice->id])}}">#{{$invoice->number??"#"}}</a>
                                    / {{\Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y')}}</td>
                                <td style="text-align: center;">
                                    @if($invoice->customer_id == 1) Genel Cari @else <strong>{{$invoice->account->fullname ?? "Genel Cari"}}</strong> @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($invoice->accounting_category_id === 9999999)
                                            <?php $phone = \App\Models\Phone::with('brand','version')->where('invoice_id', $invoice->id)->first();


                                            ?>
                                        TELEFON - <?php echo $phone->brand->name ?? "Bulunamadı"; ?> / <?php if(isset($phone->version))
                                        {
                                            echo $phone->version->name;
                                        }
                                                                                                           ?>
                                    @endif
                                    @if(!empty($detail))
                                        {{ \App\Models\StockCard::find($detail[0]['stock_card_id'])->name ?? "Silinmiş"}}
                                    @endif</td>
                                <td style="text-align: center;">{{$invoice->staff->name}}</td>
                                <td style="text-align: right;font-weight: 600">
                                    @if($invoice->accounting_category_id == 9999999)
                                            <?php $phone = \App\Models\Phone::where('invoice_id', $invoice->id)->first();
                                            echo $phone->sale_price ??  0.00 ;
                                            ?>  ₺
                                    @else
                                        @if(!empty($detail))
                                            {{ $detail[0]['sale_price']  ?? 0.00}}
                                        @endif ₺
                                    @endif
                                </td>
                                <td style="text-align: right;font-weight: 600"> {{ $invoice->credit_card ??  0.00}}₺
                                </td>
                                <td style="text-align: right;font-weight: 600"> {{ $invoice->cash ??  0.00}} ₺</td>
                                <td style="text-align: right;font-weight: 600"> {{ $invoice->installment ??  0.00}}₺
                                </td>
                                <td style="text-align: center;">
                                    @if($invoice->is_status == 1)
                                        <span data-bs-toggle="tooltip" data-bs-html="true"
                                              data-bs-original-title="<span>Gönderilmedi<br> Fiyat: {{$invoice->total_price}}<br>
                                           Fatura Tarihi: {{\Carbon\Carbon::parse($invoice->create_date)->format('d-m-Y')}}</span>"
                                              aria-describedby="tooltip472596"><span
                                                class="badge badge-center rounded-pill bg-label-secondary w-px-30 h-px-30"><i
                                                    class="bx bx-paper-plane bx-xs"></i></span></span>
                                    @endif
                                    @if($invoice->is_status == 2)
                                        <span data-bs-toggle="tooltip" data-bs-html="true"
                                              aria-label="<span>Partial Payment<br> Balance: 0<br> Due Date: 09/25/2020</span>"
                                              data-bs-original-title="<span>Partial Payment<br> Balance: 0<br> Due Date: 09/25/2020</span>"
                                              aria-describedby="tooltip478233"><span
                                                class="badge badge-center rounded-pill bg-label-success w-px-30 h-px-30"><i
                                                    class="bx bx-adjust bx-xs"></i></span></span>
                                    @endif

                                    @if($invoice->is_status == 3)
                                        <span data-bs-toggle="tooltip" data-bs-html="true"
                                              aria-label="<span>Past Due<br> Balance: 0<br> Due Date: 08/01/2020</span>"
                                              data-bs-original-title="<span>Past Due<br> Balance: 0<br> Due Date: 08/01/2020</span>"
                                              aria-describedby="tooltip774099"><span
                                                class="badge badge-center rounded-pill bg-label-danger w-px-30 h-px-30"><i
                                                    class="bx bx-info-circle bx-xs"></i></span></span>
                                    @endif


                                </td>
                                <td>
                                    <a title="Düzenle" href="{{route('invoice.salesedit',['id' => $invoice->id])}}"
                                       class="btn btn-icon btn-primary">
                                        <span class="bx bx-edit-alt"></span>
                                    </a>
                                    <a title="Sil" href="{{route('sale.delete',['id' => $invoice->id])}}"
                                       class="btn btn-icon btn-danger">
                                        <span class="bx bxs-trash"></span>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>

    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="transferForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Sevk İşlemi</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Serial Number</label>
                            <input
                                type="text"
                                id="serialBackdrop"
                                class="form-control"
                                placeholder="Seri Numarası"
                                name="serial_number"
                            />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="invoiceBackdrop" class="form-label">Şube</label>
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
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Sevk İşlemi Başlat</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/js/forms-pickers.js')}}"></script>

    <script>

        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#stockCardId").val(id);
        }
    </script>
@endsection
