@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Teknik Servis /</span> Teknik Servis listesi
        </h4>

        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link  {{$_technical}}" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">Teknik
                        Servis
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link  {{$_cover}}" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">
                        Kaplama Ve Baskı
                    </button>
                </li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane fade {{$_technical}} show" id="navs-top-home" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <form action="{{route('technical_service.index')}}" id="stockSearch" method="get">
                                <input type="hidden" name="tab_type" value="_technical">

                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label" for="multicol-username">Müşteri</label>
                                        <input type="text" class="form-control" placeholder="············"
                                               name="customer">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="multicol-email">Marka</label>
                                        <div class="input-group input-group-merge">
                                            <select type="text" name="brand" class="form-select"
                                                    onchange="getVersion(this.value)" style="width: 100%">
                                                <option value="">Tümü</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="multicol-password">Model</label>
                                            <div class="input-group input-group-merge">
                                                <select type="text" id="version_id" name="version" class="form-select"
                                                        style="width: 100%">
                                                    <option value="">Tümü</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="multicol-password">Bayi</label>
                                            <div class="input-group input-group-merge">
                                                <select type="text" name="seller" class="form-select"
                                                        style="width: 100%">
                                                    <option value="">Tümü</option>
                                                    @foreach($sellers as $seller)
                                                        <option value="{{$seller->id}}">{{$seller->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="multicol-password">İşlem Durumu</label>
                                            <div class="input-group input-group-merge">
                                                <select type="text" name="status" class="form-select"
                                                        style="width: 100%">
                                                    <option value="">Tümü</option>
                                                    @foreach(\App\Models\TechnicalService::STATUS as $key => $value)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Ara
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-header">
                            @role(['Satış Sorumlusu','super-admin','Bayi Yetkilisi'])
                            <a href="{{route('technical_service.create')}}" class="btn btn-primary float-end">Yeni
                                Teknik Servis Ekle</a>
                            @endrole
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table" style="font-size:11px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Şube Adı</th>
                                    <th>Müşteri</th>
                                    <th>Marka/Model</th>
                                    <th>Ödeme Durumu</th>
                                    <th>Tarih</th>
                                    <th>Personel</th>
                                    <!-- th>T.Per.</th>
                                    <th>Total</th -->
                                    <th>Fiyat</th>
                                    <th>Durum</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($technical_services as $technical_service)
                                    <tr @if($technical_service->payment_status == 1) style="background: #99c9e1;color:#fff" @endif>
                                        <td>
                                            <a href="{{route('technical_service.detail',['id' => $technical_service->id])}}">#{{$technical_service->id}}</a>
                                        </td>
                                        <td>
                                            <i class="fab fa-angular fa-lg text-danger me-3"></i><strong>{{$technical_service->seller->name??"bulunamadı"}}
                                        </td>
                                        <td>{{$technical_service->customer->fullname ?? "Silinmiş"}}</td>
                                        <td>{{$technical_service->brand->name??"Bulunamadı"}}
                                            / {{$technical_service->version->name??"Bulunamadı"}}</td>
                                        <td><span
                                                class="badge bg-label-primary me-1">{{$technical_service->payment_status == 1 ? "Ödeme Alındı":"Ödeme Beklemede" }}</span>
                                        </td>

                                        <td>{{\Carbon\Carbon::parse($technical_service->created_at)->format('d-m-Y')}}</td>
                                        <td>{{$technical_service->delivery->name??null}}</td>
                                        <!-- td>{{$technical_service->technical_person??null}}</td>
                                        <td>{{$technical_service->total_price}}</td -->
                                        <td>{{$technical_service->customer_price}}</td>
                                        <td>
                                            <select id="status" data-technicalservice_id="{{$technical_service->id}}"
                                                name="status" class="form-control"
                                                @if($technical_service->status >= 5) disabled @endif >
                                                @foreach(\App\Models\TechnicalService::STATUS as $key => $value)
                                                    <option value="{{$key}}"
                                                            @if($technical_service->status == $key) selected @endif>{{$key}}
                                                        - {{$value}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <a target="_blank"
                                               href="{{route('technical_service.print',['id' => $technical_service->id])}}"
                                               class="btn btn-icon btn-danger btn-sm">
                                                <span class="bx bxs-printer"></span>
                                            </a>
                                            @if($technical_service->payment_status == 0)
                                                <a class="btn btn-icon btn-success btn-sm" target="_blank"
                                                   href="{{route('technical_service.payment',['id' => $technical_service->id])}}">
                                                    <span class="bx bxs-dollar-circle"></span>
                                                </a>
                                            @endif
                                            <a
                                                class="btn btn-icon btn-warning btn-sm"
                                                data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                                data-bs-html="true"
                                                data-bs-original-title="Sms Gönder" onclick="smsModalOpen()">
                                                <span class="bx bxs-message-add"></span>
                                            </a>


                                            <a href="{{route('technical_service.detail',['id' => $technical_service->id])}}"
                                               class="btn btn-icon btn-primary btn-sm"
                                               data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                               data-bs-html="true"
                                               data-bs-original-title="Düzenle">
                                                <span class="bx bx-edit-alt"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="card mt-4">
                            <div class="card-body mt-4 p-4 box has-text-centered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                                {{$technical_services->links()}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade  {{$_cover}}" id="navs-top-profile" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <form action="{{route('technical_service.index')}}" id="stockSearch" method="get">
                                <input type="hidden" name="tab_type" value="_cover">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label" for="multicol-username">Müşteri</label>
                                        <input type="text" class="form-control" placeholder="············" name="cover_customer">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="multicol-email">Marka</label>
                                        <div class="input-group input-group-merge">
                                            <select type="text" name="cover_brand" class="form-select"
                                                    onchange="getVersion(this.value)" style="width: 100%">
                                                <option value="">Tümü</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="multicol-password">Model</label>
                                            <div class="input-group input-group-merge">
                                                <select type="text" id="version_id" name="cover_version" class="form-select"
                                                        style="width: 100%">
                                                    <option value="">Tümü</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="multicol-password">Bayi</label>
                                            <div class="input-group input-group-merge">
                                                <select type="text" name="cover_seller" class="form-select"
                                                        style="width: 100%">
                                                    <option value="">Tümü</option>
                                                    @foreach($sellers as $seller)
                                                        <option value="{{$seller->id}}">{{$seller->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button   type="submit"  class="btn btn-sm btn-outline-primary">Ara  </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-header">
                            @role(['Satış Sorumlusu','super-admin','Bayi Yetkilisi'])
                            <a href="{{route('technical_service.covering')}}" class="btn btn-danger ">Yeni Kaplama
                                Ekle</a>
                            @endrole
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table" style="font-size:11px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Şube Adı</th>
                                    <th>Müşteri</th>
                                    <th>Marka/Model</th>
                                    <th>Ödeme Durumu</th>
                                    <th>Tarih</th>
                                    <th>Personel</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($technical_covering_services as $technical_service)
                                    <tr>
                                        <td>
                                            #{{$technical_service->id}}
                                        </td>
                                        <td style="font-size: 12px;">
                                            <i class="fab fa-angular fa-lg text-danger me-3"></i><strong>{{$technical_service->seller->name??"bulunmadı"}}
                                        </td>
                                        <td style="font-size: 12px;">{{$technical_service->customer->fullname ?? "Silinmiş"}}</td>
                                        <td style="font-size: 12px;">{{$technical_service->brand->name??"bulunamadı"}}/ {{$technical_service->version->name??"bulunamadı"}}</span></td>
                                        <td style="font-size: 12px;">
                                            @if($technical_service->payment_status == 0)
                                                <span class="badge bg-danger">Ödeme Alınmadı</span>
                                            @else
                                                <span class="badge bg-success">Ödeme Alındı</span>
                                            @endif
                                        </td>
                                        <td style="font-size: 12px;">{{\Carbon\Carbon::parse($technical_service->created_at)->format('d-m-Y')}}</td>
                                        <td style="font-size: 12px;">{{$technical_service->delivery->name??"bulunamadı"}}</td>
                                        <td>
                                            <a target="_blank"
                                               href="{{route('technical_service.coverprint',['id' => $technical_service->id])}}"
                                               class="btn btn-icon btn-danger btn-sm">
                                                <span class="bx bxs-printer"></span>
                                            </a>
                                            <a
                                                class="btn btn-icon btn-warning btn-sm"
                                                data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                                data-bs-html="true"
                                                data-bs-original-title="Sms Gönder"
                                                onclick="smsCoverModalOpen({{$technical_service->id}})">
                                                <span class="bx bxs-message-add"></span>
                                            </a>

                                            <a href="{{route('technical_service.coveredit',['id' => $technical_service->id])}}"
                                               class="btn btn-icon btn-primary btn-sm"
                                               data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                               data-bs-html="true"
                                               data-bs-original-title="Düzenle">
                                                <span class="bx bx-edit-alt"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card mt-4">
                            <div class="card-body mt-4 p-4 box has-text-centered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                                {{$technical_covering_services->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection
@include('components.smsmodal')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #section-to-print, #section-to-print * {
                visibility: visible;
            }

            #section-to-print {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
@endsection

@section('custom-js')
    <script>
        function print(id) {

            var divName = 'barcodeFormSet' + id + '';
            var printContents = document.getElementById(divName).innerHTML;
            w = window.open();
            w.document.write(printContents);
            w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
            w.document.close();
            w.focus();
        }

        function getForm(id) {

        }
    </script>
    <script>
        function technicalServiceOpen() {
            $("#technicalServiceModal").modal('show');
        }
    </script>
    <script>
        function smsModalOpen() {
            $("#smsModal").modal('show');
        }

        function smsCoverModalOpen(id) {
            $("#smsCoverModal").modal('show');
            $("#smsCoverForm").find("input[name=id]").val(id);
        }

        function smsCoverSend() {
            alert("Kod Yazılacak");
        }

        function smsSend() {
            alert("Kod Yazılacak");
        }

    </script>
    <script>
        function checkoutModalOpen() {
            $("#checkoutModal").modal('show');
        }
    </script>
    <script>
        $("select#status").change(function () {

            if (!confirm("Durum Değişikliği yapmak istediğinizden eminmisiniz?")) {
                return;
            }

            var val = $(this).val();
            var technicalservice_id = $(this).data('technicalservice_id');
            var postUrl = window.location.origin + '/technical_service/statusCgange?id=' + technicalservice_id + '&val=' + val;   // Returns base URL (https://example.com)
            $.ajax({
                type: "POST",
                url: postUrl,
                success: function (data) {
                    Swal.fire(data);
                    window.location.reload();
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $(placeholder).append(xhr.statusText + xhr.responseText);
                    $(placeholder).removeClass('loading');
                },
                complete: function () {
                    //
                },

            });
        })
    </script>
    <script>
        app.controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {

        });
    </script>
@endsection
<!-- route('technical_service.print',['id' => $technical_service->id])  -->

