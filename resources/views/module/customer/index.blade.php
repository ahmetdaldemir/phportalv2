@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Standart Header Component -->
        <x-list-page.header 
            title="Müşteriler"
            :createRoute="route('customer.create')"
            :count="$customers->where('type', 'customer')->count()"
            icon="bx-user"
            description="Müşteri yönetimi ve bilgileri"
        />

        <!-- Standart Card Component -->
        <x-list-page.card :title="'Müşteri Listesi'" :badge="$customers->where('type', 'customer')->count() . ' Müşteri'">
            <x-list-page.table>
                <thead>
                    <tr>
                        <th style="width: 60px;"><i class="bx bx-hash me-1"></i>#</th>
                        <th><i class="bx bx-user me-1"></i>Müşteri Adı</th>
                        <th><i class="bx bx-phone me-1"></i>Telefon</th>
                        <th><i class="bx bx-envelope me-1"></i>Email</th>
                        <th style="width: 150px;"><i class="bx bx-calendar me-1"></i>Kayıt Tarihi</th>
                        <th style="width: 100px;"><i class="bx bx-check-circle me-1"></i>Durum</th>
                        <th style="width: 120px;"><i class="bx bx-shield me-1"></i>Satış Yapma</th>
                        <th style="width: 150px;"><i class="bx bx-cog me-1"></i>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $filteredCustomers = $customers->where('type', 'customer');
                        $index = 0;
                    @endphp
                    @forelse($filteredCustomers as $customer)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ ++$index }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="brand-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="bx bx-user text-white"></i>
                                    </div>
                                    <div>
                                        <div class="brand-name">{{ $customer->fullname }}</div>
                                        <small class="text-muted">ID: {{ $customer->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary status-badge">
                                    {{ $customer->phone1 ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-info status-badge">
                                    {{ $customer->email ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary status-badge">
                                    {{ $customer->created_at->format('d.m.Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('customer/update',{{$customer->id}},{{$customer->is_status == 1 ? 0:1}})"
                                           id="status_{{ $customer->id }}" {{$customer->is_status == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="status_{{ $customer->id }}">
                                        {{ $customer->is_status == 1 ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateDanger('customer/updateDanger',{{$customer->id}},{{$customer->is_danger == 1 ? 0:1}})"
                                           id="danger_{{ $customer->id }}" {{$customer->is_danger == 1 ? 'checked':''}} />
                                    <label class="form-check-label" for="danger_{{ $customer->id }}">
                                        {{ $customer->is_danger == 1 ? 'Yasaklı' : 'İzinli' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{route('customer.edit',['id' => $customer->id])}}"
                                       class="btn btn-outline-primary btn-sm" title="Düzenle">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <a href="{{route('customer.delete',['id' => $customer->id])}}"
                                       onclick="return confirm('Silmek istediğinizden emin misiniz?')"  
                                       class="btn btn-outline-danger btn-sm" title="Sil">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-user display-4 text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz müşteri bulunmuyor</h5>
                                    <p class="text-muted mb-3">İlk müşterinizi ekleyerek başlayın</p>
                                    <a href="{{route('customer.create')}}" class="btn btn-primary">
                                        <i class="bx bx-plus me-1"></i>
                                        Yeni Müşteri Ekle
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
