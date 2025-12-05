@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($blog) ? 'Blog Düzenle' : 'Yeni Blog Ekle' }}"
            icon="bx-news"
            description="{{ isset($blog) ? $blog->title . ' blog yazısını düzenleyin' : 'Yeni bir blog yazısı ekleyin' }}"
            :backRoute="route('blog.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Blog Bilgileri" icon="bx-news">
            <form action="{{route('blog.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $blog->id ?? '' }}" />
                
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="title" class="form-label">
                                <i class="bx bx-news me-1"></i>Blog Başlığı
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title"
                                   value="{{ $blog->title ?? '' }}"  
                                   name="title" 
                                   placeholder="Blog başlığını giriniz..."
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                <i class="bx bx-text me-1"></i>Blog Açıklama
                            </label>
                            <textarea class="form-control" 
                                      name="description" 
                                      rows="8" 
                                      id="description" 
                                      placeholder="Blog içeriğini giriniz...">{{ $blog->description ?? '' }}</textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="image" class="form-label">
                                <i class="bx bx-image me-1"></i>Blog Resmi
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            @if(isset($blog) && $blog->image)
                                <div class="form-text">Mevcut resim: {{ $blog->image }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="meta_title" class="form-label">
                                <i class="bx bx-code me-1"></i>Meta Title
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="meta_title"
                                   value="{{ $blog->meta_title ?? '' }}"  
                                   name="meta_title" 
                                   placeholder="SEO başlığı...">
                        </div>

                        <div class="form-group">
                            <label for="meta_description" class="form-label">
                                <i class="bx bx-info-circle me-1"></i>Meta Açıklama
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="meta_description"
                                   value="{{ $blog->meta_description ?? '' }}"  
                                   name="meta_description" 
                                   placeholder="SEO açıklaması...">
                        </div>
                        
                        <div class="form-group">
                            <label for="labels" class="form-label">
                                <i class="bx bx-tag me-1"></i>Meta Kelimeler
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="labels"
                                   value="{{ $blog->labels ?? '' }}"  
                                   name="labels" 
                                   placeholder="Anahtar kelimeler (virgülle ayırın)...">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{route('blog.index')}}" class="btn btn-outline-secondary">
                        <i class="bx bx-x me-1"></i>İptal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Kaydet
                    </button>
                </div>
            </form>
        </x-form-page.card>
    </div>
@endsection

@section('custom-js')
    <script src="https://cdn.tiny.cloud/1/oj6zyoqfb6eqi7142vqs78p5k23x3vdo28svzv867z9cd3fu/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->

    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });
    </script>

@endsection


