@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">RAPOR</span></h4>

        <div class="card">
            <div class="card-body">
                <form action="{{route('report')}}" id="stockSearch" method="get">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4 fv-plugins-icon-container">
                            <label class="form-label" for="formValidationName">Başlangıç - Bitiş Tarihi</label>


                            <div class="input-group input-daterange">
                                <input type="text" class="form-control" name="date1" value="@if($sendData){{$sendData->date1}}@endif" autocomplete="off">
                                <div class="input-group-addon">to</div>
                                <input type="text" class="form-control" name="date2" value="@if($sendData){{$sendData->date2}}@endif" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="multicol-email">Marka</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="brand" class="form-select" onchange="getVersion(this.value)"
                                        style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($brands as $brand)
                                        <option @if($sendData && $brand->id == $sendData->brand) selected @endif value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Şube</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="seller" class="form-select" style="width: 100%">
                                        <option value="">Tümü</option>
                                        @foreach($sellers as $seller)
                                            <option @if($sendData && $seller->id == $sendData->seller) selected @endif value="{{$seller->id}}">{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Kategori</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="category" class="select2" style="width: 100%">
                                        <option value="">Seçiniz</option>
                                         @foreach($types as $key => $type)
                                             <option @if($sendData && $key == $sendData->category) selected @endif value="{{$key}}">{{$type}}</option>
                                         @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Satışı Yapan Personel</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="sales_person" class="select2" style="width: 100%">
                                        <option value="">Tümü</option>
                                        @foreach($users as $value)
                                            <option @if($sendData && $value->id == $sendData->sales_person) selected @endif value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Teknisyen</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="technical_person" class="select2" style="width: 100%">
                                        <option value="">Tümü</option>
                                        @foreach($users as $value)
                                            <option @if($sendData && $value->id == $sendData->technical_person) selected @endif value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>
            </div>
            <div class="card-header">

            </div>
            <div class="card-body">
                <table class="table table-responsive">
                    <tr>
                        <td>Personel</td>
                        <td>ID</td>
                        <td>Tip</td>
                        <td>Marka</td>
                        <td>Alış Fiyatı</td>
                        <td>Maliyet Fiyatı</td>
                        <td>Satış Fiyatı</td>
                        <td>Kar</td>
                        <td>Tarih</td>
                    </tr>
                    <?php
                    $totalSale = 0;
                    $totalCostPrice = 0;
                    $totalBasePrice = 0;
                    foreach ($report as $item){
                       if($item->type != 1) {
                           $sale_price = $item->sale_price ?? 0;
                           $base_cost_price = $item->stock_card_movement->base_cost_price ?? 0;
                           $cost_price = $item->stock_card_movement->cost_price ?? 0;

                           $totalSale += $sale_price;
                           $totalCostPrice += $cost_price;
                           $totalBasePrice += $base_cost_price;
                       }else{
                           $sale_price = $item->sale_price ?? 0;
                           $base_cost_pricePhone = $item->phone->cost_price ?? 0;
                           $cost_pricePhone = $item->phone->cost_price ?? 0;

                           $totalSale += $sale_price;
                           $totalCostPrice += $base_cost_pricePhone;
                           $totalBasePrice += $cost_pricePhone;
                       }
                       ?>
                        <tr>
                            <td>{{$item->user->name}}{{$item->serial}}</td>
                            <td>{{$item->id}}</td>
                            <td>{{$item->statusName()}}</td>
                             <?php if($item->type != 1){ ?>
                            <td>{{$item->stock_card->brand->name ?? "Bulunamadı"}}</td>
                            <?php }else{  ?>
                            <td>{{$item->phone->brand->name ?? "Bulunamadı"}}/{{$item->phone->version->name ?? "Bulunamadı"}}</td>
                            <?php } ?>
                            <?php if($item->type == 1){ ?>
                            <td>{{number_format($base_cost_pricePhone??0,2)}} TL</td>
                            <td>{{number_format($base_cost_pricePhone??0,2)}} TL</td>
                            <td>{{number_format($item->sale_price??0,2)}} TL</td>
                            <td>{{number_format($sale_price - $base_cost_pricePhone,2)}} TL</td>
                            <?php }else{ ?>
                            <td>{{number_format($item->stock_card_movement->cost_price??0,2)}} TL</td>
                            <td>{{number_format($item->stock_card_movement->base_cost_price??0,2)}} TL</td>
                            <td>{{number_format($item->sale_price??0,2)}} TL</td>
                            <td>{{number_format($item->sale_price - $base_cost_price,2)}} TL</td>
                            <?php } ?>

                            <td>{{$item->created_at}}</td>
                            <td>{{$item->technical->customer_price}}</td>
                        </tr>
                    <?php } ?>
                    <tr style="    background: blue;
                    color: #fff;
                    font-weight: 600;">
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{{number_format($totalCostPrice,2)}} TL</td>
                        <td>{{number_format($totalBasePrice,2)}} TL</td>
                        <td>{{number_format($totalSale,2)}} TL</td>
                        <td>{{number_format($totalSale-$totalBasePrice,2)}} TL</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>


                </table>


            </div>
        </div>
        <hr class="my-5">
    </div>

@endsection
<style>
    table > :not(caption) > * > * {
        padding: 0.225rem 0.5rem;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        font-size: 12px;
    }
    .position-relative {
        width: 100%;
        position: relative !important;
    }
</style>
@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/daterangepicker/daterangepicker.css')}}"/>

@endsection
@section('custom-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script src="{{asset('assets/vendor/libs/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('assets/js/daterangepicker-init.js')}}"></script>
    <script>
        $('.input-daterange input').each(function() {
            $(this).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });
        });
    </script>
@endsection
