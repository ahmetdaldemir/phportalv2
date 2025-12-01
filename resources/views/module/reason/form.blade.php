@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($reasons) ? 'Neden Düzenle' : 'Yeni Neden Ekle' }}"
            icon="bx-info-circle"
            description="{{ isset($reasons) ? $reasons->name . ' nedenini düzenleyin' : 'Yeni bir neden ekleyin' }}"
            :backRoute="route('reason.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Neden Bilgileri" icon="bx-info-circle">
            <form action="{{route('reason.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $reasons->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="type" class="form-label">
                        <i class="bx bx-tag me-1"></i>Neden Tipi
                        <span class="text-danger">*</span>
                    </label>
                    <select name="type" class="form-select" id="type" required>
                        <option value="">Tip Seçiniz</option>
                        <option value="1" {{ (isset($reasons) && $reasons->type == 1) ? 'selected' : '' }}>İPTAL</option>
                        <option value="2" {{ (isset($reasons) && $reasons->type == 2) ? 'selected' : '' }}>İADE</option>
                        <option value="3" {{ (isset($reasons) && $reasons->type == 3) ? 'selected' : '' }}>SATIŞ</option>
                        <option value="4" {{ (isset($reasons) && $reasons->type == 4) ? 'selected' : '' }}>TEKNİK SERVİS</option>
                        <option value="5" {{ (isset($reasons) && $reasons->type == 5) ? 'selected' : '' }}>ALIŞ</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-info-circle me-1"></i>Neden Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $reasons->name ?? '' }}"  
                           name="name" 
                           placeholder="Neden adını giriniz..."
                           required>
                </div>

                <div class="form-actions">
                    <a href="{{route('reason.index')}}" class="btn btn-outline-secondary">
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
