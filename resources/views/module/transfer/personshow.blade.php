@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex svg-illustration mb-3 gap-2">
                                    <span class="app-brand-text demo text-body fw-bolder">{{$transfer->user($transfer->delivery_id)->name}}</span>
                                </div>
                                <p class="mb-1">Şube :  {{$transfer->user($transfer->delivery_id)->seller->name}}</p>
                            </div>
                            <div>
                                <h4>#{{$transfer->number}}</h4>
                                <div class="mb-2">
                                    <span class="me-1">Tarih:</span>
                                    <span class="fw-semibold">{{$transfer->created_at}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="table-responsive">
                        <table class="table border-top m-0">
                            <thead>
                            <tr>
                                <th>Seri No</th>
                                <th>Ürün</th>
                                <th>Adet</th>
                                <th>Alış Fiyatı</th>
                                <th>Destekli Fiyat</th>
                                <th>Satış Fiyatı</th>
                            </tr>
                            </thead>
                            <tbody>
                             @if($transfer->serial_list)
                                @foreach($transfer->serial_list as $value)
                                         <?php $stock_card_moveement =  \App\Models\StockCardMovement::where('serial_number',$value)->first();  ?>
                                    @if(!empty($stock_card_moveement))

                                        <tr>
                                            <td>{{$value}}</td>
                                            <td class="text-nowrap">{{$stock_card_moveement->stock->name ?? "Stok Kartı Bulunmayan Seri Eklediniz"}}</td>
                                            <td class="text-nowrap">1</td>
                                            <td>{{$stock_card_moveement->cost_price}}</td>
                                            <td>{{$stock_card_moveement->base_cost_price}}</td>
                                            <td>{{$stock_card_moveement->sale_price}}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" style="text-align: center"> Stok Kartı Bulunmayan Seri Eklediniz</td>
                                        </tr>
                                    @endif
                                 @endforeach
                            @else
                                <tr>
                                    <td colspan="5" style="text-align: center;font-weight: bolder">Ürün Bulunamadı</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="align-top px-4 py-5">
                                    <p class="mb-2">
                                        <span class="me-1 fw-semibold">Personel:</span>
                                        <span>{{$transfer->user($transfer->user_id)->name}}</span>
                                    </p>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-semibold">Not:</span>
                                <span>{{$transfer->description}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-label-secondary d-grid w-100 mb-3" target="_blank"
                           href="#">
                            Print
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
