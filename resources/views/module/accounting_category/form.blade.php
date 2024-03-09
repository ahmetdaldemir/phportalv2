@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Muhasebe Kategorileri /</span> @if(isset($accounting_category)) {{$accounting_category->name}} @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Kategori Adı</h5>
            <form action="{{route('accounting_category.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($accounting_category)) value="{{$accounting_category->id}}" @endif />
            <div class="card-body">
                <div>
                    <label for="category" class="form-label">Kategori</label>
                    <select name="category" class="form-control">
                        <option value="gelir">Gelir</option>
                        <option value="gider">Gider</option>

                    </select>
                    <div id="category" class="form-text">
                        We'll never share your details with anyone else.
                    </div>
                </div>
                <div>
                    <label for="defaultFormControlInput" class="form-label">Adı</label>
                    <input type="text" class="form-control" id="name"  @if(isset($accounting_category)) value="{{$accounting_category->name}}" @endif  name="name" aria-describedby="name">
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
