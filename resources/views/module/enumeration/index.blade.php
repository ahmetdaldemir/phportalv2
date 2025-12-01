@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/form-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Sayımlar"
            :createRoute="null"
            :count="$enumerations->count()"
            icon="bx-clipboard"
            description="Stok sayım işlemleri yönetimi"
        />

        @if($errors->any())
            <div class="alert alert-warning mb-4">
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </div>
        @endif

        <!-- Yeni Sayım Başlatma Formu -->
        <x-form-page.card title="Yeni Sayım Başlat" icon="bx-play-circle">
            <form method="post" action="{{route('enumeration.store')}}" id="startTraking">
                @csrf
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="seller_id" class="form-label">
                                <i class="bx bx-store me-1"></i>Şube
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="seller_id" name="seller_id" required>
                                <option value="">Şube Seçiniz</option>
                                @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}">{{$seller->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" id="stockTrakingButton" class="btn btn-primary w-100">
                                <i class="bx bx-play me-1"></i>Başlat
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </x-form-page.card>

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Sayım Listesi'" :badge="$enumerations->count() . ' Sayım'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-store me-1"></i>Bayi Adı</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Başlangıç Tarihi</th>
                        <th style="width: 150px;"><i class="bx bx-calendar-check me-1"></i>Bitiş Tarihi</th>
                        <th style="width: 200px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enumerations as $index => $enumeration)
                        <tr class="{{ $enumeration->finish_date == NULL ? 'table-success' : '' }}">
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-store text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name {{ $enumeration->finish_date == NULL ? 'text-white' : '' }}">
                                            {{ $sellers->find($enumeration->seller_id)->name ?? 'N/A' }}
                                        </div>
                                        <small class="{{ $enumeration->finish_date == NULL ? 'text-white' : 'text-muted' }}">
                                            ID: {{ $enumeration->id }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $enumeration->start_date ? \Carbon\Carbon::parse($enumeration->start_date)->format('d.m.Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($enumeration->finish_date)
                                    <span class="badge bg-label-success status-badge">
                                        {{ \Carbon\Carbon::parse($enumeration->finish_date)->format('d.m.Y') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning status-badge">
                                        <i class="bx bx-time me-1"></i>Devam Ediyor
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('enumeration.stocktracking',['id' => $enumeration->id])}}" 
                                       class="btn btn-outline-success btn-sm" 
                                       title="Sayıma Git">
                                        <i class="bx bx-clipboard me-1"></i>Sayıma Git
                                    </a>
                                    <a href="{{route('enumeration.newPrint',['id' => $enumeration->id])}}" 
                                       class="btn btn-outline-info btn-sm" 
                                       title="İncele">
                                        <i class="bx bx-show me-1"></i>İncele
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-clipboard display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz sayım bulunmuyor</h5>
                                    <p class="text-muted mb-3">Yeni bir sayım başlatarak başlayın</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-list-page.table>
        </x-list-page.card>
    </div>
@endsection
