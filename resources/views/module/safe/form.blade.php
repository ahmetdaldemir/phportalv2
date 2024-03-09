@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kasalar /</span> @if(isset($safes)) {{$safes->name}} @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Kasa Bilgileri</h5>
            <form action="{{route('safe.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($safes)) value="{{$safes->id}}" @endif />
            <div class="card-body">
                <div>
                    <label for="defaultFormControlInput" class="form-label">Kasa Adı</label>
                    <input type="text" class="form-control" id="name"  @if(isset($safes)) value="{{$safes->name}}" @endif  name="name" aria-describedby="name">
                    <div id="name" class="form-text">
                        We'll never share your details with anyone else.
                    </div>
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
