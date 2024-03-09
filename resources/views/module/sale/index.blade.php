@extends('layouts.admin')

@section('content')
    <style>
        .collapse.in {
            display: block;
        }

        .hiddenRow {
            padding: 0 !important;
        }
    </style>
    <style>
        .position-relative {
            width: 100%;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Satışlar /</span> Satış listesi</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{route('sale.index')}}" class="row" id="stockSearch" method="get">
                    @csrf
                    <div class="col-md-6 fv-plugins-icon-container">
                        <label class="form-label" for="formValidationName">Başlangıç - Bitiş Tarihi</label>
                        <input type="text" class="form-control flatpickr-input" name="daterange"
                               value="{{date('Y-m-d')}}" id="flatpickr-range" readonly="readonly">
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

                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-password-toggle">
                            <label class="form-label" for="multicol-password">Kategori</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="category" class="select2" style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($categories as $value)
                                        <option value="{{$value->id}}">{{$value->path}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-password-toggle">
                            <label class="form-label" for="multicol-password">Bayi</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="seller" class="form-select" style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{$seller->id}}">{{$seller->name}}</option>
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
                <table class="table" style="font-size: 14px">
                    <thead>
                    <tr>
                        <th style="font-size: 10px !important;text-align: left">Fatura No / Tarih</th>
                        <th style="font-size: 10px !important;text-align: left">Tip</th>
                        <th style="font-size: 10px !important;text-align: left">Müşteri</th>
                        <th style="font-size: 10px !important;text-align: left">Şube</th>
                        <th style="font-size: 10px !important;text-align: left">Satışı Yapan</th>
                        <th style="font-size: 10px !important;text-align: right">Kredi Kartı</th>
                        <th style="font-size: 10px !important;text-align: right">Nakit</th>
                        <th style="font-size: 10px !important;text-align: right">Taksit</th>
                        <th style="font-size: 10px !important;text-align: right">G.Toplam</th>
                        <th style="font-size: 10px !important;text-align: right">S.Toplam</th>
                        <th style="font-size: 10px !important;text-align: right">İnd.</th>
                        <th style="font-size: 10px !important;text-align: center">Kar</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    <?php $totalCash = 0;
                    $totalInstallment = 0;
                    $totalCredit_card = 0;
                    $totalPrice = 0;
                    $discountTotal = 0;
                     $totals = 0; ?>
                    @foreach($invoices as $key => $value)

                            <?php $invoice = \App\Models\Invoice::find($key); ?>
                        @if($invoice)
                                <?php
                                $totalCash += $invoice->cash ?? 0;
                                $totalInstallment += $invoice->installment ?? 0;
                                $totalCredit_card += $invoice->credit_card ?? 0;
                                $totals += $invoice->total_price ?? 0;

                                ?>

                            <tr style="{{$profits}}" data-toggle="collapse" data-target="#l{{$key}}" class="accordion-toggle">
                                <td>{{$key}} / <b>{{\Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i')}}</b>
                                </td>
                                <td>
                                        <?php
                                        $sumOfRooms = $value->map(function ($items, $key) {
                                            return \App\Models\Sale::STATUS[$items->type];
                                        });
                                        echo $sumOfRooms[0];
                                        ?></b></td>
                                <td><b class="text-danger">{{$invoice->account->fullname??'Bulunamadi'}}</b></td>
                                <td><b class="text-danger"> <?php
                                                                $sumOfRooms = $value->map(function ($items, $key) {
                                                                    return $items->seller_id;
                                                                });
                                                                echo \App\Models\Seller::find($sumOfRooms[0])->name ?? "bulunamadı";
                                                                ?></b></td>
                                <td>{{$invoice->staff->name??"Bulunamadı"}}</td>
                                <td style="text-align: right"><span class="badge bg-label-primary me-1"
                                                                    style="color: #000 !important;font-size: 15px;">{{number_format($invoice->credit_card,2)}} ₺</span>
                                </td>
                                <td style="text-align: right"><span class="badge bg-label-danger me-1"
                                                                    style="color: #000 !important;font-size: 15px;">{{number_format($invoice->cash,2)}} ₺</span>
                                </td>
                                <td style="text-align: right"><span class="badge bg-label-success me-1"
                                                                    style="color: #000 !important;font-size: 15px;">{{number_format($invoice->installment,2)}} ₺</span>
                                </td>
                                <td style="text-align: right"><span class="badge bg-label-success me-1"
                                                                    style="color: #000 !important;font-size: 15px;">
                                    <?php
                                            $sumOfRooms = $value->map(function ($items, $key) {

                                                if ($items->type == 1) {
                                                    return \App\Models\Phone::find($items->stock_card_movement_id)->sale_price ?? 0;

                                                } else {
                                                    return \App\Models\StockCardMovement::find($items->stock_card_movement_id)->sale_price ?? 0;
                                                }

                                            });
                                            $x = json_decode($sumOfRooms, true);
                                            $a = array_sum($x);
                                            $totalPrice += $a;
                                            ?>
                                        {{number_format($a,2)}} ₺

                                </span>
                                </td>
                                <td style="text-align: right">
                                    <span class="badge bg-label-success me-1"
                                          style="color: #000 !important;font-size: 15px;">{{number_format($invoice->total_price,2)}} ₺</span>
                                </td>
                                <td style="text-align: right">
                                <span class="badge bg-label-success me-1"
                                      style="color: #000 !important;font-size: 15px;">
                                     <?php
                                        $sumOfRooms = $value->map(function ($items, $key) {
                                            if ($items->type == 1) {
                                                return \App\Models\Phone::find($items->stock_card_movement_id)->sale_price ?? 0;

                                            } else {
                                                return \App\Models\StockCardMovement::find($items->stock_card_movement_id)->sale_price ?? 0;
                                            }
                                        });
                                        $x = json_decode($sumOfRooms, true);
                                        $a = array_sum($x);
                                        $discountTotal += $a - $invoice->total_price;
                                        ?>
                                    {{number_format($a-$invoice->total_price,2)}} ₺</span>
                                </td>
                                <td style="text-align: right">
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin'))
                                        <b class="text-danger"> {{number_format($invoice->total_price - $invoice->totalSaleBaseCost(),2)}}
                                            ₺ </b>

                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <td colspan="9" class="hiddenRow">
                                    <div class="accordian-body collapse" id="l{{$key}}">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr class="info" style="background: #e7e7ff;font-weight: 800 !important;">
                                                <th style="text-align: left">Ürün</th>
                                                <th>Satış Fiyatı</th>
                                                <th>Seri No</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($value as $item)
                                                <tr class="info">
                                                    <td class="text-left"> {{$item->name}} </td>
                                                    <td class="text-right"><span class="badge rounded-pill bg-danger">{{$item->sale_price}} ₺</span>
                                                    </td>

                                                    @if($item->type == 2 || $item->type != 1)
                                                        <td>{{$item->stock_card_movement->serial_number??0}}</td>
                                                    @elseif($item->type == 1)
                                                        <td>{{\App\Models\Phone::find($item->stock_card_movement_id)->barcode??0}}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <th style="font-size: 10px !important;text-align: left">Fatura No / Tarih</th>
                    <th style="font-size: 10px !important;text-align: left">Tip</th>
                    <th style="font-size: 10px !important;text-align: left">Müşteri</th>
                    <th style="font-size: 10px !important;text-align: left">Şube</th>
                    <th style="font-size: 10px !important;text-align: left">Satışı Yapan</th>
                    <th style="font-size: 10px !important;text-align: right">{{number_format($totalCredit_card,2)}}₺
                    </th>
                    <th style="font-size: 10px !important;text-align: right">{{number_format($totalCash,2)}} ₺</th>
                    <th style="font-size: 10px !important;text-align: right">{{number_format($totalInstallment,2)}}₺
                    </th>
                    <th style="font-size: 10px !important;text-align: right">{{number_format($totalPrice,2)}} ₺</th>
                    <th style="font-size: 10px !important;text-align: right">{{number_format($totals,2)}} ₺</th>
                    <th style="font-size: 10px !important;text-align: right">{{number_format($discountTotal,2)}} ₺</th>
                    <th style="font-size: 10px !important;text-align: center">
                        @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin'))
                            {{number_format($profits,2)}} ₺
                        @endif
                    </th>
                    </tfoot>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>

@endsection

@section('custom-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script src="{{asset('assets/js/forms-pickers.js')}}"></script>

    <script>

        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#stockCardId").val(id);
        }
    </script>
@endsection
