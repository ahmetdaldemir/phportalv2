@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-add">
            <div class="col-lg-12 col-12 mb-lg-0 mb-4">
                <div class="card invoice-preview-card">
                    <form id="invoiceForm" method="post" target="_blank" action="{{route('invoice.stockcardmovementstore')}}">
                        @csrf
                        <input type="hidden" name="type" value="1"/>
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                                <div class="col-md-6 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
                                        <div class="col-md-9">
                                            <select id="selectCustomer" class="w-100 select2" data-style="btn-default" name="customer_id" ng-init="getCustomers()">
                                                <option value="0">Genel Cari</option>
                                                <option ng-repeat="customer in customers"  ng-if="customer.type == 'account'" data-value="@{{customer.id}}"  value="@{{customer.id}}"> @{{customer.fullname}}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-secondary btn-primary" tabindex="0" data-bs-toggle="modal" data-bs-target="#editUser" type="button">
                                                <span><i class="bx bx-plus me-md-1"></i></span>
                                            </button>
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
                                                <input type="text" class="form-control" name="number" id="invoiceId">
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tarihi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control datepicker flatpickr-input" name="create_date" value="{{date('d-m-Y')}}"  />
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <hr class="mx-n4">

                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div id="test" class="pt-0 pt-md-4">
                                    <div class="cloneBox"></div>
                                    <div class="border rounded position-relative pe-0">
                                        <div class="row w-100 m-0 p-3">
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok</p>
                                                <select name="stock_card_id[]"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($stocks as $stock)
                                                        <option value="{{$stock->id}}" @if($stock->id == $stock_card_id) selected @endif >
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
                                                <input type="text" class="form-control" name="serial[]"
                                                       placeholder="11111111"/>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Renk</p>
                                                <select name="color_id[]"  class="form-select item-details select2 mb-2" required>
                                                    <option value="">Seçiniz</option>
                                                    @foreach($colors as $color)
                                                        <option value="{{$color->id}}">{{$color->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" name="reason_id[]" value="9" />
                                            <!--div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Neden</p>
                                                <select name="reason_id[]"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($reasons as $reason)
                                                        @if($reason->type == 5)
                                                            <option value="{{$reason->id}}">{{$reason->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div -->
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok Takibi</p>
                                                <input type="text" class="form-control tracking_quantity" value="0" name="tracking_quantity[]"/>
                                            </div>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Gerçek Maliyet</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="cost_price[]" value="{{$last_price->cost_price??NULL}}" required/>
                                            </div>

                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Maliyet</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="base_cost_price[]"  value="{{$last_price->base_cost_price??NULL}}" required/>
                                            </div>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="sale_price[]"  value="{{$last_price->sale_price??NULL}}" required/>
                                            </div>

                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Adet</p>
                                                <input type="number" class="form-control invoice-item-qty"
                                                       name="quantity[]" min="1" max="5000">
                                            </div>
                                            <input type="hidden" name="discount[]" value="0" />

                                            <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="discountInput"
                                                       class="form-label">İndirim (%)</label>
                                                <input type="number" class="form-control"
                                                       id="discountInput"
                                                       min="0" max="100" value="0" name="discount[]">
                                            </div -->
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="taxInput1" class="form-label">Şube</label>
                                                <select name="seller_id[]" id="taxInput1"  class="form-select tax-select" required>
                                                    <option>Seciniz</option>
                                                    @foreach($sellers as $seller)
                                                        <option  value="{{$seller->id}}">{{$seller->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="taxInput2" class="form-label">Depo</label>
                                                <select name="warehouse_id[]" id="taxInput2"
                                                        class="form-select tax-select">
                                                    @foreach($warehouses as $warehouse)
                                                        <option
                                                            value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" name="tax[]" value="20" />
                                            <input type="hidden" name="description[]" value="" />

                                            <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="taxInput1" class="form-label">KDV</label>
                                                <select name="tax[]" id="taxInput1"
                                                        class="form-select tax-select">
                                                    <option value="0">0%</option>
                                                    <option value="1">1%</option>
                                                    <option value="8">10%</option>
                                                    <option value="18" selected>18%</option>
                                                </select>
                                            </div -->
                                            <!-- div class="col-md-12 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Açıklama</p>
                                                <textarea class="form-control" rows="2" name="description[]" id="description"></textarea>
                                            </div -->

                                        </div>
                                    </div>
                                    <hr class="mx-n4">

                                </div>
                                <div id="myList1">

                                </div>
                                <button type="button" onclick="myFunction()" class="btn btn-secondary clon">EKLE</button>

                            </div>
                            <div class="col-md-12">
                                <button style="width: 100%;" type="submit"   id="redirect" class="btn btn-danger ">Kaydet</button>
                            </div>
                            <!-- <div class="row mt-3">
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
                                                @if(isset($stock_card_movements))
                                                    @foreach($stock_card_movements as $item)
                                                        <tr>
                                                            <td>{{$item->stock->name}} / {{$item->stock->brand->name}}
                                                                / {{$item->stock->version()}}</td>
                                                            <td>{{$item->quant}}</td>
                                                            <td>{{$item->color->name}}</td>
                                                            <td>{{$item->cost_price}}</td>
                                                            <td>{{$item->base_cost_price}}</td>
                                                            <td>{{$item->sale_price}}</td>
                                                            <td>
                                                                <a href="{{route('invoice.stockmovementdelete',['id' => $item->stock_card_id])}}"
                                                                   class="btn btn-danger">Sil</a></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </form>
                </div>
            </div>
            <!-- div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td style="font-size: 13px">Toplam Maliyet</td>
                                <td style="font-size: 13px;text-align: center">@if(isset($invoice)) {{$invoice->totalCost()}} TL @endif</td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px">Toplam Dest. Sat. Tutarı</td>
                                <td style="font-size: 13px;text-align: center">@if(isset($invoice)) {{$invoice->totalBaseCost()}} TL @endif</td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px">Toplam Satış Tutarı</td>
                                <td style="font-size: 13px;text-align: center">@if(isset($invoice)) {{$invoice->totalSale()}} TL @endif</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body" style="display: none">
                        <div>
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]" value="0" id="credit_card"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order" value="0"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Taksit</label>
                            <input type="text" name="payment_type[installment]" value="0" id="installment"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]" value="0" id="credit_card" class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order" value="0" class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Taksit</label>
                            <input type="text" name="payment_type[installment]" value="0" id="installment" class="form-control">
                        </div>
                    </div>
                </div>
            </div -->

        </div>

        @endsection
        @include('components.customermodal')

        @section('custom-js')
            <script>
                $('form').submit(function(){
                    $(this).find('#redirect').prop('disabled', true);
                });

            </script>
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
                            window.location.href = "{{route('invoice.stockcardmovementform')}}?id=" + data + "";
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

            <script>

                function myFunction() {
                    var rand = Math.floor(Math.random() * 100);
                    $("#test").find(".select2").each(function (index) {
                        $("select.select2-hidden-accessible").select2('destroy');
                    });
                    const node = document.getElementById("test");
                    const clone = node.cloneNode(true);
                    clone.setAttribute('id', rand);
                    document.getElementById("myList1").appendChild(clone);
                    $("#"+rand).find('.cloneBox').html('<span id="removeDiv" data-id="'+rand+'" class="bx bxs-trash"></span>')
                    $("select.select2").select2();
                   // $("#"+rand).find('input:text').val('');
                    $("#"+rand).find('input.tracking_quantity').val(0);
                    $("#"+rand).find('input.invoice-item-qty').val('');
                    window.scrollBy(0,400)
                }
            </script>

    <script>
        $("#myList1").on("click","#removeDiv",function () {
           var Divid = $(this).data('id');
           $("#"+Divid).remove();
        })
    </script>

            <script>


                $("#invoiceForm").on('keyup', '#description:last', function(e) {
                    var keyCode = e.keyCode || e.which;

                    if (keyCode == 9) {
                        e.preventDefault();
                        var rand = Math.floor(Math.random() * 100);
                        $("#test").find(".select2").each(function (index) {
                            $("select.select2-hidden-accessible").select2('destroy');
                        });
                        const node = document.getElementById("test");
                        const clone = node.cloneNode(true);
                        clone.setAttribute('id', rand);
                        document.getElementById("myList1").appendChild(clone);
                        $("#"+rand).find('.cloneBox').html('<span id="removeDiv" data-id="'+rand+'" class="bx bxs-trash"></span>')
                        $("select.select2").select2();
                    }
                });

            </script>
@endsection

