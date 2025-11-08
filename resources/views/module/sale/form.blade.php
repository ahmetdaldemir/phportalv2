@extends('layouts.admin')

@section('content')
    <div id="sale-form-app" class="container-xxl flex-grow-1 container-p-y">
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
                                            <select 
                                                v-model="form.customer_id" 
                                                @change="onCustomerChange"
                                                class="form-select">
                                                <option value="1">Genel Cari</option>
                                                <option 
                                                    v-for="customer in customers" 
                                                    :key="customer.id"
                                                    :value="customer.id">
                                                    @{{ customer.fullname }}
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
                                                       @if(isset($invoices)) value="{{$invoices->number}}"
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
                                                       @if(isset($invoices)) value="{{$invoices->create_date}}"
                                                       @else  value="{{date('d-m-Y')}}" @endif />
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tipi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <select class="form-control" data-style="btn-default" name="type" id="type">
                                                    <option @if(isset($invoices) && $invoices->type == 2) selected
                                                            @endif value="2">Giden Fatura
                                                    </option>
                                                </select>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>


                            <hr class="mx-n4">


                            <div class="mb-3" data-repeater-list="group_a">
                                <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                    <div class="d-flex border rounded position-relative pe-0">
                                        <div class="row w-100 m-0 p-3">
                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok</p>
                                                <select name="stock_card_id" id="stock_card_id"  class="form-select item-details select2 mb-2">
                                                    @foreach($stocks as $stock)
                                                        <option value="{{$stock->id}}">{{$stock->name}} -
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
                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Seri No</p>
                                                <input type="text" class="form-control" name="serial"  placeholder="11111111"/>
                                            </div>

                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Neden</p>
                                                <select name="reason_id"  class="js-example-basic-single js-states form-control " id="id_label_single"  tabindex="-1" aria-hidden="true">
                                                    @foreach(\App\Models\Reason::ReasonList as $key => $myValue)
                                                        <optgroup label="{{$myValue}}">
                                                            @foreach($reasons as $reason)
                                                                @if($reason->type == $key)
                                                                    <option value="{{$reason->id}}">{{$reason->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Destekli Maliyet</p>
                                                <input type="text" class="form-control invoice-item-price" name="base_cost_price" readonly/>
                                            </div>
                                            <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                <input type="text" class="form-control invoice-item-price" name="sale_price" required="required"/>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Adet</p>
                                                <input type="number" class="form-control invoice-item-qty" value="1"  name="quantity" min="1" max="50" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" data-repeater-create="">Add Item
                                    </button>
                                </div>
                            </div>

                            <hr class="my-4 mx-n4">

                            <div class="row py-sm-3">
                                <div class="col-md-6 mb-md-0 mb-3">
                                    <div class="d-flex align-items-center mb-3">
                                        <label for="salesperson" class="form-label me-5 fw-semibold">Personel:</label>
                                        <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                                data-style="btn-default" name="staff_id" data-live-search="true">
                                            @foreach($users as $user)
                                                <option @if(isset($invoices))
                                                            {{ $invoices->hasStaff($user->id) ? 'selected' : '' }}
                                                        @endif value="{{$user->id}}"
                                                        data-value="{{$user->id}}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-md-0 mb-3" id="safeArea"></div>
                                <hr>
                                <div class="col-md-12 d-flex justify-content-start">
                                    <div class="invoice-calculations">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="w-px-100">Aratoplam:</span>
                                            <span class="fw-semibold"></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="w-px-100">İndirim:</span>
                                            <span class="fw-semibold"></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="w-px-100">Kdv:</span>
                                            <span class="fw-semibold"></span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="w-px-100">Toplam:</span>
                                            <span class="fw-semibold"> @if(isset($invoices))
                                                    {{ $invoices->total_price}}
                                                @endif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

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
                            <button onclick="save()" type="button" class="btn btn-primary d-grid w-100 mb-3">
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
                            <select name="payment_type" class="form-select mb-4">
                                <option value="1">Havale</option>
                                <option value="2">Kredi Kartı</option>
                                <option value="3">Nakit</option>
                            </select>
                            <p class="mb-2"><i class="bx bx-folder-open bx-md me-1"></i> Kategori</p>
                            <select name="accounting_category_id" class="form-select mb-4">
                                @foreach($categories as $category)
                                    <option @if($category->id == '1') selected
                                            @endif value="{{$category->id}}">{{$category->name}}</option>
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
            var postUrl = window.location.origin + '/sale/store';   // Returns base URL (https://example.com)
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
                    window.location.href = "{{route('invoice.index')}}";
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
                var postUrl = window.location.origin + '/custom_customerstore';
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
                    var result = response.data;
                    
                    // Check if it's a success response
                    if (result.success) {
                        $scope.getCustomers();
                        $('#selectCustomer').val(result.id);
                        
                        var modalDiv = $("#editUser");
                        modalDiv.modal('hide');
                        modalDiv.find("input,textarea,select").val('').end()
                            .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
                        
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(result.message);
                        }
                    }
                }, function errorCallback(response) {
                    // Handle duplicate or error
                    if (response.status === 409 && response.data.warning) {
                        var existingCustomer = response.data.existing_customer;
                        var useExisting = confirm(
                            '⚠️ ' + response.data.message + '\n\n' +
                            'Mevcut Müşteri Bilgileri:\n' +
                            'Ad Soyad: ' + existingCustomer.fullname + '\n' +
                            'Telefon: ' + existingCustomer.phone1 + '\n' +
                            'Tip: ' + existingCustomer.type + '\n\n' +
                            'Bu müşteriyi kullanmak ister misiniz?'
                        );
                        
                        if (useExisting) {
                            $scope.getCustomers();
                            $('#selectCustomer').val(existingCustomer.id);
                            
                            var modalDiv = $("#editUser");
                            modalDiv.modal('hide');
                            modalDiv.find("input,textarea,select").val('').end()
                                .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
                            
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Mevcut Müşteri Seçildi',
                                    text: existingCustomer.fullname + ' müşterisi seçildi.'
                                });
                            }
                        }
                    } else {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: response.data.message || 'Müşteri kaydedilirken hata oluştu!'
                            });
                        } else {
                            alert(response.data.message || 'Müşteri kaydedilirken hata oluştu!');
                        }
                    }
                });
            }
        });
    </script>

    <!-- Vue.js App for Sale Form -->
    <script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                form: {
                    id: @json($invoices->id ?? null),
                    customer_id: @json($invoices->customer_id ?? '1'),
                    number: @json($invoices->number ?? ''),
                    create_date: @json($invoices->create_date ?? date('Y-m-d'))
                },
                customers: @json($customers ?? []),
                submitting: false
            }
        },
        computed: {
            selectedCustomer() {
                return this.customers.find(c => c.id == this.form.customer_id);
            }
        },
        methods: {
            onCustomerChange() {
                console.log('Customer changed:', this.selectedCustomer);
            },
            
            async loadCustomers() {
                try {
                    const response = await fetch('/api/customers');
                    const data = await response.json();
                    this.customers = data;
                } catch (error) {
                    console.error('Error loading customers:', error);
                }
            }
        },
        
        mounted() {
            // Listen for customer save events
            window.addEventListener('customerSaved', (event) => {
                this.customers.push(event.detail);
                this.form.customer_id = event.detail.id;
            });
        }
    }).mount('#sale-form-app');
    </script>

@endsection

