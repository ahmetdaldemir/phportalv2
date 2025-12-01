@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Sahte Ürünler"
            :createRoute="route('fakeproduct.create')"
            :count="$fakeproducts->count()"
            icon="bx-package"
            description="Sahte ürün yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Sahte Ürün Listesi'" :badge="$fakeproducts->count() . ' Ürün'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-package me-1"></i>Ürün Adı</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakeproducts as $index => $fakeproduct)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-package text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $fakeproduct->name }}</div>
                                        <small class="text-muted">ID: {{ $fakeproduct->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $fakeproduct->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('fakeproduct/update',{{$fakeproduct->id}},{{$fakeproduct->is_status == 1 ? 0:1}})"
                                           id="status_{{ $fakeproduct->id }}" {{$fakeproduct->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $fakeproduct->id }}">
                                        {{ $fakeproduct->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('fakeproduct.edit',['id' => $fakeproduct->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('fakeproduct.delete',['id' => $fakeproduct->id])}}"
                                       onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                       class="btn btn-outline-danger btn-sm" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-package display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz sahte ürün bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk sahte ürününüzü ekleyerek başlayın</p>
                                    <a href="{{route('fakeproduct.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Ürün Ekle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-list-page.table>
        </x-list-page.card>
    </div>
@endsection
