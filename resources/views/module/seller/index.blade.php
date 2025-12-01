@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Şubeler"
            :createRoute="route('seller.create')"
            :count="$sellers->count()"
            icon="bx-store"
            description="Şube ve bayi yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Şube Listesi'" :badge="$sellers->count() . ' Şube'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-store me-1"></i>Şube Adı</th>
                        <th><i class="bx bx-building me-1"></i>Firma</th>
                        <th><i class="bx bx-phone me-1"></i>Telefon</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 120px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sellers as $index => $seller)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-store text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $seller->name }}</div>
                                        <small class="text-muted">ID: {{ $seller->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $seller->company->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary status-badge">
                                    {{ $seller->phone ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $seller->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('seller/update',{{$seller->id}},{{$seller->is_status == 1 ? 0:1}})"
                                           id="status_{{ $seller->id }}" {{$seller->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $seller->id }}">
                                        {{ $seller->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('seller.edit',['id' => $seller->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-store display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz şube bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk şubenizi ekleyerek başlayın</p>
                                    <a href="{{route('seller.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Şube Ekle
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
