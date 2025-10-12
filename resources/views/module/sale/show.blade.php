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
                                    <span
                                        class="app-brand-text demo text-body text-uppercase fw-bolder">{{is_null($invoice->account) ? 'Genel Cari':$invoice->account->fullname}}</span>
                                </div>
                                <p class="mb-1">{{is_null($invoice->account) ? 'Genel Cari':$invoice->account->phone1}}</p>
                                <p class="mb-1">{{is_null($invoice->account) ? 'Genel Cari':$invoice->account->email}}</p>
                                <p class="mb-0">{{is_null($invoice->account) ? 'Genel Cari':$invoice->account->address.'/'.$invoice->account->city->name .'/'.$invoice->account->town->name}}</p>
                            </div>
                            <div>
                                <h4>#{{$invoice->number}}</h4>
                                <div class="mb-2">
                                    <span class="me-1">Tarih:</span>
                                    <span class="fw-semibold">{{$invoice->create_date}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="table-responsive">
                        <table class="table border-top m-0">
                            <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Seri Numarası</th>
                                <th>Adet</th>
                                <th>Alış Fiyatı</th>
                                <th>Destekli Fiyat</th>
                                <th>Satış Fiyatı</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($invoice->detail)
                                @foreach($invoice->detail as $item)
                                    <tr>
                                        <td class="text-nowrap">{{$item->stock->brand->name}}/
                                                <?php
                                                $datas = json_decode($item->stock->version(), TRUE);
                                                foreach ($datas as $mykey => $myValue) {
                                                    echo "<b>".$myValue,"</b>";
                                                }
                                                ?>  /{{$item->stock->name}}</td>
                                        <td class="text-nowrap">{{$item->serial_number}}</td>
                                        <td class="text-nowrap">{{$item->quantity}}</td>
                                        <td>{{$item->cost_price}}</td>
                                        <td>{{$item->base_cost_price}}</td>
                                        <td>{{$item->sale_price}}</td>
                                    </tr>
                                @endforeach
                            @endif

                            <tr>
                                <td colspan="3" class="align-top px-4 py-5">
                                    <p class="mb-2">
                                        <span class="me-1 fw-semibold">Personel:</span>
                                        <span>{{$invoice->staff->name}}</span>
                                    </p>
                                </td>
                                <td class="text-end px-4 py-5">
                                    <p class="mb-2">Ara Toplam:</p>
                                    <p class="mb-2">İndirim:</p>
                                    <p class="mb-2">KDV:</p>
                                    <p class="mb-0">Toplam:</p>
                                </td>
                                <td class="px-4 py-5">
                                    <p class="fw-semibold mb-2">1</p>
                                    <p class="fw-semibold mb-2">1</p>
                                    <p class="fw-semibold mb-2">1</p>
                                    <p class="fw-semibold mb-0">{{$invoice->total_price}}</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-semibold">Not:</span>
                                <span>{{$invoice->description}}</span>
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
