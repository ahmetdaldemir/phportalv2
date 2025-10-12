@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">İade Listesi </span></h4>

        <div class="card">
                <div class="card-body">
                    <form action="{{route('stockcard.refundlist')}}" id="stockSearch" method="get">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label" for="multicol-email">Marka</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="brand" class="form-select" onchange="getVersion(this.value)" style="width: 100%">
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
                                        <select type="text" id="version_id" name="version" class="form-select" style="width: 100%">
                                            <option value="">Tümü</option>
                                        </select>
                                    </div>
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
                            <div class="col-md-2">
                                <div class="form-password-toggle">
                                    <label class="form-label" for="multicol-password">Şube</label>
                                    <div class="input-group input-group-merge">
                                        <select type="text" name="seller" class="form-select" style="width: 100%">
                                            <option value="">Tümü</option>
                                            @foreach($sellers as $seller)
                                                <option value="{{$seller->id}}">{{$seller->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-password-toggle">
                                    <label class="form-label" for="multicol-password">İade Nedeni</label>
                                    <div class="input-group input-group-merge">
                                        <select type="text" name="seller" class="form-select" style="width: 100%">
                                            <option value="">Tümü</option>
                                            @foreach($reasons as $reason)
                                                <option value="{{$reason->id}}">{{$reason->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-password-toggle">
                                    <label class="form-label" for="multicol-confirm-password">Seri Numarası</label>
                                    <div class="input-group input-group-merge">
                                        <input type="text" name="barcode" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button   type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                        </div>
                    </form>
                </div>
            <div class="card-header">

            </div>
            <div class="table-responsive text-nowrap">
                <table class="table" style="font-size: 13px;">
                    <thead>
                    <tr>
                        <th>Stok Adı</th>
                        <th>Marka</th>
                        <th>Model</th>
                        <th>Renk</th>
                        <th>İade Nedeni</th>
                        <th>Seri No</th>
                        <th>Açıklama</th>
                        <th>Detay</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($refunds as $refund)

                        <tr>
                            <td><strong>{{$refund->stock->name??"Bulunamadı"}}</strong></td>
                            <td><strong>{{$refund->stock->brand->name??"Bulunamadı"}}</strong></td>
                            <td><strong>@if(!empty($refund->stock)){{$refund->stock->version()??"Bulunamadı"}}@endif</strong></td>
                            <td><strong>{{$refund->color->name??"Bulunamadı"}}</strong></td>
                            <td><strong>{{$refund->reason->name??"Bulunamadı"}}</strong></td>
                            <td><strong>{{$refund->serial_number??"Bulunamadı"}}</strong></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="popover" data-bs-offset="0,14" data-bs-placement="right" data-bs-html="true" data-bs-content="<p>{{$refund->description}}</p>" data-bs-original-title="Açıklama">
                                    <i class="bx bx-text"></i>
                                </button>
                            </td>
                            <td>
                                <button type="button" ng-click="refundDetail({{$refund->id}})" class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="tooltip" data-bs-offset="0,14" data-bs-placement="right" data-bs-html="true" data-bs-content="Detay" data-bs-original-title="Detay">
                                    <i class="bx bx-text"></i>
                                </button>
                            </td>
                            <td>
                                <!--
                                @if($refund->status == 0)
                                    <a href="{{route('stockcard.refundcomfirm',['id' => $refund->id])}}"
                                       title="Kabul Et" class="btn btn-sm btn-success">
                                        <span class="bx bx-check"></span>
                                    </a>
                                @endif

                                @if($refund->status == 1)
                                    <a href="{{route('stockcard.refundreturn',['id' => $refund->id])}}" title="Kabul Et"
                                       class="btn btn-sm btn-danger">
                                        Stoğa Al
                                    </a>
                                @endif
                                -->
                                @if($refund->status == 1)
                                    Satışa Alındı
                                @elseif($refund->status == 3)
                                    HasarLı İade Alındı
                                @elseif($refund->status == 4)
                                    Müşteriye Teslim Edildi
                                @elseif($refund->status == 5)
                                    <a href="{{route('stockcard.refundcomfirm',['id' => $refund->id,'type' => 'service_return'])}}" title="Kabul Et" class="btn btn-sm btn-warning">
                                       Servisten Geldi
                                    </a>
                                @elseif($refund->status == 0)
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin') && \Illuminate\Support\Facades\Auth::user()->hasRole('Depo Sorumlusu'))

                                    <a ng-click="newSale({{$refund->id}},'seller')" title="Kabul Et" class="btn btn-sm btn-success">
                                        Satışa Çıkart
                                    </a>
                                    @endif
                                    <a href="{{route('stockcard.refundcomfirm',['id' => $refund->id,'type' => 'service_send'])}}" title="Kabul Et" class="btn btn-sm btn-warning">
                                        Servise Gönder
                                    </a>
                                    <a href="{{route('stockcard.refundcomfirm',['id' => $refund->id,'type' => 'refund'])}}" title="Kabul Et" class="btn btn-sm btn-danger">
                                        Hasarlı İade
                                    </a>
                                @elseif($refund->status == 6)
                                    <a href="{{route('stockcard.refundcomfirm',['id' => $refund->id,'type' => 'delivered'])}}" title="Kabul Et" class="btn btn-sm btn-warning">
                                        Teslim Edildi
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content" style="padding: 1px">

                <form  ng-submit="refundDetailStore()"  id="refunDetailForm" method="post">
                    <input type="hidden" name="id" id="id" value="@{{id}}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Açıklama</label>
                                <textarea  id="textarea" name="description" class="form-control">@{{detail}}</textarea>
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
    <div class="modal fade" id="newSaleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content" style="padding: 1px">

                <form  ng-submit="newSaleStore()"  id="newSaleStoreForm" method="post">
                    <input type="hidden" name="id" id="id" value="@{{newSaleId}}">
                    <input type="hidden" name="type" id="type" value="@{{newSaleType}}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-9 col-12 mb-md-0 mb-3 ps-md-0">
                                <p class="mb-2 repeater-title">Stok</p>
                                <select name="stock_card_id[]" id="stock_card_id" class="form-select item-details select2 mb-2">
                                    @foreach($stocks as $stock)
                                        <option value="{{$stock->id}}" ng-selected="{{$stock->id}} == itemData.stock_card_id">
                                            {{$stock->name}} -
                                            <small> {{$stock->brand->name}}</small> - <b>
                                                    <?php  $datas = json_decode($stock->version(), TRUE);
                                                    foreach ($datas as $mykey => $myValue) {
                                                        echo "$myValue,";
                                                    }
                                                    ?></b>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                <p class="mb-2 repeater-title">Renk</p>
                                <select name="color_id[]"
                                        class="form-select item-details select2 mb-2">
                                    @foreach($colors as $color)
                                        <option  ng-selected="itemData.color_id == {{$color->id}}"  value="{{$color->id}}">{{$color->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                <p class="mb-2 repeater-title">Gerçek Maliyet</p>
                                <input type="text" class="form-control invoice-item-price" id="cost_price" value="@{{cost_price}}"
                                       name="cost_price[]"  />
                            </div>

                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                <p class="mb-2 repeater-title">Maliyet</p>
                                <input type="text" class="form-control invoice-item-price" id="base_cost_price" value="@{{base_cost_price}}"
                                       name="base_cost_price[]" />
                            </div>
                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                <input type="text" class="form-control invoice-item-price"
                                       name="sale_price[]" id="sale_price" value="@{{sale_price}}"/>
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
@endsection

@section('custom-js')
    <script>
        "use strict";[].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(function(e){return new bootstrap.Popover(e,{html:!0,sanitize:!1})});
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
        }).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {
            $scope.refundDetail = function (id) {
                $scope.id = id;
                $("#refundModal").modal('show');
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/stockcard/refunddetail?id='+id+'';   // Returns base URL (https://example.com)
                $http({
                    method: 'get',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.detail = response.data.description;
                });
            }


            $scope.refundDetailStore = function () {
                 var postUrl = window.location.origin + '/stockcard/refunddetailStore';   // Returns base URL (https://example.com)
                $http({
                    method: 'post',
                    url: postUrl,
                    data: $("#refunDetailForm").serialize(),
                     headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    Swal.fire('Güncellendi');
                    $scope.detail = response.data.detail;
                });
            }

            $scope.newSale = function (id,type) {

                var postUrl = window.location.origin + '/stockcard/newSale?id='+id+'';   // Returns base URL (https://example.com)
                $http({
                    method: 'get',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    console.log(response);
                    $("#newSaleModal").modal('show');

                    if(response.data.status == false)
                    {
                        Swal.fire(response.data.data);
                    }else{
                        $("#stock_card_id").val(response.data.data.stock_card_id).trigger('change');
                        $scope.newSaleId = id;
                        $scope.newSaleType = type;
                        $("#newSaleModal").find('#id').val(id);
                        $("#newSaleModal").find('#type').val(type);

                        $("#newSaleModal").find('#sale_price').val(response.data.data.sale_price);
                        $("#newSaleModal").find('#cost_price').val(response.data.data.cost_price);
                        $("#newSaleModal").find('#base_cost_price').val(response.data.data.base_cost_price);

                        $scope.sale_price = response.data.data.sale_price;
                        $scope.cost_price = response.data.data.cost_price;
                        $scope.base_cost_price = response.data.data.base_cost_price;
                    }

                });
            }
            $scope.newSaleStore = function ()
            {
                var postUrl = window.location.origin + '/stockcard/newSaleStore';   // Returns base URL (https://example.com)
                $http({
                    method: 'post',
                    url: postUrl,
                    data: $("#newSaleStoreForm").serialize(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    Swal.fire('Kaydedildi');
                    window.location.reload();
                 });
            }

        });
    </script>
@endsection
