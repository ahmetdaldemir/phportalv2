@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Form Header Component -->
        <x-form-page.header 
            title="{{ isset($permissions) ? 'İzin Düzenle' : 'Yeni İzin Ekle' }}"
            icon="bx-key"
            description="{{ isset($permissions) ? $permissions->title . ' iznini düzenleyin' : 'Yeni bir izin ekleyin' }}"
            :backRoute="route('permission.index')"
        />

        <!-- Standart Form Card Component -->
        <x-form-page.card title="İzin Bilgileri" icon="bx-key">
            <form action="{{route('permission.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $permissions->id ?? '' }}" />
                
                <div class="form-group">
                    <label for="title" class="form-label">
                        <i class="bx bx-key me-1"></i>İzin Adı
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="title"
                           value="{{ $permissions->title ?? '' }}"  
                           name="title" 
                           placeholder="İzin adını giriniz..."
                           required>
                </div>
                
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bx bx-code me-1"></i>İzin Kodu
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="name"
                           value="{{ $permissions->name ?? '' }}"  
                           name="name" 
                           placeholder="İzin kodunu giriniz (örn: create-user)..."
                           required>
                    <div class="form-text">İzin kodu benzersiz olmalıdır</div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="bx bx-shield me-1"></i>Roller
                    </div>
                    <div class="form-group">
                        <div class="list-group">
                            @foreach($roles as $role)
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" 
                                           type="checkbox" 
                                           name="roles[]"
                                           value="{{$role->id}}"
                                           {{ (isset($permissions) && $permissions->roles->contains($role->id)) ? 'checked' : '' }}>
                                    {{$role->name}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{route('permission.index')}}" class="btn btn-outline-secondary">
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
