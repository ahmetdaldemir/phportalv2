@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Müşteriler /</span> @if(isset($customers)) {{$customers->name}} @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Müşteri Bilgileri</h5>
            <form action="{{route('customer.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" @if(isset($customers)) value="{{$customers->id}}" @endif />
            <div class="card-body">
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img
                            src="../assets/img/avatars/1.png"
                            alt="user-avatar"
                            class="d-block rounded"
                            height="100"
                            width="100"
                            id="uploadedAvatar"
                        />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Kimlik / Passport Ön Yüzü</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input
                                    type="file"
                                    id="upload"
                                    class="account-file-input"
                                    hidden
                                    accept="image/png, image/jpeg"
                                    name="image"
                                />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                            <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="fullname" class="form-label">İsim Soyisim</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="fullname"
                                    name="fullname"
                                    value="John Deo"
                                    autofocus required
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="lastName" class="form-label">TC Kimlik / Passport No</label>
                                <input class="form-control" type="text" name="tc" id="tc" value="11111111" maxlength="13" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="email"
                                    name="email"
                                    value="john.doe@example.com"
                                    placeholder="john.doe@example.com"
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="organization" class="form-label">Iban</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="organization"
                                    name="iban"
                                    value="TR000000"
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">Telefon 1</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">TR (+90)</span>
                                    <input
                                        type="text"
                                        id="phoneNumber"
                                        name="phone1"
                                        class="form-control"
                                        placeholder="202 555 0111"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="address" class="form-label">Telefon 2</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">TR (+90)</span>
                                    <input
                                        type="text"
                                        id="phoneNumber"
                                        name="phone2"
                                        class="form-control"
                                        placeholder="202 555 0111"
                                    />
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">Adres</label>
                                <textarea class="form-control"  id="address" name="address"></textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">Not</label>
                                <textarea class="form-control"  id="note" name="note"></textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">İl</label>
                                <input class="form-control" type="text" id="city" name="city" placeholder="İstanbul" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">İlçe</label>
                                <input class="form-control" type="text" id="district" name="district" placeholder="Beylikdüzü" />
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="zipCode" class="form-label">Şube</label>
                                <select id="seller" name="seller_id" class="select2 form-select">
                                    @foreach($sellers as $seller)
                                        <option value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                </div>
                <!-- /Account -->

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

@section('custom-js')
    <script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
@endsection
