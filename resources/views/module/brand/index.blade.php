@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
    <style>
        /* Brand specific styles */
        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .brand-name {
            font-weight: 600;
            color: #2d3748;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.25rem;
        }
        
        .action-buttons .btn {
            padding: 0.375rem;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-switch .form-check-input {
            width: 2.5rem;
            height: 1.25rem;
        }
        
        .form-switch .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .professional-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.75rem;
        }
        
        .professional-table td {
            padding: 0.75rem;
            border-color: #f1f3f4;
        }
        
        .professional-table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="me-3">
                        <i class="bx bx-purchase-tag display-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-0" style="font-size: 1.5rem; font-weight: 600; color: white;">
                            <i class="bx bx-purchase-tag me-2"></i>
                            MARKA LİSTESİ
                        </h2>
                        <p class="mb-0" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Marka yönetimi ve düzenleme</p>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{route('brand.create')}}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>
                        Yeni Marka Ekle
                    </a>
                </div>
            </div>
        </div>

        <!-- Professional Card -->
        <div class="professional-card">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2 text-primary"></i>
                        Marka Listesi
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">{{ $brands->count() }} Marka</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table professional-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                                <th><i class="bx bx-purchase-tag me-1"></i>Marka Adı</th>
                                <th style="width: 120px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                                <th style="width: 100px;"><i class="bx bx-building me-1"></i>Firma</th>
                                <th style="width: 100px;"><i class="bx bx-cog me-1"></i>Teknik</th>
                                <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                                <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $index => $brand)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="brand-icon me-3">
                                                {{ strtoupper(substr($brand->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="brand-name">{{ $brand->name }}</div>
                                                <small class="text-muted">ID: {{ $brand->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary status-badge">
                                            {{ $brand->created_at->format('d.m.Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info status-badge">
                                            {{ $brand->company_id }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   onclick="updateTechnical('brand/technical',{{$brand->id}},{{$brand->technical == 1 ? 0:1}})"
                                                   id="technical_{{ $brand->id }}" {{$brand->technical == 1 ? 'checked':''}} />
                                            <label class="form-check-label" for="technical_{{ $brand->id }}">
                                                {{ $brand->technical == 1 ? 'Aktif' : 'Pasif' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   onclick="updateStatus('brand/update',{{$brand->id}},{{$brand->is_status == 1 ? 0:1}})"
                                                   id="status_{{ $brand->id }}" {{$brand->is_status == 1 ? 'checked':''}} />
                                            <label class="form-check-label" for="status_{{ $brand->id }}">
                                                {{ $brand->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{route('brand.edit',['id' => $brand->id])}}"
                                               class="btn btn-outline-primary btn-sm" title="Düzenle">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                            <a href="{{route('version.create',['id' => $brand->id])}}"
                                               class="btn btn-outline-success btn-sm" title="Model Ekle">
                                                <i class="bx bx-plus"></i>
                                            </a>
                                            <a href="{{route('brand.delete',['id' => $brand->id])}}"
                                               onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                               class="btn btn-outline-danger btn-sm" title="Sil">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-package display-4 text-muted mb-3"></i>
                                            <h5 class="text-muted">Henüz marka bulunmuyor</h5>
                                            <p class="text-muted mb-3">İlk markanızı ekleyerek başlayın</p>
                                            <a href="{{route('brand.create')}}" class="btn btn-primary">
                                                <i class="bx bx-plus me-1"></i>
                                                Yeni Marka Ekle
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
