@extends('layouts.admin')

@section('content')
    <div id="technical-service-covering-app" class="container-xxl flex-grow-1 container-p-y" onload="getTownLoad(34)">
        <h4 class="fw-bold py-3 mb-4"><span
                class="text-muted fw-light">Teknik Servis Formu /</span> @if(isset($technical_services))
                {{$technical_services->name}}
            @endif</h4>
        <form action="{{route('technical_service.coveringstore')}}"  method="post" id="coveringStoreForm" class="source-item ">
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
                                    <select id="selectCustomer" class="w-100 select2"
                                            data-style="btn-default" name="customer_id">
                                        <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-primary" tabindex="0" data-bs-toggle="modal" data-bs-target="#editUser" type="button"><span><i class="bx bx-plus me-md-1"></i></span></button>
                                </div>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Hizmet Tipi</label>
                                <div id="physical_condition" class="form-text">
                                    <select class="form-select" name="type">
                                        <option>Kaplama</option>
                                        <option>Kılıf Baskı</option>
                                    </select>
                                 </div>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Kaplama Bilgisi</label>
                                <textarea class="form-control" id="coating_information" name="coating_information"
                                          aria-describedby="accessories">@if(isset($technical_services))
                                        {{$technical_services->coating_information}}
                                    @endif</textarea>
                             </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Baskı Bilgisi</label>
                                <textarea class="form-control" id="print_information" name="print_information" aria-describedby="print_information">@if(isset($technical_services))
                                        {{$technical_services->print_information}}
                                    @endif</textarea>
                             </div>
                            <hr class="my-4 mx-n4">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <h5 class="card-header">Özellikler</h5>
                        <div class="card-body">

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Şube Adı</label>
                                <select id="seller_id" name="seller_id" class="select2 form-select" readonly disabled>
                                    @foreach($sellers as $seller)
                                        <option  @if(\Illuminate\Support\Facades\Auth::user()->seller_id == $seller->id) selected @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="seller_id" value="{{\Illuminate\Support\Facades\Auth::user()->seller_id}}">

                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Marka</label>
                                <select id="brand_id" name="brand_id" class="select2 form-select"
                                        onchange="getVersion(this.value)" required>
                                    <option>Seçiniz</option>
                                    @foreach($brands as $value)
                                        <option
                                            @if(isset($technical_services) && $technical_services->brand_id == $key) selected
                                            @endif  value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Model</label>
                                <select id="version_id" name="version_id" class="select2 form-select" required></select>
                            </div>


                            <div>
                                <label for="defaultFormControlInput" class="form-label">Teslim Alan Personel</label>
                                <select id="brand_id" name="delivery_staff" class="select2 form-select" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($users as $user)
                                        @if($user->is_status == 1)
                                        <option  @if(isset($technical_services) && $technical_services->brand_id == $user->id) selected
                                            @endif  value="{{$user->id}}">{{$user->name}}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-bg-secondary">
                <div class="card-header">
                    <button type="submit" id="saveButton" class="btn btn-danger btn-buy-now">Kaydet</button>
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
        $("#coveringStoreForm").submit(function () {
            $("#saveButton").prop( "disabled", true );
        })

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

    <!-- Vue.js App for Technical Service Covering -->
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
                    // No data needed for this simple app
                }
            },
            methods: {
                addCustomerToSelect(customer) {
                    // Add new customer option to select
                    const selectCustomer = document.getElementById('selectCustomer');
                    if (selectCustomer) {
                        // Check if option already exists
                        const existingOption = selectCustomer.querySelector(`option[value="${customer.id}"]`);
                        if (!existingOption) {
                            const newOption = document.createElement('option');
                            newOption.value = customer.id;
                            newOption.textContent = customer.fullname;
                            selectCustomer.appendChild(newOption);
                        }
                        
                        // Select the new customer
                        selectCustomer.value = customer.id;
                        
                        // Trigger select2 update if available
                        if (jQuery && jQuery.fn.select2) {
                            jQuery('#selectCustomer').trigger('change');
                        }
                        
                        console.log('New customer added and selected:', customer);
                    }
                }
            },
            mounted() {
                // Listen for customer save events
                window.addEventListener('customerSaved', (event) => {
                    const customer = event.detail;
                    if (customer && customer.id) {
                        this.addCustomerToSelect(customer);
                    }
                });
            }
        }).mount('#technical-service-covering-app');
    });

    // Global function for getting versions by brand
    function getVersion(brandId) {
        if (!brandId || brandId === '') {
            // Clear version select if no brand selected
            const versionSelect = document.getElementById('version_id');
            if (versionSelect) {
                versionSelect.innerHTML = '<option value="">Seçiniz</option>';
            }
            return;
        }

        // Show loading state
        const versionSelect = document.getElementById('version_id');
        if (versionSelect) {
            versionSelect.innerHTML = '<option value="">Yükleniyor...</option>';
            versionSelect.disabled = true;
        }

        // Make AJAX request to get versions using existing API
        fetch(`/api/common/versions?brand_id=${brandId}`)
            .then(response => response.json())
            .then(data => {
                if (versionSelect) {
                    versionSelect.innerHTML = '<option value="">Seçiniz</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(version => {
                            const option = document.createElement('option');
                            option.value = version.id;
                            option.textContent = version.name;
                            
                            // Check if this version should be selected (for edit mode)
                            @if(isset($technical_services) && $technical_services->version_id)
                                if (version.id == {{$technical_services->version_id}}) {
                                    option.selected = true;
                                }
                            @endif
                            
                            versionSelect.appendChild(option);
                        });
                    }
                    
                    versionSelect.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error loading versions:', error);
                if (versionSelect) {
                    versionSelect.innerHTML = '<option value="">Hata oluştu</option>';
                    versionSelect.disabled = false;
                }
            });
    }

    // Load versions on page load if brand is already selected
    document.addEventListener('DOMContentLoaded', function() {
        const brandSelect = document.getElementById('brand_id');
        if (brandSelect && brandSelect.value) {
            getVersion(brandSelect.value);
        }
    });
    </script>
@endsection
