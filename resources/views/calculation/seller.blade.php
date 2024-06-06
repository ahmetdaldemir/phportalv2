@extends('layouts.calculation')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Bayiler /</span> Bayiler</h4>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Bayi adi</th>
                        <th>Kira</th>
                        <th>Fatura</th>
                        <th>Vergi</th>
                        <th>Ek Gider</th>
                        <th>Kazanc</th>
                     </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($sellers as $seller)
                        <tr>
                            <td>
                                <button class="btn btn-dual" ng-click="onSeller({{$seller->id}},'{{$seller->name}}')"><i
                                        class="fab fa-angular fa-lg text-danger me-3"></i>
                                    <strong>{{$seller->name}}</strong></button>
                            </td>
                            <td><span class="badge bg-label-warning text-black  me-1">{{number_format($seller->account()->rent ?? 0, 2, '.', ',')}} ₺</span></td>
                            <td><span class="badge bg-label-warning text-black  me-1">{{number_format($seller->account()->invoice ?? 0, 2, '.', ',')}} ₺</span></td>
                            <td><span class="badge bg-label-warning text-black  me-1">{{number_format($seller->account()->tax ?? 0, 2, '.', ',')}} ₺</span></td>
                            <td><span class="badge bg-label-warning text-black  me-1">{{number_format($seller->account()->additional_expense ?? 0, 2, '.', ',')}} ₺</span></td>
                            <td><span class="badge bg-label-success me-1">{{number_format($seller->totalCost() ?? 0, 2, '.', ',')}} ₺</span></td>
                         </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
    <div class="modal fade" id="sellerModal" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-refer-and-earn">
            <div class="modal-content p-0 p-md-0">
                <form class="row g-3" ng-submit="save()" id="sellerForm"  method="post">

                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h5>@{{ sellerName }}</h5>
                        @csrf
                        <input type="hidden" class="form-control" name="seller_id" value="@{{sellerid}}">

                        <div class="row">
                            <div class="col-lg-6">
                                <label class="form-label" for="modalRnFEmail">Kira</label>
                                <input type="text" class="form-control" name="rent">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="modalRnFEmail">Fatura</label>
                                <input type="text" class="form-control" name="invoice">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="modalRnFEmail">Vegi</label>
                                <input type="text" class="form-control" name="tax">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="modalRnFEmail">Ek Gider</label>
                                <input type="text" class="form-control" name="additional_expense">
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label" for="modalRnFEmail">Kazanc</label>
                                <input type="text" class="form-control" name="price" disabled>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">
                            KAYDET
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('custom-js')
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
        }).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window, $interval) {


            $scope.onSeller = function (id, name) {
                $('#sellerModal').modal("show");
                $scope.sellerName = name;
                $scope.sellerid = id;
            }

            $scope.getFunc = function (id) {
                Swal.showLoading();
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/enumeration/get?id=' + id + '';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.datas = response.data.dataCol;
                    $scope.datas1 = response.data.dataCol1;
                    Swal.close();

                });
            }

            $scope.save = function () {
                Swal.showLoading();
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/calculation/saveSeller';   // Returns base URL (https://example.com)
                $http({
                    method: 'POST',
                    data: $('#sellerForm').serialize(),
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $('#sellerModal').modal("hide");
                    Swal.close();
                    window.location.reload();

                });
            }


        });
    </script>
@endsection
