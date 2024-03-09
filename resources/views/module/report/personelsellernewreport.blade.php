@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">RAPOR <small>(<b>2 rapordada aynı veriler gözükmektedir.Sorun teşkil etmemektedir.</b>)</small></span></h4>

        <div class="card mt-5">
            <div class="card-header">
                <h3>Personel Teknik Raporu</h3>
            </div>
            <div class="card-header">
                <form action="{{route('personelsellernewreport')}}" id="stockSearch" method="get">
                    @csrf
                    <div class="row g-3">
                        <input name="types" value="personel" type="hidden">
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
                <label>Teknik Servis Raporu</label>
                <table class="table table-responsive">
                    <tr>
                        <td colspan="3">Personel</td>
                        <td>Toplam Satış</td>
                        <td>Toplam Alış</td>
                        <td>Toplam Kar</td>
                    </tr>
                    @if(!empty($technicalReport))
                        @foreach($technicalReport as $item)
                            <tr style="    background: blue; color: #fff;  font-weight: 600;">
                                <td colspan="3">{{$item['name']??NULL}}</td>
                                <td>{{$item['CTotal']??NULL}}</td>
                                <td>{{$item['bTotal']??NULL}}</td>
                                <td>{{$item['CTotal'] - $item['bTotal']}}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
                <hr/>

            </div>
        </div>


        <div class="card mt-5">
            <div class="card-header">
                <h3>Bayi Teknik Raporu</h3>
            </div>
            <div class="card-header">
                <form action="{{route('personelsellernewreport')}}" id="stockSearch" method="get">
                    @csrf
                    <div class="row g-3">
                        <input name="types" value="bayi" type="hidden">

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
                <label>Teknik Servis Raporu</label>
                <table class="table table-responsive">
                    <tr>
                        <td colspan="3">Personel</td>
                        <td>Toplam Satış</td>
                        <td>Toplam Alış</td>
                        <td>Toplam Kar</td>
                    </tr>
                    @if(!empty($technicalReport))
                        @foreach($technicalReport as $item)
                            <tr style="    background: blue; color: #fff;  font-weight: 600;">
                                <td colspan="3">{{$item['name']??NULL}}</td>
                                <td>{{$item['CTotal']??NULL}}</td>
                                <td>{{$item['bTotal']??NULL}}</td>
                                <td>{{$item['CTotal'] - $item['bTotal']}}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
                <hr/>

            </div>
        </div>

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

