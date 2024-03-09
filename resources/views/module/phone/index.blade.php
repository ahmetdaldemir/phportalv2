@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Telefonlar /</span> Telefon listesi</h4>

        <div class="card">

            <div class="card-header">

                @role(['Depo Sorumlusu','super-admin','Bayi Yetkilisi'])
                <div class="btn-group demo-inline-spacing float-end">
                    <a href="{{route('phone.create')}}" class="btn btn-primary float-end">Yeni Telefon Ekle</a>
                </div>
                @endrole

            </div>
            <div class="card-body">
                <form action="{{route('phone.list')}}" id="stockSearch" method="get">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label" for="multicol-email">Marka</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="brand" class="form-select" onchange="getVersion(this.value)"
                                        style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                                <label class="form-label" for="multicol-password">Renk</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="color" class="form-select" style="width: 100%">
                                        <option value="">Tümü</option>
                                        @foreach($colors as $color)
                                            <option value="{{$color->id}}">{{$color->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Durumu</label>
                                <div class="input-group input-group-merge">
                                    <select name="status" class="form-select" style="width: 100%">
                                        <option value="">Tümü</option>
                                        <option value="1">Satıldı</option>
                                        <option value="0">Beklemede</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Tipi</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="type" class="form-select" style="width: 100%">
                                        <option value="">Tümü</option>
                                        <option value="old">İkinci El</option>
                                        <option value="new">Sıfır</option>
                                        <option value="assigned_device">Temnikli</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Şube</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="seller" class="form-select" style="width: 100%">
                                        <option value="all">Tümü</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{$seller->id}}"
                                                    @if(\Illuminate\Support\Facades\Auth::user()->seller_id == $seller->id) selected @endif >{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-confirm-password">BArkod</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" name="barcode" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-confirm-password">IMEI</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" name="imei" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>IMEI</th>
                        <th>Barkod</th>
                        <th>Marka</th>
                        <th>Model</th>
                        <th>Tipi</th>
                        <th>Hafıza</th>
                        <th>Renk</th>
                        <th>Pil</th>
                        <th>Garanti</th>
                        <th>Bayi</th>
                        <th>Alış F</th>
                        <th>Satış F</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" style="    font-size: 13px;">
                    @foreach($phones as $phone)
                        <tr>
                            <td>{{$phone->imei}}</td>
                            <td>{{$phone->barcode}}</td>
                            <td>{{$phone->brand->name}}</td>
                            <td>{{$phone->version->name??'Buluanamadı'}}</td>
                            <td>{{\App\Models\Phone::TYPE[$phone->type]}}</td>
                            <td>{{$phone->memory}} GB</td>
                            <td>{{$phone->color->name}}</td>
                            <td>@if($phone->batery == 0)
                                    Bilinmiyor
                                @else
                                    % {{$phone->batery}}
                                @endif</td>
                            <td><span style='color:#f00'>
                                    <?php if ($phone->warranty == null){ ?> Garantisiz <?php }elseif($phone->warranty == '2'){ ?>

                                    {{\App\Models\Phone::WARRANTY[$phone->warranty]}}
                                    <?php } else { ?>
                                        <?php
                                        if ($phone->warranty == null) {
                                            echo "Garantisiz!";
                                        } else if ($phone->warranty == 1) {
                                            echo "Garantili";
                                        } else {
                                            echo \Carbon\Carbon::parse($phone->warranty)->format('d-m-Y');
                                        }
                                    }
                                        ?>
                                    </span>
                            </td>
                            <td>{{$phone->seller->name}}</td>
                            <td>
                                @role('Depo Sorumlusu|super-admin')
                                {{number_format($phone->cost_price,2)}} <b>₺</b>
                                @endrole
                            </td>
                            <td>{{number_format($phone->sale_price,2)}} <b>₺</b></td>
                            <td>
                                @if($phone->status == 2)
                                    <span class="badge bg-primary">Transfer Sürecinde</span>
                                @endif
                                @if($phone->status == 0 && $phone->is_confirm == 1)

                                    <a href="{{route('transfer.create',['serial_number' => $phone->barcode,'type'=> 'phone'])}}"
                                       class="btn btn-sm btn-success"><i class="bx bx-transfer"></i></a>
                                    <a href="{{route('phone.sale',['id' => $phone->id])}}"
                                       class="btn btn-sm btn-secondary"><i class="bx bx-shopping-bag"></i></a>


                                    @role('Depo Sorumlusu|super-admin')
                                    <a href="{{route('phone.edit',['id' => $phone->id])}}"
                                       class="btn btn-sm btn-dribbble"><i class="bx bx-edit"></i></a>
                                    @endrole
                                @endif
                                @if($phone->is_confirm == 0)
                                    @role(['Depo Sorumlusu','super-admin'])
                                    <a onclick="return confirm('Onaylamak istediğinizden eminmisiniz?')"
                                       href="{{route('phone.confirm',['id' => $phone->id])}}"
                                       class="btn btn-sm btn-success"><i class="bx bxl-ok-ru"></i></a>

                                    @endrole
                                @endif
                                @role(['super-admin'])

                                <a  ng-click="deleteMovement({{$phone->id}})"
                                    class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></a>
                                @endrole
                                <a target="_blank" href="{{route('phone.barcode',['id' => $phone->id])}}"
                                   class="btn btn-sm btn-warning"><i class="bx bx-barcode"></i></a>
                                <a href="{{route('phone.show',['id' => $phone->id])}}"
                                   class="btn btn-sm btn-dark"><i
                                        class="bx bx-show"></i></a>
                                <a href="{{route('phone.printconfirm',['id' => $phone->id])}}"
                                   class="btn btn-sm btn-danger"><i class="bx bx-printer"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body mt-4 p-4 box has-text-centered"
                 style="padding-top: 0 !important; padding-bottom: 0 !important;">
                {!! $phones->links() !!}
            </div>
        </div>
        <hr class="my-5">
    </div>
    <div class="modal fade" id="deleteModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{route('phone.delete')}}" id="deleteModalForm">
                @csrf
                <input id="stockCardMovementIdDelete" name="id" type="hidden">
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
                <input id="type" name="type" type="hidden" value="phone">
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
@endsection


@section('custom-js')
    @if($errors->any())
        <script>
            Swal.fire('Satış yapılamaz');
        </script>
    @endif
    <script>
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
        }).directive('ngConfirmClick', [
            function () {
                return {
                    link: function (scope, element, attr) {
                        var msg = attr.ngConfirmClick || "Are you sure?";
                        var clickAction = attr.confirmedClick;
                        element.bind('click', function (event) {
                            if (window.confirm(msg)) {
                                scope.$eval(clickAction)
                            }
                        });
                    }
                };
            }]).controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {


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
