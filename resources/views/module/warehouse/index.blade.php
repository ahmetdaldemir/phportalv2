@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Depolar"
            :createRoute="route('warehouse.create')"
            :count="$warehouses->count()"
            icon="bx-building"
            description="Depo ve şube yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Depo Listesi'" :badge="$warehouses->count() . ' Depo'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-building me-1"></i>Depo/Şube Adı</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $index => $warehouse)
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
                                        <div class="brand-name">{{ $warehouse->name }}</div>
                                        <small class="text-muted">ID: {{ $warehouse->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $warehouse->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('warehouse/update',{{$warehouse->id}},{{$warehouse->is_status == 1 ? 0:1}})"
                                           id="status_{{ $warehouse->id }}" {{$warehouse->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $warehouse->id }}">
                                        {{ $warehouse->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('warehouse.edit',['id' => $warehouse->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('warehouse.delete',['id' => $warehouse->id])}}"
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
                                    <i class="bx bx-building display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz depo bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk deponuzu ekleyerek başlayın</p>
                                    <a href="{{route('warehouse.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Depo Ekle
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
