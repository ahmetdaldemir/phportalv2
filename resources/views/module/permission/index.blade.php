@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">permission /</span> permission listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('permission.create')}}" class="btn btn-primary float-end">Yeni permission Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Adı</th>
                        <th>Kod</th>
                        <th>Guard</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($permissions as $permission)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$permission->title}}</strong></td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$permission->name}}</strong></td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$permission->guard_name}}</strong></td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$permission->role}}</strong></td>
                            <td>
                                <a href="{{route('permission.delete',['id' => $permission->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('permission.edit',['id' => $permission->id])}}"
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
