@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <h4 class="card-title mb-0">
                            <i class="bx bx-transfer me-2"></i>
                            Transfer Formu
                        </h4>
                        <p class="card-text mt-2 mb-0">Yeni transfer oluşturun veya mevcut transferi düzenleyin</p>
                    </div>
                </div>
            </div>
        </div>

        <form id="invoiceForm" method="post" class="form-repeater source-item py-sm-3 fade-in" autocomplete="off">
            @csrf
            <input type="hidden" name="id" @if(isset($transfers)) value="{{$transfers->id}}" @endif />
            @if($request->filled('serial_number'))
                <input type="hidden" name="type" value="{{$request->type}}" />
            @else
                <input type="hidden" name="type" value="other"/>
            @endif
            <div class="row invoice-add">
                <div class="col-lg-10 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-edit me-2"></i>
                                Transfer Bilgileri
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                                <div class="col-md-3 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectpickerLiveSearch" class="form-label">Gönderici Bayi  Seçiniz</label>

                                        @if($request->type == 'phone')
                                            <div class="col-md-9">
                                                    <?php
                                                    $role = \Illuminate\Support\Facades\Auth::user()->getRoleNames();
                                                    ?>
                                                <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                                        data-style="btn-default" name="main_seller_id" id="main_seller_id"
                                                        data-live-search="true"
                                                        @role(['Depo Sorumlusu','super-admin'])
                                                ""
                                                @else
                                                    disabled
                                                    @endrole
                                                    >

                                                    @foreach($sellers as $seller)
                                                        <option value="{{$seller->id}}"
                                                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('Depo Sorumlusu'))
                                                                    @if($request->filled('serial_number') && $request->type == 'phone' )
                                                                        {{$seller_id = \App\Models\Phone::where('barcode',$request->serial_number)->first()}}
                                                                        @if($seller_id && $seller->id == $seller_id->seller_id) selected  @else
                                                                    @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif
                                                                @endif
                                                                @endif
                                                                @else
                                                                    @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif
                                                                @endif
                                                                data-value="{{$seller->id}}">{{$seller->name}}</option>
                                                        @endforeach
                                                        </select>
                                            </div>
                                        @else
                                        <div class="col-md-9">
                                            <?php
                                            $role = \Illuminate\Support\Facades\Auth::user()->getRoleNames();
                                            ?>
                                            <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                                    data-style="btn-default" name="main_seller_id" id="main_seller_id"
                                                    data-live-search="true"
                                                    @role(['Depo Sorumlusu','super-admin'])
                                            ""
                                            @else
                                                disabled
                                                @endrole
                                            >

                                                @foreach($sellers as $seller)
                                                        <option value="{{$seller->id}}"
                                                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('Depo Sorumlusu'))
                                                        @if($request->filled('serial_number') && $request->type == 'other' )
                                                            {{$seller_id = \App\Models\StockCardMovement::where('serial_number',$request->serial_number)->first()}}
                                                            @if($seller_id && $seller->id == $seller_id->seller_id) selected  @else
                                                                    @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif
                                                                @endif
                                                        @endif
                                                    @else
                                                        @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif
                                                    @endif
                                                     data-value="{{$seller->id}}">{{$seller->name}}</option>
                                                 @endforeach
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectpickerLiveSearch" class="form-label">Alıcı Bayi
                                            Seçiniz</label>
                                        <div class="col-md-9">
                                            <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                                    data-style="btn-default" name="delivery_seller_id" id="delivery_seller_id"
                                                    data-live-search="true">
                                                @foreach($sellers as $seller)
                                                    <option value="{{$seller->id}}"
                                                            @if(isset($transfers) && $seller->id == $transfers->delivery_seller_id) selected
                                                            @endif data-value="{{$seller->id}}">{{$seller->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-2">
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="h4 text-capitalize mb-0 text-nowrap">Sevk NO #</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control"
                                                       @if(isset($transfers)) value="{{$transfers->number}}"
                                                       @else value="{{rand(111,999999989)}}" @endif name="number"
                                                       id="invoiceId">
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <hr class="mx-n4">
                            <div class="mb-3 row" id="serialBox">
                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bx bx-barcode me-2"></i>
                                        Seri Numaraları
                                    </label>
                                    <div class="serial-list-container">
                                        @if(isset($transfers))
                                            @foreach($transfers->serial_list as $item)
                                                <div id="{{$item}}" class="input-group mt-2 col-md-3">
                                                    <input type="text" class="form-control" name="sevkList[]"
                                                           id="basic-default-password12" value="{{$item}}">
                                                    <span id="basic-default-password2" class="input-group-text cursor-pointer"
                                                          onclick="deleteBox('{{$item}}')"><i class="bx bx-trash"></i></span>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row w-100 m-0 p-3">
                                                <input class="form-control" id="serial" autocomplete="off" name="serial" 
                                                       placeholder="Seri numarası girin ve Enter tuşuna basın...">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="row" id="serialList">

                                    @if($request->filled('serial_number'))
                                    <div id="{{$request->serial_number}}" class="input-group mt-2">
                                        <input type="text" class="form-control serialNewList" name="sevkList[]" id="basic-default-password12" value="{{$request->serial_number}}">
                                        <span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="deleteBox({{$request->serial_number}})"><i class="bx bx-trash"></i></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="note" class="form-label fw-semibold">
                                            <i class="bx bx-note me-2"></i>
                                            Not:
                                        </label>
                                        <textarea class="form-control" name="description" rows="3" id="note" 
                                                  placeholder="Transfer hakkında notlarınızı buraya yazabilirsiniz...">@if(isset($transfers))
                                                {{ $transfers->description}}
                                            @endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-12 invoice-actions">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-cog me-2"></i>
                                İşlemler
                            </h6>
                        </div>
                        <div class="card-body">
                            <button onclick="save()" type="button" class="btn btn-primary d-grid w-100 mb-3">
                                <span class="d-flex align-items-center justify-content-center text-nowrap">
                                    <i class="bx bx-paper-plane bx-xs me-1"></i>
                                    Transfer Kaydet
                                </span>
                            </button>
                            <a href="{{route('transfer.index')}}" class="btn btn-secondary d-grid w-100">
                                <span class="d-flex align-items-center justify-content-center text-nowrap">
                                    <i class="bx bx-arrow-back bx-xs me-1"></i>
                                    Geri Dön
                                </span>
                            </a>
                        </div>
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
        /* Modern Transfer Form Stilleri */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        /* Card Stilleri */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
            border: none;
        }
        
        .card-body {
            padding: 2rem;
        }

        /* Form Stilleri */
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            transform: translateY(-2px);
        }
        
        .form-control:hover {
            border-color: #667eea;
            transform: translateY(-1px);
        }

        /* Select Stilleri */
        .selectpicker {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .selectpicker:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Button Stilleri */
        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.6);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
        }

        /* Input Group Stilleri */
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .input-group-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 0 10px 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
        }
        
        .input-group-text:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: scale(1.05);
        }
        
        .input-group .form-control {
            border-radius: 10px 0 0 10px;
        }

        /* Serial List Stilleri */
        .serial-list-container {
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1rem 0;
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }
        
        .serial-list-container:hover {
            border-color: #667eea;
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .serial-item {
            background: white;
            border-radius: 10px;
            padding: 0.5rem;
            margin: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: inline-block;
            min-width: 200px;
        }
        
        .serial-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        /* Textarea Stilleri */
        textarea.form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            resize: vertical;
            min-height: 100px;
        }
        
        textarea.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Invoice Actions Stilleri */
        .invoice-actions .card {
            background: linear-gradient(145deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        
        .invoice-actions .card-body {
            padding: 1.5rem;
        }
        
        .invoice-actions .btn {
            width: 100%;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .invoice-actions .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        /* Header Stilleri */
        .invoice-add .row {
            margin-bottom: 2rem;
        }
        
        .invoice-add .col-md-3,
        .invoice-add .col-md-6 {
            margin-bottom: 1rem;
        }

        /* Loading Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            opacity: 1;
            transition: all 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lds-dual-ring {
            display: inline-block;
            width: 80px;
            height: 80px;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 8px;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .input-group {
                width: 100%;
            }
            
            .serial-item {
                min-width: 150px;
            }
        }

        /* Animation Effects */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
@endsection

@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
    <script>
        /*     $("#serial").keydown(function (e) {
                 console.log(e.key)
               //  if (e.key === 'Control' && e.key == 'v') {
     console.log("test")
                     $("#serialList").append('<div id="' + $(this).val() + '" class="input-group mt-2">' +
                         '<input type="text" class="form-control" name="sevkList[]" id="basic-default-password12" value="' + $(this).val() + '">' +
                         '<span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="deleteBox(\'' + $(this).val() + '\')"><i class="bx bx-trash"></i></span>' +
                         '</div>');
             //    }

                // $(this).val('');
             });*/
        $("#serial").keyup(function (e) {
            if ($(this).val() != "" && $(this).val().length > 6) {

                var newVal = $(this).val();
                var selectpickerLiveSearch = $("#selectpickerLiveSearch").find(":selected").val();

                var Arr = [];
                $('.serialNewList').each(function () {
                    Arr.push($(this).val());
                });
                console.log(Arr);
                var totalSerial = Arr.filter(x => x == newVal).length;
                if (totalSerial > 0) {
                    Swal.fire("Aynı Seri numarası sevk edilemez");
                    $(this).val('');
                    return false;
                } else {
                   getTransferSerial(newVal,selectpickerLiveSearch);

                    $("#serialList").append('<div id="' + newVal + '" class="input-group mt-2 col-md-2 slide-in">' +
                        '<input type="text" class="form-control serialNewList" name="sevkList[]" id="basic-default-password12" value="' + newVal + '">' +
                        '<span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="deleteBox(\'' + newVal + '\')"><i class="bx bx-trash"></i></span>' +
                        '</div>');
                    $(this).val('');
                }
            }

        });

        function deleteBox(value) {
            $("#" + value).fadeOut(300, function() {
                $(this).remove();
            });
        }
    </script>
    <script>
        function getTransferSerial(serial,selectpickerLiveSearch) {
            var postUrl = window.location.origin + '/getTransferSerialCheck?serial_number=' + serial + '&seller_id='+selectpickerLiveSearch+'';   // Returns base URL (https://example.com)
            $.ajax({
                type: "GET",
                url: postUrl,
                encode: true,
            }).done(function (data) {
                if(data != 'Yes')
                {
                    Swal.fire("Seri numarası transfer edilemez.Bulunamamakta veya başka bayiye ait.");
                    deleteBox(serial);
                    return false;
                }
            });
        }

        function getCustomer(id) {
            var postUrl = window.location.origin + '/custom_customerget?id=' + id + '';   // Returns base URL (https://example.com)
            $.ajax({
                type: "POST",
                url: postUrl,
                encode: true,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function (data) {
                $(".customerinformation").html('<p className="mb-1">' + data.address + '</p><p className="mb-1">' + data.phone1 + '</p><p className="mb-1">' + data.email + '</p>');
            });
        }

        function save() {
            var postUrl = window.location.origin + '/transfer/store';   // Returns base URL (https://example.com)
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
                    $(".swal2-confirm").click(function () {
                        window.location.href = "{{route('transfer.index')}}";
                    })
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
        }
    </script>
@endsection

