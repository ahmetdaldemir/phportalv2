@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Şubeler /</span> @if(isset($sellers)) {{$sellers->name}} @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Firma Bilgileri</h5>
            <form action="{{route('seller.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($sellers)) value="{{$sellers->id}}" @endif />
            <div class="card-body">
                <div>
                    <label for="defaultFormControlInput" class="form-label">Şube Adı</label>
                    <input type="text" class="form-control" id="name"  @if(isset($sellers)) value="{{$sellers->name}}" @endif  name="name" aria-describedby="name">
                    <div id="name" class="form-text">
                        We'll never share your details with anyone else.
                    </div>
                </div>
                <div>
                    <label for="defaultFormControlInput" class="form-label">Şube Telefon</label>
                    <input type="text" class="form-control" id="phone" @if(isset($sellers)) value="{{$sellers->phone}}" @endif  name="phone" aria-describedby="phone">
                    <div id="phone" class="form-text">
                        We'll never share your details with anyone else.
                    </div>
                </div>
                <div>
                    <label for="defaultFormControlInput" class="form-label">Firma</label>
                    <select  name="company_id" class="form-select">
                        @foreach($companys as $item)
                                <option  @if(isset($sellers) && $sellers->company_id == $item->id) selected @endif  value="{{$item->id}}">{{$item->name}}</option>
                         @endforeach
                    </select>

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
