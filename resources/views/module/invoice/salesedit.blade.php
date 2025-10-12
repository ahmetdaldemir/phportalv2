,@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form id="invoiceForm" method="post" class="form-repeater source-item py-sm-3">
            <input type="hidden" name="id" @if(isset($invoice)) value="{{$invoice->id}}" @endif />
            <input type="hidden" name="staff_id" value="{{auth()->user()->id}}"/>
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
                                                    data-style="btn-default" name="customer_id" ng-init="getCustomers()" onchange="getCustomer(this.value)">
                                                <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                                <option ng-repeat="customer in customers" ng-if="customer.type == 'customer'"
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
                                    <div class="customerinformation">

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
                                                <input type="text" class="form-control single-datepicker"
                                                       name="create_date"
                                                       @if(isset($invoice)) value="{{$invoice->create_date}}"
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


                            <div class="mb-3" data-repeater-list="group_a">

                                <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                    @foreach($stockcardmovements as $stockcardmovement)
                                    <div class="d-flex border rounded position-relative pe-0">
                                        <input type="hidden" name="stockcardmovementid" value="{{$stockcardmovement->id}}" />
                                        <div class="row w-100 m-0 p-3">
                                            <div class="col-md-5 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok</p>
                                                <select name="stock_card_id"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($stocks as $stock)
                                                        <option @if($stockcardmovement->stock_card_id == $stock->id) selected  @endif value="{{$stock->id}}">
                                                            {{$stock->name}} - <small> {{$stock->brand->name}}</small> - <b>  <?php
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
                                                <input type="text" id="serialnumber" class="form-control" name="serial"
                                                       required readonly
                                                       @if(isset($stockcardmovement->serial_number)) value="{{$stockcardmovement->serial_number}}" @endif
                                                />
                                                <h5 id="serialcheck" style="color: red;">
                                                    **Seri Numarası Zorunludur
                                                </h5>
                                            </div>
                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">IMEI</p>
                                                <input minlength="15" maxlength="15" placeholder="Boş Bıralabilirsiniz"
                                                       class="form-control" name="imei" readonly/>
                                            </div>

                                            @role('admin')
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Destekli Maliyet</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="base_cost_price"
                                                       value="{{$stockcardmovement->base_cost_price}}"
                                                       readonly/>
                                            </div>
                                            @endrole
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="sale_price"
                                                       value="{{$stockcardmovement->sale_price}}"
                                                       readonly/>
                                            </div>


                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Neden</p>
                                                <select name="reason_id"
                                                        class="form-select item-details select2 mb-2">
                                                    @foreach($reasons as $reason)
                                                        @if($reason->type == "3")
                                                            <option value="{{$reason->id}}">{{$reason->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <label for="discountInput"
                                                       class="form-label">İndirim (%)</label>
                                                <input type="number" class="form-control"
                                                       id="discountInput"
                                                       min="0"
                                                       @role('admin')
                                                max="{{setting('admin.discount_admin')}}"
                                                @else
                                                    max="{{setting('admin.discount')}}"
                                                    @endrole
                                                    name="discount">
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                            <i class="bx bx-x fs-4 text-muted cursor-pointer"
                                               data-repeater-delete=""></i>
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
                                                                      name="description"></textarea>
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
                                    @endforeach
                                </div>
                            </div>
                            <!--div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" data-repeater-create="">Add Item
                                    </button>
                                </div>
                            </div -->

                            <hr class="my-4 mx-n4">
                            <div class="col-md-6 mb-md-0 mb-3">
                                <div class="d-flex align-items-center mb-3">
                                    <label for="salesperson" class="form-label me-5 fw-semibold">Personel:</label>
                                    <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                            data-style="btn-default" name="staff_id" data-live-search="true">
                                        @foreach($users as $user)
                                            <option @if(isset($invoice))
                                                        {{ $invoice->hasStaff($user->id) ? 'selected' : '' }}
                                                    @endif value="{{$user->id}}"
                                                    data-value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-md-0 mb-3" id="safeArea"></div>


                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Kredi Kartı</label>
                                    <input type="text" name="payment_type[credit_card]" id="credit_card" value="{{$invoice->credit_card}}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Nakit</label>
                                    <input type="text" name="payment_type[cash]" id="money_order" value="{{$invoice->cash}}"  class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Taksit</label>
                                    <input type="text" name="payment_type[installment]" value="{{$invoice->installment}}"  id="installment"
                                           class="form-control">
                                </div>

                            </div>


                            <hr>


                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="note" class="form-label fw-semibold">Not:</label>
                                        <textarea class="form-control" name="description" rows="2" id="note"> @if(isset($invoice))
                                                {{ $invoice->description}}
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
                            <input type="text" class="form-control single-datepicker" placeholder="DD-MM-YYYY"
                                   id="payment-date" readonly="readonly">
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
      //  $(document).ready(function () {
//
      //      $("#serialnumber").keyup(function (qualifiedName) {
      //          var postUrl = window.location.origin + '/serialcheck?id=' + $(this).val() + '';   // Returns base URL (https://example.com)
      //          $.ajax({
      //              type: "GET",
      //              url: postUrl,
      //              success: function (data) {
      //                  if (data === false) {
      //                      $("#saveButton").attr('disabled', true);
      //                  } else {
      //                      $('#saveButton').prop('disabled', false);
      //                  }
      //              }
      //          });
      //      });
      //  });

        $("#serialnumber").show();

        function validateSerial() {
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
            validateSerial();
            var postUrl = window.location.origin + '/invoice/salesupdate';   // Returns base URL (https://example.com)
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
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $(placeholder).append(xhr.statusText + xhr.responseText);
                    $(placeholder).removeClass('loading');
                },
                complete: function () {
                    window.location.href = "{{route('sale.index')}}";
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
                        '<option @if(isset($invoice)) {{ $invoice->hasSafe($safe->id) ? 'selected' : '' }} @endif value="{{$safe->id}}" data-value="{{$safe->id}}">{{$safe->name}}</option>' +
                    @endforeach
                        '</select>' +
                    '</div>');
            } else if (type == 'paidOutOfPocket') {
                $("#safeArea").html('<div class="d-flex align-items-center mb-3">' +
                    '<label for="salesperson" class="form-label me-5 fw-semibold">İsim Soyisim:</label>' +
                    '<input type="text" id="pay_to" class="form-control" name="pay_to" @if(isset($invoice)) value="{{$invoice->pay_to}}" @endif />' +
                    '</div>');
            } else {
                $("#safeArea").html(' ');
            }
        })
    </script>

    <!-- Vue.js App for Sales Edit Form -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Vue === 'undefined') {
            console.error('Vue.js is not loaded.');
            return;
        }

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    customers: @json($customers ?? []),
                    globalStore: window.globalStore || { cache: { brands: [], colors: [], versions: [], customers: [] } }
                }
            },
            methods: {
                updateSelectOptions() {
                    // Update select2 if needed
                    if (jQuery && jQuery.fn.select2) {
                        setTimeout(() => {
                            jQuery('#selectCustomer').trigger('change');
                        }, 100);
                    }
                }
            },
            mounted() {
                // Listen for customer save events
                window.addEventListener('customerSaved', (event) => {
                    const customer = event.detail;
                    if (customer && customer.id) {
                        // Check if customer already exists
                        const exists = this.customers.find(c => c.id === customer.id);
                        if (!exists) {
                            this.customers.push(customer);
                        }
                        
                        // Update select option
                        setTimeout(() => {
                            const selectCustomer = document.getElementById('selectCustomer');
                            if (selectCustomer) {
                                selectCustomer.value = customer.id;
                                jQuery('#selectCustomer').trigger('change');
                            }
                        }, 100);
                        
                        console.log('New customer selected:', customer);
                    }
                });
            }
        }).mount('body');
    });
    </script>

    <script>
        $(document).ready(function () {
            // Validate Username


            // Validate Email
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

