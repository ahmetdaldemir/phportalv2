@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="İzinler"
            :createRoute="route('permission.create')"
            :count="$permissions->count()"
            icon="bx-key"
            description="Sistem izinleri yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'İzin Listesi'" :badge="$permissions->count() . ' İzin'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-key me-1"></i>İzin Adı</th>
                        <th><i class="bx bx-code me-1"></i>Kod</th>
                        <th><i class="bx bx-shield me-1"></i>Guard</th>
                        <th><i class="bx bx-user me-1"></i>Rol</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $index => $permission)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-key text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $permission->title ?? 'N/A' }}</div>
                                        <small class="text-muted">ID: {{ $permission->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-compact">{{ $permission->name ?? 'N/A' }}</code>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $permission->guard_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-warning status-badge">
                                    {{ $permission->role ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('permission.edit',['id' => $permission->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('permission.delete',['id' => $permission->id])}}"
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
                                    <i class="bx bx-key display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz izin bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk izninizi ekleyerek başlayın</p>
                                    <a href="{{route('permission.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni İzin Ekle
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
