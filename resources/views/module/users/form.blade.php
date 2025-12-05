@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($users) ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle' }}"
            icon="bx-user"
            description="{{ isset($users) ? $users->name . ' kullanıcısını düzenleyin' : 'Yeni bir kullanıcı ekleyin' }}"
            :backRoute="route('user.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Kullanıcı Bilgileri" icon="bx-user">
            <form action="{{route('user.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $users->id ?? '' }}" />
                
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-user me-1"></i>Kişisel Bilgiler
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="bx bx-user me-1"></i>İsim Soyisim
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       value="{{ $users->name ?? '' }}"
                                       name="name" 
                                       placeholder="İsim soyisim giriniz..."
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="bx bx-envelope me-1"></i>Email
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       value="{{ $users->email ?? '' }}"
                                       name="email" 
                                       placeholder="Email adresi giriniz..."
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="bx bx-lock me-1"></i>Şifre
                                    @if(!isset($users))
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Şifre giriniz..."
                                       {{ !isset($users) ? 'required' : '' }}>
                                @if(isset($users))
                                    <div class="form-text">Boş bırakırsanız şifre değişmez</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-building me-1"></i>Firma ve Şube Bilgileri
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_id" class="form-label">
                                    <i class="bx bx-building me-1"></i>Firma
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="company_id" class="form-select" id="company_id" required>
                                    <option value="">Firma Seçiniz</option>
                                    @foreach($companys as $company)
                                        <option value="{{$company->id}}" {{ (isset($users) && $users->company_id == $company->id) ? 'selected' : '' }}>
                                            {{$company->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="seller_id" class="form-label">
                                    <i class="bx bx-store me-1"></i>Şube
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="seller_id" class="form-select" id="seller_id" required {{ (isset($edit) && $edit == 1) ? 'disabled' : '' }}>
                                    <option value="">Şube Seçiniz</option>
                                    @foreach($companys as $company)
                                        <optgroup label="{{$company->name}}">
                                            @foreach($sellers as $seller)
                                                @if($seller->company_id == $company->id)
                                                    <option value="{{$seller->id}}" {{ (isset($users) && $users->seller_id == $seller->id) ? 'selected' : '' }}>
                                                        {{$seller->name}}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @if(isset($edit) && $edit == 1)
                                    <input type="hidden" name="seller_id" value="{{ $users->seller_id ?? '' }}">
                                    <div class="form-text">Şube bilgisi düzenlenemez</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-shield me-1"></i>Yetki Bilgileri
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role" class="form-label">
                                    <i class="bx bx-shield me-1"></i>Rol
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="role" class="form-select" id="role" required>
                                    <option value="">Rol Seçiniz</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}" {{ (isset($users) && $users->hasRole($role->name)) ? 'selected' : '' }}>
                                            {{$role->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{route('user.index')}}" class="btn btn-outline-secondary">
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
