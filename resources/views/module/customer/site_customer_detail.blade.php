@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row fv-plugins-icon-container">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item"><a class="nav-link {{$active}}"
                                            href="{{route('site_customer.detail',['id' => $customer->id])}}"><i
                                class="bx bx-user me-1"></i> Hesap Bilgileri</a></li>
                    <li class="nav-item"><a class="nav-link @if($active == 'orders') active @endif "
                                            href="{{route('site_customer.profil',['id' => $customer->id,'type' => 'orders'])}}"><i
                                class="bx bx-shopping-bag me-1"></i> Siparişler</a></li>
                    <li class="nav-item"><a class="nav-link @if($active == 'invoices') active @endif"
                                            href="{{route('site_customer.profil',['id' => $customer->id,'type' => 'invoices'])}}"><i
                                class="bx bx-detail me-1"></i>Faturalar</a></li>
                </ul>
                <div class="card mb-4">
                    <h5 class="card-header">Müşteri Frofili</h5>
                    <!-- Account -->
                    <hr class="my-0">
                    <div class="card-body">
                        <form id="formAccountSettings" method="GET" onsubmit="return false"
                              class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                            <div class="row">
                                <div class="mb-3 col-md-12 fv-plugins-icon-container">
                                    <label for="firstName" class="form-label">Tam İsim</label>
                                    <input class="form-control" type="text" id="fullname" name="fullname" value="{{$customer->fullname}}" autofocus="">
                                    <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control" type="text" id="email" name="email" value="{{$customer->email}}">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="phoneNumber">Phone Number</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">TR - (+90)</span>
                                        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" value="{{$customer->phone}}">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="address" class="form-label">Adres</label>
                                    <input type="text" class="form-control" id="address" name="address"  value="{{$customer->phone}}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="country">İl</label>
                                    <div class="position-relative" ng-init="getStates({{$customer->city}})">
                                        <select id="country" class="form-select" tabindex="-1" ng-chage="getStates()" aria-hidden="true">
                                            <option value="">Select</option>
                                            @foreach($cities as $item)
                                                <option value="{{$item->id}}" @if($item->id == $customer->city) selected @endif>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="state" class="form-label">İlçe</label>
                                    <select id="country" class="form-select" tabindex="-1" aria-hidden="true">
                                        <option ng-repeat="item in stateList" value="@{{ item.id }}" ng-selected="item.id == {{$customer->district??1}}">@{{ item.name }} </option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Değişiklikleri Kaydet</button>
                            </div>
                            <input type="hidden"></form>
                    </div>
                    <!-- /Account -->
                </div>
                <div class="card">
                    <h5 class="card-header">Delete Account</h5>
                    <div class="card-body">
                        <div class="mb-3 col-12 mb-0">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-medium mb-1">Are you sure you want to delete your
                                    account?</h6>
                                <p class="mb-0">Once you delete your account, there is no going back. Please be
                                    certain.</p>
                            </div>
                        </div>
                        <form id="formAccountDeactivation" onsubmit="return false"
                              class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="accountActivation"
                                       id="accountActivation">
                                <label class="form-check-label" for="accountActivation">I confirm my account
                                    deactivation</label>
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                            </div>
                            <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
                            <input type="hidden"></form>
                    </div>
                </div>
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


            $scope.getStates = function (id = '') {

                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/get_cities?id=' + id + '';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    console.log(response.data);
                    $scope.stateList = response.data;
                });
            }


        });
    </script>
@endsection
