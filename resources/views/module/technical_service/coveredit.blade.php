@extends('layouts.admin')

@section('content')
    <div id="technicalServiceCoverApp" class="container-xxl flex-grow-1 container-p-y" onload="getTownLoad(34)">
        <h4 class="fw-bold py-3 mb-4"><span
                class="text-muted fw-light">Teknik Servis Formu /</span> @if(isset($technical_service_cover))
                {{$technical_service_cover->name}}
            @endif</h4>
        <form action="{{route('technical_service.coveringupdate')}}" method="post" class="source-item ">
            @csrf
            <input type="hidden" name="id"
                   @if(isset($technical_service_cover)) value="{{$technical_service_cover->id}}" @endif />
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <h5 class="card-header">Cihaz Bilgileri</h5>
                        <div class="card-body">
                            <div class="row mb-4">
                                <label for="selectpickerLiveSearch" class="form-label">Müşteri Seçiniz</label>
                                <div class="col-md-9">
                                    <select id="selectCustomer" class="w-100 select2" data-style="btn-default" name="customer_id">
                                        <option value="1" data-tokens="ketchup mustard">Genel Cari</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                @if(isset($technical_service_cover) && $technical_service_cover->customer_id == $customer->id) selected @endif>
                                                {{ $customer->fullname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-secondary btn-primary" tabindex="0" data-bs-toggle="modal"
                                            data-bs-target="#editUser" type="button"><span><i
                                                class="bx bx-plus me-md-1"></i></span></button>
                                </div>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Hizmet Tipi</label>
                                <div id="physical_condition" class="form-text">
                                    <select class="form-select" name="type">
                                        <option value="Kaplama"
                                                @if($technical_service_cover->type == "Kaplama") selected @endif>Kaplama
                                        </option>
                                        <option value="Kılıf Baskı"
                                                @if($technical_service_cover->type == "Kılıf Baskı") selected @endif>
                                            Kılıf Baskı
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Kaplama Bilgisi</label>
                                <textarea class="form-control" id="coating_information"
                                          name="coating_information">{{$technical_service_cover->coating_information}}</textarea>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Baskı Bilgisi</label>
                                <textarea class="form-control" id="print_information"
                                          name="print_information">{{$technical_service_cover->print_information}}</textarea>
                            </div>
                            <hr class="my-4 mx-n4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label" for="fullname">Kredi Kartı</label>
                                    <input type="number" name="payment_type[credit_card]" id="credit_card"  value="0"
                                           class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="fullname">Nakit</label>
                                    <input type="number" name="payment_type[cash]" id="money_order" class="form-control"  value="0"
                                           required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullname">.</label>
                                    <input type="number" name="payment_type[installment]" id="installment" value="0"
                                           class="form-control" style="display: none;">
                                </div>

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
                                            @if(isset($technical_service_cover) && $technical_service_cover->brand_id == $value->id) selected
                                            @endif  value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Model</label>
                                <select id="version_id" name="version_id" class="select2 form-select"
                                        @if(isset($technical_service_cover)) data-version="{{$technical_service_cover->version_id}}"
                                        @endif  required>
                                    @if(isset($technical_service_cover) && $technical_service_cover->version_id)
                                        <option value="{{$technical_service_cover->version_id}}" selected>
                                            {{$technical_service_cover->version->name ?? 'Yükleniyor...'}}
                                        </option>
                                    @else
                                        <option value="">Önce marka seçiniz</option>
                                    @endif
                                </select>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Müşteri Fiyat</label>
                                    <input type="text" class="form-control" id="customer_price" name="customer_price"
                                           value="{{$technical_service_cover->customer_price}}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="defaultFormControlInput" class="form-label">Toplam Fiyat</label>
                                    <input type="text" class="form-control" id="total_price" name="total_price"
                                           @if(isset($technical_service_cover)) value="{{$technical_service_cover->sumPrice()}}"
                                           @else value="0" @endif readonly>
                                </div>
                            </div>


                            <div>
                                <label for="defaultFormControlInput" class="form-label">Teslim Alan Personel</label>
                                <select id="brand_id" name="delivery_staff" class="select2 form-select">
                                    @foreach($users as $user)
                                        @if($user->is_status == 1)
                                        <option
                                            @if(isset($technical_service_cover) && $technical_service_cover->delivery_staff == $user->id) selected
                                            @endif  value="{{$user->id}}">{{$user->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($technical_service_cover->payment_status == 0)
            <div class="card card-bg-secondary">
                <div class="card-header">
                    <button type="submit" class="btn btn-danger btn-buy-now">Kaydet</button>
                </div>
            </div>
            @endif
        </form>
        <hr class="my-5">

        <div class="row">
            <form method="post" id="detailForm"
                  @if($technical_service_cover->payment_status == 0) action="{{route('technical_service.coveringdetailstore')}}" @endif>
                @csrf
                <input type="hidden" name="id" value="{{$technical_service_cover->id}}">
                <input type="hidden" name="stock_card_movement_id" id="stock_card_movement_id">
                <div class="mb-3">
                    <div class="pt-0 pt-md-4">
                        <div class="d-flex border rounded position-relative pe-0">
                            <div class="row w-100 m-0 p-3">
                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                    <p class="mb-2 ">Barkod <span class="text-danger">*</span></p>
                                    <input type="text" class="form-control" name="barcode" id="barcode"
                                           placeholder="Barkod okutun veya yazın" 
                                           autocomplete="off"
                                           @if($technical_service_cover->payment_status != 0) disabled @endif/>
                                    <small class="text-muted">
                                        <i class="bx bx-barcode me-1"></i>
                                        Barkod okutarak hızlı arama yapabilirsiniz
                                    </small>
                                </div>
                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                    <p class="mb-2 ">Stok</p>
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
                                           placeholder="Otomatik doldurulacak" readonly/>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                    <p class="mb-2 ">Satış Fiyatı</p>
                                    <input type="text" class="form-control invoice-item-price" name="sale_price"
                                           id="sale_price" readonly/>
                                </div>
                                <div class="col-md-2 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 ">Adet</p>
                                    <input type="number" class="form-control invoice-item-qty" name="quantity"
                                           id="quantity" min="1" max="50" value="1">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @if($technical_service_cover->payment_status == 0)
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Ürün Ekle</button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
        <hr class="my-5">
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
                            @if($technical_service_cover->payment_status == 0)
                                <a href="{{route('technical_service.coverdetaildelete',['id' => $technical_service_product->id,'technical_service_id' => $technical_service_product->technical_service_id])}}">Sil</a>
                        </td>
                        @endif
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
@endsection
@include('components.customermodal')
@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>

    <script>
        $("#customer_price").change(function () {
            var price = $(this).val();
            var Totalprice = $("#total_price").val();
            var discountPrice = Totalprice - (Totalprice*10)/100;
            if(price < discountPrice)
            {
                Swal.fire("Maximum indirim %10 Yapılabilir");
                $(this).val(discountPrice);
            }
        });
    </script>

    <script>

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

    <!-- Vue.js App for Technical Service Cover Edit -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Vue === 'undefined') {
            console.error('Vue.js is not loaded.');
            return;
        }

        const { createApp } = Vue;

        const app = createApp({
            data() {
                return {
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
                addCustomerToSelect(customer) {
                    const selectCustomer = document.getElementById('selectCustomer');
                    if (!selectCustomer) return;
                    
                    // Check if customer already exists in select
                    const existingOption = selectCustomer.querySelector(`option[value="${customer.id}"]`);
                    
                    if (!existingOption) {
                        // Create new option
                        const newOption = document.createElement('option');
                        newOption.value = customer.id;
                        newOption.textContent = customer.fullname;
                        selectCustomer.appendChild(newOption);
                    }
                    
                    // Select the customer
                    selectCustomer.value = customer.id;
                    
                    // Trigger select2 update if available
                    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                        jQuery(selectCustomer).trigger('change');
                    }
                }
            },
            mounted() {
                // Listen for customer save events
                window.addEventListener('customerSaved', (event) => {
                    const customer = event.detail;
                    if (customer && customer.id) {
                        this.addCustomerToSelect(customer);
                        console.log('New customer added and selected:', customer);
                    }
                });
            }
        }).mount('#technicalServiceCoverApp');
    });
    </script>

    <!-- Get Versions by Brand -->
    <script>
    function getVersion(brandId) {
        const versionSelect = document.getElementById('version_id');
        const selectedVersion = versionSelect.getAttribute('data-version');
        
        if (!brandId) {
            versionSelect.innerHTML = '<option value="">Önce marka seçiniz</option>';
            return;
        }
        
        // Show loading
        versionSelect.innerHTML = '<option value="">Yükleniyor...</option>';
        versionSelect.disabled = true;
        
        const url = `/api/common/versions?brand_id=${brandId}`;
        
        // Fetch versions
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(versions => {
                versionSelect.innerHTML = '<option value="">Model Seçiniz</option>';
                
                if (versions && versions.length > 0) {
                    versions.forEach(version => {
                        const option = document.createElement('option');
                        option.value = version.id;
                        option.textContent = version.name;
                        
                        // Select the previously selected version
                        if (selectedVersion && version.id == selectedVersion) {
                            option.selected = true;
                            console.log('✅ Seçili model bulundu ve işaretlendi:', version.name);
                        }
                        
                        versionSelect.appendChild(option);
                    });
                    
                    // Trigger select2 refresh if available
                    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                        jQuery('#version_id').trigger('change');
                    }
                    
                    console.log('✅ Modeller yüklendi, seçili model:', selectedVersion);
                } else {
                    versionSelect.innerHTML = '<option value="">Bu marka için model bulunamadı</option>';
                }
                
                versionSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading versions:', error);
                versionSelect.innerHTML = '<option value="">Model yüklenirken hata oluştu</option>';
                versionSelect.disabled = false;
            });
    }
    
    // Sayfa yüklendiğinde seçili marka için modelleri yükle
    $(document).ready(function() {
        const brandSelect = document.getElementById('brand_id');
        const versionSelect = document.getElementById('version_id');
        const selectedBrandId = brandSelect.value;
        const selectedVersionId = versionSelect.getAttribute('data-version');
        
        if (selectedBrandId && selectedBrandId !== '') {
            console.log('🔄 Sayfa yüklendi, seçili marka için modeller yükleniyor:', selectedBrandId);
            console.log('🎯 Seçili model ID:', selectedVersionId);
            
            // Eğer zaten bir model seçiliyse, onu koru
            if (selectedVersionId) {
                versionSelect.innerHTML = '<option value="' + selectedVersionId + '" selected>Yükleniyor...</option>';
            }
            
            getVersion(selectedBrandId);
        }
    });
    </script>

    @if (\Illuminate\Support\Facades\Session::has('msg'))
        <script>
            Swal.fire('{{ \Illuminate\Support\Facades\Session::get('msg') }}');
        </script>
    @endif

    <script>
        // Sayfa yüklendiğinde barkod alanına odaklan
        $(document).ready(function() {
            $('#barcode').focus();
        });
        
        // Enter tuşu ile barkod arama
        $(document).on('keypress', '#barcode', function (e) {
            if (e.which === 13) { // Enter tuşu
                e.preventDefault();
                $(this).trigger('change');
            }
        });
        
        // Barkod ile ürün arama - basit ve etkili
        $(document).on('change', '#barcode', function (e) {
            var barcode = $(this).val().trim();
            
            if (!barcode) {
                clearProductFields();
                return;
            }
            
            // Minimum 3 karakter gerekli
            if (barcode.length < 3) {
                return;
            }
            
            
            // serialcheck endpoint'ini kullan (zaten çalışıyor)
            var postUrl = window.location.origin + '/serialcheck?id=' + encodeURIComponent(barcode);
            
            $.ajax({
                type: "GET",
                url: postUrl,
                beforeSend: function () {
                    $("#serial").val("Aranıyor...");
                    $("#sale_price").val("Aranıyor...");
                },
                success: function (data) {
                    if (data.status == false) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ürün Bulunamadı',
                            text: data.message || 'Bu barkod ile eşleşen ürün bulunamadı'
                        });
                        clearProductFields();
                    } else {
                        // Başarılı arama - alanları doldur
                        $("#detailForm").find('input#stock_card_movement_id').val(data.id);
                        $("#serial").val(data.serial_number);
                        $("#sale_price").val(data.sales_price);
                        $("#quantity").val(1);
                        $("#detailForm").find('select#stock_card_id').val(data.stock_card_id).trigger('change');
                        
                        // Başarı mesajı
                        Swal.fire({
                            icon: 'success',
                            title: 'Ürün Bulundu!',
                            text: data.stock_card_name + ' - ' + data.serial_number,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Barkod alanını temizle
                        setTimeout(() => {
                            $('#barcode').val('').focus();
                        }, 500);
                    }
                },
                error: function (xhr) {
                    console.error('Barkod arama hatası:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Barkod arama sırasında hata oluştu.'
                    });
                    clearProductFields();
                }
            });
        });
        
        // Alanları temizleme fonksiyonu
        function clearProductFields() {
            $("#serial").val("");
            $("#sale_price").val("");
            $("#quantity").val(1);
            $("#detailForm").find('select#stock_card_id').val("").trigger('change');
            $("#detailForm").find('input#stock_card_movement_id').val("");
        }
        
        // Seri numarası ile arama (manuel giriş için)
        $(document).on('change', '#serial', function (e) {
            var serial = $(this).val().trim();
            
            if (!serial) {
                clearProductFields();
                return;
            }
            
            var postUrl = window.location.origin + '/serialcheck?id=' + encodeURIComponent(serial);
            
            $.ajax({
                type: "GET",
                url: postUrl,
                beforeSend: function () {
                    $("#sale_price").val("Aranıyor...");
                },
                success: function (data) {
                    if (data.status == false) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ürün Bulunamadı',
                            text: data.message || 'Bu seri numarası ile eşleşen ürün bulunamadı'
                        });
                        clearProductFields();
                    } else {
                        // Başarılı arama - alanları doldur
                        $("#detailForm").find('input#stock_card_movement_id').val(data.id);
                        $("#sale_price").val(data.sales_price);
                        $("#quantity").val(1);
                        $("#detailForm").find('select#stock_card_id').val(data.stock_card_id).trigger('change');
                        
                        // Başarı mesajı
                        Swal.fire({
                            icon: 'success',
                            title: 'Ürün Bulundu!',
                            text: data.stock_card_name + ' - ' + data.serial_number,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Seri numarası arama sırasında hata oluştu.'
                    });
                    clearProductFields();
                }
            });
        })
    </script>
@endsection
