@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($versions) ? 'Model Düzenle' : 'Yeni Model Ekle' }}"
            icon="bx-mobile-alt"
            description="{{ isset($versions) ? $versions->name . ' modelini düzenleyin' : 'Yeni bir model ekleyin' }}"
            :backRoute="route('version.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Model Bilgileri" icon="bx-mobile-alt">
            <form action="{{route('version.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $versions->id ?? '' }}" />
                <input type="hidden" name="brand_id" value="{{ $versions->brand_id ?? $brand_id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-mobile-alt me-1"></i>Model Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"
                           value="{{ $versions->name ?? '' }}"  
                           name="name" 
                           placeholder="Model adını giriniz..."
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="bx bx-image me-1"></i>Model Resmi
                    </label>
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                            src="{{ isset($versions) && $versions->image ? $versions->image : asset('assets/img/placeholder.png') }}"
                            alt="model-image"
                            class="d-block rounded"
                            height="100"
                            width="100"
                            id="uploadedAvatar"
                            style="object-fit: cover; border: 2px solid #e9ecef;"
                        />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Yeni Resim</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input
                                    type="file"
                                    id="upload"
                                    name="image"
                                    class="account-file-input"
                                    hidden
                                    accept="image/png, image/jpeg"
                                />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Yenile</span>
                            </button>
                            <p class="text-muted mb-0">JPG, GIF veya PNG formatında. Maksimum 800KB</p>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{route('version.index')}}" class="btn btn-outline-secondary">
                        <i class="bx bx-x me-1"></i>İptal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Kaydet
                    </button>
                </div>
            </form>
        </x-form-page.card>

        @if(isset($versionlist) && $versionlist->count() > 0)
        <!-- Model Listesi -->
        <x-list-page.card :title="'Model Listesi'" :badge="$versionlist->count() . ' Model'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-package me-1"></i>Marka</th>
                        <th><i class="bx bx-mobile-alt me-1"></i>Model</th>
                        <th style="width: 100px;"><i class="bx bx-cog me-1"></i>Teknik</th>
                        <th style="width: 100px;"><i class="bx bx-building me-1"></i>Firma</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($versionlist as $index => $version)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $version->brand->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="brand-name">{{ $version->name }}</div>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateTechnical('technical',{{$version->id}},{{$version->technical == 1 ? 0:1}})"
                                           id="technical_list_{{ $version->id }}" {{$version->technical == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="technical_list_{{ $version->id }}">
                                        {{ $version->technical == 1 ? 'Evet' : 'Hayır' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary status-badge">
                                    {{ $version->company_id }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('version.edit',['id' => $version->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('version.delete',['id' => $version->id])}}"
                                       onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                       class="btn btn-outline-danger btn-sm" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-list-page.table>
        </x-list-page.card>
        @endif
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection
