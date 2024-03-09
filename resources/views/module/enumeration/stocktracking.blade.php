@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">RAPOR</span></h4>
        <button ng-click="runQuene()" class="btn btn-secondary">Kuyrugu Calistir</button>
        <div class="row">
            <div class="col-md-12 mb-5">
                <div class="card">
                    <div class="card-body">
                        <select class="form-select" id="seller_id" name="seller_id">
                            @foreach($sellers as $seller)
                                <option value="{{$seller->id}}" @if($seller->id == $enumeration->seller_id) selected @endif>{{$seller->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <a style="width: 100%;" href="{{route('enumeration.finish',['id' => $enumeration->id])}}" class="btn btn-success">Bitir</a>
                    </div>
                </div>
                <div class="card mt-5">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Seri No Okutunuz</label>
                            <input type="text"  data-id="{{$enumeration->id}}" ng-model="fda" ng-change="myFunc({{$enumeration->id}})" ng-keyup="onKeyUp($event)" name="serial" id="serial" class="form-control" autocomplete="off" autofocus>
                        </div>
                        <div id="serialList">

                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">

                        <table class="table table-responsive">
                            <thead>
                            <tr style="font-weight: 600; background: cadetblue; color: #fff; font-size: 12px;">
                                <td>Stok Adı</td>
                                <td>Stok Renk</td>
                                <td>Stok Bayi</td>
                                <td>Seri</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr  style="font-size: 15px;font-weight: 600;">
                                <td>@{{lastserial.name}}</td>
                                <td>@{{lastserial.color}}</td>
                                <td>@{{lastserial.seller}}</td>
                                <td>@{{lastserial.serial}}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive" ng-init="getNewFunc({{$enumeration->id}})">
                            <thead>
                            <tr style="font-weight: 600; background: cadetblue; color: #fff; font-size: 12px;">
                                <td>Stok Adı</td>
                                <td>Stok Renk</td>
                                <td>Stok Bayi</td>
                                <td>Seri</td>
                                <td>Durum</td>
                                <td>---</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="data in datas"  style="font-size: 15px;font-weight: 600;">
                                <td>@{{data.stock.name}}</td>
                                <td>@{{data.color.name}}</td>
                                <td>@{{data.seller.name}}</td>
                                <td>@{{data.serial_number}}</td>
                                <td><!-- span
                                        ng-class="{
                                          2:'badge rounded-pill bg-danger' ,
                                          1:'badge rounded-pill bg-success',
                                          3:'badge rounded-pill bg-warning',
                                          4:'badge rounded-pill bg-info',
                                          5:'badge rounded-pill bg-primary'}[data.typeId]"
                                    >@{{data.type}}</span--></td>
                                <td>

                                </td>
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
        var controlCheckConnection = true;
        function checkInternetConnection() {
            if (navigator.onLine) {
                console.log('İnternet bağlantısı var.');
                controlCheckConnection = true;
            } else {
                console.log('İnternet bağlantısı yok.');
                controlCheckConnection = false;
            }
        }

        // Bağlantı durumunu kontrol et
        checkInternetConnection();

        // Bağlantı durumu değiştiğinde kontrol et
        window.addEventListener('online', checkInternetConnection);
        window.addEventListener('offline', checkInternetConnection);
    </script>

    <script>
             document.getElementById("serial").addEventListener("keyup", function(event) {
                 if(event.key == 'Enter') {
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

                             if(!controlCheckConnection)
                             {
                                 Swal.fire("Internet Baglantisi Yok")

                             }
                             $("#serialList").prepend('<div id="' + newVal + '" class="input-group mt-2">' +
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
                 }
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
        }).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window,$interval) {
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



            $interval(function () {
                $scope.getNewFunc({{$enumeration->id}});
            }, 10000);



/*

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
                    $scope.lastserial = response.data;
                });
            }
*/
            $scope.runQuene = function () {
                var postUrl = window.location.origin + '/enumeration/runquene';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    //
                });
            }

            $scope.getFunc = function (id) {
                Swal.showLoading();
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/enumeration/get?id='+id+'';   // Returns base URL (https://example.com)
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


            $scope.getLastSerial = function (id,serial) {

                var postUrl = window.location.origin + '/enumeration/getLastSerial';   // Returns base URL (https://example.com)
                $http({
                    method: 'POST',
                    url: postUrl,
                    data: "serial="+ serial + "&id="+id,

                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {

                });
            }


            $scope.getNewFunc = function (id) {

                var postUrl = window.location.origin + '/enumeration/newGet?id='+id+'';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.datas = response.data.dataCol;
                    $scope.datas1 = response.data.dataCol1;

                });
            }

            $scope.onKeyUp = function(event) {

                if(!controlCheckConnection){
                    Swal.fire("Sorun var veya Internet Baglantisi Yok");
                    return false;
                }
                if(event.keyCode == 13 && controlCheckConnection)
                {
                    $scope.loading = true; // Show loading image
                    var postUrl = window.location.origin + '/updateTracking';   // Returns base URL (https://example.com)
                    $http({
                        method: 'POST',
                        url: postUrl,
                        data: "serial="+ $scope.fda + "&id="+{{$enumeration->id}},
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).then(function successCallback(response) {
                        $scope.lastserial = response.data;
                    });
                }

            };

        });
    </script>
@endsection
