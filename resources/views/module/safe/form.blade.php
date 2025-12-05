@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($safes) ? 'Kasa Düzenle' : 'Yeni Kasa Ekle' }}"
            icon="bx-wallet"
            description="{{ isset($safes) ? $safes->name . ' kasasını düzenleyin' : 'Yeni bir kasa ekleyin' }}"
            :backRoute="route('safe.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Kasa Bilgileri" icon="bx-wallet">
            <form action="{{route('safe.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $safes->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-wallet me-1"></i>Kasa Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $safes->name ?? '' }}"  
                           name="name" 
                           placeholder="Kasa adını giriniz..."
                           required>
                </div>

                <div class="form-actions">
                    <a href="{{route('safe.index')}}" class="btn btn-outline-secondary">
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
