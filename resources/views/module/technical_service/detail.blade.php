@extends('layouts.admin')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span
                class="text-muted fw-light">Teknik Servis Formu /</span> @if(isset($technical_services))
                {{$technical_services->name}}
            @endif</h4>
        <form action="javascript():;" id="technicalForm" method="post">
            @csrf
            <input type="hidden" name="id" @if(isset($technical_services)) value="{{$technical_services->id}}" @endif />
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <h5 class="card-header">Cihaz Bilgileri</h5>
                        <div class="card-body">
                            <div class="row mb-4">
                                <label for="selectpickerLiveSearch" class="form-label">Müşteri Seçiniz</label>
                                <div class="col-md-9">
                                    <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                            data-style="btn-default" name="customer_id"
                                            onchange="getCustomer(this.value)" id="customer_id"
                                            data-live-search="true">
                                        <option value="1" @if($technical_services->customer_id == 1) selected
                                                @endif data-tokens="ketchup mustard">Genel Müşteri
                                        </option>
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}"
                                                    @if(isset($technical_services) && $customer->id == $technical_services->customer_id) selected
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
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Fiziksel Durumu</label>
                                <div id="physical_condition" class="form-text">
                                    <select class="form-select select2" name="physically_category[]" multiple>
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "physically")
                                                <option value="{{$item->id}}"
                                                        @if(!is_null($technical_services->physically_category) and in_array($item->id,$technical_services->physically_category)) selected @endif> {{$item->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <textarea class="form-control" id="physical_condition" name="physical_condition"
                                          aria-describedby="physical_condition">@if(isset($technical_services)){{$technical_services->physical_condition}}@endif </textarea>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Aksesuar</label>

                                <div id="accessories" class="form-text">
                                    <select class="form-select select2" name="accessory_category[]" multiple>
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "accessory")
                                                <option value="{{$item->id}}"
                                                        @if(!is_null($technical_services->accessory_category) and in_array($item->id,$technical_services->accessory_category)) selected @endif> {{$item->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <textarea class="form-control" id="accessories" name="accessories"
                                          aria-describedby="accessories">@if(isset($technical_services)){{$technical_services->accessories}}@endif</textarea>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Arıza Açıklaması</label>
                                <div id="fault_information" class="form-text">
                                    <select class="form-select select2" name="fault_category[]" multiple>
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "fault")
                                                <option value="{{$item->id}}"
                                                        @if(!is_null($technical_services->fault_category) and in_array($item->id,$technical_services->fault_category)) selected @endif> {{$item->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <textarea class="form-control" id="fault_information" name="fault_information"
                                          aria-describedby="fault_information">@if(isset($technical_services))
                                        {{$technical_services->fault_information}}
                                    @endif</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <h5 class="card-header">Özellikler</h5>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Şube Adı</label>
                                    <select id="seller_id" name="seller_id" class="select2 form-select"
                                            @if(\Illuminate\Support\Facades\Auth::user()->getRoleNames() != 'super-admin') disabled @endif>
                                        @foreach($sellers as $seller)
                                            <option
                                                @if(isset($technical_services) && $technical_services->seller_id == $seller->id) selected
                                                @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Marka</label>
                                    <select id="brand_id" name="brand_id" class="select2 form-select"
                                            onchange="getVersion(this.value)" required>
                                        <option>Seçiniz</option>
                                        @foreach($brands as   $value)
                                            <option
                                                @if(isset($technical_services) && $technical_services->brand_id == $value->id) selected
                                                @endif  value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Model</label>
                                    <select id="version_id" name="version_id" class="select2 form-select" required>
                                        <option>Seçiniz</option>
                                        @foreach($versions as $value)
                                            <option
                                                @if(isset($technical_services) && $technical_services->version_id == $value->id) selected
                                                @endif  value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Durum</label>
                                    <select class="form-control" id="status" name="status">
                                        @foreach(\App\Models\TechnicalService::STATUS as $key=>$value)
                                            <option @if($technical_services->status == $key) selected
                                                    @endif value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Toplam Tutar</label>
                                    <input type="text" class="form-control" id="total_price"
                                           @if(isset($technical_services)) value="{{$technical_services->sumPrice()}}"
                                           @endif  name="total_price" aria-describedby="total_price" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Müşteri Fiyatı</label>
                                    <input type="text" class="form-control" id="customer_price"
                                           @if(isset($technical_services)) value="{{$technical_services->customer_price}}"
                                           @endif  name="customer_price" aria-describedby="customer_price">
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Cihaz Şifresi</label>
                                    <input type="text" class="form-control" id="device_password" name="device_password"
                                           @if(isset($technical_services)) value="{{$technical_services->device_password}}"
                                           @endif aria-describedby="device_password">
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Teslim Alan Personel</label>
                                    <select id="delivery_staff" name="delivery_staff" class="select2 form-select"
                                            required>
                                        <option value="">Seçiniz</option>

                                        @foreach($users as $user)
                                            @if($user->is_status == 1)
                                                <option
                                                    @if(isset($technical_services) && $technical_services->delivery_staff == $user->id) selected
                                                    @endif  value="{{$user->id}}">{{$user->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">IMEI</label>
                                    <input type="text" class="form-control" id="imei" minlength="15" maxlength="15"
                                           required name="imei"
                                           @if(isset($technical_services) && $technical_services->imei != 0) value="{{$technical_services->imei}}"
                                           @endif   aria-describedby="customer_price">
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Teknik Personel - {{$technical_services->technical_person}} </label>
                                    <select id="technical_person" name="technical_person" class="select2 form-select" disabled>
                                        <option value="">Seçiniz</option>
                                        @foreach($users as $user)
                                            @if($user->is_status == 1)
                                                <option @if(isset($technical_services) && $technical_services->technical_person == $user->id) selected @endif  value="{{$user->id}}">{{$user->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    @if($technical_services->payment_status == 0)
                        <div class="card card-bg-secondary">
                            <div class="card-header">
                                <button type="button" onclick="save()" class="btn btn-danger btn-buy-now">Kaydet
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>


        </form>
        <hr class="my-1">

        <div class="row">
            <form method="post" id="detailForm"
                  @if($technical_services->payment_status == 0) action="{{route('technical_service.detailstore')}}" @endif>
                @csrf
                <input type="hidden" name="id" value="{{$technical_services->id}}">
                <input type="hidden" name="stock_card_movement_id" id="stock_card_movement_id">
                <div class="mb-3">
                    <div class="pt-0 pt-md-4">
                        <div class="d-flex border rounded position-relative pe-0">
                            <div class="row w-100 m-0 p-3">
                                <div class="col-md-4 col-12 mb-md-0 mb-3 ps-md-0">
                                    <p class="mb-2 ">Stok</p>
                                    <!-- onchange="stockCardId(this.value)" -->
                                    <select name="stock_card_id" id="stock_card_id"
                                            class="form-select item-details mb-2" disabled>
                                        <option>Seçiniz</option>
                                        @foreach($stocks as $stock)
                                            <option value="{{$stock->id}}">{{$stock->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                    <p class="mb-2 ">Seri No</p>
                                    <input type="text" class="form-control" name="serial" id="serial"
                                           placeholder="11111111" required/>
                                </div>
                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                    <p class="mb-2 ">Satış Fiyatı</p>
                                    <input type="text" class="form-control invoice-item-price" name="sale_price"
                                           id="sale_price"/>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 ">Adet</p>
                                    <input type="number" class="form-control invoice-item-qty" name="quantity"
                                           id="quantity" min="1" max="50">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @if($technical_services->payment_status == 0)
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Ürün Ekle</button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
        <hr class="my-5">
        <div class="card">
            <div class="card-header">ÜRÜNLER</div>
            <div class="card-body">
                <table class="table table-responsive">
                    <tr>
                        <td>Ürün Adı - FORM NO</td>
                        <td>Seri No</td>
                        <td>Fiyat</td>
                        <td>İşlemler</td>
                    </tr>
                    @foreach($technical_service_products as $technical_service_product)
                        @if($technical_service_product->stock_card)
                            <tr>
                                <td>{{$technical_service_product->stock_card->name}} -
                                    PHTEC{{$technical_service_product->technical_service_id}} </td>
                                <td>{{$technical_service_product->serial_number}}</td>
                                <td>{{$technical_service_product->sale_price}}</td>
                                <td>
                                    @if($technical_services->payment_status == 0)
                                        <a href="{{route('technical_service.detaildelete',['id' => $technical_service_product->id,'technical_service_id' => $technical_service_product->technical_service_id])}}">Sil</a>
                                </td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
        <hr/>
        <div class="card">
            <div class="card-body">
                <table class="table table-responsive">
                    <tr>
                        <td>İşlem</td>
                        <td>Personel</td>
                        <td>Tarih</td>
                    </tr>
                    @foreach($technical_service_process as $item)
                        <tr>
                            <td>{{\App\Models\TechnicalService::STATUS[$item->status]}} Olarak Değiştirildi</td>
                            <td>{{\App\Models\User::find($item->user_id)->name}}</td>
                            <td>{{$item->created_at}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

    </div>
@endsection
@include('components.customermodal')
@section('custom-js')

    <script>
        function save() {


            if ($("#delivery_staff option:selected").val() == '') {
                Swal.fire('Teslim Alan Personel Alanı boş olamaz');
                return false;
            } else {
                if ($('#imei').val().length < 15) {
                    Swal.fire('IMEI Alanı boş olamaz');
                    return false;
                } else {
                    var postUrl = window.location.origin + '/technical_service/store';   // Returns base URL (https://example.com)
                    $.ajax({
                        type: "POST",
                        url: postUrl,
                        data: $("#technicalForm").serialize(),
                        dataType: "json",
                        encode: true,
                        beforeSend: function () {
                            $('#loader').removeClass('display-none')
                        },
                        success: function (data) {
                            Swal.fire("Güncellendi");
                        },
                        error: function (xhr) { // if error occured
                            alert("Error occured.please try again");
                            $(placeholder).append(xhr.statusText + xhr.responseText);
                            $(placeholder).removeClass('loading');
                        },


                    });
                }
            }
        }


        function stockCardId(value) {
            var postUrl = window.location.origin + '/getStockCard?id=' + value + '';   // Returns base URL (https://example.com)
            $.ajax({
                type: "GET",
                url: postUrl,
                beforeSend: function () {
                    $('#loader').removeClass('display-none')
                },
                success: function (data) {
                    if (data.status == false) {
                        Swal.fire(data.message);
                        return false;
                    } else {
                        $("#sale_price").val(data.sales_price.sale_price);
                        $("#serial").val(data.sales_price.serial_number);
                        $("#quantity").val(1);
                        $("#detailForm").find('select#stock_card_id').val(data.sales_price.stock_card_id).trigger('change');
                        return false;
                    }
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $(placeholder).append(xhr.statusText + xhr.responseText);
                    $(placeholder).removeClass('loading');
                },
                complete: function (data) {

                },
            });
            return false;
        }

    </script>


    <script>
        $("#detailForm").on('change', '#serial', function (e) {
            e.preventDefault();
            var postUrl = window.location.origin + '/serialcheck?id=' + $(this).val() + '';   // Returns base URL (https://example.com)
            $.ajax({
                type: "GET",
                url: postUrl,
                beforeSend: function () {
                    $('#loader').removeClass('display-none')
                },
                success: function (data) {
                    if (data.status == false) {
                        Swal.fire(data.message);
                        return false;
                    } else {
                        $("#detailForm").find('input#stock_card_movement_id').val(data.sales_price.id);
                        $("#sale_price").val(data.sales_price.sale_price);
                        $("#quantity").val(1);
                        $("#detailForm").find('select#stock_card_id').val(data.sales_price.stock_card_id).trigger('change');
                        e.stopPropagation();
                        return false;
                    }
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $(placeholder).append(xhr.statusText + xhr.responseText);
                    $(placeholder).removeClass('loading');
                },
                complete: function (data) {

                },
            });
            e.stopPropagation();
            return false;
        })
    </script>
@endsection
