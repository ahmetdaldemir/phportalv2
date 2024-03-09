@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Sahte Ürünler /</span> Ürün listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('fakeproduct.create')}}" class="btn btn-primary float-end">Yeni Ürün Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Ürün Adı</th>
                        <th>Kayıt Tarihi</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($fakeproducts as $fakeproduct)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$fakeproduct->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$fakeproduct->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('fakeproduct/update',{{$fakeproduct->id}},{{$fakeproduct->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$fakeproduct->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <a href="{{route('fakeproduct.delete',['id' => $fakeproduct->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('fakeproduct.edit',['id' => $fakeproduct->id])}}"
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
