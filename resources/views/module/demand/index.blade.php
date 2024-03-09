@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Talepler /</span> Talep listesi</h4>
        <div class="card">
            <div class="card-header">

                <div class="btn-group demo-inline-spacing float-end">
                    <a href="{{route('demand.print')}}" class="btn btn-primary float-end">Talepleri Yazdır</a>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>Stok Adı</th>
                        <th>Renk</th>
                        <th>Açıklama</th>
                        <th>Kayıt Tarihi</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($demands as $demand)
                             <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$demand->stock->name}}</strong></td>
                                <td><span class="badge bg-label-primary me-1">{{$demand->color->name}}</span></td>
                                <td><span class="badge bg-label-primary me-1">{{$demand->description}}</span></td>
                                <td><span class="badge bg-label-primary me-1">{{$demand->created_at}}</span></td>
                                <td>
                                    <div class="form-check form-switch mb-2">
                                        @if($demand->is_status == 0)
                                        <a href="{{route('demand.status',['id' => $demand->id])}}" class="btn btn-sm btn-success"><i class="bx bxl-ok-ru"></i> </a>
                                        @else
                                            Tamamlandı
                                        @endif
                                    </div>
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
