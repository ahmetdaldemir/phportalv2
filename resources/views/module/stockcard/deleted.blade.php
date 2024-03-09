@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kartları /</span> Silinen Stok Kart Hareketleri</h4>

        <hr class="my-5">


        <div class="card">

            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Stok Adı</th>
                        <th>SKU</th>
                        <th>Barkod</th>
                        <th>Serial</th>
                        <th>Adet</th>
                        <th>Şube</th>
                        <th>Personel</th>
                         <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($stockCardMovement as $movement)
                        <tr>
                            <td>{{$movement->id}}</td>
                            <td>{{$movement->stock->name??"Stok Kaydı Silinmiş"}}</td>
                            <td>
                                @if(!empty($movement->stock->category))
                                {{$movement->categorySeperator($movement->testParent($movement->stock->category->id))}}
                                @endif
                            </td>
                            <td>{{$movement->stock->barcode??"Stok Kaydı Silinmiş"}}</td>
                            <td>{{$movement->serial_number??"Stok Kaydı Silinmiş"}}</td>
                            <td>{{$movement->quantity}}</td>
                            <td>{{$movement->seller->name??"Şube Kaydı Silinmiş"}}</td>
                            <td>{{$movement->deletedUser()}}</td>
                             <td>{{$movement->deleted_at}}</td>
                            <td>
                                <a href="#"class="btn btn-icon btn-success">Geri Al</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {!! $stockCardMovement->links() !!}
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('assets/js/forms-extras.js')}}"></script>
@endsection
