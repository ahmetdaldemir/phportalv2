@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($colors) ? 'Renk Düzenle' : 'Yeni Renk Ekle' }}"
            icon="bx-palette"
            description="{{ isset($colors) ? $colors->name . ' rengini düzenleyin' : 'Yeni bir renk ekleyin' }}"
            :backRoute="route('color.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Renk Bilgileri" icon="bx-palette">
            <form action="{{route('color.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $colors->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-palette me-1"></i>Renk Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $colors->name ?? '' }}"  
                           name="name" 
                           placeholder="Renk adını giriniz..."
                           required>
                </div>

                <div class="form-actions">
                    <a href="{{route('color.index')}}" class="btn btn-outline-secondary">
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
