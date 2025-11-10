@extends('layouts.admin')

@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printableArea, #printableArea * {
                visibility: visible;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width:100%
            }
            #printableArea table {
                border:solid #000 !important;
                border-width:1px 0 0 1px !important;
            }
            th, td {
                border:solid #000 !important;
                border-width:0 1px 1px 0 !important;
            }
        }
    </style>


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div id="printableArea" class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex svg-illustration mb-3 gap-2">
                                    <span class="app-brand-text text-body fw-bolder">GÖNDERİCİ BAYİ : {{\App\Models\Seller::find($transfer->main_seller_id)->name}}</span>
                                </div>
                                    <div class="d-flex svg-illustration mb-3 gap-2">
                                    <span class="app-brand-text text-body fw-bolder">ALICI BAYİ : {{\App\Models\Seller::find($transfer->delivery_seller_id)->name}}</span>
                                </div>
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
                        <table class="table table-responsive border-top m-0" border="1" width="100%">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Seri No</th>
                                <th>Stok Adı</th>
                                <th>Kategori</th>
                                <th>Marka</th>
                                <th>Model</th>
                                <th>RENK</th>
                                <th>Adet</th>

                            </tr>
                            </thead>
                            <tbody>
                             @if($transfer->serial_list)
                                 <?php $i = 1; ?>
                                @foreach($transfer->detail as $value)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$value['serial']}}</td>
                                            <td>{{$value['name']}}</td>
                                            <td class="text-nowrap"> {{$value['category']}}</td>
                                            <td class="text-nowrap">{{$value['brand'] ?? 'Bulunamadı'}}</td>
                                            <td class="text-nowrap"><?php
                                                $as = json_decode($value['version'], JSON_UNESCAPED_UNICODE);
                                                if (is_array($as) && count($as) > 0) {
                                                    echo $as[0];
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                                 </td>
                                            <td class="text-nowrap">{{$value['color']}} </td>
                                            <td class="text-nowrap">1</td>
                                        </tr>

                                     <?php $i++; ?>
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
                                        <span>{{$transfer->user->name ?? 'N/A'}}</span>
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
                           href="#" onclick="printDiv()">
                            Print
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    function printDiv(){
        var divName = 'printableArea';
        var printContents = document.getElementById(divName).innerHTML;
        w = window.open();
        w.document.write(printContents);
        w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
        w.document.close(); // necessary for IE >= 10
        w.focus(); // necessary for IE >= 10
    }
 </script>
