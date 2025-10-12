@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kartları /</span> Stok Kart listesi</h4>

        <div class="card">
            <div class="card-body">
                @include('components.search')
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Stok Adı</th>
                        <!-- th>SKU</th -->
                        <!-- th>Barkod</th -->
                        <th>Adet</th>
                        <th>Kategori</th>
                        <th>Marka</th>
                        <th>Model</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                        <tr ng-repeat="item in stockSearchLists">
                            <td>@{{item.name}}</td>
                            <!-- td>@{{item.sku}}</td -->
                            <!-- td>@{{item.barcode}}</td -->
                            <td>@{{item.category}}</td>
                            <td>@{{item.brand}}</td>
                            <td>@{{item.version}}</td>
                            <td>@{{item.quantity}}</td>
                            <td>
                                <button type="button" title="Sevk Et" onclick="openModal(item.id)"
                                        class="btn btn-icon btn-success">
                                    <span class="bx bx-transfer"></span>
                                </button>
                                <!-- a title="Hareket Ekle" href="{{route('stockcard.movement',['id' => 1])}}"
                                   class="btn btn-icon btn-success">
                                    <span class="bx bxl-product-hunt"></span>
                                </a -->
                                <a title="Düzenle" href="{{route('stockcard.edit',['id' => 1])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                                <a title="Sil" href="{{route('stockcard.delete',['id' => 1])}}"
                                   class="btn btn-icon btn-danger">
                                    <span class="bx bxs-trash"></span>
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
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
                            <input
                                type="text"
                                id="serialBackdrop"
                                class="form-control"
                                placeholder="Seri Numarası"
                                name="serial_number"
                            />
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
    </div>
@endsection

@section('custom-js')
    <script>

        function openModal(id) {
            $("#backDropModal").modal('show');
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
    </script>


    <script>
        app.directive('loading', function () {
            return {
                restrict: 'E',
                replace:true,
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
                    $scope.stockSearchLists = response.data;
                });
            }

        });
    </script>
@endsection

