@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Talepler"
            :createRoute="null"
            :count="$demands->count()"
            icon="bx-clipboard"
            description="Stok talepleri yönetimi"
        >
            <div class="btn-group demo-inline-spacing">
                <a href="{{route('demand.print')}}" class="btn btn-primary">
                    <i class="bx bx-printer me-1"></i>Talepleri Yazdır
                </a>
            </div>
        </x-list-page.header>

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Talep Listesi'" :badge="$demands->count() . ' Talep'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-package me-1"></i>Stok Adı</th>
                        <th><i class="bx bx-palette me-1"></i>Renk</th>
                        <th><i class="bx bx-info-circle me-1"></i>Açıklama</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 120px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 120px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($demands as $index => $demand)
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
                                        <div class="brand-name">{{ $demand->stock->name ?? 'N/A' }}</div>
                                        <small class="text-muted">ID: {{ $demand->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $demand->color->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary status-badge">
                                    {{ $demand->description ?? 'Açıklama yok' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $demand->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                @if($demand->is_status == 0)
                                    <span class="badge bg-warning">Beklemede</span>
                                @else
                                    <span class="badge bg-success">Tamamlandı</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($demand->is_status == 0)
                                        <a href="{{route('demand.status',['id' => $demand->id])}}" 
                                           class="btn btn-outline-success btn-sm" 
                                           title="Tamamla"
                                           onclick="return confirm('Talebi tamamlamak istediğinizden emin misiniz?')">
                                            <i class="bx bx-check"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-clipboard display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz talep bulunmuyor</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-list-page.table>
        </x-list-page.card>
    </div>
@endsection
