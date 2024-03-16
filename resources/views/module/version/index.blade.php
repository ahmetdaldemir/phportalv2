@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Modeller /</span> Model listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('version.create')}}" class="btn btn-primary float-end">Yeni Model Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Model Adı</th>
                        <th>Resim</th>
                        <th>Kayıt Tarihi</th>
                        <th>Firma</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($versions as $version)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$version->name}}</strong></td>
                            <td><img src="{{$version->image}}"/></td>
                            <td><span class="badge bg-label-primary me-1">{{$version->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateTechnical('version/technical',{{$version->id}},{{$version->technical == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$version->technical == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('version/update',{{$version->id}},{{$version->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$version->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>{{$version->company_id}}</td>
                            <td>
                                <a href="{{route('version.delete',['id' => $version->id])}}"
                                   onclick="return confirm('Silmek istediğinizden eminmisiniz?')"  class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('version.edit',['id' => $version->id])}}"
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
