@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Role /</span> Role listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('role.create')}}" class="btn btn-primary float-end">Yeni Role Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Adı</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($roles as $role)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$role->name}}</strong></td>
                            <td>
                                <!-- a href="{{route('role.permission',['id' => $role->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a -->
                                <a href="{{route('role.delete',['id' => $role->id])}}"
                                   class="btn btn-icon btn-danger">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('role.edit',['id' => $role->id])}}"
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
