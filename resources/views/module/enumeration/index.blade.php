@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Markalar /</span> Marka listesi</h4>
        <div class="card">
            <div class="card-header border-b-4" style="border-bottom: 8px solid #f5f5f9;">
                @if($errors->any())
                    <div class="alert alert-warning">
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="card-header">
                <form class="form-group" id="startTraking" method="post" action="{{route('enumeration.store')}}">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-9">
                                <label for="exampleInputPassword1">Şube</label>
                                <select class="form-select" id="seller_id" name="seller_id">
                                    @foreach($sellers as $seller)
                                        <option value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" id="stockTrakingButton" class="btn btn-primary w-100 mt-3">Baslat</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Bayi Adı</th>
                        <th>Baslama Tarihi</th>
                        <th>Bitis Tarihi</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($enumerations as $enumeration)
                        <tr @if($enumeration->finish_date == NULL) style="background: #1eb48e" @endif>
                            <td @if($enumeration->finish_date == NULL) style="color: #fff" @else  style="color: #000" @endif ><i class="fab fa-angular fa-lg text-danger me-3"></i><strong>{{$sellers->find($enumeration->seller_id)->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$enumeration->start_date}}</span></td>
                            <td><span class="badge bg-label-primary me-1">{{$enumeration->finish_date}}</span></td>
                            <td>
                                <a href="{{route('enumeration.stocktracking',['id' => $enumeration->id])}}" class="btn btn-success" >Sayima Git</a>
                                <a href="{{route('enumeration.newPrint',['id' => $enumeration->id])}}" class="btn btn-success" >INCELE</a>
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
