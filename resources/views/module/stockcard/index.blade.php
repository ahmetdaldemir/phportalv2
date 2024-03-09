@extends('layouts.admin')

@section('content')
    <style>
        .collapse.in {
            display: block;
        }
        .hiddenRow {
            padding: 0 !important;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kartları /</span> Stok Kart listesi</h4>

        <div class="card">
            <div class="card-body">
                @include('components.searchactionstockcard')
            </div>
            <div class="card-header">
                <a href="{{route('stockcard.create')}}" class="btn btn-primary float-end">Yeni Stok Kartı Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table" style="font-size: 13px;">
                    <thead>
                    <tr>
                        <th>Stok Adı</th>
                        <th>Kategori</th>
                     </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                    @foreach($stockcards as $stockcard)
                         <tr data-toggle="collapse" data-target="#l{{$stockcard['id']}}" class="accordion-toggle">
                            <td><i class="bx bx-down-arrow"></i> </td>
                            <td>{{$stockcard['category_sperator_name']}}{{$stockcard['category']}}</td>
                         </tr>
                         <tr>
                             <td colspan="6" class="hiddenRow">
                                 <div class="accordian-body collapse" id="l{{$stockcard['id']}}">
                                     <table class="table table-bordered table-hover">
                                         <thead>
                                         <tr class="info">
                                             <th style="width: 20%">Ürün Adı</th>
                                             <th style="width: 5%">Adet</th>
                                             <th style="width: 30%">Kategori</th>
                                             <th style="width: 5%">Marka</th>
                                             <th style="width: 10%">Model</th>
                                             <th style="width: 5%">Alış F</th>
                                             <th style="width: 5%">Destek. F.</th>
                                             <th style="width: 5%">Satış F.</th>
                                             <th  style="width: 2%;display: none" >Status</th>
                                             <th style="width: 20%">Actions</th>
                                         </tr>
                                         </thead>
                                         <tbody>
                                         @foreach($stockcard['stockData'] as $stockData)
                                             <tr class="info">
                                                 <td><strong>{{$stockData['name']}}</strong></td>

                                                 <td><strong>{{$stockData['quantity']}}</strong></td>
                                                 <td><strong>{{$stockData['category_sperator_name']}}{{$stockData['category'] ?? "Belirtilmedi"}}</strong></td>
                                                 <td><strong>{{$stockData['brand']}}</strong></td>

                                                 <td> <?php
                                                          foreach ($stockData['version'] as $mykey => $myValue) {
                                                              echo "$myValue</br>";
                                                          }
                                                          ?></td>
                                                 <td>
                                                     @role('Depo Sorumlusu|super-admin')
                                                     {{number_format($stockData['cost_price'],2)}}
                                                     @endrole
                                                 </td>
                                                 <td>
                                                     @role('Depo Sorumlusu|super-admin')
                                                     {{number_format($stockData['base_cost_price'],2)}}
                                                     @endrole
                                                 </td>
                                                 <td>{{number_format($stockData['sale_price'],2)}}</td>
                                                 <td  style="display: none" >
                                                     <div class="form-check form-switch mb-2">
                                                         <input class="form-check-input" type="checkbox"
                                                                onclick="updateStatus('stockcard/update',{{$stockData['id']}},{{$stockData['is_status'] == 1 ? 0:1}})"
                                                                id="flexSwitchCheckChecked" {{$stockData['is_status'] == 1 ? 'checked':''}} />
                                                     </div>
                                                 </td>
                                                 <td>

                                                     @role(['Depo Sorumlusu','super-admin'])
                                                     <a href="{{route('invoice.create',['id' => $stockData['id']])}}" title="Fatura Ekle"  class="btn btn-icon btn-danger">
                                                         <span class="bx bx-list-plus"></span>
                                                     </a>
                                                     <a title="Düzenle" href="{{route('stockcard.edit',['id' => $stockData['id']])}}"
                                                        class="btn btn-icon btn-primary">
                                                         <span class="bx bx-edit-alt"></span>
                                                     </a>

                                                     <button type="button" onclick="priceModal({{$stockData['id']}})"
                                                             class="btn btn-icon btn-success">
                                                         <span class="bx bxs-dollar-circle"></span>
                                                     </button>
                                                     @endrole
                                                     @role(['super-admin'])
                                                     <a title="Sil"
                                                        ng-click="deleteMovement({{$stockData['id']}})"
                                                         class="btn btn-icon btn-danger">
                                                         <span class="bx bxs-trash"></span>
                                                     </a>
                                                     @endrole
                                                 </td>
                                             </tr>
                                         @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                             </td>
                         </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body mt-4 p-4 box has-text-centered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                {!! $links !!}
            </div>
        </div>
        <hr class="my-5">
    </div>
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('stockcard.delete')}}" id="deleteModalForm">
                @csrf
                <input id="stockCardMovementIdDelete" name="stock_card_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Silmek icin not girmelisiniz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Not</label>
                            <input type="text" id="note" class="form-control" name="note" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Sil</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="transferForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                <input id="id" name="id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Sevk İşlemi</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Serial Number</label>
                            <input
                                type="text"
                                id="serialBackdrop"
                                class="form-control"
                                placeholder="Seri Numarası"
                                name="serial_number"
                            />
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="sellerBackdrop" class="form-label">Şube</label>
                            <select class="form-control" name="seller_id" id="sellerBackdrop">
                                @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}">{{$seller->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="sellerBackdrop" class="form-label">Neden</label>
                            <select class="form-control" name="reason_id" id="sellerBackdrop">
                                @foreach($reasons as $reason)
                                    <option value="{{$reason->id}}">{{$reason->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Sevk İşlemi Başlat</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="priceModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="priceForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                 <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Kapat
                    </button>
                    <button type="submit" class="btn btn-primary">Fiyat Değiştir</button>
                </div>
            </form>
        </div>
    </div>
 @endsection

@section('custom-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        function priceModal(id) {
            $("#priceModal").modal('show');
            $("#priceModal #stockCardId").val(id);
        }
        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#stockCardId").val(id);
        }

        $("#transferForm").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var actionUrl = '{{route('stockcard.sevk')}}';
            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardId").val() + '',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#backDropModal").modal('hide');
                }
            });

        });
        $("#priceForm").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var actionUrl = '{{route('stockcard.priceupdate')}}';
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data, status) {
                    Swal.fire({
                        icon: status,
                        title: data,
                        customClass: {
                            confirmButton: "btn btn-success"
                        },
                        buttonsStyling: !1
                    });
                    $("#priceModal").modal('hide');
                    window.location.reload();
                },
                error: function (request, status, error) {
                    Swal.fire({
                        icon: status,
                        title: request.responseJSON,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        },
                        buttonsStyling: !1
                    });
                    $("#priceModal").modal('hide');
                }
            });

        });
    </script>

    <script>
        app.directive('loading', function () {
            return {
                restrict: 'E',
                replace: true,
                template: '<p><img src="img/loading.gif"/></p>', // Define a template where the image will be initially loaded while waiting for the ajax request to complete
                link: function (scope, element, attr) {
                    scope.$watch('loading', function (val) {
                        val = val ? $(element).show() : $(element).hide();  // Show or Hide the loading image
                    });
                }
            }
        }).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {


            $scope.deleteMovement = function (id) {
                Swal.fire({
                    title: "Silmek istediginizden eminmisiniz?",
                    text: "Silme islemi yapilirken kesinlikle not girmelisiniz!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "EVET!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#stockCardMovementIdDelete').val(id);
                        $('#deleteModal').modal('show');
                    }
                });
            }


        });
    </script>

@endsection
