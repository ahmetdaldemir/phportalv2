@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($banks) ? 'Banka Düzenle' : 'Yeni Banka Ekle' }}"
            icon="bx-credit-card"
            description="{{ isset($banks) ? $banks->name . ' bankasını düzenleyin' : 'Yeni bir banka ekleyin' }}"
            :backRoute="route('bank.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Banka Bilgileri" icon="bx-credit-card">
            <form action="{{route('bank.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $banks->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-credit-card me-1"></i>Banka Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"  
                           value="{{ $banks->name ?? '' }}"  
                           name="name" 
                           placeholder="Banka adını giriniz..."
                           required>
                </div>
                
                <div class="form-group">
                    <label for="iban" class="form-label">
                        <i class="bx bx-barcode me-1"></i>IBAN
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="iban" 
                           value="{{ $banks->iban ?? '' }}"  
                           name="iban" 
                           placeholder="IBAN numarasını giriniz...">
                    <div class="form-text">IBAN formatı: TR00 0000 0000 0000 0000 0000 00</div>
                </div>

                <div class="form-actions">
                    <a href="{{route('bank.index')}}" class="btn btn-outline-secondary">
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
