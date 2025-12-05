@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($companies) ? 'Firma Düzenle' : 'Yeni Firma Ekle' }}"
            icon="bx-building"
            description="{{ isset($companies) ? $companies->name . ' firmasını düzenleyin' : 'Yeni bir firma ekleyin' }}"
            :backRoute="route('company.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Firma Bilgileri" icon="bx-building">
            <form action="{{route('company.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $companies->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-building me-1"></i>Firma Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $companies->name ?? '' }}"  
                           name="name" 
                           placeholder="Firma adını giriniz..."
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">
                        <i class="bx bx-phone me-1"></i>Firma Telefon
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="phone" 
                           value="{{ $companies->phone ?? '' }}"  
                           name="phone" 
                           placeholder="Telefon numarasını giriniz...">
                </div>
                
                <div class="form-group">
                    <label for="authorized" class="form-label">
                        <i class="bx bx-user me-1"></i>Firma Yetkili
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="authorized"  
                           value="{{ $companies->authorized ?? '' }}"  
                           name="authorized" 
                           placeholder="Yetkili kişi adını giriniz...">
                </div>

                <div class="form-actions">
                    <a href="{{route('company.index')}}" class="btn btn-outline-secondary">
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
