@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($fakeproducts) ? 'Sahte Ürün Düzenle' : 'Yeni Sahte Ürün Ekle' }}"
            icon="bx-package"
            description="{{ isset($fakeproducts) ? $fakeproducts->name . ' ürününü düzenleyin' : 'Yeni bir sahte ürün ekleyin' }}"
            :backRoute="route('fakeproduct.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Sahte Ürün Bilgileri" icon="bx-package">
            <form action="{{route('fakeproduct.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $fakeproducts->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-package me-1"></i>Ürün Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $fakeproducts->name ?? '' }}"  
                           name="name" 
                           placeholder="Ürün adını giriniz..."
                           required>
                </div>

                <div class="form-actions">
                    <a href="{{route('fakeproduct.index')}}" class="btn btn-outline-secondary">
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
