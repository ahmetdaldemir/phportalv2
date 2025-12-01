@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($roles) ? 'Rol Düzenle' : 'Yeni Rol Ekle' }}"
            icon="bx-shield"
            description="{{ isset($roles) ? $roles->name . ' rolünü düzenleyin' : 'Yeni bir rol ekleyin' }}"
            :backRoute="route('role.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Rol Bilgileri" icon="bx-shield">
            <form action="{{route('role.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $roles->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-shield me-1"></i>Rol Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $roles->name ?? '' }}"  
                           name="name" 
                           placeholder="Rol adını giriniz..."
                           required>
                </div>

                <div class="form-actions">
                    <a href="{{route('role.index')}}" class="btn btn-outline-secondary">
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
