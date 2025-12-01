@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($warehouses) ? 'Depo Düzenle' : 'Yeni Depo Ekle' }}"
            icon="bx-package"
            description="{{ isset($warehouses) ? $warehouses->name . ' deposunu düzenleyin' : 'Yeni bir depo ekleyin' }}"
            :backRoute="route('warehouse.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Depo Bilgileri" icon="bx-package">
            <form action="{{route('warehouse.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $warehouses->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-package me-1"></i>Depo Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $warehouses->name ?? '' }}"  
                           name="name" 
                           placeholder="Depo adını giriniz..."
                           required>
                </div>
                
                <div class="form-group">
                    <label for="seller_id" class="form-label">
                        <i class="bx bx-store me-1"></i>Şube
                        <span class="text-danger">*</span>
                    </label>
                    <select name="seller_id" class="form-select" id="seller_id" required>
                        <option value="">Şube Seçiniz</option>
                        @foreach($sellers as $seller)
                            <option value="{{$seller->id}}" {{ (isset($warehouses) && $warehouses->hasSeller($seller->id)) ? 'selected' : '' }}>
                                {{$seller->name}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <a href="{{route('warehouse.index')}}" class="btn btn-outline-secondary">
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
