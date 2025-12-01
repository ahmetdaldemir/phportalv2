@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Nedenler"
            :createRoute="route('reason.create')"
            :count="$reasons->count()"
            icon="bx-info-circle"
            description="İşlem nedenleri yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Neden Listesi'" :badge="$reasons->count() . ' Neden'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-tag me-1"></i>Neden Tipi</th>
                        <th><i class="bx bx-info-circle me-1"></i>Neden Adı</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reasons as $index => $reason)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-warning status-badge">
                                    {{ \App\Models\Reason::ReasonList[$reason->type] ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-info-circle text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $reason->name }}</div>
                                        <small class="text-muted">ID: {{ $reason->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $reason->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('reason/update',{{$reason->id}},{{$reason->is_status == 1 ? 0:1}})"
                                           id="status_{{ $reason->id }}" {{$reason->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $reason->id }}">
                                        {{ $reason->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('reason.edit',['id' => $reason->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('reason.delete',['id' => $reason->id])}}"
                                       onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                       class="btn btn-outline-danger btn-sm" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-info-circle display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz neden bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk nedeninizi ekleyerek başlayın</p>
                                    <a href="{{route('reason.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Neden Ekle
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
