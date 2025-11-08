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
                                    <select id="selectCustomer" class="w-100 select2" data-style="btn-default" name="customer_id">
                                        <option value="1">Genel Cari</option>
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">
                                                {{$customer->fullname}}
                                            </option>
                                        @endforeach
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

    <!-- Customer Save Event Listener -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for customer save events from modal
        window.addEventListener('customerSaved', (event) => {
            const customer = event.detail;
            if (customer && customer.id) {
                const selectCustomer = document.getElementById('selectCustomer');
                if (selectCustomer) {
                    // Add new option if it doesn't exist
                    const existingOption = selectCustomer.querySelector(`option[value="${customer.id}"]`);
                    if (!existingOption) {
                        const newOption = new Option(customer.fullname, customer.id, true, true);
                        selectCustomer.add(newOption);
                    } else {
                        selectCustomer.value = customer.id;
                    }
                    
                    // Trigger select2 update if available
                    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                        jQuery(selectCustomer).trigger('change');
                    }
                    
                    console.log('Customer selected:', customer);
                }
            }
        });
    });
    </script>

@endsection
