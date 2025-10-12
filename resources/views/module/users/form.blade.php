@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kullanıcı /</span> @if(isset($users))
                {{$users->name}}
            @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Kullanıcı Bilgileri</h5>
            <form action="{{route('user.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($users)) value="{{$users->id}}" @endif />
                <div class="card-body">
                    <div>
                        <label for="defaultFormControlInput" class="form-label">İsim Soyisim</label>
                        <input type="text" class="form-control" id="name" @if(isset($users)) value="{{$users->name}}"
                               @endif  name="name" aria-describedby="name">
                        <div id="name" class="form-text">
                            We'll never share your details with anyone else.
                        </div>
                    </div>
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" @if(isset($users)) value="{{$users->email}}"
                               @endif  name="email" aria-describedby="email">
                        <div id="email" class="form-text">
                            We'll never share your details with anyone else.
                        </div>
                    </div>
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Şifre</label>
                        <input type="text" class="form-control" id="password" name="password" aria-describedby="password">
                        <div id="password" class="form-text">
                            We'll never share your details with anyone else.
                        </div>
                    </div>
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Şube</label>
                        <select name="seller_id" class="form-select"  @if($edit == 1) disabled @endif >
                            @foreach($companys as $company)
                            <optgroup label="{{$company->name}}">
                                @foreach($sellers as $seller)
                                    @if($seller->company_id == $company->id)
                                    <option  @if(isset($users) && $seller->id == $users->seller_id) selected @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endif
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Firma</label>
                        <select name="company_id" class="form-control">
                            @foreach($companys as $company)
                                <option @if(isset($users) && $users->company_id == $company->id) selected @endif  value="{{$company->id}}">{{$company->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-control" id="role">
                            @foreach($roles as $role)
                                <option  @if(isset($users)) {{ $users->hasRole($role->name) ? 'selected' : '' }} @endif value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        <div id="seller_id" class="form-text">
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
