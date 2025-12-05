@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Kategoriler"
            :createRoute="route('category.create')"
            :count="$categories->count()"
            icon="bx-category"
            description="Kategori yönetimi ve düzenleme"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Kategori Listesi'" :badge="$categories->count() . ' Kategori'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-category me-1"></i>Kategori Adı</th>
                        <th><i class="bx bx-layer me-1"></i>Üst Kategori</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        @if(\Illuminate\Support\Facades\Auth::user()->company_id == 1)
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-category text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $category->name }}</div>
                                        <small class="text-muted">ID: {{ $category->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $category->parent_id == 0 ? 'Ana Kategori' : ($category->parentName->name ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $category->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            @if(\Illuminate\Support\Facades\Auth::user()->company_id == 1)
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('category/update',{{$category->id}},{{$category->is_status == 1 ? 0:1}})"
                                           id="status_{{ $category->id }}" {{$category->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $category->id }}">
                                        {{ $category->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ \Illuminate\Support\Facades\Auth::user()->company_id == 1 ? '5' : '4' }}" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-category display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz kategori bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk kategorinizi ekleyerek başlayın</p>
                                    <a href="{{route('category.create')}}" class="btn btn-primary">
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
