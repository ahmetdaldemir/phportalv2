@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($accounting_category) ? 'Muhasebe Kategorisi Düzenle' : 'Yeni Muhasebe Kategorisi Ekle' }}"
            icon="bx-calculator"
            description="{{ isset($accounting_category) ? $accounting_category->name . ' kategorisini düzenleyin' : 'Yeni bir muhasebe kategorisi ekleyin' }}"
            :backRoute="route('accounting_category.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Muhasebe Kategori Bilgileri" icon="bx-calculator">
            <form action="{{route('accounting_category.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $accounting_category->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="category" class="form-label">
                        <i class="bx bx-tag me-1"></i>Kategori Tipi
                        <span class="text-danger">*</span>
                    </label>
                    <select name="category" class="form-select" id="category" required>
                        <option value="gelir" {{ (isset($accounting_category) && $accounting_category->category == 'gelir') ? 'selected' : '' }}>Gelir</option>
                        <option value="gider" {{ (isset($accounting_category) && $accounting_category->category == 'gider') ? 'selected' : '' }}>Gider</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-calculator me-1"></i>Kategori Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $accounting_category->name ?? '' }}"  
                           name="name" 
                           placeholder="Kategori adını giriniz..."
                           required>
                </div>

                <div class="form-actions">
                    <a href="{{route('accounting_category.index')}}" class="btn btn-outline-secondary">
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
