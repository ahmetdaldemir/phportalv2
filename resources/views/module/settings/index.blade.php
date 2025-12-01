@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Ayarlar"
            :createRoute="null"
            :count="$settings->count()"
            icon="bx-cog"
            description="Sistem ayarları yönetimi"
        />

        <!-- Ayarları Güncelleme Formu -->
        <x-form-page.card title="Mevcut Ayarlar" icon="bx-cog">
            <form method="post" action="{{route('settings.update')}}">
                @csrf
                <div class="row g-3">

                    @foreach($settings as $setting)
                        <div class="form-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-section-title mb-0">
                                    <i class="bx bx-cog me-1"></i>{{$setting->display_name}}
                                </div>
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="deleteSetting({{$setting->id}}, '{{$setting->category}}.{{$setting->key}}', '{{$setting->display_name}}')"
                                        title="Sil">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                            <code class="d-block mb-3 text-muted">setting('{{$setting->category}}.{{$setting->key}}')</code>
                            
                            <div class="row g-3">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        @if($setting->type == "text_area")
                                            <textarea class="form-control"
                                                      name="{{$setting->category}}.{{$setting->key}}"
                                                      rows="3"
                                                      placeholder="Değer giriniz...">{{$setting->value}}</textarea>
                                        @elseif($setting->type == "image")
                                            <input type="file" 
                                                   class="form-control"
                                                   name="{{$setting->category}}.{{$setting->key}}"
                                                   accept="image/*">
                                            @if($setting->value)
                                                <div class="form-text">Mevcut: {{$setting->value}}</div>
                                            @endif
                                        @else
                                            <input type="text" 
                                                   class="form-control"
                                                   name="{{$setting->category}}.{{$setting->key}}" 
                                                   value="{{$setting->value}}"
                                                   placeholder="Değer giriniz...">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Kategori</label>
                                        <select class="form-select" name="{{$setting->category}}.category">
                                            <option value="site" {{ $setting->category == 'site' ? 'selected' : '' }}>Site</option>
                                            <option value="admin" {{ $setting->category == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="sms" {{ $setting->category == 'sms' ? 'selected' : '' }}>SMS</option>
                                            <option value="phone" {{ $setting->category == 'phone' ? 'selected' : '' }}>Telefon</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Ayarları Kaydet
                    </button>
                </div>
            </form>
        </x-form-page.card>

        <!-- Yeni Ayar Ekleme Formu -->
        <x-form-page.card title="Yeni Ayar Ekle" icon="bx-plus-circle">
            <form method="post" action="{{route('settings.store')}}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label" for="key">
                                <i class="bx bx-key me-1"></i>Key
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="key" 
                                   class="form-control" 
                                   name="key"
                                   placeholder="Ayar anahtarı..."
                                   required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label" for="display_name">
                                <i class="bx bx-text me-1"></i>Görünen Ad
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="display_name" 
                                   class="form-control" 
                                   name="display_name"
                                   placeholder="Ayar görünen adı..."
                                   required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label" for="type">
                                <i class="bx bx-list-ul me-1"></i>Tip
                                <span class="text-danger">*</span>
                            </label>
                            <select name="type" class="form-select" required>
                                <option value="">Tip Seçiniz</option>
                                <option value="text">Text Box</option>
                                <option value="text_area">Text Area</option>
                                <option value="rich_text_box">Rich Textbox</option>
                                <option value="markdown_editor">Markdown Editor</option>
                                <option value="code_editor">Code Editor</option>
                                <option value="checkbox">Check Box</option>
                                <option value="radio_btn">Radio Button</option>
                                <option value="select_dropdown">Select Dropdown</option>
                                <option value="file">File</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label" for="category">
                                <i class="bx bx-category me-1"></i>Kategori
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="category" required>
                                <option value="sms">SMS</option>
                                <option value="site">Site</option>
                                <option value="admin">Admin</option>
                                <option value="phone">Telefon</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-plus me-1"></i>Ekle
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </x-form-page.card>
    </div>
@endsection

@section('custom-js')
    <script>
        function deleteSetting(id, key, name) {
            if (confirm('"' + name + '" ayarını silmek istediğinizden emin misiniz?')) {
                // Silme işlemi için AJAX çağrısı yapılabilir
                window.location.href = '/settings/delete?id=' + id;
            }
        }
    </script>
@endsection
