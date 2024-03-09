@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">RAPOR</span></h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Personel Raporu</h3>
                    </div>
                    <div class="card-header">
                        <form action="{{route('personelsellerreport')}}" id="stockSearch" method="get">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8 fv-plugins-icon-container">
                                    <label class="form-label" for="formValidationName">Başlangıç - Bitiş
                                        Tarihi</label>

                                    <div class="input-group input-daterange">
                                        <input type="text" class="form-control" name="date1"
                                               value="@if($sendData){{$sendData->date1}}@endif"
                                               autocomplete="off">
                                        <div class="input-group-addon">to</div>
                                        <input type="text" class="form-control" name="date2"
                                               value="@if($sendData){{$sendData->date2}}@endif"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-password-toggle">
                                        <label class="form-label" for="multicol-password">Satışı Yapan
                                            Personel</label>
                                        <div class="input-group input-group-merge">
                                            <select type="text" name="person" class="select2"
                                                    style="width: 100%">
                                                <option value="">Tümü</option>
                                                @foreach($users as $value)
                                                    <option
                                                        @if($sendData && $value->id == $sendData->person) selected
                                                        @endif value="{{$value->id}}">{{$value->name}}</option>
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
                    <div class="card-body">
                        <table class="table table-responsive">
                            <tr>
                                <td colspan="3">Personel</td>
                                <td>Kategori</td>
                                <td>Toplam Satış</td>
                                <td>Toplam Kar</td>
                            </tr>
                            @if(!empty($personReport))
                                @foreach($personReport as $item)
                                    <tr style="    background: blue; color: #fff;  font-weight: 600;">
                                        <td colspan="3">@if(isset($item->user_id))
                                                {{\App\Models\User::find($item->user_id)->name}}
                                            @endif</td>
                                        <td>{{\App\Models\Sale::STATUS[$item->type]}}</td>
                                        <td>{{$item->toplamTutar}}</td>
                                        <td>{{$item->toplamTutar - $item->totalCost}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>

                        <hr/>
                        <label>Teslim Alınan Raporu</label>
                        <table class="table table-responsive">
                            <tr>
                                <td colspan="3">Personel</td>
                                <td>Kategori</td>
                                <td>Toplam Telsim Aldığı Satış</td>
                                <td>Toplam Telsim Aldığı Kar</td>
                            </tr>
                            @if(!empty($deliveryReport))
                                @foreach($deliveryReport as $item)
                                    <tr style="    background: blue; color: #fff;  font-weight: 600;">
                                        <td colspan="3">@if(isset($item->delivery_personnel))
                                                {{\App\Models\User::find($item->delivery_personnel)->name}}
                                            @endif</td>
                                        <td>{{\App\Models\Sale::STATUS[$item->type]}}</td>
                                        <td>{{$item->toplamTutar}}</td>
                                        <td>{{$item->toplamTutar - $item->totalCost}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <hr class="my-5">
            <div class="col-md-12">
             <div class="card mt-5">
                <div class="card-header">Kaplama Raporu</div>
                <div class="card-header">
                    <form action="{{route('technicalCustomReport')}}" id="stockSearch" method="get">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8 fv-plugins-icon-container">
                                <label class="form-label" for="formValidationName">Başlangıç - Bitiş
                                    Tarihi</label>

                                <div class="input-group input-daterange">
                                    <input type="text" class="form-control" name="date1"
                                           value="@if($sendData){{$sendData->date1}}@endif"
                                           autocomplete="off">
                                    <div class="input-group-addon">to</div>
                                    <input type="text" class="form-control" name="date2"
                                           value="@if($sendData){{$sendData->date2}}@endif"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-sm btn-success" style="width: 100%;margin: 22px 0 0 0;">Ara</button>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                        <tr>
                            <td colspan="3">Personel</td>
                            <td>Toplam Satış</td>
                            <td>Toplam Alış</td>
                            <td>Toplam Kar</td>
                        </tr>
                        @if(!empty($technicalCustomReport))
                            @foreach($technicalCustomReport as $item)
                                <tr>
                                    <td colspan="3">{{$item['name']}}</td>
                                    <td>{{$item['CTotal']}}</td>
                                    <td>{{$item['bTotal']}}</td>
                                    <td>{{$item['CTotal'] - $item['bTotal']}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    </div>
                    <hr/>

                </div>
            </div>
            </div>
            <hr class="my-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Bayi Raporu</div>
                    </div>
                    <div class="card-header">
                        <form action="{{route('personelsellerreport')}}" id="stockSearch" method="get">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8 fv-plugins-icon-container">
                                    <label class="form-label" for="formValidationName">Başlangıç - Bitiş
                                        Tarihi</label>


                                    <div class="input-group input-daterange">
                                        <input type="text" class="form-control" name="date1"
                                               value="@if($sendData){{$sendData->date1}}@endif"
                                               autocomplete="off">
                                        <div class="input-group-addon">to</div>
                                        <input type="text" class="form-control" name="date2"
                                               value="@if($sendData){{$sendData->date2}}@endif"
                                               autocomplete="off">
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <div class="form-password-toggle">
                                        <label class="form-label" for="multicol-password">Şube</label>
                                        <div class="input-group input-group-merge">
                                            <select type="text" name="seller" class="form-select"
                                                    style="width: 100%">
                                                <option value="">Tümü</option>
                                                @foreach($sellers as $seller)
                                                    <option @if($sendData && $seller->id == $sendData->seller) selected  @endif value="{{$seller->id}}">{{$seller->name}}</option>
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
                    <div class="card-body">
                        <table class="table table-responsive">
                            <tr>
                                <td colspan="3">Şube</td>
                                <td>Kategori</td>
                                <td>Toplam Satış</td>
                                <td>Toplam Kar</td>
                            </tr>

                            @if(!empty($query))
                                @foreach($query as $item)
                                    @if($item->type != 3)
                                        <tr>
                                            <td colspan="3">{{\App\Models\Seller::find($item->seller_id)->name}}</td>
                                            <td>{{\App\Models\Sale::STATUS[$item->type]}}</td>
                                            <td>{{$item->toplamTutar}}</td>
                                            <td>{{$item->toplamTutar - $item->totalCost}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            @if(isset($kaplama))
                                @foreach($kaplama as $item)
                                    <tr>
                                        <td colspan="3">{{$item['name']}}</td>
                                        <td>Kaplama</td>
                                        <td>{{$item['CTotal']}}</td>
                                        <td>{{$item['CTotal'] - $item['bTotal']}}</td>
                                    </tr>
                                @endforeach
                            @endif

                        </table>
                    </div>
                </div>
            </div>
        </div>



        </div>

@endsection
<style>


    .table > :not(caption) > * > * {
        padding: 0.7rem 0.5rem !important;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }

    .position-relative {
        width: 100%;
        position: relative !important;
    }
</style>
@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/datepicker/css/bootstrap-datepicker.css')}}"/>

@endsection
@section('custom-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script src="{{asset('assets/datepicker/js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('assets/js/forms-pickers.js')}}"></script>
    <script>
        $('.input-daterange input').each(function () {
            $(this).datepicker({
                orientation: "bottom auto",
            }).focus(function () {
                $(this).prop("autocomplete", "off");
                //              return false;
            });
        });
    </script>
@endsection

