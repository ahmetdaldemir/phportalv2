@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Nedenler /</span> Neden listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('reason.create')}}" class="btn btn-primary float-end">Yeni Neden Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Neden Tipi</th>
                        <th>Neden Adı</th>
                        <th>Kayıt Tarihi</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($reasons as $reason)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{ \App\Models\Reason::ReasonList[$reason->type]}}</strong></td>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                <strong>{{$reason->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$reason->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('reason/update',{{$reason->id}},{{$reason->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$reason->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <a href="{{route('reason.delete',['id' => $reason->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('reason.edit',['id' => $reason->id])}}"
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
