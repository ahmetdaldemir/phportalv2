@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form id="invoiceForm" method="post" class="form-repeater source-item py-sm-3">
            <input type="hidden" name="id" @if(isset($invoices)) value="{{$invoices->id}}" @endif />
            <div class="row invoice-add">
                <!-- Invoice Add-->
                <div class="col-lg-9 col-12 mb-lg-0 mb-4">
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
                                                       @if(isset($invoices)) value="{{$invoices->number}}"
                                                       @endif name="number" id="invoiceId">
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tarihi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" id="invoice-date" class="form-control single-datepicker"
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
                                                    <option @if(isset($invoices) && $invoices->type == 1) selected
                                                            @endif value="1">Gelen Fatura
                                                    </option>
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
                            <input name="tax" id="tax" value="18" type="hidden">
                            <input name="exchange" id="exchange" value="1" type="hidden">
                            <input name="currency" id="currency" value="1" type="hidden">
                            <hr class="my-4 mx-n4">
                            <div class="row py-sm-3">
                                <div class="col-md-6 mb-md-0 mb-3">
                                    <div class="d-flex align-items-center mb-3">
                                        <label for="salesperson" class="form-label me-5 fw-semibold">İşlem Yapan Personel:</label>
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
                            </div>
                            <hr class="my-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Kredi Kartı</label>
                                    <input type="text" name="payment_type[credit_card]"  value="0"  id="credit_card"
                                           class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Nakit</label>
                                    <input type="text" name="payment_type[cash]"  value="0"  id="money_order" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">Taksit</label>
                                    <input type="text" name="payment_type[installment]"  value="0"  id="installment"
                                           class="form-control">
                                </div>

                            </div>
                            <hr class="my-4">

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="note" class="form-label fw-semibold">Not:</label>
                                        <textarea class="form-control" name="description" rows="3" id="note"> @if(isset($invoices))
                                                {{ $invoices->description}}
                                            @endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-12 invoice-actions">
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
                        <p class="mb-2"><i class="bx bx-image-add bx-md me-1"></i> Fiş / Fatura Görseli</p>
                        <input class="form-control" type="file" id="formFile" name="image">
                            <hr>
                        <p class="mb-2"><i class="bx bx-money bx-md me-1"></i> Ödeme Durumu</p>
                        <select name="paymentStatus" id="paymentStatus" class="form-select mb-4">
                            <option value="unpaid">Ödenecek</option>
                            <option value="paid">Ödendi</option>
                            <option value="paidOutOfPocket">Çalışan Cebinden Ödedi</option>
                        </select>

                        <p class="mb-2"><i class="bx bx-folder-open bx-md me-1"></i> Kategori</p>
                        <select name="accounting_category_id" class="form-select mb-4">
                            @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                        <p class="mb-2"><i class="bx bx-calendar bx-md me-1"></i> Ödeneceği Tarih</p>
                        <input type="text" class="form-control single-datepicker" placeholder="DD-MM-YYYY" id="payment-date" readonly="readonly">
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
    <script src="{{asset('assets/js/forms-pickers.js')}}"></script>
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
            if(type == 'paid')
            {
               $("#safeArea").html('<div class="d-flex align-items-center mb-3">'+
               '<label for="salesperson" class="form-label me-5 fw-semibold">Kasa / Banka:</label>'+
                   '<select id="selectpickerLiveSearch" class="form-select w-100" data-style="btn-default" name="staff_id" data-live-search="true">'+
                    @foreach($safes as $safe)
                    '<option @if(isset($invoices)) {{ $invoices->hasSafe($safe->id) ? 'selected' : '' }} @endif value="{{$safe->id}}" data-value="{{$safe->id}}">{{$safe->name}}</option>'+
                    @endforeach
                        '</select>'+
                '</div>');
            }else if(type == 'paidOutOfPocket')
            {
                $("#safeArea").html('<div class="d-flex align-items-center mb-3">'+
                    '<label for="salesperson" class="form-label me-5 fw-semibold">İsim Soyisim:</label>'+
                    '<input type="text" id="pay_to" class="form-control" name="pay_to" @if(isset($invoices)) value="{{$invoices->pay_to}}" @endif />'+
                    '</div>');
            }else{
                $("#safeArea").html(' ');
            }
        })


        $("#total_price").keyup(function () {
            var price = $(this).val();
            var tax = $("input[name='tax']").val();
            var tax_total = (price * tax)/100;
            $("#tax_total").val(tax_total);
        })


        function taxCalculate(tax)
        {
            var price = $("#total_price").val();
            $("#tax_text").text(tax);
            if(price == '')
            {
                $("#tax_total").val(null);
            }else{
                var tax_total = (price * tax)/100;
                $("#tax_total").val(tax_total);
            }

        }
        function currencyCalculate(symbol,currency,exchange)
        {
            var price = $("#total_price").val();
            $("input[name='exchange']").text(exchange);
            $("input[name='currency']").text(currency);
            $("#exchange_text").text(parseFloat(exchange, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
            $("#currencySymbol").text(symbol);
            if(price == '')
            {
                $("#total_price").val(null);
            }else{
                var total_price = (price * exchange);
                $("#total_price").val(total_price);
            }

        }

    </script>
@endsection

