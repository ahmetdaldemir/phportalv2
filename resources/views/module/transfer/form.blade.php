@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form id="invoiceForm" method="post" class="form-repeater source-item py-sm-3" autocomplete="off">
            <input type="hidden" name="id" @if(isset($transfers)) value="{{$transfers->id}}" @endif />
            @if($request->filled('serial_number'))
                <input type="hidden" name="type" value="{{$request->type}}" />
            @else
                <input type="hidden" name="type" value="other"/>
            @endif
            <div class="row invoice-add">
                <div class="col-lg-10 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
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
                                        <input class="form-control" id="serial" autocomplete="off" name="serial">
                                    </div>
                                @endif
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
                                        <label for="note" class="form-label fw-semibold">Not:</label>
                                        <textarea class="form-control" name="description" rows="2" id="note"> @if(isset($transfers))
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
                        <div class="card-body">
                            <button onclick="save()" type="button" class="btn btn-primary d-grid w-100 mb-3">
                            <span class="d-flex align-items-center justify-content-center text-nowrap"><i
                                    class="bx bx-paper-plane bx-xs me-1"></i>Kaydet</span>
                            </button>
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
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 16.66666666%;
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

                    $("#serialList").append('<div id="' + newVal + '" class="input-group mt-2 col-md-2">' +
                        '<input type="text" class="form-control serialNewList" name="sevkList[]" id="basic-default-password12" value="' + newVal + '">' +
                        '<span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="deleteBox(\'' + newVal + '\')"><i class="bx bx-trash"></i></span>' +
                        '</div>');
                    $(this).val('');
                }
            }

        });

        function deleteBox(value) {
            $("#" + value).remove();
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

