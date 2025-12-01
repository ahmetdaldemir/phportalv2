@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Roller"
            :createRoute="route('role.create')"
            :count="$roles->count()"
            icon="bx-shield"
            description="Kullanıcı rolleri yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Rol Listesi'" :badge="$roles->count() . ' Rol'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-shield me-1"></i>Rol Adı</th>
                        <th style="width: 200px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-shield text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $role->name }}</div>
                                        <small class="text-muted">ID: {{ $role->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('role.edit',['id' => $role->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('role.delete',['id' => $role->id])}}"
                                       onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                       class="btn btn-outline-danger btn-sm" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-shield display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz rol bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk rolünüzü ekleyerek başlayın</p>
                                    <a href="{{route('role.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Rol Ekle
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
