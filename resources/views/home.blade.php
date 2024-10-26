@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            @role(['Satış Sorumlusu','super-admin','Bayi Yetkilisi'])
            <div class="col-lg-6 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <h5 class="card-title text-primary">SATIŞ! 🎉</h5>
                                <form action="javascript():;" id="stockSearch" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="multicol-confirm-password">Seri
                                                    Numarası</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" name="serialNumber" id="serialNumberSale"
                                                           class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="  width: 0;
    margin: 7px;
    height: 0;
    font-size: 0;" for="serialbuttun" class="label">.</label>
                                            <button style="width: 100%" ng-click="getStockSearch()" type="button"
                                                    id="serialbuttun" class="btn btn-md btn-outline-primary">Ara
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div id="nameText"></div>
                        </div>
                        <!-- div class="card-footer">
                            <table class="table table-responsive">
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Marka</th>
                                    <th>Model</th>
                                    <th>Adet</th>
                                    <th>İşlemler</th>
                                </tr>
                                <tr ng-repeat="item in stockSearchLists">
                                    <td>@{{item.name}}</td>
                                    <td>@{{item.category}}</td>
                                    <td>@{{item.brand}}</td>
                                    <td>@{{item.version}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary text-nowrap"
                                                ng-click="getStockSeller(item.id)">
                                            @{{item.quantity}}
                                        </button>
                                    </td>
                                    <td><a ng-if="item.quantity > 0" data-id="@{{item.id}}"
                                           href="{{route('invoice.sales')}}?id=@{{item.id}}">Satış</a>
                                    </td>
                                </tr>
                            </table>
                        </div -->
                    </div>
                </div>
            </div>
            @endrole
            <div class="col-lg-6 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <h5 class="card-title text-primary">SEVK! 🎉</h5>
                                <form action="javascript():;" id="transferForm" method="post">
                                    @csrf
                                    <input type="hidden" id="sellerID" class="form-control" name="sellerID"
                                           value="{{auth()->user()->seller_id}}">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-password-toggle">
                                                <label class="form-label" for="multicol-confirm-password">Seri
                                                    Numarası</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" id="serialBackdrop" class="form-control"
                                                           placeholder="Seri Numarası" name="serial_number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="  width: 0; margin: 7px; height: 0; font-size: 0;"
                                                   for="serialbuttun" class="label">.</label>
                                            <button style="width: 100%" type="button" id="serialbuttunNext"
                                                    class="btn btn-md btn-secondary">Ara
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="newChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="newMonthChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div id="totalAylik"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>

        <div class="row">
            <div class="col-md-6 col-lg-6 order-2 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Azalan Ürünler</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Kalan Stok</th>
                                    <th>Son Maliyet</th>
                                    <th>Son Satış Fiyatı</th>
                                    <th>İşlem</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('Depo Sorumlusu'))
                                    @foreach($stockTracks as $stockTrack)
                                        @if($stockTrack['quantity'] < $stockTrack['tracking_quantity'])
                                            <tr>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>{{$stockTrack['quantity']}}</td>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>{{$stockTrack['name']}}</td>
                                                <td>
                                                    <button
                                                        onclick="demandModal({{$stockTrack['id']}},'{{$stockTrack['name']}}')"
                                                        type="button" class="btn btn-danger">
                                                        <i class="bx bx-radar"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-6 order-3 order-md-2 mb-4">
                <div class="card">
                    <form class="modal-content" id="refundForm">
                        @csrf
                        <div class="card-header">İade İşlemi <small style="font-size: 9px;color: #f00;">*(Seri numarası
                                girilirse ise stock seçimine gerek yok)</small></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="nameBackdrop" class="form-label">Stok</label>
                                    <select class="form-control select2" name="stock_id" id="stockBackdrop">
                                        @foreach($stocks as $stock)
                                            <option value="{{$stock->id}}">{{$stock->name}}
                                                /{{$stock->brand->name??"Bulunamadı"}}
                                                /{{$stock->version()??"Bulunamadı"}}
                                                /{{$stock->category->name??"Kategori Yok"}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="sellerBackdrop" class="form-label">Neden</label>
                                    <select class="form-control" name="reason_id" id="sellerBackdrop">
                                        @foreach($reasons as $reason)
                                            @if($reason->type==2)
                                                <option value="{{$reason->id}}">{{$reason->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="sellerBackdrop" class="form-label">Renk</label>
                                    <select class="form-control" name="color_id" id="sellerBackdrop">
                                        @foreach($colors as $color)
                                            <option value="{{$color->id}}">{{$color->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-0">
                                    <label for="sellerBackdrop" class="form-label">Seri No</label>
                                    <input name="serial_number" type="text" class="form-control">
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="sellerBackdrop" class="form-label">Açıklama</label>
                                    <input type="text" name="description" class="form-control" id="description">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer" style="padding: 10px;">
                            <button type="submit" class="btn btn-primary">İADE İŞLEMİ BAŞLAT</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-12 order-3 order-md-2 mb-4">
                <div class="card">
                    <form action="{{route('deleted_at_serial_number_store')}}" id="deleted_at_serial_number_storeForm" method="post">
                        @csrf
                        <div class="card-header">Silinecek Seri Numaralari</div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="sellerBackdrop" class="form-label">Seri Numarasi</label>
                                    <input type="text" name="serial_number" class="form-control" id="serial_number">
                                </div>
                                <div class="col mb-0">
                                    <label for="sellerBackdrop" class="form-label"></label>
                                    <button type="submit" class="btn btn-primary" style="display: flex;">Kaydet</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    <div class="modal fade" id="getCCModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <table class="table table-bordered">
                            <tr>
                                <td>Bayi</td>
                                <td>Adet</td>
                            </tr>
                            <tr ng-repeat="item in data">
                                <td>@{{item.sellerName}}</td>
                                <td>@{{item.quantity}}</td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
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
                                <select class="form-select" name="color_id">
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
@endsection
@section('custom-js')
    <script>
        $("#sell").keypress(function () {
            $("#nameText").html("");
            var postUrl = window.location.origin + '/searchStockCard?key=' + $(this).val() + '';   // Returns base URL (https://example.com)
            $.ajax({
                type: "GET",
                url: postUrl,
                encode: true,
            }).done(function (data) {
                $.each(data, function (index, value) {
                    $("#nameText").append("<br>" + value.name);
                });
            });
        });
    </script>
    <script>
        $("#serialbuttunNext").click(function (e) {
            var serial = $("#transferForm").find("#serialBackdrop").val();
            var sellerID = $("#transferForm").find("#sellerID").val();

            var postUrl = window.location.origin + '/getTransferSerialCheck?serial_number=' + serial + '&seller_id=' + sellerID + '';   // Returns base URL (https://example.com)

            $.ajax({
                type: "GET",
                url: postUrl,
                encode: true,
            }).done(function (data) {
                if (data != 'Yes') {
                    Swal.fire("Seri numarası transfer edilemez.Bulunamamakta veya başka bayiye ait.");
                    deleteBox(serial);
                    return false;
                } else {
                    window.location.href = 'transfer/create?serial_number=' + serial + '&type=other';
                }
            });

        });
    </script>
    <script>
        $(function () {
            $('#serialBackdrop').on('paste', function () {
                var _this = this;
                var serial = $(_this).val();
                setTimeout(function () {
                    //now do something with the paste data
                    window.location.href = 'transfer/create?serial_number=' + _this.value + '&type=other';
                }, 1000);
            });
        });
    </script>


    <script>
        var input = document.getElementById("serialBackdrop");
        input.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("serialbuttunNext").click();
            }
        });
    </script>

    <script>
        var input = document.getElementById("serialNumberSale");
        input.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("serialbuttun").click();
            }
        });
    </script>


    <script>
        $("#refundForm").submit(function (e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var actionUrl = '{{route('stockcard.refund')}}';

            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    alert('sad');
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                    // window.location.reload();
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
                    if (typeof response.data.autoredirect !== "undefined" && response.data.autoredirect == false) {
                        Swal.fire('Satışı Yapılamaz');
                        $scope.stockSearchLists = null;
                    }
                    if (typeof response.data.autoredirect !== "undefined" && response.data.autoredirect) {
                        window.location.href = "{{route('invoice.sales')}}?id=" + response.data.id + "&serial=" + response.data.serial + "";
                    } else if (typeof response.data.autoredirect === "undefined") {
                        $scope.stockSearchLists = response.data;
                    }
                });
            }


            $scope.getStockSeller = function (id) {
                var postUrl = window.location.origin + '/getStockSeller?id=' + id + '';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $("#getCCModal").modal('show');
                    $scope.data = response.data;
                });
            }

        });
    </script>

    <script>
        function demandModal(id, name) {
            $("#demandModal").modal('show');
            $("#demandModal").find('.modal-header span').html(name);
            $("#demandModal").find('input#id').val(id);
        }

    </script>


@endsection
