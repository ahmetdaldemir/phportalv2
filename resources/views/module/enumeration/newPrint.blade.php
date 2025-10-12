<html lang="tr">
<head>
    <title>Aksesuarlar | PhoneHospital</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<!--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            window.onload = function() { window.print(); window.close(); }
        });
    </script>-->
    <link rel="stylesheet"  type="text/css"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
</head>
<body>
<div id="printableArea">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">
                   @foreach($sellers as $seller)
                   @if($seller->id == $enumeration->seller_id) {{$seller->name}} @endif
                @endforeach
            </span></h4>
        <div class="row">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Sayim Yapilan</div>
                    <div class="card-body">
                        <table class="table table-responsive">
                            <thead>
                            <tr style="font-weight: 600; background: cadetblue; color: #fff; font-size: 10px;">
                                <td>Stok Adı</td>
                                <td>Stok Renk</td>
                                <td>Stok Bayi</td>
                                <td>Seri</td>
                                <td>Durum</td>
                                <td>---</td>
                             </tr>
                            </thead>
                            <tbody>

                            @foreach($dataCol as $item)
                               <tr style="font-size: 10px;font-weight: 600;">
                                   <td>{{$item['stock']['name']}}</td>
                                   <td>{{$item['color']['name']}}</td>
                                   <td>{{$item['seller']['name']}}</td>
                                   <td>{{$item['serial_number'] ??'Bulunamadı'}}</td>
                                   <td><span class="{{$item["class_string"]}}">{{$item["type_name"]}}</span></td>
                                   <td>
                                       @if($item['seller_id'] == $enumeration->seller_id && $item['type'] == 1)
                                       <span>OK</span>
                                       @endif
                                       @if($item['seller_id'] != $enumeration->seller_id)
                                       <span class="badge rounded-pill bg-danger" >Bayi Farkli</span>
                                       @endif
                                       @if($item['seller_id'] == 0)
                                       <span class="badge rounded-pill bg-warning" >Hatali Urun</span>
                                       @endif
                                   </td>
                                </tr>
                            @endforeach
                            </tbody>

                            <?php
                            $falseseller = [];
                            $ok = [];
                            foreach($dataCol  as $item)
                            {
                                if($item['seller_id'] == $enumeration->seller_id && $item['type'] == 1)
                                {
                                    $ok[] = $item['seller_id'];
                                }

                                if($item['seller_id'] != $enumeration->seller_id)
                                {
                                    $falseseller[] = $item['seller_id'];
                                }
                            }
                            ?>
                            <tbody style="border: 5px solid #d62c2c;">
                               <tr>
                                   <td colspan="5" style="font-size: 12px;font-weight: 700;">Eksik</td>
                                   <td>{{$totalstock - count($ok)}}</td>
                               </tr>
                               <tr>
                                   <td colspan="5" style="font-size: 12px;font-weight: 700;">Yanlış bayide</td>
                                   <td>{{count($falseseller)}}</td>
                               </tr>
                               <tr>
                                   <td colspan="5" style="font-size: 12px;font-weight: 700;">Satıldı</td>
                                   <td></td>
                               </tr>
                               <tr>
                                   <td colspan="5" style="font-size: 12px;font-weight: 700;">Ok</td>
                                   <td>{{count($ok)}}</td>
                               </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Eksik</div>

                    <div class="card-body">
                        <table class="table table-responsive">
                            <thead>
                            <tr style="font-weight: 600; background: cadetblue; color: #fff; font-size: 12px;">
                                <td>Stok Adı</td>
                                <td>Stok Renk</td>
                                <td>Stok Bayi</td>
                                <td>Seri</td>
                                <td>Durum</td>
                                <td>---</td>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($dataCol1 as $item)
                                <tr style="font-size: 15px;font-weight: 600;">
                                    <td>{{$item['stock']['name']}}</td>
                                    <td>{{$item['color']['name']}}</td>
                                    <td>{{$item['seller']['name']}}</td>
                                    <td>{{$item['serial_number'] ??'Bulunamadı'}}</td>
                                    <td><span class="{{$item["class_string"]}}">{{$item["type_name"]}}</span></td>
                                    <td>
                                      Eksik
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </body>
</html>
