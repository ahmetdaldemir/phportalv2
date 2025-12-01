@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Muhasebe Kategorileri"
            :createRoute="route('accounting_category.create')"
            :count="$accounting_categories->count()"
            icon="bx-calculator"
            description="Muhasebe kategorileri yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Muhasebe Kategori Listesi'" :badge="$accounting_categories->count() . ' Kategori'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-tag me-1"></i>Kategori Tipi</th>
                        <th><i class="bx bx-calculator me-1"></i>Kategori Adı</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounting_categories as $index => $accounting_category)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $accounting_category->category == 'gelir' ? 'bg-success' : 'bg-danger' }} status-badge">
                                    {{ $accounting_category->category == 'gelir' ? 'Gelir' : 'Gider' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-calculator text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $accounting_category->name }}</div>
                                        <small class="text-muted">ID: {{ $accounting_category->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $accounting_category->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('accounting_category/update',{{$accounting_category->id}},{{$accounting_category->is_status == 1 ? 0:1}})"
                                           id="status_{{ $accounting_category->id }}" {{$accounting_category->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $accounting_category->id }}">
                                        {{ $accounting_category->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('accounting_category.edit',['id' => $accounting_category->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('accounting_category.delete',['id' => $accounting_category->id])}}"
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
                                    <i class="bx bx-calculator display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz muhasebe kategorisi bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk kategorinizi ekleyerek başlayın</p>
                                    <a href="{{route('accounting_category.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Kategori Ekle
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
