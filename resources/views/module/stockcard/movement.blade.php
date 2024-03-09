@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kartları /</span> Stok Kart Hareketleri</h4>

        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Hareket Formu</h5>
                <div class="card-body">
                    <form method="post" id="stockmovementform" class="form-repeater" action="javascript();">
                        <input name="stock_card_id" value="{{$stock_card_id}}" type="hidden">
                        <div data-repeater-list="group_a">
                            <div style="    border: 1px solid #ccc;padding: 10px;margin-top: 5px" data-repeater-item="">
                                <div class="row">
                                    <div class="mb-3 col-lg-3 col-xl-1 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-1">Müşteri</label>
                                        <select name="type" id="form-repeater-1-1" class="form-select">
                                            <option value="2">Giriş</option>
                                            <option value="1">Çıkış</option>
                                            <option value="0">Sevk</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-3 col-xl-1 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-1">Tip</label>
                                        <select name="type" id="form-repeater-1-1" class="form-select">
                                            <option value="2">Giriş</option>
                                            <option value="1">Çıkış</option>
                                            <option value="0">Sevk</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Seri No</label>
                                        <input name="serial" type="text" id="form-repeater-1-4" class="form-control" placeholder="············">
                                    </div>
                                    <div class="mb-3 col-lg-3 col-xl-1 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-2">Adet</label>
                                        <input type="number" name="quantity" id="form-repeater-1-2" class="form-control" value="1" min="1">
                                    </div>
                                    <div class="mb-3 col-lg-3 col-xl-1 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-3">KDV</label>
                                        <select name="tax" id="form-repeater-1-3" class="form-select">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="8">8</option>
                                            <option value="18">18</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Maliyet</label>
                                        <input name="cost_price" type="text" id="form-repeater-1-4" class="form-control" placeholder="············">
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Destekli Maliyet</label>
                                        <input name="base_cost_price" type="text" id="form-repeater-1-4" class="form-control" placeholder="············">
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Satış Fiyatı</label>
                                        <input name="sale_price" type="text" id="form-repeater-1-4" class="form-control" placeholder="············">
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Şube</label>
                                        <select name="seller_id" class="form-control">
                                            @foreach($sellers as $seller)
                                                <option @if(isset($stockcards))
                                                            {{ $stockcards->hasSeller($seller->id) ? 'selected' : '' }}
                                                        @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Depo</label>
                                        <select name="warehouse_id" class="form-control">
                                            @foreach($warehouses as $warehouse)
                                                <option @if(isset($stockcards))
                                                            {{ $stockcards->hasWarehouse($warehouse->id) ? 'selected' : '' }}
                                                        @endif  value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Renk</label>
                                        <select name="color_id" class="form-control">
                                            @foreach($colors as $color)
                                                <option @if(isset($stockcards))
                                                            {{ $stockcards->hasColor($color->id) ? 'selected' : '' }}
                                                        @endif  value="{{$color->id}}">{{$color->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Neden</label>
                                        <select name="reason_id" class="form-control">
                                            @foreach($reasons as $reason)
                                                <option @if(isset($stockcards))
                                                            {{ $stockcards->hasReason($reason->id) ? 'selected' : '' }}
                                                        @endif  value="{{$reason->id}}">{{$reason->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-lg-4 col-xl-2 col-12 mb-0">
                                        <label class="form-label" for="form-repeater-1-4">Açıklama</label>
                                       <input name="description" class="form-control" type="text" />
                                    </div>
                                    <div class="mb-3 col-lg-12 col-xl-1 col-12 d-flex align-items-center mb-0">
                                        <button class="btn btn-label-danger mt-4" data-repeater-delete="">
                                            <i class="bx bx-x me-1"></i>
                                            <span class="align-middle">Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0 mt-4">
                            <button type="button" class="btn btn-primary" data-repeater-create="">
                                <i class="bx bx-plus me-1"></i>
                                <span class="align-middle">Yeni Hareket Ekle</span>
                            </button>
                        </div>
                        <hr class="my-5">
                        <button onclick="saveStockMovement('add_movement')" class="btn btn-danger">
                            <i class="bx bx-plus-medical me-1"></i>
                            <span class="align-middle">Tüm Hareketleri Kaydet</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <hr class="my-5">


        <div class="card">
            <div class="card-header">
                <a href="{{route('stockcard.create')}}" class="btn btn-primary float-end">Yeni Stok Kartı Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Stok Adı</th>
                        <th>SKU</th>
                        <th>Barkod</th>
                        <th>Serial</th>
                        <th>Adet</th>
                        <th>Şube</th>
                        <th>Depo</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($movements as $movement)
                        <tr>
                            <td><strong>{{$movement->stock->name}}</strong></td>
                            <td><strong>{{$movement->stock->sku}}</strong></td>
                            <td><strong>{{$movement->stock->barcode}}</strong></td>
                            <td><strong>{{$movement->serial_number}}</strong></td>
                            <td><strong>{{$movement->quantity}}</strong></td>
                            <td><strong>{{$movement->seller->name}}</strong></td>
                            <td><strong>{{$movement->warehouse->name}}</strong></td>
                            <td>
                                <a href="{{route('stockcard.showmovemnet',['id' => 1])}}"
                                   class="btn btn-icon btn-success">
                                    <span class="bx bxl-edge"></span>
                                </a>
                                <a href="{{route('stockcard.edit',['id' => 1])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                                <a href="{{route('stockcard.delete',['id' => 1])}}"
                                   class="btn btn-icon btn-danger">
                                    <span class="bx bxs-trash"></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
@endsection
