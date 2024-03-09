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

                <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-3">
                                <label for="firstname" class="form-label">İsim</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="firstname"
                                    name="firstname"
                                    @if(isset($customers)) value="{{$customers->firstname}}" @endif
                                    autofocus required
                                />
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="lastname" class="form-label">Soyisim</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="lastname"
                                    name="lastname"
                                    @if(isset($customers)) value="{{$customers->lastname}}" @endif
                                    autofocus required
                                />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="lastName" class="form-label">TC Kimlik / Passport No</label>
                                <input class="form-control" type="text" name="tc" id="tc" @if(isset($customers)) value="{{$customers->tc}}" @endif maxlength="13" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="email"
                                    name="email"
                                    @if(isset($customers)) value="{{$customers->email}}" @endif
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
                                    @if(isset($customers)) value="{{$customers->iban}}" @endif
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
                                        @if(isset($customers)) value="{{$customers->phone1}}" @endif

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
                                        @if(isset($customers)) value="{{$customers->phone2}}" @endif
                                    />
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">Adres</label>
                                <textarea class="form-control"  id="address"  name="address">@if(isset($customers))  {{$customers->address}}  @endif</textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">Not</label>
                                <textarea class="form-control"  id="note" name="note">@if(isset($customers)) {{$customers->note}}  @endif</textarea>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state" class="form-label">İl</label>
                                <input class="form-control" type="text" id="city" name="city" @if(isset($customers)) value="{{$customers->city}}" @endif />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="zipCode" class="form-label">İlçe</label>
                                <input class="form-control" type="text" id="district" name="district" @if(isset($customers)) value="{{$customers->district}}" @endif />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="zipCode" class="form-label">Şube</label>
                                <select id="seller" name="seller_id" class="select2 form-select">
                                    @foreach($sellers as $seller)
                                        <option  @if(isset($customers)) {{ $customers->hasSeller($seller->id) ? 'selected' : '' }} @endif  value="{{$seller->id}}">{{$seller->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="zipCode" class="form-label">Firma Türü</label>
                                <select id="seller" name="company_type" class="select2 form-select">
                                  <option value="sahis">Şahıs</option>
                                  <option value="firma">Firma</option>
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
