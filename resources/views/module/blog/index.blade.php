@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Bloglar"
            :createRoute="route('blog.create')"
            :count="$blogs ? $blogs->count() : 0"
            icon="bx-news"
            description="Blog yazıları yönetimi"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Blog Listesi'" :badge="($blogs ? $blogs->count() : 0) . ' Blog'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-news me-1"></i>Blog Başlığı</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @if($blogs && $blogs->count() > 0)
                        @foreach($blogs as $index => $blog)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="bx bx-news text-white"></i>
                                        </div>
                                        <div>
                                            <div class="brand-name">{{ $blog->title }}</div>
                                            <small class="text-muted">ID: {{ $blog->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary status-badge">
                                        {{ $blog->created_at->format('d.m.Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{route('blog.edit',['id' => $blog->id])}}"
                                           class="btn btn-outline-primary btn-sm" title="Düzenle">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                        <a href="{{route('blog.delete',['id' => $blog->id])}}"
                                           onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                           class="btn btn-outline-danger btn-sm" title="Sil">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-news display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz blog bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk blogunuzu ekleyerek başlayın</p>
                                    <a href="{{route('blog.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Blog Ekle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </x-list-page.table>
        </x-list-page.card>
    </div>
@endsection
