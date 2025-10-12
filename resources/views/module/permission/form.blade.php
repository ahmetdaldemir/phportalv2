@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">permission'ler /</span> @if(isset($permissions))
                {{$permissions->name}}
            @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">permission Bilgileri</h5>
            <form action="{{route('permission.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($permissions)) value="{{$permissions->id}}" @endif />
                <div class="card-body">
                    <div>
                        <label for="defaultFormControlInput" class="form-label">İzin Adı</label>
                        <input type="text" class="form-control" id="name"
                               @if(isset($permissions)) value="{{$permissions->title}}" @endif  name="title"
                               aria-describedby="title">
                    </div>
                    <div>
                        <label for="defaultFormControlInput" class="form-label">İzin Kod</label>
                        <input type="text" class="form-control" id="name"
                               @if(isset($permissions)) value="{{$permissions->name}}" @endif  name="name"
                               aria-describedby="name">
                    </div>

                    <hr class="my-5">
                    <div class="row">
                        <div class="list-group">
                            @foreach($roles as $role)
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="checkbox" name="roles[]"
                                           value="{{$role->id}}">
                                    {{$role->name}}
                                </label>
                            @endforeach
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
