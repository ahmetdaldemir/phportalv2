@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Firmalar /</span> Şube listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('seller.create')}}" class="btn btn-primary float-end">Yeni Şube Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Şube Adı</th>
                        <th>Firma</th>
                        <th>Telefon</th>
                        <th>Kayıt Tarihi</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($sellers as $seller)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$seller->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$seller->company->name}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$seller->phone}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$seller->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('seller/update',{{$seller->id}},{{$seller->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$seller->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <!-- a href="{{route('seller.delete',['id' => $seller->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a -->
                                <a href="{{route('seller.edit',['id' => $seller->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection
