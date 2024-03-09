@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Ürün /</span> @if(isset($fakeproducts)) {{$fakeproducts->name}} @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Ürün Bilgileri</h5>
            <form action="{{route('fakeproduct.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($fakeproducts)) value="{{$fakeproducts->id}}" @endif />
            <div class="card-body">
                <div>
                    <label for="defaultFormControlInput" class="form-label">Ürün Adı</label>
                    <input type="text" class="form-control" id="name"  @if(isset($fakeproducts)) value="{{$fakeproducts->name}}" @endif  name="name" aria-describedby="name">
                </div>
                <hr class="my-5">
                <div>
                    <button type="submit" class="btn btn-danger btn-buy-now">Kaydet</button>
                </div>
            </div>
            </form>
        </div>
        <hr class="my-5">
    </div>
@endsection
