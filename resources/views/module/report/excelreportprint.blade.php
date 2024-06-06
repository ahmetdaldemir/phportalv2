<html>
<head>
    <title>Aksesuarlar | PhoneHospital</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <link rel="stylesheet"  type="text/css"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
    <script>
        $(document).ready(function() {
            window.onload = function() { window.print(); window.close(); }
        });
    </script>
</head>
<body>
<div id="printableArea">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">

            </span>
        </h4>
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{$date1}} / {{$date2}}
                    </div>
                    <div class="card-body">
                        <table style="width: 100%;font-size: 12px;font-weight: 700" class="table table-bordered">
                            <tr>
                                <td colspan="1">Personel</td>
                                <td colspan="2">Aksesuar</td>
                                <td colspan="2">Teknik Servis</td>
                                <td colspan="2">Kaplama</td>
                                <td colspan="2">Telefon</td>
                                <td colspan="2">Teslim Alan</td>
                            </tr>
                            <tr>
                                <td colspan="1">-</td>
                                <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: red;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: red;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Kar</td>
                            </tr>
                            @foreach($users as $user)
                                <tr>
                                    <td colspan="1">{{$user->name}}</td>
                                    <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">
                                        @if(isset($accessory['ar'][$user->id])) {{$accessory['ar'][$user->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">
                                        @if(isset($accessory['ar'][$user->id]))
                                            {{$accessory['ar'][$user->id] - $accessory['arbc'][$user->id]}}
                                        @else
                                            0
                                        @endif
                                       </td>
                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($technical['arbc'][$user->id])) {{$technical['arbc'][$user->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($technical['arbc'][$user->id]))
                                            {{$technical['arbc'][$user->id] - $technical['ar'][$user->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">
                                        @if(isset($cover['arbc'][$user->id])) {{$cover['arbc'][$user->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">
                                        @if(isset($cover['arbc'][$user->id]))
                                            {{$cover['arbc'][$user->id] - $cover['ar'][$user->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td colspan="1" style="background: blue;color: #fff;text-align: center;">
                                        @if(isset($phones['ar'][$user->id])) {{$phones['ar'][$user->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: blue;color: #fff;text-align: center;">
                                        @if(isset($phones['ar'][$user->id]))
                                            {{$phones['ar'][$user->id] - $phones['arbc'][$user->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>

                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($teslimalan['arbc'][$user->id])) {{$teslimalan['arbc'][$user->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($teslimalan['arbc'][$user->id]))
                                            {{$teslimalan['arbc'][$user->id] - $teslimalan['ar'][$user->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="card-body">
                        <table style="width: 100%;font-size: 12px;font-weight: 700" class="table table-bordered">
                            <tr>
                                <td colspan="1">Bayi</td>
                                <td colspan="2">Aksesuar</td>
                                <td colspan="2">Teknik Servis</td>
                                <td colspan="2">Kaplama</td>
                                <td colspan="2">Telefon</td>
                                <td colspan="2">Teslim Alan</td>
                            </tr>
                            <tr>
                                <td colspan="1">-</td>
                                <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: red;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: red;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Kar</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Ciro</td>
                                <td colspan="1" style="background: blue;color: #fff;text-align: center;">Kar</td>
                            </tr>
                            @foreach($sellers as $seller)
                                <tr>
                                    <td colspan="1">{{$seller->name}}</td>
                                    <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">
                                        @if(isset($accessorySeller['ar'][$seller->id])) {{$accessorySeller['ar'][$seller->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: cadetblue;color: #fff;text-align: center;">
                                        @if(isset($accessorySeller['ar'][$seller->id]))
                                            {{$accessorySeller['ar'][$seller->id] - $accessorySeller['arbc'][$seller->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($technicalSeller['arbc'][$seller->id])) {{$technicalSeller['arbc'][$seller->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($technicalSeller['arbc'][$seller->id]))
                                            {{$technicalSeller['arbc'][$seller->id] - $technicalSeller['ar'][$seller->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">
                                        @if(isset($coverSeller['arbc'][$seller->id])) {{$coverSeller['arbc'][$seller->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: #31a449;color: #fff;text-align: center;">
                                        @if(isset($coverSeller['arbc'][$seller->id]))
                                            {{$coverSeller['arbc'][$seller->id] - $coverSeller['ar'][$seller->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td colspan="1" style="background: blue;color: #fff;text-align: center;">
                                        @if(isset($phonesSeller['ar'][$seller->id])) {{$phonesSeller['ar'][$seller->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: blue;color: #fff;text-align: center;">
                                        @if(isset($phonesSeller['ar'][$seller->id]))
                                            {{$phonesSeller['ar'][$seller->id] - $phonesSeller['arbc'][$seller->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>

                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($teslimalanSeller['arbc'][$seller->id])) {{$teslimalanSeller['arbc'][$seller->id]}}@else 0 @endif
                                    </td>
                                    <td colspan="1" style="background: red;color: #fff;text-align: center;">
                                        @if(isset($teslimalanSeller['arbc'][$seller->id]))
                                            {{$teslimalanSeller['arbc'][$seller->id] - $teslimalanSeller['ar'][$seller->id]}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>



<style>
    .position-relative {
        width: 100%;
        position: relative !important;
    }
    .table>:not(caption)>*>* {
        padding: 0.2rem 0.2rem;
     }
</style>
