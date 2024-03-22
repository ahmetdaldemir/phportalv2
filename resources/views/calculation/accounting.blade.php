@extends('layouts.calculation')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 col-lg-8 order-1 order-md-3 order-lg-2 mb-4">
                <div class="card">
                    <div class="row row-bordered g-0">
                        <div class="col-md-8">
                            <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
                            <div id="totalRevenueChart" class="px-2"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm btn-outline-primary dropdown-toggle"
                                            type="button"
                                            id="growthReportId"
                                            data-bs-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            2022
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                                            <a class="dropdown-item" href="javascript:void(0);">2021</a>
                                            <a class="dropdown-item" href="javascript:void(0);">2020</a>
                                            <a class="dropdown-item" href="javascript:void(0);">2019</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="growthChart"></div>
                            <div class="text-center fw-medium pt-3 mb-2">62% Company Growth</div>

                            <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                                <div class="d-flex">
                                    <div class="me-2">
                                        <span class="badge bg-label-primary p-2"><i
                                                class="bx bx-dollar text-primary"></i></span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <small>2022</small>
                                        <h6 class="mb-0">$32.5k</h6>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="me-2">
                                        <span class="badge bg-label-info p-2"><i
                                                class="bx bx-wallet text-info"></i></span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <small>2021</small>
                                        <h6 class="mb-0">$41.2k</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 order-2 mb-4">
                <div class="card h-100">

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <div class="col-12">
                                    <button class="btn btn-success"  ng-click="income()">Gelir</button>
                                    <button class="btn btn-danger" ng-click="expense()">Gider</button>
                                </div>

                                <div class="clearfix"></div>
                                <h5 class="mb-2">
                                    @foreach ($currencyDifferences as $difference)
                                        <p> kur farkı: {{ $difference->difference }} {{ \App\Models\Currency::find($difference->currency_id)->symbol }}</p>
                                    @endforeach
                                 </h5>
                             </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 order-2 mb-4">
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Tip</th>
                                <th>Kullanıcı</th>
                                <th>Tutar</th>
                                <th>Tarih</th>
                                <th>Odeme Tipi</th>
                                <th>Islem Tip</th>
                                <th>Kur</th>
                                <th>Kur Oran</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            @foreach($finantransactions as $finantransaction)

                                <tr style="{{\App\Models\FinansTransaction::MODEL_COLOR[$finantransaction->model_class]}}">
                                    <td>{{\App\Models\FinansTransaction::MODEL_STRING[$finantransaction->model_class]}}</td>
                                    <td>{{$finantransaction->finansModel()}}</td>
                                    <td>{{$finantransaction->price}} {{$finantransaction->currency('symbol')}}</td>
                                    <td>{{$finantransaction->created_date}}</td>
                                    <td>{{$finantransaction->payment_type}}</td>
                                    <td>{{$finantransaction->category->name ?? 'Belirtilmedi'}}</td>
                                    <td>{{$finantransaction->currency('code')}}</td>
                                    <td>{{$finantransaction->rate}}</td>
                                    <td></td>
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
    <div class="modal fade" id="processModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="{{route('calculation.process_store')}}" id="deleteModalForm">
                @csrf
                <input type="hidden" name="payment_type" value="@{{paymentType}}">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Gelir / Gider Kaydi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nameBackdrop" class="form-label">Islem Tipi</label>
                            <select class="form-select" name="model"  ng-model="selectedItem" ng-change="onChange()">
                                <option value="staff">Personel</option>
                                <option value="seller">Bayi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBackdrop" class="form-label">KUR</label>
                            <select name="currency_id" class="form-select" >
                                @foreach($currencies as $currency)
                                <option value="{{$currency->id}}">{{$currency->code}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3" id="selectedDiv" style="display: none">
                            <label for="nameBackdrop" class="form-label">Sube / Personel</label>
                            <select name="model_id" class="form-select" id="selectStaffOrSeller">
                                <option ng-repeat="item in selectStaffOrSeller" value="@{{ item.id }}">@{{ item.name }}</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Islem Turu</label>
                            <select name="process_type" class="form-select">
                                <option ng-repeat="item in processTypeSelect" value="@{{ item.id }}">@{{ item.name }}</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Bakiye Giriniz</label>
                            <input type="text" id="price" class="form-control" name="price" required/>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
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
    }).directive('ngConfirmClick', [
        function () {
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


        $scope.income = function () {
            $('#processModal').modal('show');
            $scope.paymentType = "income";

            var postUrl = window.location.origin + '/calculation/getCategories?id=income';   // Returns base URL (https://example.com)
            $http({
                method: 'GET',
                url: postUrl,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then(function successCallback(response) {
                $scope.processTypeSelect = response.data
            });

        }

        $scope.expense = function () {
            $('#processModal').modal('show');
            $scope.paymentType = "expense";

            var postUrl = window.location.origin + '/calculation/getCategories?id=expense';   // Returns base URL (https://example.com)
            $http({
                method: 'GET',
                url: postUrl,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then(function successCallback(response) {

                $scope.processTypeSelect = response.data

            });
        }

        $scope.onChange = function() {
            var selected = $scope.selectedItem;
            var postUrl = window.location.origin + '/calculation/selected?id=' + selected + '';   // Returns base URL (https://example.com)
            $http({
                method: 'GET',
                url: postUrl,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then(function successCallback(response) {
                $('#selectedDiv').show();
                $scope.selectStaffOrSeller = response.data

            });
         };

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


    });
</script>
@endsection
