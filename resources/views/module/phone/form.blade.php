@extends('layouts.admin')

@section('content')
    <div id="phone-form-app" class="container-xxl flex-grow-1 container-p-y">
        <form action="{{route('phone.store')}}" id="PhoneinsertForm" method="post">
            @csrf
            <input name="company_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->company_id}}">
            <input name="user_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
            <input type="hidden" name="quantity" value="1">

            <div class="row">
                <div class="col-md-6 mb-md-0 mb-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-4">
                                <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
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
                                            data-bs-target="#editUser" type="button">
                                        <span><i class="bx bx-plus me-md-1"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Bayi</label>
                                <select name="seller_id" id="seller_id" class="form-control"
                                        @role(['Depo Sorumlusu','super-admin'])
                                        ""
                                        @else
                                        disabled
                                        @endrole

                                    required>
                                    <option value="">Seçiniz</option>
                                    @foreach($sellers as $seller)
                                        <option
                                            @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected
                                            @endif value="{{$seller->id}}">{{$seller->name}}</option>
                                        @endforeach
                                        </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Imei</label>
                                <input type="text" class="form-control" id="imei" name="imei" maxlength="15" required>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Tipi</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach(\App\Models\Phone::TYPE as $key => $item)
                                        <option value="{{$key}}">{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Marka</label>
                                <select name="brand_id" id="brand_id" onchange="getVersion(this.value)"
                                        class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Model</label>
                                <select name="version_id" id="version_id" class="form-control select2"
                                        required></select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Renk</label>
                                <select name="color_id" id="color_id" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($colors as $color)
                                        <option value="{{$color->id}}">{{$color->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Alış Fiyatı</label>
                                <input type="text" class="form-control" id="cost_price" name="cost_price" required>

                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Satış Fiyatı</label>
                                <input type="text" class="form-control" id="sale_price" name="sale_price" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row" style="    font-size: 10px;">
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Hafıza</label>
                                    <input type="number" class="form-control" id="memory" name="memory" value=""
                                           required>
                                </div>
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Pil Durumu</label>
                                    <input type="number" class="form-control" id="batery" name="batery" value="0"
                                           required>
                                </div>
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Garanti Süresi</label>
                                    <input type="date" class="form-control" id="warranty" value="{{date('d.m.Y')}}"
                                           name="warranty">
                                </div>
                                <div class="col-md-2">
                                    <label for="defaultFormControlInput" class="form-label">Garantisiz Mi ?</label>
                                    <input type="checkbox" class="form-check-input" id="is_warranty" value="0" name="is_warranty">
                                </div>
                                <div class="col-md-12">
                                    <label for="defaultFormControlInput" class="form-label">Fiziksel durum</label>
                                    <textarea class="form-control" id="physical_condition"
                                              name="physical_condition"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label for="defaultFormControlInput" class="form-label">Değişmiş Parçalar</label>
                                    <textarea class="form-control" id="physical_condition"
                                              name="altered_parts"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Açıklama</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                            <div class="pt-4">
                                <button type="submit" id="btnSubmit" class="btn btn-primary me-sm-3 me-1">Kaydet</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@include('components.customermodal')

@section('custom-js')
    <script>
        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('input[id=TagifyBasic]');
        var input1 = document.querySelector('input[id=TagifyBasic1]');

        // initialize Tagify on the above input node reference
        new Tagify(input);
        new Tagify(input1);

    </script>

    <!-- Vue.js App for Phone Form -->
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
                },
                colors() {
                    return this.globalStore.cache.colors.length > 0 
                        ? this.globalStore.cache.colors 
                        : @json($colors ?? []);
                }
            },
            methods: {
                onCustomerChange() {
                    console.log('Customer changed:', this.form.customer_id);
                },
                
                handleTypeChange() {
                    const typeSelect = document.getElementById('type');
                    const value = typeSelect.value;
                    const warrantyInput = document.getElementById('warranty');
                    const isWarrantyCheckbox = document.getElementById('is_warranty');
                    
                    if(value == 'old') {
                        isWarrantyCheckbox.removeAttribute('disabled');
                        
                        if (isWarrantyCheckbox.checked) {
                            warrantyInput.setAttribute('disabled', 'disabled');
                            warrantyInput.removeAttribute('required');
                            warrantyInput.value = '';
                        } else {
                            warrantyInput.removeAttribute('disabled');
                            warrantyInput.setAttribute('required', 'required');
                        }
                    } else {
                        isWarrantyCheckbox.checked = false;
                        isWarrantyCheckbox.setAttribute('disabled', 'disabled');
                        warrantyInput.setAttribute('disabled', 'disabled');
                    }
                },
                
                handleWarrantyChange() {
                    const typeSelect = document.getElementById('type');
                    const value = typeSelect.value;
                    const warrantyInput = document.getElementById('warranty');
                    const isWarrantyCheckbox = document.getElementById('is_warranty');
                    
                    if(value == 'old') {
                        if (isWarrantyCheckbox.checked) {
                            warrantyInput.setAttribute('disabled', 'disabled');
                            warrantyInput.removeAttribute('required');
                        } else {
                            warrantyInput.removeAttribute('disabled');
                            warrantyInput.setAttribute('required', 'required');
                        }
                    } else {
                        warrantyInput.removeAttribute('disabled');
                        warrantyInput.removeAttribute('required');
                    }
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
                
                // Setup event listeners for warranty logic
                const typeSelect = document.getElementById('type');
                const isWarrantyCheckbox = document.getElementById('is_warranty');
                
                if (typeSelect) {
                    typeSelect.addEventListener('change', this.handleTypeChange);
                }
                
                if (isWarrantyCheckbox) {
                    isWarrantyCheckbox.addEventListener('click', this.handleWarrantyChange);
                }
            }
        }).mount('#phone-form-app');
    });
    </script>
@endsection
