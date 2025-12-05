@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($sellers) ? 'Şube Düzenle' : 'Yeni Şube Ekle' }}"
            icon="bx-store"
            description="{{ isset($sellers) ? $sellers->name . ' şubesini düzenleyin' : 'Yeni bir şube ekleyin' }}"
            :backRoute="route('seller.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Şube Bilgileri" icon="bx-store">
            <form action="{{route('seller.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $sellers->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-store me-1"></i>Şube Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $sellers->name ?? '' }}"  
                           name="name" 
                           placeholder="Şube adını giriniz..."
                           required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">
                        <i class="bx bx-phone me-1"></i>Şube Telefon
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="phone" 
                           value="{{ $sellers->phone ?? '' }}"  
                           name="phone" 
                           placeholder="Telefon numarasını giriniz...">
                </div>
                
                <div class="form-group">
                    <label for="company_id" class="form-label">
                        <i class="bx bx-building me-1"></i>Firma
                        <span class="text-danger">*</span>
                    </label>
                    <select name="company_id" class="form-select" id="company_id" required>
                        <option value="">Firma Seçiniz</option>
                        @foreach($companys as $item)
                            <option value="{{$item->id}}" {{ (isset($sellers) && $sellers->company_id == $item->id) ? 'selected' : '' }}>
                                {{$item->name}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <a href="{{route('seller.index')}}" class="btn btn-outline-secondary">
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
