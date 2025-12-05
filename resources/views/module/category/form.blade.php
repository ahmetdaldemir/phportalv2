@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
    <style>
        .tree {
            margin: 1rem 0;
            width: 100%;
            max-width: 600px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }

        .tree ul {
            display: none;
            margin: 4px auto;
            margin-left: 6px;
            border-left: 1px dashed #dfdfdf;
        }

        .tree li {
            padding: 12px 18px;
            cursor: pointer;
            vertical-align: middle;
            background: #fff;
            transition: background 0.2s;
        }

        .tree li:hover {
            background: #f8f9fa;
        }

        .tree li:first-child {
            border-radius: 12px 12px 0 0;
        }

        .tree li:last-child {
            border-radius: 0 0 12px 12px;
        }

        .tree .active,
        .active li {
            background: #f8f9fa;
        }

        .tree label {
            cursor: pointer;
            font-weight: 500;
            color: #495057;
        }

        .tree input[type=radio] {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .has > label {
            color: #2d3748;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($categories) ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' }}"
            icon="bx-category"
            description="{{ isset($categories) ? $categories->name . ' kategorisini düzenleyin' : 'Yeni bir kategori ekleyin' }}"
            :backRoute="route('category.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="Kategori Bilgileri" icon="bx-category">
            <form action="{{route('category.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $categories->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-category me-1"></i>Kategori Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"
                           value="{{ $categories->name ?? '' }}"  
                           name="name" 
                           placeholder="Kategori adını giriniz..."
                           required>
                </div>
                
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-layer me-1"></i>Üst Kategori Seçimi
                    </div>
                    <div class="form-group">
                        <ul class="tree">
                            <li class="has">
                                <input type="radio" 
                                       name="parent_id" 
                                       value="0" 
                                       id="parent_0"
                                       {{ (!isset($categories) || $categories->parent_id == 0) ? 'checked' : '' }}>
                                <label for="parent_0">Üst Kategori</label>
                            </li>
                            @foreach($categories_all as $category)
                                @if($category->parent_id == 0)
                                    <li class="has">
                                        <input type="radio" 
                                               name="parent_id" 
                                               value="{{$category->id}}" 
                                               id="parent_{{ $category->id }}"
                                               {{ (isset($categories) && $categories->parent_id == $category->id) ? 'checked' : '' }}>
                                        <label for="parent_{{ $category->id }}">{{$category->name}}</label>
                                        <ul>
                                            @foreach($categories_all as $categorya)
                                                @if($categorya->parent_id == $category->id)
                                                    <li>
                                                        <input type="radio" 
                                                               name="parent_id" 
                                                               value="{{$categorya->id}}" 
                                                               id="parent_{{ $categorya->id }}"
                                                               {{ (isset($categories) && $categories->parent_id == $categorya->id) ? 'checked' : '' }}>
                                                        <label for="parent_{{ $categorya->id }}">{{$categorya->name}}</label>
                                                    </li>
                                                    @foreach($categories_all as $categoryaa)
                                                        @if($categorya->id == $categoryaa->parent_id)
                                                            <li>
                                                                <div style="float: left; line-height: 1.5; margin-left: 11px; padding-right: 10px;">--</div>
                                                                <input type="radio" 
                                                                       name="parent_id" 
                                                                       value="{{$categoryaa->id}}" 
                                                                       id="parent_{{ $categoryaa->id }}"
                                                                       {{ (isset($categories) && $categories->parent_id == $categoryaa->id) ? 'checked' : '' }}>
                                                                <label for="parent_{{ $categoryaa->id }}">{{$categoryaa->name}}</label>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{route('category.index')}}" class="btn btn-outline-secondary">
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
    <script>
        $(document).on('click', '.tree label', function (e) {
            $(this).next('ul').fadeToggle();
            e.stopPropagation();
        });
    </script>
@endsection
