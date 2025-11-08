@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Firmalar /</span> Firma listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('company.create')}}" class="btn btn-primary float-end">Yeni Firma Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Firma Adı</th>
                        <th>Yetkili</th>
                        <th>Telefon</th>
                        <th>Kayıt Tarihi</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($companies as $category)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$category->name}}</strong></td>
                            <td>{{$category->authorized}}</td>
                            <td><span class="badge bg-label-primary me-1">{{$category->phone}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$category->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('company/update',{{$category->id}},{{$category->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$category->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <a href="{{route('company.delete',['id' => $category->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('company.edit',['id' => $category->id])}}"
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
