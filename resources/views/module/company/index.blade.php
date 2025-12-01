@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Firmalar"
            :createRoute="route('company.create')"
            :count="$companies->count()"
            icon="bx-building"
            description="Firma ve şirket yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Firma Listesi'" :badge="$companies->count() . ' Firma'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-building me-1"></i>Firma Adı</th>
                        <th><i class="bx bx-user me-1"></i>Yetkili</th>
                        <th><i class="bx bx-phone me-1"></i>Telefon</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $index => $company)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-building text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $company->name }}</div>
                                        <small class="text-muted">ID: {{ $company->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $company->authorized ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary status-badge">
                                    {{ $company->phone ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $company->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('company/update',{{$company->id}},{{$company->is_status == 1 ? 0:1}})"
                                           id="status_{{ $company->id }}" {{$company->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $company->id }}">
                                        {{ $company->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('company.edit',['id' => $company->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('company.delete',['id' => $company->id])}}"
                                       onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                       class="btn btn-outline-danger btn-sm" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-building display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz firma bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk firmanızı ekleyerek başlayın</p>
                                    <a href="{{route('company.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Firma Ekle
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
