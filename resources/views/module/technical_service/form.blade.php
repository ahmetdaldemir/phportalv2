@extends('layouts.admin')

@section('content')
    <div id="technical-service-app" class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span
                class="text-muted fw-light">Teknik Servis Formu /</span> @if(isset($technical_services))
                {{$technical_services->name}}
            @endif</h4>
        <form action="javascript():;" id="technicalForm" method="post" class="form-repeater source-item ">
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
                                    <select 
                                        v-model="form.customer_id" 
                                        @change="onCustomerChange"
                                        class="form-select" 
                                        name="customer_id">
                                        <option value="1">Genel Cari</option>
                                        <option 
                                            v-for="customer in customers" 
                                            :key="customer.id"
                                            :value="customer.id">
                                            @{{ customer.fullname }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-primary" tabindex="0" data-bs-toggle="modal"
                                            data-bs-target="#editUser" type="button"><span><i
                                                class="bx bx-plus me-md-1"></i></span></button>
                                </div>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Fiziksel Durumu</label>
                                <div id="physical_condition" class="form-text">
                                    <select class="form-select select2" name="physically_category[]" multiple>
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "physically")
                                                <option value="{{$item->id}}"> {{$item->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <textarea class="form-control" id="physical_condition" name="physical_condition"
                                          aria-describedby="physical_condition">@if(isset($technical_services))
                                        {{$technical_services->physical_condition}}
                                    @endif </textarea>

                            </div>
                            <hr>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Aksesuar</label>

                                <div id="accessories" class="form-text">
                                    <select class="form-select select2" name="accessory_category[]" multiple>
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "accessory")
                                                <option value="{{$item->id}}"> {{$item->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <textarea class="form-control" id="accessories" name="accessories"
                                          aria-describedby="accessories">@if(isset($technical_services))
                                        {{$technical_services->accessories}}
                                    @endif</textarea>

                            </div>
                            <hr>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Arıza Açıklaması</label>
                                <div id="fault_information" class="form-text">
                                    <select class="form-select select2" name="fault_category[]" multiple>
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "fault")
                                                <option  value="{{$item->id}}"> {{$item->name}}</option>
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

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Şube Adı</label>
                                <select id="seller_id" name="seller_id" class="select2 form-select"
                                        @role('super-admin')
                                ""
                                @else
                                    disabled
                                    @endrole  >
                                    @foreach($sellers as $seller)
                                        <option  @if(\Illuminate\Support\Facades\Auth::user()->seller_id == $seller->id) selected  @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Marka</label>
                                <select id="brand_id" name="brand_id" class="select2 form-select"
                                        onchange="getVersion(this.value)" required>
                                    <option>Seçiniz</option>
                                    @foreach($brands as $value)
                                        <option
                                            @if(isset($technical_services) && $technical_services->brand_id == $value->id) selected
                                            @endif  value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Model</label>
                                <select id="version_id" name="version_id" class="select2 form-select" required></select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Durum</label>
                                <select class="form-control" id="status" name="status">
                                    @foreach(\App\Models\TechnicalService::STATUS as $key=>$value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- div>
                                <label for="defaultFormControlInput" class="form-label">Müşteri Fiyatı</label>
                                <input type="text" class="form-control" id="customer_price" value="0"
                                       name="customer_price" aria-describedby="customer_price" readonly>
                            </div -->

                            <!-- div>
                                <label for="defaultFormControlInput" class="form-label">Cihaz Şifresi</label>
                                <input type="text" class="form-control" id="device_password"
                                       @if(isset($technical_services)) value="{{$technical_services->device_password}}"
                                       @endif aria-describedby="device_password">
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Teslim Alan Personel</label>
                                <select id="brand_id" name="delivery_staff" class="select2 form-select">
                                    @foreach($users as $user)
                                    @if($user->is_status == 1)
                                        <option @if(isset($technical_services) && $technical_services->brand_id == $user->id) selected
                                            @endif  value="{{$user->id}}">{{$user->name}}</option>
                                            @endif
                                    @endforeach
                                </select>
                            </div -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-bg-secondary">
                <div class="card-header">
                    <button type="submit" onclick="save()" id="saveButton"  class="btn btn-danger btn-buy-now">Kaydet</button>
                </div>
            </div>
        </form>
        <hr class="my-5">
    </div>
@endsection
@include('components.customermodal')
@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>


    <script>
        function save() {
            $("#saveButton").prop( "disabled", true );

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
                    window.location.href = window.location.origin + '/technical_service/detail?id=' + data + '';
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");
                    $(placeholder).append(xhr.statusText + xhr.responseText);
                    $(placeholder).removeClass('loading');
                },
                complete: function () {
                    window.location.href = window.location.origin + '/technical_service/detail?id=' + data + '';
                },

            });
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
                    if (data == "") {
                        Swal.fire("Stok Bulunmamaktadır");
                        $("#serial").val("");
                        $("#base_cost_price").val("");
                        $("#sale_price").val("");
                        $("#quantity").val("");
                    } else {
                        $("#serial").val(data.serialNumber);
                        $("#base_cost_price").val(data.baseCostPrice);
                        $("#sale_price").val(data.salePrice);
                        $("#quantity").val(1).attr("max", data.quantity);
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
        }

    </script>

    <!-- Vue.js App for Technical Service Form -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Vue === 'undefined') {
            console.error('Vue.js is not loaded.');
            return;
        }

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    form: {
                        customer_id: '1'
                    },
                    customers: @json($customers ?? []),
                    globalStore: window.globalStore || { cache: { brands: [], colors: [], versions: [], customers: [] } }
                }
            },
            computed: {
                brands() {
                    return this.globalStore.cache.brands.length > 0 
                        ? this.globalStore.cache.brands 
                        : @json($brands ?? []);
                }
            },
            methods: {
                onCustomerChange() {
                    console.log('Customer changed:', this.form.customer_id);
                }
            },
            mounted() {
                // Listen for customer save events
                window.addEventListener('customerSaved', (event) => {
                    const customer = event.detail;
                    if (customer && customer.id) {
                        // Check if customer already exists
                        const exists = this.customers.find(c => c.id === customer.id);
                        if (!exists) {
                            this.customers.push(customer);
                        }
                        this.form.customer_id = customer.id;
                        
                        console.log('New customer selected:', customer);
                    }
                });
            }
        }).mount('#technical-service-app');
    });
    </script>
@endsection
