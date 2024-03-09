@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Bankalar /</span> Banka listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('bank.create')}}" class="btn btn-primary float-end">Yeni Banka Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Banka Adı</th>
                        <th>Iban</th>
                        <th>Kayıt Tarihi</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($banks as $bank)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$bank->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$bank->iban}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$bank->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('bank/update',{{$bank->id}},{{$bank->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$bank->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <a href="{{route('bank.delete',['id' => $bank->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('bank.edit',['id' => $bank->id])}}"
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
