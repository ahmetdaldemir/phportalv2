@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form action="{{route('phone.salestore')}}" method="post">
            @csrf
            <input name="company_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->company_id}}">
            <input name="user_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
            <input name="phone_id" type="hidden" value="{{$phone->id}}">
            <input name="sales_price" type="hidden" value="{{$phone->sale_price}}">
            <div class="row">
                <div class="col-md-6 mb-md-0 mb-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-4">
                                <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
                                <div class="col-md-9">
                                    <select id="selectCustomer" class="w-100 select2" data-style="btn-default"
                                            name="customer_id" ng-init="getCustomers()">
                                        <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                        <option ng-repeat="customer in customers"
                                                @if(isset($invoices) && '@{{customer.id}}' == $invoices->customer_id) selected
                                                @endif data-value="@{{customer.id}}" value="@{{customer.id}}">
                                            @{{customer.fullname}}
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
                                <label for="defaultFormControlInput" class="form-label">Imei</label>
                                <span>{{$phone->imei}}</span>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Bayi</label>
                                <span>{{$phone->seller->name}}</span>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Marka</label>
                                <span>{{$phone->brand->name}} / {{$phone->version->name}}</span>
                            </div>

                            <div>
                                <label for="defaultFormControlInput" class="form-label">Renk</label>
                                <span>{{$phone->color->name}}</span>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Satış Fiyatı</label>
                                <span data-sales="{{$phone->sales_price}}" class="invoice-item-sales-price">{{$phone->sale_price}} ₺</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 mx-n4">
                <div class="col-md-6 mb-md-0 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <label for="salesperson" class="form-label me-5 fw-semibold">Personel:</label>
                        <select id="selectpickerLiveSearch" class="selectpicker w-100" data-style="btn-default" name="sales_person" data-live-search="true" required>
                            <option value="">Seçiniz</option>
                            @foreach($users as $user)
                                @if($user->id != 1)
                                <option @if(isset($invoices))  {{ $invoices->hasStaff($user->id) ? 'selected' : '' }}
                                        @endif value="{{$user->id}}"  data-value="{{$user->id}}">{{$user->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Kredi Kartı</label>
                                    <input type="number" class="form-control" id="credit_card" value="0" name="payment_type[credit_card]">
                                </div>
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Nakit</label>
                                    <input type="number" class="form-control" id="cash" value="0" name="payment_type[cash]">

                                </div>
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Taksit</label>
                                    <input type="number" class="form-control" id="installment" value="0" name="payment_type[installment]">
                                </div>
                                <input type="hidden" class="form-control" id="discount_total" name="discount_total" value="0" required>

                                <!--div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">indirim</label>
                                    <input type="number" class="form-control" id="discount_total" name="discount_total" value="0" required>
                                </div-->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="pt-4">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">Kaydet</button>
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
    @if($errors->any())
        <script>
            Swal.fire('Satış fiyatından düşük satılamaz');
        </script>
    @endif
    <script>
        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('input[id=TagifyBasic]');
        var input1 = document.querySelector('input[id=TagifyBasic1]');

        // initialize Tagify on the above input node reference
        new Tagify(input);
        new Tagify(input1);

    </script>

    <script>
        $("#discount_total").change(function () {
            var salesprice = $(".invoice-item-sales-price").data('sales');
            var discount = $(this).val();
            var newSalesPrice = salesprice - ((discount * salesprice) / 100);
            if(newSalesPrice < salesprice)
            {
                Swal.fire('Destekli Satış Fiyatı altına satılamaz');
            }
        })
    </script>

    <!-- Vue.js App for Phone Sale -->
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
                    customers: @json($customers ?? []),
                    globalStore: window.globalStore || { cache: { brands: [], colors: [], versions: [], customers: [] } }
                }
            },
            methods: {
                updateSelectOptions() {
                    // Update select2 if needed
                    if (jQuery && jQuery.fn.select2) {
                        setTimeout(() => {
                            jQuery('#selectCustomer').trigger('change');
                        }, 100);
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
                        
                        // Update select option
                        setTimeout(() => {
                            const selectCustomer = document.getElementById('selectCustomer');
                            if (selectCustomer) {
                                selectCustomer.value = customer.id;
                                jQuery('#selectCustomer').trigger('change');
                            }
                        }, 100);
                        
                        console.log('New customer selected:', customer);
                    }
                });
            }
        }).mount('body');
    });
    </script>

@endsection
