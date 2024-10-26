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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kartları /</span> Stok Kart listesi
            <a  href="{{route('stockcard.deleted')}}" type="button" formtarget="_blank" style="margin-left: 7px;" class="btn btn-success float-end ml-4 ml-3">Silinen Seri Numaraları </a>
            <button id="barcode" type="button" formtarget="_blank"
                    onclick="document.getElementById('itemFrom').submit();" style="margin-left: 7px;"
                    disabled="disabled" class="btn btn-danger float-end ml-3">Barkod Yazdır
            </button>

            @role(['Depo Sorumlusu','super-admin'])
            <a href="{{route('stockcard.create',['category'=>$category])}}" class="btn btn-primary float-end">Yeni Stok
                Kartı Ekle</a>
            <button id="multiplepriceUpdate" type="button" style="margin-right: 7px;"
                    class="btn btn-danger float-end ml-3">Fiyat Güncelle
            </button>

            @endrole
        </h4>

        <div class="card">
            <div class="card-body">
                <form action="{{route('stockcard.serialList')}}" id="stockSearch" method="get">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-confirm-password">Seri Numarası</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" name="serialNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>

            </div>
             <form id="itemFrom" role="form" method="POST" action="{{route('stockcard.barcodes')}}">
                @csrf
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr style="    font-size: .45rem;">
                            <th><input name="all_selected" type="checkbox"></th>
                            <th></th>
                            <th>S. No</th>
                            @role(['Depo Sorumlusu','super-admin'])
                            <th>M.</th>
                            <th>D. M.</th>
                            @endrole
                            <th>Satış F.</th>
                            <th>Renk</th>
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Kategori</th>
                            <th>Şube</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stockcards as $stockData)
                            @if($stockData->quantity > 0)
                                <tr
                                    @if($stockData->quantity == 0) style="background: #99c9e1;    font-size: 10px;"
                                    @elseif($stockData->type == 3) style="background: #f00;    font-size: 10px;"
                                    @elseif($stockData->type == 2) style="background: #acb9ff;    font-size: 10px;"
                                    @endif data-type="{{$stockData->type}}" data-quantity="{{$stockData->quantity}}">
                                    <td style="font-size: 10px;" class="text-center">
                                        @if($stockData->type == 1)
                                        <input type="checkbox" name="selected[]" id="{{$stockData->quantity}}_c"
                                                                   value="{{ $stockData->id}} "
                                                                   class="form-check-input {{$stockData->id}}_c">
                                        <input type="hidden"
                                               name="barcode[{{ $stockData->id }}][]"
                                               value="{{ $stockData->id }}">
                                        @endif
                                    </td>
                                    <td style="font-size: 10px;">{{$stockData->id}}</td>
                                    <td style="font-size: 10px;">
                                        {{$stockData->serial_number}}
                                        <a href="{{route('invoice.stockcardmovementform',['id' => $stockData->invoice_id])}}"> {{$stockData->invoice_id}}</a>
                                    </td>
                                    @role(['Depo Sorumlusu','super-admin'])
                                    <td style="font-size: 10px;"><strong>{{$stockData->cost_price}} ₺</strong></td>
                                    <td style="font-size: 10px;"><strong>{{$stockData->base_cost_price}} ₺</strong></td>
                                    @endrole
                                    <td style="font-size: 10px;"><strong>{{$stockData->sale_price}} ₺</strong></td>
                                    <td style="font-size: 10px;">{{$stockData->color->name}}</td>
                                    <td style="font-size: 10px;">{{$stockData->stock->brand->name}}</td>
                                    <td style="font-size: 10px;">{!! $stockData->stock->version() !!}</td>
                                    <td style="font-size: 10px;">

                                        {{$stockData->categorySeperator($stockData->testParent($stockData->stock->category->id))}}
                                    </td>
                                    <td style="font-size: 10px;">{{$stockData->seller->name}}</td>
                                    <td style="font-size: 10px;">
                                        @if($stockData->type == 4)
                                            <span class="badge bg-primary">TRANSFER SÜRECİNDE</span>
                                        @elseif($stockData->type == 3)
                                            <span class="badge bg-primary">HASARLI ÜRÜN</span>
                                        @elseif($stockData->type == 2)
                                                <div style="width: 100%">ÜRÜN SATILDI</div>
                                                <div style="width: 100%">{{$stockData->sale->user->name}}</div>
                                                <div style="width: 100%">{{$stockData->sale->created_at}}</div>

                                        @elseif($stockData->type == 5)
                                            <span class="badge bg-primary">TEKNİK SERVİS SÜRECİNDE</span>
                                        @else
                                            <a title="Sevk Et"
                                               href="{{route('transfer.create',['serial_number' => $stockData->serial_number,'type'=>'other'])}}"
                                               class="btn btn-sm btn-icon btn-success">
                                                <span class="bx bx-transfer"></span>
                                            </a>
                                            @role('Depo Sorumlusu|super-admin')
                                            <button type="button"
                                                    onclick="priceModal({{$stockData->id}})"
                                                    class="btn btn-sm btn-icon btn-danger">
                                                <span class="bx bxs-dollar-circle"></span>
                                            </button>
                                            <a title="Sevk Et" ng-click="deleteMovement({{$stockData->id}})"
                                                class="btn btn-sm btn-icon btn-success">
                                                <span class="bx bx-trash"></span>
                                            </a>
                                            @endrole
                                            <button onclick="demandModal({{$stockData->id}},'{{$stockData->stock->name}}','{{$stockData->color_id}}')"
                                                type="button" class="btn btn-sm btn-danger">
                                                <i style="font-size: medium;" class="bx bx-radar"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </form>
        </div>
         <div class="card mt-4">
            <div class="card-body mt-4 p-4 box has-text-centered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                {!! $links !!}
            </div>
        </div>

        <hr class="my-5">
    </div>
    <div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="transferForm">
                @csrf
                <input id="stockCardId" name="stock_card_id" type="hidden">
                <input id="id" name="id" type="hidden">
                <input id="type" name="type" value="other" type="hidden">
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
                            <input type="text" id="serialBackdrop" class="form-control" name="serial_number[]"/>
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
                                <option value="4">SATIŞ</option>
                                <option value="5">SIFIR</option>
                                <option value="6">İKİNCİ El SATIŞ</option>
                                <option value="7">SATIŞ İADE</option>
                                <option value="8">HASARLI İADE</option>
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
                <input id="stockCardMovementId" name="stock_card_id" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
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
    <div class="modal fade" id="demandModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content" style="padding: 1px">
                <div class="modal-header">Ürün Adı : <span></span></div>
                <form action="{{route('demand.store')}}" method="post">
                    <input type="hidden" name="id" id="id" value="">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Renk</label>
                                <select class="form-select" id="color" name="color_id">
                                    @foreach($colors as $color)
                                        <option value="{{$color->id}}">{{$color->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="nameSmall" class="form-label">Açıklama</label>
                                <input type="text" id="nameSmall" name="description" class="form-control"
                                       placeholder="Açıklama">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('stockcard.movementdelete')}}" id="deleteModalForm">
                @csrf
                <input id="stockCardMovementIdDelete" name="stock_card_movement_id" type="hidden">
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

    <div class="modal fade" id="multiplepriceModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="multiplepriceForm">
                @csrf
                <input id="stockCardMovementIdArray" name="stock_card_id_multiple" type="hidden">
                <div class="modal-header">
                    <h5 class="modal-title" id="backDropModalTitle">Fiyat Değişiklik İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Maliyet</label>
                            <input type="text" id="cost_price" class="form-control" name="cost_price"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Destekli Maliyet</label>
                            <input type="text" id="base_cost_price" class="form-control" name="base_cost_price"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBackdrop" class="form-label">Satış Fiyatı</label>
                            <input type="text" id="serialBackdrop" class="form-control" name="sale_price"/>
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
    <script type="text/javascript">
        var selected = [];
        $(document).ready(function () {
            $("#multiplepriceUpdate").click(function (e) {
                $("input:checkbox[name^='selected']:checked").each(function () {
                    selected.push($(this).val());
                });
                if(selected.length > 0)
                {
                    $("#multiplepriceModal").modal('show');
                    $("#multiplepriceModal").find("#stockCardMovementIdArray").val(selected);
                }else{
                    Swal.fire('Seçim Yapınız');
                }
            });
        });


        $("#multiplepriceForm").submit(function (e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var actionUrl = '{{route('stockcard.multiplepriceupdate')}}';
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
                        }, buttonsStyling: !1
                    });
                    $("#multiplepriceModal").modal('hide');
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
                    $("#multiplepriceModal").modal('hide');
                }
            });

        });
    </script>

    <script>
        $("input[name^='selected']").on('change', function () {

            var selected = $("input[type^='checkbox']:checked");
            if (selected.length) {
                $('#barcode').attr('disabled', false);
            } else {
                $('#barcode').attr('disabled', true);
            }
        });

        function priceModal(id) {
            $("#priceModal").modal('show');
            $("#priceModal #stockCardMovementId").val(id);
        }

        function openModal(id) {
            $("#backDropModal").modal('show');
            $("#serialBackdrop").val(id);
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
            var actionUrl = '{{route('stockcard.singlepriceupdate')}}';
            $.ajax({
                type: "POST",
                url: actionUrl + '?id=' + $("#stockCardMovementId").val() + '',
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
            $scope.getStockSearch = function () {
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/stockSearch';   // Returns base URL (https://example.com)
                $http({
                    method: 'POST',
                    url: postUrl,
                    data: $("#stockSearch").serialize(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.stockSearchLists = response.data;
                });
            }


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
            $scope.getStockCard = function () {
                $scope.loading = true; // Show loading image
                var postUrl = window.location.origin + '/getStockCardCategory?id=' + {{$category}} + '';   // Returns base URL (https://example.com)
                $http({
                    method: 'GET',
                    url: postUrl,
                    data: $("#stockSearch").serialize(),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).then(function successCallback(response) {
                    $scope.stockSearchLists = response.data;
                });
            }

        });
    </script>


    <script>
        function demandModal(id, name, color) {
            $("#demandModal").modal('show');
            $("#demandModal").find('.modal-header span').html(name);
            $("#demandModal").find('input#id').val(id);
            $("#demandModal").find('select#color').val(color).trigger('change');
            $("#demandModal").find('select#color').attr('data-color', color);
        }
    </script>


    <script>

        $('input[name=all_selected]').click(function(event) {
            var id = this.getAttribute('id')

            var checkboxes = document.getElementsByClassName(''+event.target.id+'_c');
            console.log(checkboxes.length);
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = event.checked;
            }

            if (this.checked) {
                 $('.'+event.target.id+'_c').each(function() {
                     $('.'+event.target.id+'_c').prop('checked', true);
                 });
               // $('#'+event.target.id+'_c').prop('checked', true);
            } else {
                 $('#'+event.target.id+'_c').prop('checked', false);
            }
        });

    </script>

@endsection

