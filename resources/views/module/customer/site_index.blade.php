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
                        <th>Adres</th>
                        <th>Detay</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($customers as $customer)
                             <tr>
                                <td><a href="{{route('site_customer.detail',['id' => $customer->id])}}"><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$customer->fullname}}</strong></a></td>
                                <td><span class="badge bg-label-primary me-1">{{$customer->phone1}}</span></td>
                                <td><span class="badge bg-label-primary me-1">{{$customer->email}}</span></td>
                                <td><span class="badge bg-label-primary me-1">{{$customer->address}}</span></td>
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
                     @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection
