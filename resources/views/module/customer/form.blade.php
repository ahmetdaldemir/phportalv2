@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Müşteriler /</span> @if(isset($customers)) {{$customers->name}} @endif</h4>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bx bx-user me-2"></i>Müşteri Bilgileri
                </h5>
            </div>
            <form action="{{route('customer.store')}}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="id" @if(isset($customers)) value="{{$customers->id}}" @endif />
            <div class="card-body p-4">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="firstname" class="form-label fw-semibold">
                            <i class="bx bx-user me-1 text-primary"></i>İsim
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            class="form-control form-control-lg"
                            type="text"
                            id="firstname"
                            name="firstname"
                            @if(isset($customers)) value="{{$customers->firstname}}" @endif
                            placeholder="Adınızı giriniz..."
                            autofocus required
                        />
                    </div>
                    <div class="col-md-3">
                        <label for="lastname" class="form-label fw-semibold">
                            <i class="bx bx-user me-1 text-success"></i>Soyisim
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            class="form-control form-control-lg"
                            type="text"
                            id="lastname"
                            name="lastname"
                            @if(isset($customers)) value="{{$customers->lastname}}" @endif
                            placeholder="Soyadınızı giriniz..."
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="tc" class="form-label fw-semibold">
                            <i class="bx bx-id-card me-1 text-info"></i>TC Kimlik / Passport No
                        </label>
                        <input 
                            class="form-control form-control-lg" 
                            type="text" 
                            name="tc" 
                            id="tc" 
                            @if(isset($customers)) value="{{$customers->tc}}" @endif 
                            placeholder="TC Kimlik veya Passport numaranız..."
                            maxlength="13" 
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bx bx-envelope me-1 text-warning"></i>E-mail
                        </label>
                        <input
                            class="form-control form-control-lg"
                            type="email"
                            id="email"
                            name="email"
                            @if(isset($customers)) value="{{$customers->email}}" @endif
                            placeholder="john.doe@example.com"
                        />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="organization" class="form-label">Iban</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="organization"
                                    name="iban"
                                    @if(isset($customers)) value="{{$customers->iban}}" @endif
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">Telefon 1</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">TR (+90)</span>
                                    <input
                                        type="text"
                                        id="phoneNumber"
                                        name="phone1"
                                        class="form-control"
                                        @if(isset($customers)) value="{{$customers->phone1}}" @endif

                                    />
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="address" class="form-label">Telefon 2</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">TR (+90)</span>
                                    <input
                                        type="text"
                                        id="phoneNumber"
                                        name="phone2"
                                        class="form-control"
                                        @if(isset($customers)) value="{{$customers->phone2}}" @endif
                                    />
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">Adres</label>
                                <textarea class="form-control"  id="address"  name="address">@if(isset($customers))  {{$customers->address}}  @endif</textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">Not</label>
                                <textarea class="form-control"  id="note" name="note">@if(isset($customers)) {{$customers->note}}  @endif</textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">İl</label>
                                <input class="form-control" type="text" id="city" name="city" @if(isset($customers)) value="{{$customers->city}}" @endif />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">İlçe</label>
                                <input class="form-control" type="text" id="district" name="district" @if(isset($customers)) value="{{$customers->district}}" @endif />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="zipCode" class="form-label">Şube</label>
                                <select id="seller" name="seller_id" class="select2 form-select">
                                    @foreach($sellers as $seller)
                                        <option  @if(isset($customers)) {{ $customers->hasSeller($seller->id) ? 'selected' : '' }} @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="zipCode" class="form-label">Firma Türü</label>
                                <select id="seller" name="company_type" class="select2 form-select">
                                  <option value="sahis">Şahıs</option>
                                  <option value="firma">Firma</option>
                                </select>
                            </div>

                        </div>
                </div>
                <!-- /Account -->

                <hr class="my-5">
                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bx bx-x me-2"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bx bx-save me-2"></i>Kaydet
                    </button>
                </div>
            </div>
            </form>
        </div>
        <hr class="my-5">
    </div>
    
    <style>
        /* Modern Form Stilleri */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .form-control-lg {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 16px;
            padding: 12px 16px;
        }

        .form-control-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .card {
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            border: none;
            padding: 20px 24px;
        }

        .card-body {
            padding: 24px;
        }

        .btn-lg {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 16px;
            }
            
            .form-control-lg {
                font-size: 14px;
                padding: 10px 12px;
            }
        }
    </style>
@endsection

@section('custom-js')
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection
