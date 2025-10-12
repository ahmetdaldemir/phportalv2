@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Modeller /</span> @if(isset($versions))
                {{$versions->name}}
            @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Model Bilgileri</h5>
            <form action="{{route('version.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" @if(isset($versions)) value="{{$versions->id}}" @endif />
                <input type="hidden" name="brand_id" @if(isset($versions)) value="{{$versions->brand_id}}"
                       @else  value="{{$brand_id}}" @endif />
                <div class="card-body">
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Model Adı</label>
                        <input type="text" class="form-control" id="name"
                               @if(isset($versions)) value="{{$versions->name}}" @endif  name="name"
                               aria-describedby="name">
                        <div id="name" class="form-text">
                            We'll never share your details with anyone else.
                        </div>
                    </div>
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Resim</label>
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img
                                src=""
                                alt="user-avatar"
                                class="d-block rounded"
                                height="100"
                                width="100"
                                id="uploadedAvatar"
                            />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Yeni Resim</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input
                                        type="file"
                                        id="upload"
                                        name="image"
                                        class="account-file-input"
                                        hidden
                                        accept="image/png, image/jpeg"
                                    />
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Yenile</span>
                                </button>

                                <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                            </div>
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

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <td>Marka</td>
                        <td>Versiyon</td>
                        <td>Status</td>
                        <td>Firma</td>
                        <td>İşlemler</td>
                    </tr>
                    @foreach($versionlist as $version)
                        <tr>
                            <td>{{$version->brand->name}}</td>
                            <td>{{$version->name}}</td>  <td>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="updateTechnical('technical',{{$version->id}},{{$version->technical == 1 ? 0:1}})"
                                           id="flexSwitchCheckChecked" {{$version->technical == 1 ? 'checked':''}} />
                                </div>
                            </td>
                            <td>{{$version->company_id}}</td>
                            <td>
                                <a href="{{route('version.delete',['id' => $version->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('version.edit',['id' => $version->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection
