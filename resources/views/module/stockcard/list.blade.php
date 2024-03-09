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
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kartları /</span> Stok Kart listesi
            <a  href="{{route('stockcard.deleted')}}" type="button" formtarget="_blank" style="margin-left: 7px;" class="btn btn-success float-end ml-4 ml-3">Silinen Seri Numaraları </a>
            <button id="barcode" type="button" formtarget="_blank"
                    onclick="document.getElementById('itemFrom').submit();" style="margin-left: 7px;"
                    disabled="disabled" class="btn btn-danger float-end ml-3">Barkod Yazdır
            </button>

            @role(['Depo Sorumlusu','super-admin'])
            <a href="{{route('stockcard.create',['category'=>$category])}}" class="btn btn-primary float-end">Yeni Stok
                Kartı Ekle</a>
            <button id="multiplepriceUpdate" type="button" style="margin-right: 7px;"
                    class="btn btn-danger float-end ml-3">Fiyat Güncelle
            </button>

            @endrole
        </h4>
        <div class="card">
            <div class="card-body">
                <form action="{{route('stockcard.list')}}" id="stockSearch" method="get">
                    @csrf
                    <input type="hidden" name="category_id" value="{{$category}}"/>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="multicol-username">Stok</label>
                            <input type="text" class="form-control" value="{{old('stockName')}}" name="stockName">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="multicol-email">Marka</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="brand" class="select2" onchange="getVersion(this.value)"
                                        style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
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
                        <style>
                            .position-relative {
                                width: 100%;
                            }
                        </style>
                        <div class="col-md-3">
                            <label class="form-label" for="multicol-email">Kategori</label>
                            <div class="input-group input-group-merge w-100">
                                <select name="category" id="category" class="select2">
                                    <option value="">Tümü</option>
                                    @foreach($categories as $value)
                                        <option value="{{$value->id}}">{{$value->path}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Renk</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="color" class="form-select" style="width: 100%">
                                        <option value="">Tümü</option>
                                        @foreach($colors as $color)
                                            <option value="{{$color->id}}">{{$color->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Şube</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="seller" class="form-select" style="width: 100%">
                                        <option value="all">Tümü</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{$seller->id}}"
                                                    @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif>{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-confirm-password">Seri Numarası</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" name="serialNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>

            </div>
            <form id="itemFrom" role="form" method="POST" action="{{route('stockcard.barcodes')}}">
                @csrf
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Stok Adı</th>
                            <th>Kategori</th>
                            <th>Marka</th>
                            <th>Stok Adedi</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($stockcards as $stockcard)



                                <?php $newIdArray = implode(",",$stockcard['ids'][0]); ?>
                            <tr style="" data-toggle="collapse" data-target="#l{{$stockcard['id']}}" class="accordion-toggle">
                                <td><a href="#btn_{{$stockcard['id']}}" class="btn btn-default btn-xs selectButtonTable" id="btn_{{$stockcard['id']}}"
                                       ng-click="getStockMovementList('{{$newIdArray}}',{{$stockcard['id']}},'{{$serialNumber}}','{{$sellerCode}}','{{$colorCode}}')"><span class="bx bxs-eyedropper"></span></a></td>
                                <td>
                                    <a target="_blank" href="{{route('stockcard.barcode',['ids' => $stockcard['ids']])}}"><span class="bx bx-barcode"></span></a>
                                    @role('Depo Sorumlusu|super-admin')
                                    <button style="padding: 0" type="button" class="btn btn-sm btn-success" ng-click="multipleAllPriceUpdate(stockcarddatalistsId)" >
                                        <span style="font-size: 13px;
    padding-left: 5px;
    padding-right: 5px;" class="bx bx-dollar"></span>
                                    </button>
                                    <button style="padding: 0" type="button" class="btn btn-sm btn-danger" ng-click="multipleAllSaleUpdate(stockcarddatalistsId)" >
                                        <span style="font-size: 13px;
    padding-left: 5px;
    padding-right: 5px;" class="bx bx-dollar-circle"></span>
                                    </button>
                                    @endrole
                                </td>
                                <td>{{$stockcard['stock_name']}}</td>
                                <td>{{$stockcard['category_sperator_name']}}{{$stockcard['category_name']}}</td>
                                <td>{{$stockcard['brand_name']}}</td>
                                <td> {{$stockcard['stockData']}}</span></td>
                                <td>
                                    @role('Depo Sorumlusu|super-admin')
                                    <a target="_blank" href="{{route('stockcard.stockforserial',['id' => $stockcard['ids']])}}" class="btn btn-small"><i class='bx bxs-paper-plane'></i></a>
                                    @endrole
                                </td>
                            </tr>
                            <tr id="btn_{{$stockcard['id']}}">
                                <td colspan="6" class="hiddenRow"  id="j{{$stockcard['id']}}">
                                    <div class="accordian-body collapse" id="l{{$stockcard['id']}}">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr class="info">
                                                <th><input id="{{$stockcard['id']}}" name="all_selected" type="checkbox"></th>
                                                <th></th>
                                                <th>Seri No</th>
                                                @role(['Depo Sorumlusu','super-admin'])
                                                <th>Maliyet</th>
                                                <th>D. Maliyet</th>
                                                @endrole
                                                <th>Satış F.</th>
                                                <th>Renk</th>
                                                <th>Marka</th>
                                                <th>Model</th>
                                                <th>Kategori</th>
                                                <th>Şube</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>


                                            <tr class="info" ng-repeat="stockcarddatalist in stockcarddatalists.slice((currentPage -1) * itemsPerPage, currentPage * itemsPerPage)"

                                                ng-style="stockcarddatalist.quantity == 0 && {'background': '#99c9e1'} || stockcarddatalist.type == 3 && {'background': '#f00'} "
                                                data-quantity="stockcarddatalist.quantity" ng-if="stockcarddatalist.quantity > 0" style="font-size: 12px">
                                                <td class="text-center"><input type="checkbox" ng-model="barcodeinput_{{$stockcard['id']}}_c" ng-change="checkInputs()" name="selected[]" id="{{$stockcard['id']}}_c"
                                                                               value="@{{stockcarddatalist.id}} "
                                                                               class="form-check-input {{$stockcard['id']}}_c">
                                                    <input type="hidden"
                                                           name="barcode[@{{stockcarddatalist.id}}][]"
                                                           value="@{{stockcarddatalist.id}}">
                                                </td>
                                                <td>@{{stockcarddatalist.id}}</td>
                                                <td>@{{stockcarddatalist.serial_number}}</td>
                                                @role(['Depo Sorumlusu','super-admin'])
                                                <td><strong>@{{stockcarddatalist.cost_price}} ₺</strong></td>
                                                <td><strong>@{{stockcarddatalist.base_cost_price}} ₺</strong></td>
                                                @endrole
                                                <td><strong>@{{stockcarddatalist.sale_price}} ₺</strong></td>
                                                <td>@{{stockcarddatalist.color_name}}</td>
                                                <td>@{{stockcarddatalist.brand_name}}</td>
                                                <td>@{{stockcarddatalist.versions}}</td>
                                                <td>
                                                    @{{stockcarddatalist.category_sperator_name}}  @{{stockcarddatalist.category_name}}
                                                </td>
                                                <td>@{{stockcarddatalist.seller_name}}</td>
                                                <td data="@{{stockcarddatalist.type}}">
                                                    <span ng-if="stockcarddatalist.type == 4" class="badge bg-primary">TRANSFER SÜRECİNDE</span>
                                                    <span ng-if="stockcarddatalist.type == 3"  class="badge bg-primary">HASARLI ÜRÜN</span>
                                                    <span ng-if="stockcarddatalist.type == 5"  class="badge bg-primary">TEKNİK SERVİS SÜRECİNDE</span>

                                                    <span ng-if="stockcarddatalist.type == 1">
                                                                <a title="Sevk Et"
                                                                   href="{{route('transfer.create')}}?serial_number=@{{stockcarddatalist.serial_number}}&type=other"
                                                                   class="btn btn-sm btn-icon btn-success">
                                                                    <span class="bx bx-transfer"></span>
                                                                </a>
                                                                @role('Depo Sorumlusu|super-admin')
                                                                <button type="button"
                                                                        ng-click="priceModal(stockcarddatalist.id)"
                                                                        class="btn btn-sm btn-icon btn-danger">
                                                                    <span class="bx bxs-dollar-circle"></span>
                                                                </button>

                                                                @endrole
                                                            @role('super-admin')

                                                                <a title="Sevk Et" ng-click="deleteMovement(stockcarddatalist.id)"

                                                                   class="btn btn-sm btn-icon btn-success">
                                                                    <span class="bx bx-trash"></span>
                                                                </a>
                                                                @endrole
                                                                <button
                                                                    ng-click="demandModal({{$stockcard['id']}},'{{$stockcard['stock_name']}}','@{{stockcarddatalist.color_id}}')"
                                                                    type="button" class="btn btn-sm btn-danger">
                                                                    <i class="bx bx-radar"></i>
                                                                </button>
                                                            </span>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>

                                        <pagination
                                            total-items="totalItems"
                                            items-per-page="itemsPerPage"
                                            ng-model="currentPage"
                                            data-max-size="maxSize"
                                            class="pagination-sm">
                                        </pagination>


                                    </div>
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="card mt-4">
            <div class="card-body mt-4 p-4 box has-text-centered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                {!! $links !!}
            </div>
        </div>

        <hr class="my-5">
    </div>
    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="transferForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                <input id="id" name="id" type="hidden">
                <input id="type" name="type" value="other" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Sevk İşlemi</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Serial Number</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="serial_number[]"/>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="sellerBackdrop" class="form-label">Şube</label>
                            <select class="form-control" name="seller_id" id="sellerBackdrop">
                                @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}">{{$seller->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="sellerBackdrop" class="form-label">Neden</label>
                            <select class="form-control" name="reason_id" id="sellerBackdrop">
                                <option value="4">SATIŞ</option>
                                <option value="5">SIFIR</option>
                                <option value="6">İKİNCİ El SATIŞ</option>
                                <option value="7">SATIŞ İADE</option>
                                <option value="8">HASARLI İADE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Sevk İşlemi Başlat</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="priceModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="priceForm">
                @csrf
                <input id="stockCardMovementId" name="stock_card_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Fiyat Değiştir</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="demandModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content" style="padding: 1px">
                <div class="modal-header">Ürün Adı : <span></span></div>
                <form action="{{route('demand.store')}}" method="post">
                    <input type="hidden" name="id" id="id" value="">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Renk</label>
                                <select class="form-select" id="color" name="color_id">
                                    @foreach($colors as $color)
                                        <option value="{{$color->id}}">{{$color->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Açıklama</label>
                                <input type="text" id="nameSmall" name="description" class="form-control"
                                       placeholder="Açıklama">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="multiplepriceModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="multiplepriceForm">
                @csrf
                <input id="stockCardMovementIdArray" name="stock_card_id_multiple" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Maliyet</label>
                            <input type="text" id="cost_price" class="form-control" name="cost_price"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Destekli Maliyet</label>
                            <input type="text" id="base_cost_price" class="form-control" name="base_cost_price"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Fiyat Değiştir</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="multiplesaleModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="multiplesaleForm">
                @csrf
                <input id="stockCardMovementIdArray" name="stock_card_id_multiple" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Fiyat Değiştir</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('stockcard.movementdelete')}}" id="deleteModalForm">
                @csrf
                <input id="stockCardMovementIdDelete" name="stock_card_movement_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Silmek icin not girmelisiniz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Not</label>
                            <input type="text" id="note" class="form-control" name="note" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Sil</button>
                </div>
            </form>
        </div>
    </div>
@endsection
<style>
    .pagination {
        display: inline-block;
        padding-left: 0;
        margin: 20px 0;
        border-radius: 4px
    }

    .pagination>li {
        display: inline
    }

    .pagination>li>a,.pagination>li>span {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #337ab7;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd
    }

    .pagination>li:first-child>a,.pagination>li:first-child>span {
        margin-left: 0;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px
    }

    .pagination>li:last-child>a,.pagination>li:last-child>span {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px
    }

    .pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover {
        z-index: 3;
        color: #23527c;
        background-color: #eee;
        border-color: #ddd
    }

    .pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover {
        z-index: 2;
        color: #fff;
        cursor: default;
        background-color: #337ab7;
        border-color: #337ab7
    }

    .pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover {
        color: #777;
        cursor: not-allowed;
        background-color: #fff;
        border-color: #ddd
    }

    .pagination-lg>li>a,.pagination-lg>li>span {
        padding: 10px 16px;
        font-size: 18px;
        line-height: 1.3333333
    }

    .pagination-lg>li:first-child>a,.pagination-lg>li:first-child>span {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px
    }

    .pagination-lg>li:last-child>a,.pagination-lg>li:last-child>span {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px
    }

    .pagination-sm>li>a,.pagination-sm>li>span {
        padding: 5px 10px;
        font-size: 12px;
        line-height: 1.5
    }

    .pagination-sm>li:first-child>a,.pagination-sm>li:first-child>span {
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px
    }

    .pagination-sm>li:last-child>a,.pagination-sm>li:last-child>span {
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px
    }

    .pager {
        padding-left: 0;
        margin: 20px 0;
        text-align: center;
        list-style: none
    }

    .pager li {
        display: inline
    }

    .pager li>a,.pager li>span {
        display: inline-block;
        padding: 5px 14px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 15px
    }

    .pager li>a:focus,.pager li>a:hover {
        text-decoration: none;
        background-color: #eee
    }

    .pager .next>a,.pager .next>span {
        float: right
    }

    .pager .previous>a,.pager .previous>span {
        float: left
    }

    .pager .disabled>a,.pager .disabled>a:focus,.pager .disabled>a:hover,.pager .disabled>span {
        color: #777;
        cursor: not-allowed;
        background-color: #fff
    }
</style>
@section('custom-js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        var selected = [];
        $(document).ready(function () {
            $("#multiplepriceUpdate").click(function (e) {
                $("input:checkbox[name^='selected']:checked").each(function () {
                    selected.push($(this).val());
                });
                if(selected.length > 0)
                {
                    $("#multiplepriceModal").modal('show');
                    $("#multiplepriceModal").find("#stockCardMovementIdArray").val(selected);
                }else{
                    Swal.fire('Seçim Yapınız');
                }
            });


        });


        $("#multiplepriceForm").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var actionUrl = '{{route('stockcard.multiplepriceupdate')}}';
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        }, buttonsStyling: !1
                    });
                    $("#multiplepriceModal").modal('hide');
                    window.location.reload();
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#multiplepriceModal").modal('hide');
                }
            });

        });
        $("#multiplesaleForm").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var actionUrl = '{{route('stockcard.multiplesaleupdate')}}';
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        }, buttonsStyling: !1
                    });
                    $("#multiplesaleModal").modal('hide');
                    window.location.reload();
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#multiplesaleModal").modal('hide');
                }
            });

        });
    </script>

    <script>
        $("input[name^='selected']").on('change', function () {

            var selected = $("input[type^='checkbox']:checked");
            if (selected.length) {
                $('#barcode').attr('disabled', false);
            } else {
                $('#barcode').attr('disabled', true);
            }
        });


        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#serialBackdrop").val(id);
            $("#stockCardId").val(id);
        }

        $("#transferForm").submit(function (e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var actionUrl = '{{route('stockcard.sevk')}}';

            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardId").val() + '',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                }
            });

        });

        $("#priceForm").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var actionUrl = '{{route('stockcard.singlepriceupdate')}}';
            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardMovementId").val() + '',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#priceModal").modal('hide');
                    window.location.reload();
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#priceModal").modal('hide');
                }
            });

        });
    </script>


    <script>
        app.directive('loading', function () {
            return {
                restrict: 'E',
                replace: true,
                template: '<p><img src="img/loading.gif"/></p>', // Define a template where the image will be initially loaded while waiting for the ajax request to complete
                link: function (scope, element, attr) {
                    scope.$watch('loading', function (val) {
                        val = val ? $(element).show() : $(element).hide();  // Show or Hide the loading image
                    });
                }
            }
        }).directive('ngConfirmClick', [
            function() {
                return {
                    link: function (scope, element, attr) {
                        var msg = attr.ngConfirmClick || "Are you sure?";
                        var clickAction = attr.confirmedClick;
                        element.bind('click', function (event) {
                            if (window.confirm(msg)) {
                                scope.$eval(clickAction)
                            }
                        });
                    }
                };
            }]).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {




            $scope.stockcarddatalists = [];
            $scope.currentPage = 1;
            $scope.itemsPerPage = 20;
            $scope.maxSize = 20;
            $scope.totalItems = 20;

            $scope.getStockSearch = function () {
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/stockSearch';   // Returns base URL (https://example.com)
                $http({
                    method: 'POST',
                    url: postUrl,
                    data: $("#stockSearch").serialize(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.stockSearchLists = response.data;
                });
            }

            $scope.deleteMovement = function (id) {
                Swal.fire({
                    title: "Silmek istediginizden eminmisiniz?",
                    text: "Silme islemi yapilirken kesinlikle not girmelisiniz!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "EVET!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#stockCardMovementIdDelete').val(id);
                        $('#deleteModal').modal('show');
                    }
                });
            }


            $scope.getStockCard = function () {
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/getStockCardCategory?id=' + {{$category}} + '';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    data: $("#stockSearch").serialize(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.stockSearchLists = response.data;
                });
            }

            $scope.getStockquantity = function (ids,jId,serialNumber,seller,color) {
                //
            }


            $scope.getStockMovementList = function (id,jId,serialNumber,seller,color) {
                $scope.stockcarddatalists = [];

                $(".hiddenRow").hide();
                $(".selectButtonTable").show();
                Swal.showLoading();
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/getStockMovementList?id=' + id+ '&serialNumber=' + serialNumber+ '&seller=' + seller+ '&color=' + color+ '';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.stockcarddatalists = response.data.data;
                    $scope.stockcarddatalistsId = response.data.ids;
                    $scope.totalItems =  $scope.stockcarddatalists.length;
                    Swal.close();
                    $("#j"+jId).show();
                    $("#btn_"+jId).hide();
                    $("#l"+jId).show();
                });
            }

            $scope.priceModal = function (id) {
                $("#priceModal").modal('show');
                $("#priceModal #stockCardMovementId").val(id);
            }

            $scope.demandModal= function (id, name, color) {
                $("#demandModal").modal('show');
                $("#demandModal").find('.modal-header span').html(name);
                $("#demandModal").find('input#id').val(id);
                $("#demandModal").find('select#color').val(color).trigger('change');
                $("#demandModal").find('select#color').attr('data-color', color);
            }

            $scope.confirmedAction = function() {
                alert('confirmed!')
            }

            $scope.isButtonDisabled = true;

            $scope.checkInputs = function() {

                var selected = $("input[type^='checkbox']:checked");
                if (selected.length) {
                    $('#barcode').attr('disabled', false);
                } else {
                    $('#barcode').attr('disabled', true);
                }

                // Herhangi bir input seçildiğinde, düğmenin durumunu kontrol et
                /*  if ($scope.input1 || $scope.input2 || $scope.input3) {
                      $scope.isButtonDisabled = false; // Herhangi bir input seçiliyse düğmeyi etkinleştir
                  } else {
                      $scope.isButtonDisabled = true; // Hiçbir input seçili değilse düğmeyi devre dışı bırak
                  } */
            };

            $scope.multipleAllPriceUpdate = function (ids) {

                if(ids == undefined)
                {
                    Swal.fire('Seçim Yapılmadı');
                }
                $("#multiplepriceModal").modal('show');
                $("#multiplepriceModal").find("#stockCardMovementIdArray").val(ids);
            }

            $scope.multipleAllSaleUpdate = function (ids) {

                if(ids == undefined)
                {
                    Swal.fire('Seçim Yapılmadı');
                }
                $("#multiplesaleModal").modal('show');
                $("#multiplesaleModal").find("#stockCardMovementIdArray").val(ids);
            }

        });
    </script>


    <!--script>
        function demandModal(id, name, color) {
            $("#demandModal").modal('show');
            $("#demandModal").find('.modal-header span').html(name);
            $("#demandModal").find('input#id').val(id);
            $("#demandModal").find('select#color').val(color).trigger('change');
            $("#demandModal").find('select#color').attr('data-color', color);
        }
    </script -->


    <script>

        $('input[name=all_selected]').click(function(event) {
            var id = this.getAttribute('id')

            var checkboxes = document.getElementsByClassName(''+event.target.id+'_c');
            console.log(checkboxes.length);
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = event.checked;
            }

            if (this.checked) {
                $('.'+event.target.id+'_c').each(function() {
                    $('.'+event.target.id+'_c').prop('checked', true);
                });
                // $('#'+event.target.id+'_c').prop('checked', true);
            } else {
                $('#'+event.target.id+'_c').prop('checked', false);
            }
        });

    </script>

@endsection

