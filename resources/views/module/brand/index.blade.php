@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Markalar /</span> Marka listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('brand.create')}}" class="btn btn-primary float-end">Yeni Marka Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Marka Adı</th>
                        <th>Kayıt Tarihi</th>
                        <th>Firma</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($brands as $brand)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$brand->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$brand->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateTechnical('brand/technical',{{$brand->id}},{{$brand->technical == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$brand->technical == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>{{$brand->company_id}}</td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('brand/update',{{$brand->id}},{{$brand->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$brand->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <a href="{{route('brand.delete',['id' => $brand->id])}}"
                                   onclick="return confirm('Silmek istediğinizden eminmisiniz?')"  class="btn btn-icon btn-danger">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('brand.edit',['id' => $brand->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                                <a href="{{route('version.create',['id' => $brand->id])}}"
                                   class="btn btn-icon btn-success">
                                    <span class="bx bxl-ok-ru"></span>
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
