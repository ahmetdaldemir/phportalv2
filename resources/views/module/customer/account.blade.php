@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Müşteriler /</span> Müşteri listesi</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{route('customer.create')}}" class="btn btn-primary float-end">Yeni Müşteri Ekle</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Müşteri Adı</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Kayıt Tarihi</th>
                            <th>Status</th>
                            <th><strong>Satış Yapma</strong></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($customers as $customer)
                        @if($customer->type == "account")
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$customer->fullname}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$customer->phone1}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$customer->email}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$customer->created_at}}</span></td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateStatus('customer/update',{{$customer->id}},{{$customer->is_status == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$customer->is_status == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateDanger('customer/updateDanger',{{$customer->id}},{{$customer->is_danger == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$customer->is_danger == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>
                                <a href="{{route('customer.delete',['id' => $customer->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('customer.edit',['id' => $customer->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>

                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection
