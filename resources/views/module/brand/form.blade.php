@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($brands) ? 'Marka Düzenle' : 'Yeni Marka Ekle' }}"
            icon="bx-purchase-tag"
            description="{{ isset($brands) ? $brands->name . ' markasını düzenleyin' : 'Yeni bir marka ekleyin' }}"
            :backRoute="route('brand.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Marka Bilgileri" icon="bx-purchase-tag">
            <form action="{{route('brand.store')}}" method="post" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="id" value="{{ $brands->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-purchase-tag me-1"></i>Marka Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $brands->name ?? '' }}"  
                           name="name" 
                           placeholder="Marka adını giriniz..."
                           required>
                    <div class="form-text">Marka adı benzersiz olmalıdır</div>
                </div>

                <div class="form-actions">
                    <a href="{{route('brand.index')}}" class="btn btn-outline-secondary">
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
