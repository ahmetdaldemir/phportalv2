@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Teknik Sevis Kategorileri /</span> </h4>
        <div class="card  mb-4">
            <h5 class="card-header">Kategori Bilgileri</h5>
            <form action="{{route('technical_service.categorystore')}}" method="post">
                @csrf
             <div class="card-body">
                <div>
                    <label for="defaultFormControlInput" class="form-label">Kategori Adı</label>
                    <input type="text" class="form-control" id="name"  @if(isset($categories)) value="{{$categories->name}}" @endif  name="name" aria-describedby="name">
                </div>
                <div>
                    <label for="parent_id" class="form-label">Üst Kategori</label>
                    <select name="parent_id" class="form-control" required>
                       <option value=" ">Üst Kategori</option>
                       <option value="physically">FİZİKSEL DURUM</option>
                       <option value="accessory">AKSESUAR</option>
                       <option value="fault">ARIZA AÇIKLAMASI</option>
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
        <div class="card">
            <div class="card-body">
                <table class="table table-responsive">
                    <tr>
                        <th>Kategori Adı</th>
                        <th>Üst Kategori</th>
                        <th>İşlemler</th>
                     </tr>
                    @foreach($categories_all as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->parent_id}}</td>
                            <td><a href="{{route('technical_service.categorydelete',['id' => $item->id])}}" class="btn btn-danger">Sil</a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
