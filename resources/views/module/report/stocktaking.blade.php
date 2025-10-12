@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">RAPOR</span></h4>
        <div class="row">
            <div class="col-md-12 mb-5">
                <div class="card">
                    <div class="card-body">
                        <form class="form-group" id="startTraking" ng-submit="startFunc()">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label for="exampleInputPassword1">Şube</label>
                                        <select class="form-select" id="seller_id" name="seller_id">
                                            @foreach($sellers as $seller)
                                                <option value="{{$seller->id}}">{{$seller->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" id="stockTrakingButton" class="btn btn-primary w-100 mt-3">Baslat</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                             <div class="form-group">
                                <label for="exampleInputEmail1">Seri No Okutunuz</label>
                                <input type="text"  data-id="enumerationId" ng-model="fda" ng-change="myFunc(enumerationId)" name="serial" id="serial" class="form-control" autofocus>
                            </div>
                            <div id="serialList">

                            </div>
                     </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                <td>Stok Adı</td>
                                <td>Mevcut Stok</td>
                                <td>Satılan Stok</td>
                                <td>Kalan Stok</td>
                                <td>Olması Gereken Stok</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="list in items">
                                <td>@{{list.name}}</td>
                                <td class="text-center">@{{list.realy_stock}}</td>
                                <td class="text-center">@{{list.sell_stock}}</td>
                                <td class="text-center">@{{list.found_in_stock}}</td>
                                <td class="text-center text-danger">@{{list.remaining_stock}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
<style>
    .position-relative {
        width: 100%;
        position: relative !important;
    }
</style>
@section('custom-js')
    <script>
        $("#serial").keyup(function (e) {
            if ($(this).val() != "" && $(this).val().length > 6) {

                var newVal = $(this).val();

                var seller_id = $("select[name^='seller_id']").val();

                var Arr = [];
                $('.serialNewList').each(function () {
                    Arr.push($(this).val());
                });
                console.log(Arr);
                var totalSerial = Arr.filter(x => x == newVal).length;
                if (totalSerial > 1) {
                    Swal.fire("Aynı Seri İşlenemez");
                    $("#" + newVal).remove();
                    $(this).val('');
                    return false;
                } else {

                    $("#serialList").append('<div id="' + newVal + '" class="input-group mt-2">' +
                        '<input type="text" class="form-control serialNewList" name="sevkList[]" id="basic-default-password12" value="' + newVal + '">' +
                        '<span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="deleteBox(\'' + newVal + '\')"><i class="bx bx-trash"></i></span>' +
                        '</div>');
                    $("input#serial").val('');

                    /*
                    var postUrl = window.location.origin + '/stocktakingserialcheck';   // Returns base URL (https://example.com)
                    $.ajax({
                        type: "GET",
                        url: postUrl + '?seller_id=' + seller_id + '&serial=' + newVal + '',
                        dataType: "json",
                        encode: true,
                        success: function (data) {
                            console.log(data)

                            if (data.status == 'success') {
                                $("#serialList").append('<div id="' + newVal + '" class="input-group mt-2">' +
                                    '<input type="text" class="form-control serialNewList" name="sevkList[]" id="basic-default-password12" value="' + newVal + '">' +
                                    '<span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="deleteBox(\'' + newVal + '\')"><i class="bx bx-trash"></i></span>' +
                                    '</div>');
                                $("input#serial").val('');
                            } else {
                                Swal.fire(data.seller + ' Bayiye Ait Seri Numarası');
                                $("input#serial").val('');
                            }


                        },
                        error: function (xhr) {
                            alert("Error occured.please try again");
                            $(placeholder).append(xhr.statusText + xhr.responseText);
                            $(placeholder).removeClass('loading');
                        },
                        complete: function () {
                            //  window.location.href = "{{route('transfer.index')}}";
                        },
                    });
*/

                }
            }

        });

        function deleteBox(value) {
            $("#" + value).remove();
        }

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
            $scope.startFunc = function () {
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/startTracking';   // Returns base URL (https://example.com)
                $http({
                    method: 'POST',
                    url: postUrl,
                    data: $("#startTraking").serialize(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.enumerationId = response.data.id;
                    Swal.fire(response.data.message);
                });
            }

            $scope.myFunc = function (id) {
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/updateTracking';   // Returns base URL (https://example.com)
                $http({
                    method: 'POST',
                    url: postUrl,
                    data: "serial="+ $scope.fda + "&id="+id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    console.log(response);
                    $scope.items = response.data;
                });
            }

        });
    </script>
@endsection
