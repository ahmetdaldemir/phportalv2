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
                            <div class="mb-3">
                                <div class="row">
                                    <label for="total_price" class="col-md-3 col-form-label">Toplam Tutar / Döviz  </label>
                                    <div class="col-md-9">
                                        <div class=" input-group">
                                            <input type="text" class="form-control" id="total_price"
                                                   @if(isset($invoices)) value="{{$invoices->total_price}}"
                                                   @endif  name="total_price"
                                                   aria-describedby="name"
                                                   aria-label="Text input with segmented dropdown button" required>
                                            <input name="exchange" id="exchange" value="1" type="hidden">
                                            <input name="currency" id="currency" value="1" type="hidden">
                                            <span class="input-group-text" id="exchange_text">1.00</span>
                                            <button type="button" class="btn btn-outline-primary"> <span id="currencySymbol" style="font-weight: 800;margin-right: 10px;">₺</span> Döviz</button>
                                            <button type="button"
                                                    class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @foreach($currencies as $currency)
                                                    <li><a class="dropdown-item" onclick="currencyCalculate('{{$currency->symbol}}',{{$currency->id}},{{$currency->exchange_rate}})" data-id="{{$currency->id}}" data-exchange="{{$currency->exchange_rate}}" href="javascript:void(0);">{{$currency->name}}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4 mx-n4">
                            <div class="row py-sm-3">
                                <div class="col-md-6 mb-md-0 mb-3" id="safeArea"></div>
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
                                    <option @if($category->id == '5') selected @endif value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                            <p class="mb-2"><i class="bx bx-calendar bx-md me-1"></i> Vade Tarih</p>
                            <input type="text" class="form-control single-datepicker" placeholder="DD-MM-YYYY" id="payment-date" readonly="readonly">
                        </div>
                    </div>
                    <!-- /Invoice Actions -->

                </div>
        </form>
        <div id="loader" class="lds-dual-ring display-none overlay"></div>
    </div>
@endsection

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

    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
    <script src="{{asset('assets/js/forms-pickers.js')}}"></script>
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

