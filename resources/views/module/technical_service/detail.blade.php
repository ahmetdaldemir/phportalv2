@extends('layouts.admin')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-wrench me-2"></i>Teknik Servis Formu
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Teknik Servis</a></li>
                        <li class="breadcrumb-item active">@if(isset($technical_services))PHTEC{{$technical_services->id}} - {{$technical_services->name}}@endif</li>
                    </ol>
                </nav>
            </div>
            @if($technical_services->payment_status == 0)
            <div>
                <button type="button" onclick="save()" class="btn btn-primary btn-lg">
                    <i class="bx bx-save me-2"></i>Kaydet
                </button>
            </div>
            @endif
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

            <div class="row">
            <div class="col-lg-8">
                  <form action="javascript():;" id="technicalForm" method="post">
                    @csrf
                   <input type="hidden" name="id" @if(isset($technical_services)) value="{{$technical_services->id}}" @endif />
                    
                      <div class="row">
                        <!-- Cihaz Bilgileri Card -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-label-primary">
                                    <h5 class="mb-0"><i class="bx bx-mobile-alt me-2"></i>Cihaz Bilgileri</h5>
                                </div>
                        <div class="card-body">
                                    <!-- Müşteri Seçimi -->
                                    <div class="mb-3">
                                        <label for="selectpickerLiveSearch" class="form-label fw-semibold">
                                            <i class="bx bx-user me-1"></i>Müşteri
                                        </label>
                                        <div class="input-group">
                                    <select id="selectpickerLiveSearch" class="selectpicker w-100"
                                            data-style="btn-default" name="customer_id"
                                                    onchange="getCustomer(this.value)"
                                            data-live-search="true">
                                                <option value="1" @if($technical_services->customer_id == 1) selected @endif>
                                                    Genel Müşteri
                                        </option>
                                        @foreach($customers as $customer)
                                            <option value="{{$customer->id}}"
                                                            @if(isset($technical_services) && $customer->id == $technical_services->customer_id) selected @endif>
                                                        {{$customer->fullname}}
                                                    </option>
                                        @endforeach
                                    </select>
                                            <button class="btn btn-outline-primary" tabindex="0"
                                                    data-bs-toggle="modal" data-bs-target="#editUser" type="button"
                                                    data-bs-toggle="tooltip" title="Yeni Müşteri Ekle">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                </div>
                            </div>
                                    <!-- Fiziksel Durum -->
                                    <div class="mb-3">
                                        <label for="physical_condition_text" class="form-label fw-semibold">
                                            <i class="bx bx-shield me-1"></i>Fiziksel Durumu
                                        </label>
                                        <select class="form-select select2 mb-2" name="physically_category[]" multiple
                                                id="physically_category_select">
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "physically")
                                                <option value="{{$item->id}}"
                                                            @if(!is_null($technical_services->physically_category) and in_array($item->id,$technical_services->physically_category)) selected @endif>
                                                        {{$item->name}}
                                                    </option>
                                            @endif
                                        @endforeach
                                    </select>
                                        <textarea class="form-control" id="physical_condition_text" name="physical_condition"
                                                  rows="2" placeholder="Detaylı açıklama...">@if(isset($technical_services)){{$technical_services->physical_condition}}@endif</textarea>
                            </div>

                                    <!-- Aksesuar -->
                                    <div class="mb-3">
                                        <label for="accessories_text" class="form-label fw-semibold">
                                            <i class="bx bx-box me-1"></i>Aksesuar
                                        </label>
                                        <select class="form-select select2 mb-2" name="accessory_category[]" multiple
                                                id="accessory_category_select">
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "accessory")
                                                <option value="{{$item->id}}"
                                                            @if(!is_null($technical_services->accessory_category) and in_array($item->id,$technical_services->accessory_category)) selected @endif>
                                                        {{$item->name}}
                                                    </option>
                                            @endif
                                        @endforeach
                                    </select>
                                        <textarea class="form-control" id="accessories_text" name="accessories"
                                                  rows="2" placeholder="Detaylı açıklama...">@if(isset($technical_services)){{$technical_services->accessories}}@endif</textarea>
                                </div>

                                    <!-- Arıza Açıklaması -->
                                    <div class="mb-3">
                                        <label for="fault_information_text" class="form-label fw-semibold">
                                            <i class="bx bx-error me-1"></i>Arıza Açıklaması
                                        </label>
                                        <select class="form-select select2 mb-2" name="fault_category[]" multiple
                                                id="fault_category_select">
                                        @foreach($categories_all as $item)
                                            @if($item->parent_id == "fault")
                                                <option value="{{$item->id}}"
                                                            @if(!is_null($technical_services->fault_category) and in_array($item->id,$technical_services->fault_category)) selected @endif>
                                                        {{$item->name}}
                                                    </option>
                                            @endif
                                        @endforeach
                                    </select>
                                        <textarea class="form-control" id="fault_information_text" name="fault_information"
                                                  rows="3" placeholder="Arıza detaylarını yazınız...">@if(isset($technical_services)){{$technical_services->fault_information}}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Özellikler Card -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-label-success">
                                    <h5 class="mb-0"><i class="bx bx-cog me-2"></i>Özellikler</h5>
                </div>
                        <div class="card-body">
                                    <div class="row g-3">
                                        <!-- Şube -->
                                <div class="col-md-6">
                                            <label for="seller_id" class="form-label fw-semibold">
                                                <i class="bx bx-store me-1"></i>Şube
                                            </label>
                                    <select id="seller_id" name="seller_id" class="select2 form-select"
                                            @if(\Illuminate\Support\Facades\Auth::user()->getRoleNames() != 'super-admin') disabled @endif>
                                        @foreach($sellers as $seller)
                                                    <option @if(isset($technical_services) && $technical_services->seller_id == $seller->id) selected @endif
                                                            value="{{$seller->id}}">{{$seller->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Durum -->
                                        <div class="col-md-6">
                                            <label for="status" class="form-label fw-semibold">
                                                <i class="bx bx-info-circle me-1"></i>Durum
                                            </label>
                                            <select class="form-select" id="status" name="status">
                                                @foreach(\App\Models\TechnicalService::STATUS as $key=>$value)
                                                    <option @if($technical_services->status == $key) selected @endif value="{{$key}}">
                                                        {{$value}}
                                                    </option>
                                        @endforeach
                                    </select>
                                </div>

                                        <!-- Marka -->
                                <div class="col-md-6">
                                            <label for="brand_id" class="form-label fw-semibold">
                                                <i class="bx bx-purchase-tag me-1"></i>Marka
                                            </label>
                                    <select id="brand_id" name="brand_id" class="select2 form-select"
                                            onchange="getVersion(this.value)" required>
                                        <option>Seçiniz</option>
                                                @foreach($brands as $value)
                                                    <option @if(isset($technical_services) && $technical_services->brand_id == $value->id) selected @endif
                                                            value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                        <!-- Model -->
                                <div class="col-md-6">
                                            <label for="version_id" class="form-label fw-semibold">
                                                <i class="bx bx-devices me-1"></i>Model
                                            </label>
                                    <select id="version_id" name="version_id" class="select2 form-select" required>
                                        <option>Seçiniz</option>
                                        @foreach($versions as $value)
                                                    <option @if(isset($technical_services) && $technical_services->version_id == $value->id) selected @endif
                                                            value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                        <!-- IMEI -->
                                <div class="col-md-6">
                                            <label for="imei" class="form-label fw-semibold">
                                                <i class="bx bx-barcode me-1"></i>IMEI <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="imei" name="imei"
                                                   minlength="15" maxlength="15" required
                                                   placeholder="15 haneli IMEI numarası"
                                                   @if(isset($technical_services) && $technical_services->imei != 0) value="{{$technical_services->imei}}" @endif>
                                </div>

                                        <!-- Cihaz Şifresi -->
                                <div class="col-md-6">
                                            <label for="device_password" class="form-label fw-semibold">
                                                <i class="bx bx-lock me-1"></i>Cihaz Şifresi
                                            </label>
                                    <input type="text" class="form-control" id="device_password" name="device_password"
                                                   placeholder="Şifre (varsa)"
                                                   @if(isset($technical_services)) value="{{$technical_services->device_password}}" @endif>
                                </div>

                                        <!-- Teslim Alan Personel -->
                                <div class="col-md-6">
                                            <label for="delivery_staff" class="form-label fw-semibold">
                                                <i class="bx bx-user-check me-1"></i>Teslim Alan Personel <span class="text-danger">*</span>
                                            </label>
                                            <select id="delivery_staff" name="delivery_staff" class="select2 form-select" required>
                                        <option value="">Seçiniz</option>
                                        @foreach($users as $user)
                                            @if($user->is_status == 1)
                                                        <option @if(isset($technical_services) && $technical_services->delivery_staff == $user->id) selected @endif
                                                                value="{{$user->id}}">{{$user->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                        <!-- Teknik Personel -->
                                <div class="col-md-6">
                                            <label for="technical_person" class="form-label fw-semibold">
                                                <i class="bx bx-user-pin me-1"></i>Teknik Personel
                                            </label>
                                    <select id="technical_person" name="technical_person" class="select2 form-select" disabled>
                                        <option value="">Seçiniz</option>
                                        @foreach($users as $user)
                                            @if($user->is_status == 1)
                                                        <option @if(isset($technical_services) && $technical_services->technical_person == $user->id) selected @endif
                                                                value="{{$user->id}}">{{$user->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                            @if(isset($technical_services->technical_person))
                                                <small class="text-muted">Atanan: ID {{$technical_services->technical_person}}</small>
                                            @endif
                                        </div>

                                        <!-- Toplam Tutar -->
                                        <div class="col-md-6">
                                            <label for="total_price" class="form-label fw-semibold">
                                                <i class="bx bx-calculator me-1"></i>Toplam Tutar
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="text" class="form-control bg-light" id="total_price" name="total_price"
                                                       @if(isset($technical_services)) value="{{$technical_services->sumPrice()}}" @endif readonly>
                                </div>
                            </div>

                                        <!-- Müşteri Fiyatı -->
                                        <div class="col-md-6">
                                            <label for="customer_price" class="form-label fw-semibold">
                                                <i class="bx bx-money me-1"></i>Müşteri Fiyatı
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">₺</span>
                                                <input type="text" class="form-control" id="customer_price" name="customer_price"
                                                       placeholder="0.00"
                                                       @if(isset($technical_services)) value="{{$technical_services->customer_price}}" @endif>
                                            </div>
                                        </div>
                        </div>
                    </div>
                            </div>
                        </div>
                </div>
                      </div>
                </form>
                </div>

            <!-- Ürün Ekle Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-label-info">
                        <h5 class="mb-0"><i class="bx bx-package me-2"></i>Ürün Ekle</h5>
                    </div>
                        <div class="card-body">
                        <form method="post" id="detailForm">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$technical_services->id}}">
                                    <input type="hidden" name="stock_card_movement_id" id="stock_card_movement_id">
                            <input type="hidden" name="payment_status" value="{{$technical_services->payment_status}}">
                            
                            <!-- Seri No -->
                            <div class="mb-3">
                                <label for="serial" class="form-label fw-semibold">
                                    <i class="bx bx-barcode-reader me-1"></i>Seri No <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="serial" id="serial"
                                       placeholder="Seri numarasını giriniz" required
                                       @if($technical_services->payment_status != 0) disabled @endif/>
                                <small class="text-muted">Seri no otomatik kontrol edilecek</small>
                            </div>

                            <!-- Stok -->
                                    <div class="mb-3">
                                <label for="stock_card_id" class="form-label fw-semibold">
                                    <i class="bx bx-box me-1"></i>Stok
                                </label>
                                                        <select name="stock_card_id" id="stock_card_id"
                                        class="form-select" disabled>
                                                            <option>Seçiniz</option>
                                                            @foreach($stocks as $stock)
                                                                <option value="{{$stock->id}}">{{$stock->name}}</option>
                                                            @endforeach
                                                        </select>
                                <small class="text-muted">Seri no ile otomatik doldurulur</small>
                                                    </div>

                            <!-- Satış Fiyatı -->
                            <div class="mb-3">
                                <label for="sale_price" class="form-label fw-semibold">
                                    <i class="bx bx-money me-1"></i>Satış Fiyatı
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">₺</span>
                                    <input type="text" class="form-control" name="sale_price" id="sale_price"
                                           placeholder="0.00"
                                           @if($technical_services->payment_status != 0) disabled @endif/>
                                                    </div>
                                                </div>

                            <!-- Adet -->
                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-semibold">
                                    <i class="bx bx-list-ol me-1"></i>Adet
                                </label>
                                <input type="number" class="form-control" name="quantity" id="quantity"
                                       min="1" max="50" placeholder="1"
                                       @if($technical_services->payment_status != 0) disabled @endif>
                                            </div>

                                    @if($technical_services->payment_status == 0)
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-plus-circle me-2"></i>Ürün Ekle
                                    </button>
                                            </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bx bx-info-circle me-2"></i>Ödeme tamamlandı, ürün eklenemez
                                        </div>
                                    @endif
                                </form>
                            </div>
                    </div>
                </div>
            </div>


        <!-- Eklenen Ürünler -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-shopping-bag me-2"></i>Eklenen Ürünler</h5>
                <span class="badge bg-primary product-count-badge">{{count($technical_service_products)}} Ürün</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="productsTable">
                        <thead class="table-light">
                            <tr>
                                <th><i class="bx bx-package me-1"></i>Ürün Adı</th>
                                <th><i class="bx bx-hash me-1"></i>Form No</th>
                                <th><i class="bx bx-barcode me-1"></i>Seri No</th>
                                <th><i class="bx bx-money me-1"></i>Fiyat</th>
                                    @if($technical_services->payment_status == 0)
                                <th class="text-center"><i class="bx bx-cog me-1"></i>İşlemler</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($technical_service_products as $technical_service_product)
                                @if($technical_service_product->stock_card)
                                    <tr id="product-row-{{$technical_service_product->id}}">
                                        <td class="fw-semibold">{{$technical_service_product->stock_card->name}}</td>
                                        <td><span class="badge bg-label-primary">PHTEC{{$technical_service_product->technical_service_id}}</span></td>
                                        <td><code>{{$technical_service_product->serial_number}}</code></td>
                                        <td class="fw-semibold text-success">₺{{number_format($technical_service_product->sale_price, 2)}}</td>
                                        @if($technical_services->payment_status == 0)
                                        <td class="text-center">
                                            <a href="#" 
                                               class="btn btn-sm btn-danger delete-product"
                                               data-id="{{$technical_service_product->id}}"
                                               data-technical-service-id="{{$technical_service_product->technical_service_id}}">
                                                <i class="bx bx-trash"></i> Sil
                                            </a>
                                        </td>
                                @endif
                            </tr>
                        @endif
                            @empty
                                <tr class="empty-state-row">
                                    <td colspan="{{$technical_services->payment_status == 0 ? 5 : 4}}" class="text-center py-4 text-muted">
                                        <i class="bx bx-info-circle me-2"></i>Henüz ürün eklenmemiş
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- İşlem Geçmişi -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-history me-2"></i>İşlem Geçmişi</h5>
                <span class="badge bg-info">{{count($technical_service_process)}} İşlem</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><i class="bx bx-task me-1"></i>İşlem</th>
                                <th><i class="bx bx-user me-1"></i>Personel</th>
                                <th><i class="bx bx-calendar me-1"></i>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($technical_service_process as $item)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-secondary">
                                            {{\App\Models\TechnicalService::STATUS[$item->status]}}
                                        </span>
                                        <span class="text-muted ms-2">Olarak Değiştirildi</span>
                                    </td>
                                    <td class="fw-semibold">
                                        <i class="bx bx-user-circle me-1"></i>
                                        {{\App\Models\User::find($item->user_id)->name}}
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bx bx-time me-1"></i>
                                            {{$item->created_at->format('d.m.Y H:i')}}
                                        </small>
                                    </td>
                    </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="bx bx-info-circle me-2"></i>Henüz işlem kaydı yok
                                    </td>
                        </tr>
                            @endforelse
                        </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
@endsection
@include('components.customermodal')
@section('custom-js')

    <script>
        // Form kaydetme fonksiyonu
        function save() {
            // Validasyon kontrolleri
            if ($("#delivery_staff option:selected").val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Eksik Bilgi',
                    text: 'Teslim Alan Personel alanı boş olamaz',
                    confirmButtonColor: '#3085d6'
                });
                $('#delivery_staff').focus();
                return false;
            }

                if ($('#imei').val().length < 15) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Geçersiz IMEI',
                    text: 'IMEI numarası 15 haneli olmalıdır',
                    confirmButtonColor: '#3085d6'
                });
                $('#imei').focus();
                    return false;
            }

            // AJAX isteği
            var postUrl = window.location.origin + '/technical_service/store';
            
            Swal.fire({
                title: 'Kaydediliyor...',
                text: 'Lütfen bekleyiniz',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

                    $.ajax({
                        type: "POST",
                        url: postUrl,
                        data: $("#technicalForm").serialize(),
                        dataType: "json",
                        encode: true,
                        success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Değişiklikler kaydedildi',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Sayfayı yenile (opsiyonel)
                        // location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Hata:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata Oluştu!',
                        text: 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }


        // Stok kartı bilgilerini getir
        function stockCardId(value) {
            var postUrl = window.location.origin + '/getStockCard?id=' + value;
            
            $.ajax({
                type: "GET",
                url: postUrl,
                beforeSend: function () {
                    $('#loader').removeClass('display-none');
                },
                success: function (data) {
                    console.log('StockCard Response:', data);
                    
                    if (data.status == false) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Uyarı',
                            text: data.message || 'Stok kartı bulunamadı'
                        });
                    } else if (data.status == true && data.stock_card_id) {
                        // Veriyi direkt data objesinden al
                        $("#sale_price").val(data.sales_price);
                        $("#serial").val(data.serial_number);
                        $("#quantity").val(1);
                        $("#detailForm").find('select#stock_card_id').val(data.stock_card_id).trigger('change');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Veri alınamadı. Lütfen tekrar deneyin.'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Hata:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Bağlantı Hatası',
                        text: 'Sunucuya bağlanırken hata oluştu.'
                    });
                },
                complete: function () {
                    $('#loader').addClass('display-none');
                }
            });
        }

        // Yardımcı Fonksiyonlar
        
        // Ürünü tabloya ekle
        function addProductToTable(product) {
            var tbody = $('#productsTable tbody');
            var paymentStatus = $('input[name="payment_status"]').val();
            
            // Empty state varsa kaldır
            tbody.find('.empty-state-row').remove();
            
            // Yeni satırı oluştur
            var deleteButton = '';
            var colspan = 4;
            
            if (paymentStatus == 0) {
                colspan = 5;
                deleteButton = '<td class="text-center">' +
                    '<a href="#" class="btn btn-sm btn-danger delete-product" ' +
                    'data-id="' + product.id + '" ' +
                    'data-technical-service-id="' + product.technical_service_id + '">' +
                    '<i class="bx bx-trash"></i> Sil' +
                    '</a>' +
                    '</td>';
            }
            
            var newRow = '<tr id="product-row-' + product.id + '" class="new-product">' +
                '<td class="fw-semibold">' + product.stock_card_name + '</td>' +
                '<td><span class="badge bg-label-primary">PHTEC' + product.technical_service_id + '</span></td>' +
                '<td><code>' + product.serial_number + '</code></td>' +
                '<td class="fw-semibold text-success">₺' + product.formatted_price + '</td>' +
                deleteButton +
                '</tr>';
            
            // Tabloya ekle ve animasyon yap
            tbody.prepend(newRow);
            $('#product-row-' + product.id).hide().fadeIn('slow');
        }
        
        // Toplam tutarı güncelle
        function updateTotalPrice(formattedPrice) {
            $('#total_price').val(formattedPrice);
            
            // Animasyon ile vurgula
            $('#total_price').addClass('updated-field');
            setTimeout(function() {
                $('#total_price').removeClass('updated-field');
            }, 1000);
        }
        
        // Formu sıfırla
        function resetProductForm() {
            $('#serial').val('');
            $('#sale_price').val('');
            $('#quantity').val('');
            $('#stock_card_movement_id').val('');
            $('#stock_card_id').val('').trigger('change');
            $('#serial').focus();
        }
        
        // Ürün sayısını güncelle
        function updateProductCount() {
            var count = $('#productsTable tbody tr:not(.empty-state-row)').length;
            $('.product-count-badge').text(count + ' Ürün');
        }
        
        // Ürünü dinamik olarak sil
        $(document).on('click', '.delete-product', function(e) {
            e.preventDefault();
            
            var productId = $(this).data('id');
            var technicalServiceId = $(this).data('technical-service-id');
            var row = $('#product-row-' + productId);
            
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu ürünü silmek istediğinize emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Silme işlemi
                    $.ajax({
                        type: "GET",
                        url: "{{route('technical_service.detaildelete')}}",
                        data: {
                            id: productId,
                            technical_service_id: technicalServiceId
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log('Product deleted:', response);
                            
                            if (response.status === true) {
                                // Satırı animasyonla kaldır
                                row.fadeOut('slow', function() {
                                    $(this).remove();
                                    
                                    // Eğer tablo boşsa empty state ekle
                                    if ($('#productsTable tbody tr').length === 0) {
                                        var paymentStatus = $('input[name="payment_status"]').val();
                                        var colspan = paymentStatus == 0 ? 5 : 4;
                                        
                                        $('#productsTable tbody').html(
                                            '<tr class="empty-state-row">' +
                                            '<td colspan="' + colspan + '" class="text-center py-4 text-muted">' +
                                            '<i class="bx bx-info-circle me-2"></i>Henüz ürün eklenmemiş' +
                                            '</td>' +
                                            '</tr>'
                                        );
                                    }
                                    
                                    // Ürün sayısını güncelle
                                    updateProductCount();
                                });
                                
                                // Toplam tutarı güncelle
                                updateTotalPrice(response.formatted_total_price);
                                
                                // Başarı mesajı
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Silindi!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Hata',
                                    text: response.message || 'Ürün silinemedi.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete error:', {xhr, status, error});
                            var errorMessage = 'Silme işlemi sırasında bir hata oluştu.';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Flash mesajlarını otomatik kapat
        $(document).ready(function() {
            // Alert mesajlarını 5 saniye sonra otomatik kapat
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // Form submit - AJAX ile dinamik ekleme
        $(document).ready(function() {
            $("#detailForm").on('submit', function(e) {
                e.preventDefault(); // Sayfanın yenilenmesini engelle
                
                var stockCardMovementId = $("#stock_card_movement_id").val();
                var serialNumber = $("#serial").val();
                var salePrice = $("#sale_price").val();
                var paymentStatus = $('input[name="payment_status"]').val();
                
                // Ödeme kontrolü
                if (paymentStatus != 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'İşlem Yapılamaz',
                        text: 'Ödeme tamamlandı, ürün eklenemez.'
                    });
                    return false;
                }
                
                // Seri numarası kontrolü
                if (!serialNumber || serialNumber.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Eksik Bilgi',
                        text: 'Lütfen seri numarası giriniz.'
                    });
                    $("#serial").focus();
                    return false;
                }
                
                // Stock card movement ID kontrolü
                if (!stockCardMovementId || stockCardMovementId === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Seri Numarası Doğrulanmadı',
                        text: 'Lütfen önce seri numarasını girin ve doğrulanmasını bekleyin.',
                        confirmButtonColor: '#3085d6'
                    });
                    $("#serial").focus();
                    return false;
                }
                
                // Satış fiyatı kontrolü
                if (!salePrice || salePrice === '' || parseFloat(salePrice) <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Eksik Bilgi',
                        text: 'Lütfen geçerli bir satış fiyatı giriniz.'
                    });
                    $("#sale_price").focus();
            return false;
        }

                // AJAX ile ürün ekle
                var formData = $(this).serialize();
                
                Swal.fire({
                    title: 'Ekleniyor...',
                    text: 'Lütfen bekleyiniz',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    type: "POST",
                    url: "{{route('technical_service.detailstore')}}",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        console.log('Product added:', response);
                        
                        if (response.status === true) {
                            // Başarı mesajı
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Ürünü tabloya dinamik ekle
                            addProductToTable(response.product);
                            
                            // Toplam tutarı güncelle
                            updateTotalPrice(response.formatted_total_price);
                            
                            // Formu temizle
                            resetProductForm();
                            
                            // Ürün sayısını güncelle
                            updateProductCount();
                            
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata',
                                text: response.message || 'Ürün eklenirken bir hata oluştu.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Add product error:', {xhr, status, error});
                        var errorMessage = 'Bir hata oluştu.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata Oluştu',
                            text: errorMessage
                        });
                    }
                });
                
                return false;
            });

            // Seri no değiştiğinde kontrol et
        $("#detailForm").on('change', '#serial', function (e) {
            e.preventDefault();
                
                var serialValue = $(this).val().trim();
                if (!serialValue) return;

                var postUrl = window.location.origin + '/serialcheck?id=' + serialValue;
                
                // Loading indicator
                $(this).addClass('is-loading');
                
            $.ajax({
                type: "GET",
                url: postUrl,
                success: function (data) {
                        console.log('Serial Check Response:', data);
                        
                    if (data.status == false) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Uyarı',
                                text: data.message || 'Seri numarası bulunamadı',
                                confirmButtonColor: '#3085d6'
                            });
                            $("#serial").val('');
                        } else if (data.status == true && data.stock_card_id) {
                            // Movement ID'yi set et - ZORUNLU!
                            if (data.id) {
                                $("#detailForm").find('input#stock_card_movement_id').val(data.id);
                                console.log('Stock card movement ID set:', data.id);
                            } else {
                                console.warn('Warning: No movement ID in response!', data);
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Uyarı',
                                    text: 'Seri numarası bulundu ancak movement ID eksik. Lütfen tekrar deneyin.'
                                });
                                $("#serial").val('');
                                return;
                            }
                            
                            // Diğer alanları doldur
                            $("#sale_price").val(data.sales_price);
                            $("#quantity").val(1);
                            $("#detailForm").find('select#stock_card_id').val(data.stock_card_id).trigger('change');
                            
                            // Başarı mesajı - daha bilgilendirici
                            Swal.fire({
                                icon: 'success',
                                title: 'Doğrulandı!',
                                html: '<strong>Seri No:</strong> ' + data.serial_number + '<br>' +
                                      '<strong>Fiyat:</strong> ₺' + data.sales_price + '<br>' +
                                      '<small class="text-muted">Ürün eklenmeye hazır</small>',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Sale price input'una focus
                            $("#sale_price").focus().select();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Bulunamadı',
                                text: 'Seri numarası bulunamadı veya geçersiz veri.'
                            });
                            $("#serial").val('');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Serial Check Error:', {xhr, status, error});
                        Swal.fire({
                            icon: 'error',
                            title: 'Bağlantı Hatası',
                            text: 'Seri numarası kontrol edilemedi.'
                        });
                    },
                    complete: function () {
                        $("#serial").removeClass('is-loading');
                    }
                });
                
                e.stopPropagation();
                return false;
            });
        });
    </script>

    <style>
        /* Modern card styles */
        .card {
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card.shadow-sm {
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        }

        .card:hover {
            box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.18);
        }

        /* Form field styles */
        .form-label.fw-semibold {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #566a7f;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
        }

        /* Loading state */
        .form-control.is-loading {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23696cff' stroke-width='2'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Table improvements */
        .table thead th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #566a7f;
            border-bottom: 2px solid #d9dee3;
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.04);
        }

        /* Badge improvements */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Button improvements */
        .btn {
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Breadcrumb improvements */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-size: 1.2rem;
        }

        /* Input group improvements */
        .input-group-text {
            font-weight: 600;
            background-color: #f5f5f9;
            border-color: #d9dee3;
        }

        /* Disabled field styling */
        .form-control:disabled,
        .form-select:disabled {
            background-color: #f5f5f9;
            cursor: not-allowed;
        }

        /* Code styling for serial numbers */
        code {
            background-color: #f5f5f9;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            color: #696cff;
        }

        /* Empty state styling */
        .text-muted i {
            font-size: 1.2em;
            vertical-align: middle;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1rem;
            }
            
            .table {
                font-size: 0.875rem;
            }
        }

        /* Dinamik ekleme animasyonları */
        .new-product {
            animation: slideInFromTop 0.5s ease-out;
            background-color: #f0fff4 !important;
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Güncellenen field animasyonu */
        .updated-field {
            animation: pulse 0.5s ease-in-out;
            border-color: #28a745 !important;
            background-color: #f0fff4 !important;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        /* Yeni eklenen ürün için highlight */
        #productsTable tbody tr.new-product:hover {
            background-color: #e6f9f0 !important;
        }

        /* Silme butonu hover efekti */
        .delete-product {
            transition: all 0.3s ease;
        }

        .delete-product:hover {
            transform: scale(1.1);
        }

        /* Badge animasyonu */
        .product-count-badge {
            transition: all 0.3s ease;
        }

        /* Loading state indicator */
        .is-loading {
            position: relative;
            pointer-events: none;
        }

        .is-loading::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #696cff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spinner 0.6s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }
    </style>
@endsection
