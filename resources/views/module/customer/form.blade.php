@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($customers) ? 'Müşteri Düzenle' : 'Yeni Müşteri Ekle' }}"
            icon="bx-user"
            description="{{ isset($customers) ? ($customers->firstname . ' ' . $customers->lastname) . ' müşterisini düzenleyin' : 'Yeni bir müşteri ekleyin' }}"
            :backRoute="route('customer.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Müşteri Bilgileri" icon="bx-user">
            <form action="{{route('customer.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="id" value="{{ $customers->id ?? '' }}" />

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-user me-1"></i>Kişisel Bilgiler
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="firstname" class="form-label">
                                    <i class="bx bx-user me-1"></i>İsim
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="firstname"
                                    name="firstname"
                                    value="{{ $customers->firstname ?? '' }}"
                                    placeholder="Adınızı giriniz..."
                                    autofocus required
                                />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="lastname" class="form-label">
                                    <i class="bx bx-user me-1"></i>Soyisim
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="lastname"
                                    name="lastname"
                                    value="{{ $customers->lastname ?? '' }}"
                                    placeholder="Soyadınızı giriniz..."
                                    required
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tc" class="form-label">
                                    <i class="bx bx-id-card me-1"></i>TC Kimlik / Passport No
                                </label>
                                <input 
                                    class="form-control" 
                                    type="text" 
                                    name="tc" 
                                    id="tc" 
                                    value="{{ $customers->tc ?? '' }}" 
                                    placeholder="TC Kimlik veya Passport numaranız..."
                                    maxlength="13" 
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-envelope me-1"></i>İletişim Bilgileri
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="bx bx-envelope me-1"></i>E-mail
                                </label>
                                <input
                                    class="form-control"
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ $customers->email ?? '' }}"
                                    placeholder="john.doe@example.com"
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="iban" class="form-label">
                                    <i class="bx bx-barcode me-1"></i>IBAN
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="iban"
                                    name="iban"
                                    value="{{ $customers->iban ?? '' }}"
                                    placeholder="IBAN numarası..."
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone1">Telefon 1</label>
                                <div class="input-group">
                                    <span class="input-group-text">TR (+90)</span>
                                    <input
                                        type="text"
                                        id="phone1"
                                        name="phone1"
                                        class="form-control"
                                        value="{{ $customers->phone1 ?? '' }}"
                                        placeholder="Telefon numarası..."
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone2">Telefon 2</label>
                                <div class="input-group">
                                    <span class="input-group-text">TR (+90)</span>
                                    <input
                                        type="text"
                                        id="phone2"
                                        name="phone2"
                                        class="form-control"
                                        value="{{ $customers->phone2 ?? '' }}"
                                        placeholder="Telefon numarası..."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-map me-1"></i>Adres Bilgileri
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address" class="form-label">
                                    <i class="bx bx-map me-1"></i>Adres
                                </label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ $customers->address ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label">
                                    <i class="bx bx-map me-1"></i>İl
                                </label>
                                <input class="form-control" type="text" id="city" name="city" value="{{ $customers->city ?? '' }}" placeholder="İl adı..." />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="district" class="form-label">
                                    <i class="bx bx-map me-1"></i>İlçe
                                </label>
                                <input class="form-control" type="text" id="district" name="district" value="{{ $customers->district ?? '' }}" placeholder="İlçe adı..." />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-info-circle me-1"></i>Diğer Bilgiler
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="note" class="form-label">
                                    <i class="bx bx-note me-1"></i>Not
                                </label>
                                <textarea class="form-control" id="note" name="note" rows="3">{{ $customers->note ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="seller_id" class="form-label">
                                    <i class="bx bx-store me-1"></i>Şube
                                </label>
                                <select id="seller_id" name="seller_id" class="form-select">
                                    <option value="">Şube Seçiniz</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{$seller->id}}" {{ (isset($customers) && $customers->hasSeller($seller->id)) ? 'selected' : '' }}>
                                            {{$seller->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company_type" class="form-label">
                                    <i class="bx bx-building me-1"></i>Firma Türü
                                </label>
                                <select id="company_type" name="company_type" class="form-select">
                                    <option value="sahis" {{ (isset($customers) && $customers->company_type == 'sahis') ? 'selected' : '' }}>Şahıs</option>
                                    <option value="firma" {{ (isset($customers) && $customers->company_type == 'firma') ? 'selected' : '' }}>Firma</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{route('customer.index')}}" class="btn btn-outline-secondary">
                        <i class="bx bx-x me-1"></i>İptal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Kaydet
                    </button>
                </div>
            </form>
        </x-form-page.card>
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection
