@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form action="{{route('phone.store')}}" method="post">
            @csrf
            <input name="company_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->company_id}}">
            <input name="user_id" type="hidden" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
            <input name="id" type="hidden" value="{{$phone->id}}">
            <div class="row">
                <div class="col-md-6 mb-md-0 mb-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-4">
                                <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
                                <div class="col-md-9">
                                    <select id="selectCustomer" class="w-100 select2" data-style="btn-default" name="customer_id" ng-init="getCustomers()">
                                        <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                        <option ng-repeat="customer in customers" @if(isset($invoices) && '@{{customer.id}}' == $invoices->customer_id) selected @endif data-value="@{{customer.id}}" value="@{{customer.id}}">
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
                                <label for="defaultFormControlInput" class="form-label">Bayi</label>
                                <select name="seller_id" id="seller_id" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($sellers as $seller)
                                        <option  @if($seller->id == $phone->seller_id) selected @endif value="{{$seller->id}}">{{$seller->name}}</option>
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
                                <input type="text" class="form-control" id="imei" name="imei" maxlength="15" value="{{$phone->imei}}" required>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Tipi</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach(\App\Models\Phone::TYPE as $key => $item)
                                        <option @if($key == $phone->type) selected @endif value="{{$key}}">{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Adet</label>
                                <input type="text" class="form-control" id="quantity" name="quantity" value="{{$phone->quantity}}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Marka</label>
                                <select name="brand_id" id="brand_id" onchange="getVersion(this.value)" ng-init="getVersion({{$phone->brand_id}})" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($brands as $brand)
                                        <option @if($brand->id == $phone->brand_id) selected @endif value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Model</label>
                                <select name="version_id" id="version_id" class="form-control select2"  data-version="{{$phone->version_id}}" required></select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Renk</label>
                                <select name="color_id" id="color_id" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($colors as $color)
                                        <option @if($color->id == $phone->color_id) selected @endif  value="{{$color->id}}">{{$color->name}}</option>
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
                                <label for="defaultFormControlInput" class="form-label">Barkod</label>
                                <input type="text" class="form-control" id="barcode" name="barcode" value="{{$phone->barcode}}" required>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Alış Fiyatı</label>
                                <input type="text" class="form-control" id="cost_price" name="cost_price" value="{{$phone->cost_price}}" required>

                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Satış Fiyatı</label>
                                <input type="text" class="form-control" id="sale_price" name="sale_price" value="{{$phone->sale_price}}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Hafıza</label>
                                    <input type="text" class="form-control" id="memory" name="memory" value="{{$phone->memory}}" required>

                                </div>
                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Pil Durumu</label>
                                    <input type="text" class="form-control" id="batery" name="batery"  value="{{$phone->batery}}" required>
                                </div>


                                <div class="col-md-3">
                                    <label for="defaultFormControlInput" class="form-label">Garanti Süresi</label>
                                    <input type="date" class="form-control" id="warranty" value="{{$phone->warranty}}"  name="warranty">
                                </div>
                                <div class="col-md-2">
                                    <label for="defaultFormControlInput" class="form-label">Garantisiz Mi ?</label>
                                    <input type="checkbox" class="form-check-input" id="is_warranty"  @if(is_null($phone->warranty)) checked value="1"  @else  value="0" @endif name="is_warranty">
                                </div>


                                <div class="col-md-12">
                                    <label for="defaultFormControlInput" class="form-label">Fiziksel durum</label>
                                    <textarea class="form-control" id="physical_condition" name="physical_condition">{{$phone->physical_condition}}</textarea>
                                </div>

                                <div class="col-md-12">
                                    <label for="defaultFormControlInput" class="form-label">Değişmiş Parçalar</label>
                                    <textarea class="form-control" id="physical_condition" name="altered_parts">{{$phone->altered_parts}}</textarea>
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
    <script>
        var input = document.querySelector('input[id=TagifyBasic]');
        var input1 = document.querySelector('input[id=TagifyBasic1]');

        // initialize Tagify on the above input node reference
        new Tagify(input);
        new Tagify(input1);

    </script>

    <!-- Vue.js App for Phone Edit -->
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
    <script>
        $('select#type').on('change', function() {

            var value = $(this).val();
            if(value == 'old')
            {
                if ($('#is_warranty').is(':checked')) {
                    $('#warranty').prop('disabled','disabled');
                    $('#warranty').removeAttr('required');
                }else{
                    $('#warranty').prop('disabled','');
                    $('#warranty').attr('required','required');
                }
            }else{
                $('#is_warranty').removeAttr('checked');
            }

        });
    </script>
    <script>
        $("#is_warranty").click(function () {
            var value =  $( "#select#type option:selected").val();

            if(value == 'old')
            {
                if ($('#is_warranty').is(':checked')) {
                    $('#warranty').prop('disabled','disabled');
                    $('#warranty').removeAttr('required');
                }else{
                    $('#warranty').prop('disabled','');
                    $('#warranty').attr('required','required');
                }
            }else{
                $('#warranty').prop('disabled','');
                $('#warranty').removeAttr('required');
            }

        });
    </script>
@endsection
