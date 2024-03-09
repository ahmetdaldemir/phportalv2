@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form id="invoiceForm" method="post" class="form-repeater source-item py-sm-3">
            <input type="hidden" name="id" @if(isset($invoices)) value="{{$invoices->id}}" @endif />
            <div class="row invoice-add">
                <!-- Invoice Add-->
                <div class="col-lg-10 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                                <div class="col-md-6 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
                                        <div class="col-md-9">
                                            <select id="selectCustomer" class="w-100 select2"
                                                    data-style="btn-default" name="customer_id" ng-init="getCustomers()"
                                                    onchange="getCustomer(this.value)">
                                                <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                                <option ng-repeat="customer in customers"
                                                        ng-if="customer.type == 'customer'"
                                                        @if(isset($invoices) && '@{{customer.id}}' == $invoices->customer_id) selected
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
                                                       @if(isset($invoices)) value="{{$invoices->number}}"
                                                       @endif name="number" id="invoiceId">
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tarihi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control datepicker flatpickr-input"
                                                       name="create_date"
                                                       @if(isset($invoices)) value="{{$invoices->create_date}}"
                                                       @else  value="{{date('d-m-Y')}}" @endif />
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tipi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <select class="form-control" data-style="btn-default" name="type"
                                                        id="type">
                                                    <option value="2">Giden Fatura</option>
                                                </select>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>


                            <hr class="mx-n4">

                            <div class="mb-3">
                                <div class="repeater-wrapper pt-0 pt-md-4" id="myList1" data-repeater-item="">
                                    <div id="99999999" class="d-flex border rounded position-relative pe-0 repeater-wrapper-new">
                                        <div class="row w-100 m-0 p-3">
                                            <div class="col-md-5 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok</p>
                                                <select name="stock_card_id[]"
                                                        class="form-select item-details item-details-stock select2 mb-2">
                                                    @foreach($stocks as $stock)
                                                        <option @if($product['stock_card']['id'] == $stock->id) selected
                                                                @endif value="{{$stock->id}}">{{$stock->name}} -
                                                            <small> {{$stock->brand->name}}</small> - <b>  <?php
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
                                                <input type="text" id="serialnumber" class="form-control serialnumber"
                                                       data-id="serialnumber0" name="serial[]"
                                                       required placeholder="11111111"
                                                       @if(isset($request->serial)) value="{{$request->serial}}" @endif
                                                />
                                                <h5 id="serialcheck" style="color: red;">
                                                    **Seri Numarası Zorunludur
                                                </h5>
                                            </div>
                                            <!--div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">IMEI</p>
                                                <input minlength="15" maxlength="15" placeholder="Boş Bıralabilirsiniz"
                                                       class="form-control" name="imei[]" readonly/>
                                            </div -->

                                            @if (auth()->user()->hasRole('admin'))
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Destekli Maliyet</p>
                                                <input type="text"
                                                       class="form-control invoice-item-price invoice-item-cost-price"
                                                       name="base_cost_price[]"  data-newid="99999999"
                                                       value="{{$product['stock_card_movement']['base_cost_price'] ?? $product['stock_card_movement']['base_cost_price']}}"
                                                       readonly/>
                                            </div>
                                            @else
                                            <input type="hidden"
                                                   class="form-control invoice-item-price invoice-item-cost-price"
                                                   name="base_cost_price[]"  data-newid="99999999"
                                                   value="{{$product['stock_card_movement']['base_cost_price'] ?? $product['stock_card_movement']['base_cost_price']}}"
                                                   readonly/>
                                            @endif
                                             <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                <input type="text"
                                                       class="form-control invoice-item-price invoice-item-sales-price"
                                                       name="sale_price[]" data-newid="99999999"
                                                       id="serial99999999"
                                                       data-sales="{{$product['stock_card_movement']['sale_price']?? $product['stock_card_movement']['sale_price']}}"
                                                       data-cost="{{$product['stock_card_movement']['base_cost_price']?? $product['stock_card_movement']['base_cost_price']}}"
                                                       value="{{$product['stock_card_movement']['sale_price']?? $product['stock_card_movement']['sale_price']}}"
                                                       readonly/>
                                            </div>

                                              <input  name="reason_id[]" value="4" type="hidden" />
                                            <!--div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Neden</p>
                                                <select name="reason_id[]"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($reasons as $reason)
                                                        @if($reason->type == "3")
                                                            <option value="{{$reason->id}}">{{$reason->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div -->
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="discountInput"
                                                       class="form-label">İndirim (%)</label>
                                                <input type="number" data-newid="99999999" class="form-control"
                                                       id="discountInput"
                                                       min="0"
                                                       @role('admin')
                                                max="{{setting('admin.discount_admin')}}"
                                                @else
                                                    max="{{setting('admin.discount')}}"
                                                    @endrole
                                                    name="discount[]">
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                            <i class="bx bx-x fs-4 text-muted cursor-pointer" id="removeDiv"></i>
                                            <div class="dropdown">
                                                <i class="bx bx-cog bx-xs text-muted cursor-pointer more-options-dropdown"
                                                   role="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                   data-bs-auto-close="outside" aria-expanded="false">
                                                </i>
                                                <div class="dropdown-menu dropdown-menu-end w-px-300 p-3"
                                                     aria-labelledby="dropdownMenuButton">

                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <p class="mb-2 repeater-title">Açıklama</p>
                                                            <textarea class="form-control" rows="2"
                                                                      name="description[]"></textarea>
                                                        </div>

                                                    </div>
                                                    <div class="dropdown-divider my-3"></div>
                                                    <button type="button"
                                                            class="btn btn-label-primary btn-apply-changes">Uygulama
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" onclick="myFunction()" class="btn btn-secondary clon">EKLE
                                </button>
                            </div>
                            <!--div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" data-repeater-create="">Add Item
                                    </button>
                                </div>
                            </div -->
                            <hr class="my-4 mx-n4">
                            <div class="col-md-6 mb-md-0 mb-3">
                                <div class="d-flex align-items-center mb-3"
                                     style="font-size: 20px;text-align: right; font-weight: 700;    line-height: 3;">
                                    <label for="salesperson" class="form-label me-5 fw-semibold">Genel Toplam:</label>
                                    <div class="col-md-6 mb-md-0 mb-3 font-weight-bold" id="totalArea"></div>
                                </div>
                            </div>
                            <hr class="my-4 mx-n4">
                            <div class="col-md-6 mb-md-0 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <label for="salesperson" class="form-label me-5 fw-semibold">Personel:</label>
                                    <select required id="selectpickerLiveSearch" class="selectpicker w-100  StaffIdClass"  data-style="btn-default" name="staff_id" data-live-search="true">
                                        <option value="">Seçiniz</option>

                                    @foreach($users as $user)
                                        @if($user->id != 1)
                                            <option @if(isset($invoices))  {{ $invoices->hasStaff($user->id) ? 'selected' : '' }}
                                                    @endif value="{{$user->id}}"  data-value="{{$user->id}}">{{$user->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr class="my-4 mx-n4">
                            <div class="col-md-6 mb-md-0 mb-3" id="safeArea"></div>

                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Kredi Kartı</label>
                                    <input type="text" name="payment_type[credit_card]" value="0" id="credit_card"
                                           class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Nakit</label>
                                    <input type="text" name="payment_type[cash]" id="money_order" value="0"
                                           class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Taksit</label>
                                    <input type="text" name="payment_type[installment]" value="0" id="installment"
                                           class="form-control">
                                </div>

                            </div>


                            <hr>


                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="note" class="form-label fw-semibold">Not:</label>
                                        <textarea class="form-control" name="description" rows="2" id="note"> @if(isset($invoices))
                                                {{ $invoices->description}}
                                            @endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Invoice Add-->
                <div class="col-lg-2 col-12 invoice-actions">
                    <div class="card mb-4 ">
                        <div class="card-body">
                            <button id="saveButton" onclick="save()" type="button"
                                    class="btn btn-primary d-grid w-100 mb-3">
                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                                    class="bx bx-paper-plane bx-xs me-1"></i>Kaydet</span>
                            </button>
                        </div>
                    </div>
                    <div class="card bg-secondary text-white mb-3  mb-4 ">
                        <div class="card-body">

                            <p class="mb-2"><i class="bx bx-money bx-md me-1"></i> Ödeme Durumu</p>
                            <select name="paymentStatus" id="paymentStatus" class="form-select mb-4">
                                <option value="unpaid">Ödenecek</option>
                                <option value="paid">Ödendi</option>
                                <option value="paidOutOfPocket">Çalışan Cebinden Ödedi</option>
                            </select>
                            <p class="mb-2"><i class="bx bx-credit-card bx-md me-1"></i> Ödeme Tipi</p>

                            <p class="mb-2"><i class="bx bx-folder-open bx-md me-1"></i> Kategori</p>
                            <select name="accounting_category_id" class="form-select mb-4">
                                @foreach($categories as $category)
                                    @if($category->category == "gelir")
                                        <option @if($category->id == '1') selected
                                                @endif value="{{$category->id}}">{{$category->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mb-2"><i class="bx bx-calendar bx-md me-1"></i> Ödeneceği Tarih</p>
                            <input type="text" class="form-control flatpickr-input" placeholder="DD-MM-YYYY"
                                   id="flatpickr-date" readonly="readonly">
                        </div>
                    </div>
                    <!-- /Invoice Actions -->

                </div>

            </div>
        </form>
        <div id="loader" class="lds-dual-ring display-none overlay"></div>
    </div>
@endsection
@include('components.customermodal')

@section('custom-css')
    <style>
        .light-style .bootstrap-select .filter-option-inner-inner {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, .8);
            z-index: 999;
            opacity: 1;
            transition: all 0.5s;
        }


        .lds-dual-ring {
            display: inline-block;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 5% auto;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .display-none {
            display: none !important;
        }
    </style>
@endsection

@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
    <script>



        $("#myList1").on("change", ".serialnumber", function () {

            var dataId = $(this).data('id');
            var newVal = $(this).val();

            var Arr = [];
            $('.serialnumber').each(function () {
                Arr.push($(this).val());
            });
            var totalSerial = Arr.filter(x => x==newVal).length;
            if(totalSerial > 1)
            {
                Swal.fire("Aynı Seri numarası eklenemez");
                $("#" + dataId).remove();
                var sum = 0;
                $('.invoice-item-sales-price').each(function () {
                    sum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
                });
                $("#totalArea").html(sum + "₺");
                return false;
            }

            var postUrl = window.location.origin + '/serialcheck?id=' + $(this).val() + '';   // Returns base URL (https://example.com)
            $.ajax({
                type: "GET",
                url: postUrl,
                success: function (data) {
                    if(data.status == false)
                    {
                        Swal.fire(data.message);
                        return false;
                    }
                    console.log(data);
                    let sales_price = JSON.stringify(data);
                    $("#myList1").find("#serialstock" + dataId).val(data.sales_price.stock_card_id).trigger('change');
                    $("#myList1").find("#serialstock" + dataId).attr('data-test', data.sales_price.stock_card_id);
                    $("#myList1").find("#serial" + dataId).attr('data-cost', data.sales_price.base_cost_price);
                    $("#myList1").find("#serial" + dataId).attr('data-sales', data.sales_price.sale_price);
                    $("#myList1").find("#serial" + dataId).val(data.sales_price.sale_price);
                    $("#myList1").find("#serial" + dataId).attr('value',data.sales_price.sale_price);
                    $("#myList1").find(".invoice-item-cost-price").val(data.sales_price.base_cost_price);
                    var inputPrice = [];

                    var sum = 0;
                    $('.invoice-item-sales-price').each(function () {
                        sum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
                    });
                    $("#totalArea").html(sum + "₺");

                    /*
                    *$(this).after().val(data.sales_price.sale_price);
                    $(this).after(".invoice-item-sales-price").attr('sales',data.sales_price.sale_price);
                    $(this).after(".invoice-item-sales-price").attr('data-sales',data.sales_price.sale_price);
                    //$('#saveButton').prop('disabled', false);
                    * */
                }
            });
        });
        $(document).ready(function () {

            //$('#saveButton').prop('disabled', {{isset($request->serial)}});


            $("#serialnumber").keyup(function (qualifiedName) {
                var postUrl = window.location.origin + '/serialcheck?id=' + $(this).val() + '';   // Returns base URL (https://example.com)
                $.ajax({
                    type: "GET",
                    url: postUrl,
                    success: function (data) {
                        if (data.status === false) {
                            $("#saveButton").attr('disabled', true);
                        } else {
                            $(".invoice-item-sales-price").val(data.sales_price.sale_price);
                            $(".invoice-item-sales-price").attr('sales', data.sales_price.sale_price);
                            $(".invoice-item-sales-price").attr('data-sales', data.sales_price.sale_price);

                            $('#saveButton').prop('disabled', false);
                        }
                    }
                });
            });


            $("#myList1").on("change","#discountInput",function () {
                var max = $(this).attr('max');
                var newID = $(this).data('newid');
                var salesprice = $("#"+newID).find('#serial'+newID).data('sales');
                var baseCostprice = $("#"+newID).find('#serial'+newID).data('cost');
                var discount = $(this).val();
                if(discount > 0)
                {
                    var newSalesPrice = salesprice - ((discount * salesprice) / 100);
                    console.log(discount,max);
                    if(parseInt(discount) > parseInt(max))
                    {
                        Swal.fire('İndirim oranı max değerden fazla olamaz');
                    }else{
                        if (newSalesPrice > baseCostprice) {
                            $("#"+newID).find('#serial'+newID).val(Math.round(newSalesPrice));

                            var sum = 0;
                            $('.invoice-item-sales-price').each(function () {
                                sum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
                            });
                            $("#totalArea").html(sum + "₺");

                        } else {
                            Swal.fire('Destekli Satış Fiyatı altına satılamaz');
                        }
                    }


                }else{
                    $(this).val('');
                    return false;
                }


            })
        });

        $("#serialnumber").show();

        function validateSerial(array) {
            let serialError = true;
            let serialValue = $("#serialnumber").val();
            if (serialValue.length == "") {
                $("#serialcheck").show();
                serialError = false;
                return false;
            } else {
                $("#serialcheck").hide();
            }

         }

        function getCustomer(id) {
            var postUrl = window.location.origin + '/custom_customerget?id=' + id + '';   // Returns base URL (https://example.com)
            $.ajax({
                type: "POST",
                url: postUrl,
                encode: true,
            }).done(function (data) {
                $(".customerinformation").html('<p className="mb-1">' + data.address + '</p><p className="mb-1">' + data.phone1 + '</p><p className="mb-1">' + data.email + '</p>');
            });
        }

        function save() {


            if($("select.StaffIdClass").val().length <= 0)
            {
               alert("Personel Seçimi Yapmadınız");
               return false;
            }
            if($("#serialnumber").val().length <= 0)
            {
                alert("Seri Seçimi Yapmadınız");
                return false;
            }

            var arrayNew = []
            $('input.serialnumber').each(function (index, elem) {
                var xyz = $(elem).val();
                arrayNew.push(xyz);
            });
            validateSerial(arrayNew);

            $("#saveButton").prop( "disabled", true );

            var postUrl = window.location.origin + '/invoice/salesstore';   // Returns base URL (https://example.com)
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
                    Swal.fire(data);
                    window.location.href = "{{route('sale.index')}}";
                },
                error: function (xhr) { // if error occured
                    Swal.fire(xhr.responseJSON,'',"error");

                },
                complete: function () {
                   // window.location.href = "{{route('sale.index')}}";
                },

            });
        }

        $("#paymentStatus").change(function () {
            var type = $(this).val();
            if (type == 'paid') {
                $("#safeArea").html('<div class="d-flex align-items-center mb-3">' +
                    '<label for="salesperson" class="form-label me-5 fw-semibold">Kasa / Banka:</label>' +
                    '<select id="selectpickerLiveSearch" class="form-select w-100" data-style="btn-default" name="staff_id" data-live-search="true">' +
                    @foreach($safes as $safe)
                        '<option @if(isset($invoices)) {{ $invoices->hasSafe($safe->id) ? 'selected' : '' }} @endif value="{{$safe->id}}" data-value="{{$safe->id}}">{{$safe->name}}</option>' +
                    @endforeach
                        '</select>' +
                    '</div>');
            } else if (type == 'paidOutOfPocket') {
                $("#safeArea").html('<div class="d-flex align-items-center mb-3">' +
                    '<label for="salesperson" class="form-label me-5 fw-semibold">İsim Soyisim:</label>' +
                    '<input type="text" id="pay_to" class="form-control" name="pay_to" @if(isset($invoices)) value="{{$invoices->pay_to}}" @endif />' +
                    '</div>');
            } else {
                $("#safeArea").html(' ');
            }
        })
    </script>


    <script>

        function myFunction() {
            var rand = Math.floor(Math.random() * 100);
            $(".repeater-wrapper-new").find(".select2").each(function (index) {
                $("select.select2-hidden-accessible").select2('destroy');
            });
            const node = document.getElementById("99999999");
            const clone = node.cloneNode(true);
            clone.setAttribute('id', rand);
            document.getElementById("myList1").appendChild(clone);
            $("#" + rand).find('#removeDiv').attr('data-id', rand);
            $("#" + rand).find('#serialnumber').attr('data-id', rand);
            $("#" + rand).find('.invoice-item-sales-price').attr('id', "serial" + rand);
            $("#" + rand).find('.item-details-stock').attr('id', "serialstock" + rand);
            $("#" + rand).find('.invoice-item-sales-price').val('');

            $("select.select2").select2();
            $("#" + rand).find('input:text').val('');
            $("#" + rand).find('input').attr('data-newId',rand);
            window.scrollBy(0, 400)

        }
    </script>
    <script>
        $("#myList1").on("click", "#removeDiv", function () {
            var Divid = $(this).data('id');
            $("#" + Divid).remove();
            var sum = 0;
            $('.invoice-item-sales-price').each(function () {
                sum += parseFloat($(this).val());  // Or this.innerHTML, this.innerText
            });
            $("#totalArea").html(sum + "₺");
        })
    </script>

    <script>
        app.controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {
            $scope.getCustomers = function () {
                var postUrl = window.location.origin + '/customers?type=customer';   // Returns base URL (https://example.com)
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
        $(document).ready(function () {

            const email = document.getElementById("email");
            email.addEventListener("blur", () => {
                let regex = /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
                let s = email.value;
                if (regex.test(s)) {
                    email.classList.remove("is-invalid");
                    emailError = true;
                } else {
                    email.classList.add("is-invalid");
                    emailError = false;
                }
            });

            // Validate Password
            $("#passcheck").hide();
            let passwordError = true;
            $("#password").keyup(function () {
                validatePassword();
            });

            function validatePassword() {
                let passwordValue = $("#password").val();
                if (passwordValue.length == "") {
                    $("#passcheck").show();
                    passwordError = false;
                    return false;
                }
                if (passwordValue.length < 3 || passwordValue.length > 10) {
                    $("#passcheck").show();
                    $("#passcheck").html(
                        "**length of your password must be between 3 and 10"
                    );
                    $("#passcheck").css("color", "red");
                    passwordError = false;
                    return false;
                } else {
                    $("#passcheck").hide();
                }
            }

            // Validate Confirm Password
            $("#conpasscheck").hide();
            let confirmPasswordError = true;
            $("#conpassword").keyup(function () {
                validateConfirmPassword();
            });

            function validateConfirmPassword() {
                let confirmPasswordValue = $("#conpassword").val();
                let passwordValue = $("#password").val();
                if (passwordValue != confirmPasswordValue) {
                    $("#conpasscheck").show();
                    $("#conpasscheck").html("**Password didn't Match");
                    $("#conpasscheck").css("color", "red");
                    confirmPasswordError = false;
                    return false;
                } else {
                    $("#conpasscheck").hide();
                }
            }

            // Submit button
            $("#submitbtn").click(function () {
                validateUsername();
                validatePassword();
                validateConfirmPassword();
                validateEmail();
                if (
                    usernameError == true &&
                    passwordError == true &&
                    confirmPasswordError == true &&
                    emailError == true
                ) {
                    return true;
                } else {
                    return false;
                }
            });
        });
    </script>
@endsection

