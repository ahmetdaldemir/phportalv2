@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Modeller"
            :createRoute="route('version.create')"
            :count="$versions->count()"
            icon="bx-mobile-alt"
            description="Telefon model yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Model Listesi'" :badge="$versions->count() . ' Model'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-mobile-alt me-1"></i>Model Adı</th>
                        <th style="width: 100px;"><i class="bx bx-image me-1"></i>Resim</th>
                        <th style="width: 100px;"><i class="bx bx-cog me-1"></i>Teknik</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($versions as $index => $version)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-mobile-alt text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $version->name }}</div>
                                        <small class="text-muted">ID: {{ $version->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($version->image)
                                    <img src="{{ $version->image }}" alt="{{ $version->name }}" style="max-width: 60px; max-height: 60px; border-radius: 8px;">
                                @else
                                    <span class="badge bg-label-secondary">Resim Yok</span>
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateTechnical('version/technical',{{$version->id}},{{$version->technical == 1 ? 0:1}})"
                                           id="technical_{{ $version->id }}" {{$version->technical == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="technical_{{ $version->id }}">
                                        {{ $version->technical == 1 ? 'Evet' : 'Hayır' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $version->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('version/update',{{$version->id}},{{$version->is_status == 1 ? 0:1}})"
                                           id="status_{{ $version->id }}" {{$version->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $version->id }}">
                                        {{ $version->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
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
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-mobile-alt display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz model bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk modelinizi ekleyerek başlayın</p>
                                    <a href="{{route('version.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Model Ekle
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
