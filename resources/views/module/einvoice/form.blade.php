@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form id="invoiceForm" method="post" class="form-repeater source-item py-sm-3">
            <input type="hidden" name="id"/>
            <div class="row invoice-add">
                <div class="col-lg-10 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                                <div class="col-md-6 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectpickerLiveSearch" class="form-label">Müşteri Seçiniz</label>
                                        <div class="col-md-9">
                                            <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                                    data-style="btn-default" name="customer_id"
                                                    onchange="getCustomer(this.value)" id="customer_id"
                                                    data-live-search="true">
                                                <option value="1" data-tokens="ketchup mustard">Genel Müşteri</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{$customer->id}}"
                                                            @if(isset($invoices) && $customer->id == $invoices->customer_id) selected
                                                            @endif data-value="{{$customer->id}}">{{$customer->fullname}}</option>
                                                @endforeach
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
                                                       value="{{rand(11111111111,99999999999999)}}" name="number"
                                                       id="invoiceId">
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
                                                <select class="form-control" data-style="btn-default" name="type"
                                                        id="type">
                                                    <option value="1">Gelen Fatura</option>
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
                                            <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Stok</p>
                                                <select name="stock_card_id" class="form-select item-details mb-2">
                                                    @foreach($stocks as $stock)
                                                        <option
                                                            @if($product['stock_card_movement']->stock_card_id == $stock->id) selected
                                                            @endif  value="{{$stock->id}}">{{$stock->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Seri No</p>
                                                <input type="text" class="form-control" name="serial"
                                                       value="{{$product['stock_card_movement']->serial_number}}"/>
                                            </div>

                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                <input type="text" class="form-control invoice-item-price"
                                                       name="sale_price"
                                                       value="{{$product['stock_card_movement']->sale_price}}"/>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Qty</p>
                                                <input type="number" class="form-control invoice-item-qty"
                                                       name="quantity" value="1" min="1" max="50">
                                            </div>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">Neden</p>
                                                <select name="reason_id" class="form-select item-details mb-2">
                                                    @foreach($reasons as $reason)
                                                        <option
                                                            @if($product['stock_card_movement']->reason_id == $reason->id) selected
                                                            @endif value="{{$reason->id}}">{{$reason->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="taxInput1" class="form-label">KDV</label>
                                                <select name="tax" id="taxInput1" class="form-select tax-select">
                                                    <option @if($product['stock_card_movement']->tax == 0) selected
                                                            @endif value="0" selected="">0%
                                                    </option>
                                                    <option @if($product['stock_card_movement']->tax == 1) selected
                                                            @endif value="1">1%
                                                    </option>
                                                    <option @if($product['stock_card_movement']->tax == 8) selected
                                                            @endif value="8">10%
                                                    </option>
                                                    <option @if($product['stock_card_movement']->tax == 18) selected
                                                            @endif value="18" selected>18%
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-5 col-12 mb-md-0 mb-3 ps-md-0">
                                                <p class="mb-2 repeater-title">IMEI</p>
                                                <input class="form-control" name="imei"
                                                       value="{{$product['stock_card_movement']->imei}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" data-repeater-create="">Stok Ekle</button>
                                    <button type="button" id="dataRepeater" class="btn btn-danger">HESAPLA</button>
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
                                <div class="col-md-6 d-flex justify-content-end">
                                    <div class="invoice-calculations">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="w-px-100">İndirim:</span>
                                            <span class="fw-semibold">{{$product['stock_card_movement']->discount ?? 0}} %</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="w-px-100">Kdv:</span>
                                            <span class="fw-semibold">{{($product['stock_card_movement']->sale_price * $product['stock_card_movement']->tax) / 100}}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="w-px-100">Toplam:</span>
                                            <span class="fw-semibold"> @if(isset($product))
                                                    {{$product['stock_card_movement']->sale_price}}
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
                <!-- Invoice Actions -->
                <div class="col-lg-2 col-12 invoice-actions">
                    <div class="card mb-4">
                        <div class="card-body">
                            <button onclick="save()" type="button" class="btn btn-primary d-grid w-100 mb-3">
                                <i class="bx bx-paper-plane bx-xs me-1"></i>Fatura Gönder
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="mb-2">Ödeme Tipi</p>
                        <select name="payment_type" class="form-select mb-4">
                            <option value="1">Havale</option>
                            <option value="2">Kredi Kartı</option>
                            <option value="3">Nakit</option>
                        </select>
                        <div class="d-flex justify-content-between mb-2">
                            <label for="payment-terms" class="mb-0">Payment Terms</label>
                            <label class="switch switch-primary me-0">
                                <input type="checkbox" class="switch-input" id="payment-terms" checked="">
                                <span class="switch-toggle-slider">
                                    <span class="switch-on">
                                      <i class="bx bx-check"></i>
                                    </span>
                                    <span class="switch-off">
                                      <i class="bx bx-x"></i>
                                    </span>
                                  </span>
                                <span class="switch-label"></span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <label for="payment-stub" class="mb-0">Payment Stub</label>
                            <label class="switch switch-primary me-0">
                                <input type="checkbox" class="switch-input" id="payment-stub">
                                <span class="switch-toggle-slider">
                            <span class="switch-on">
                              <i class="bx bx-check"></i>
                            </span>
                            <span class="switch-off">
                              <i class="bx bx-x"></i>
                            </span>
                          </span>
                                <span class="switch-label"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- /Invoice Actions -->

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
            var postUrl = window.location.origin + '/e_invoice/e_invoice_create';   // Returns base URL (https://example.com)
            $.ajax({
                type: "POST",
                url: postUrl,
                data: $("#invoiceForm").serialize(),
                dataType: "json",
                encode: true,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

    </script>

    <script>
        $("#dataRepeater").click(function () {
            $('.repeater-wrapper[data-repeater-item]').each(function(){
                 console.log($('input').val());
            });
        })
    </script>
@endsection

