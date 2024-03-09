@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-add">
            <div class="col-lg-9 col-12 mb-lg-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <form id="invoiceForm" method="post" class="form-repeater source-item">
                            <input type="hidden" name="id" @if(isset($invoice_id)) value="{{$invoice_id}}" @endif />
                            <input type="hidden" name="type" value="1"/>
                            <div class="row p-sm-3 p-0">
                                <div class="col-md-6 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
                                        <div class="col-md-9">
                                            <select id="selectCustomer" class="w-100 select2"
                                                    data-style="btn-default" name="customer_id" ng-init="getCustomers()">
                                                <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                                <option ng-repeat="customer in customers"
                                                        ng-if="customer.type == 'account'"
                                                        ng-selected="customer.id == {{$invoice->customer_id}}"
                                                        @if(isset($invoice) && '@{{customer.id}}' == $invoice->customer_id) selected
                                                        @endif data-value="@{{customer.id}}" value="@{{customer.id}}">
                                                    @{{customer.fullname}}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-secondary btn-primary" tabindex="0"
                                                    data-bs-toggle="modal" data-bs-target="#editUser" type="button">
                                                <span><i class="bx bx-plus me-md-1"></i></span></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-2">
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="h4 text-capitalize mb-0 text-nowrap">Invoice #</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control"
                                                       @if(isset($invoice)) value="{{$invoice->number}}"
                                                       @endif name="number" id="invoiceId">
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tarihi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control datepicker flatpickr-input" name="create_date"
                                                       @if(isset($invoice)) value="{{$invoice->create_date}}"
                                                       @else  value="{{date('d-m-Y')}}" @endif />
                                            </div>
                                        </dd>

                                    </dl>
                                </div>
                            </div>
                            <hr class="mx-n4">
                            <div class="row">
                                <div class="col-lg-12 col-12 invoice-actions">
                                    <div class="card">
                                        <div class="card-body">
                                            <button onclick="save()" type="button" class="btn btn-primary d-grid w-100">
                                              <span class="d-flex align-items-center justify-content-center text-nowrap">
                                             <i class="bx bx-paper-plane bx-xs me-1"></i>Kaydet</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <form method="post" action="{{route('invoice.stockcardmovementstore')}}">
                                @csrf
                                <input name="invoice_id" value="{{$invoice_id}}" type="hidden" />
                                <div class="pt-0 pt-md-4">
                                    <div class="border rounded position-relative pe-0">
                                        <div class="row w-100 m-0 p-3">
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok</p>
                                                <select name="stock_card_id"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($stocks as $stock)
                                                        <option value="{{$stock->id}}"  @if($stock->id == $refund->stock_card_id) selected @endif>
                                                            {{$stock->name}} -
                                                            <small> {{$stock->brand->name}}</small> - <b>
                                                                    <?php
                                                                    $datas = json_decode($stock->version(), TRUE);
                                                                    foreach ($datas as $mykey => $myValue) {
                                                                        echo "$myValue,";
                                                                    }
                                                                    ?></b>
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Seri No</p>
                                                <input type="text" class="form-control" name="serial" value="{{$refund->serial_number}}"/>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Renk</p>
                                                <select name="color_id"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($colors as $color)
                                                        <option value="{{$color->id}}"  @if($color->id == $refund->color_id) selected @endif>{{$color->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Neden</p>
                                                <select name="reason_id"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($reasons as $reason)
                                                            <option value="{{$reason->id}}"  @if($reason->id == 10) selected @endif>{{$reason->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Maliyet</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="cost_price"/>
                                            </div>

                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Destekli Maliyet</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="base_cost_price"/>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="sale_price"/>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Adet</p>
                                                <input type="number" class="form-control invoice-item-qty"
                                                       name="quantity" min="1" max="50">
                                            </div>

                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="discountInput"
                                                       class="form-label">İndirim (%)</label>
                                                <input type="number" class="form-control"
                                                       id="discountInput"
                                                       min="0" max="100" value="0" name="discount">
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="taxInput1" class="form-label">Şube</label>
                                                <select name="seller_id" id="taxInput1"
                                                        class="form-select tax-select">
                                                    @foreach($sellers as $seller)
                                                        <option value="{{$seller->id}}">{{$seller->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="taxInput2" class="form-label">Depo</label>
                                                <select name="warehouse_id" id="taxInput2"
                                                        class="form-select tax-select">
                                                    @foreach($warehouses as $warehouse)
                                                        <option
                                                            value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="taxInput1" class="form-label">KDV</label>
                                                <select name="tax" id="taxInput1" class="form-select tax-select">
                                                    <option value="0">0%</option>
                                                    <option value="1">1%</option>
                                                    <option value="8">10%</option>
                                                    <option value="18" selected>18%</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Açıklama</p>
                                                <textarea class="form-control" rows="2"
                                                          name="description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="mx-n4">
                                    <div class="col-md-12">
                                        <button class="btn btn-danger">Kaydet</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td style="font-size: 13px">Toplam Maliyet</td>
                                <td style="font-size: 13px;text-align: center">{{$invoice->totalCost()}} TL</td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px">Toplam Dest. Sat. Tutarı</td>
                                <td style="font-size: 13px;text-align: center">{{$invoice->totalBaseCost()}} TL</td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px">Toplam Satış Tutarı</td>
                                <td style="font-size: 13px;text-align: center">{{$invoice->totalSale()}} TL</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body" style="display: none">
                        <div>
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]"  value="0"  id="credit_card"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order"  value="0"  class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Taksit</label>
                            <input type="text" name="payment_type[installment]"  value="0"  id="installment"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]"  value="0"  id="credit_card"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order"  value="0"  class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Taksit</label>
                            <input type="text" name="payment_type[installment]"  value="0"  id="installment"
                                   class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                         <table class="table table-bordered">
                            <tr>
                                <td>Stok Adı</td>
                                <td>Adet</td>
                                <td>Renk</td>
                                <td>Maliyet</td>
                                <td>Destekli Satış Fiyatı</td>
                                <td>Satış Fiyatı</td>
                                <td>İşlemler</td>
                            </tr>
                            @foreach($stock_card_movements as $item)
                                <tr>
                                    <td>{{$item->stock->name}} {{$item->stock->brand->name}} {{$item->stock->versions}}</td>
                                    <td>{{$item->quant}}</td>
                                    <td>{{$item->color->name}}</td>
                                    <td>{{$item->cost_price}}</td>
                                    <td>{{$item->base_cost_price}}</td>
                                    <td>{{$item->sale_price}}</td>
                                    <td><a href="{{route('invoice.stockmovementdelete',['id' => $item->stock_card_id])}}" class="btn btn-danger">Sil</a></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>


        @endsection
        @include('components.customermodal')



        @section('custom-js')

            <script>


                function save() {
                    var postUrl = window.location.origin + '/invoice/store';   // Returns base URL (https://example.com)
                    $.ajax({
                        type: "POST",
                        url: postUrl,
                        data: $("#invoiceForm").serialize(),
                        dataType: "json",
                        encode: true,
                        beforeSend: function () {
                            $('#loader').removeClass('display-none')
                        },
                        success: function (data) {
                            window.location.href = "{{route('invoice.stockcardmovementform')}}?id="+data+"";
                        },
                        error: function (xhr) { // if error occured
                            alert("Error occured.please try again");
                            $(placeholder).append(xhr.statusText + xhr.responseText);
                            $(placeholder).removeClass('loading');
                        }

                    });
                }


            </script>

            <script>
                app.controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {
                    $scope.getCustomers = function () {
                        var postUrl = window.location.origin + '/customers?type=account';   // Returns base URL (https://example.com)
                        $http({
                            method: 'GET',
                            //url: './comment/change_status?id=' + id + '&status='+status+'',
                            url: postUrl,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function successCallback(response) {
                            $scope.customers = response.data;
                        });
                    }
                    $scope.customerSave = function () {
                        var postUrl = window.location.origin + '/custom_customerstore';   // Returns base URL (https://example.com)
                        var formData = $("#customerForm").serialize();

                        $http({
                            method: 'POST',
                            url: postUrl,
                            data: formData,
                            dataType: "json",
                            encode: true,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function successCallback(response) {
                            $scope.getCustomers();
                            $(".customerinformation").html('<p className="mb-1">\'+data.address+\'</p>\n' + '<p className="mb-1">\'+data.phone1+\'</p>');
                            $('#selectCustomer option:selected').val(response.data.id);
                            var modalDiv = $("#editUser");
                            modalDiv.modal('hide');
                            modalDiv
                                .find("input,textarea,select")
                                .val('')
                                .end()
                                .find("input[type=checkbox], input[type=radio]")
                                .prop("checked", "")
                                .end();
                        });
                    }
                });
            </script>

@endsection

