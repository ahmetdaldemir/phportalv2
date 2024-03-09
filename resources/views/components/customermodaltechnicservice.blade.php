<div class="modal fade" id="editUser" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Yeni Müşteri Ekle</h3>
                </div>
                <form action="javascript():;" method="post" id="customerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id"/>
                    <div class="card-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label for="firstname" class="form-label">Tip</label>
                                    <select
                                        class="form-select"
                                        id="type" name="type">
                                        <option value="account">Cari</option>
                                        <option value="customer" selected>Müşteri</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-5">
                                    <label for="firstname" class="form-label">İsim</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        id="firstname"
                                        name="firstname"
                                        autofocus required
                                    />
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="lastname" class="form-label">Soyisim</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        id="lastname"
                                        name="lastname"
                                        autofocus required
                                    />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="lastName" class="form-label">TC Kimlik / Passport No</label>
                                    <input class="form-control" type="text" name="tc" id="tc" maxlength="13"/>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        id="email"
                                        name="email"
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
                                        />
                                    </div>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="state" class="form-label">Adres</label>
                                    <textarea class="form-control" id="address" name="address"></textarea>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="zipCode" class="form-label">Not</label>
                                    <textarea class="form-control" id="note" name="note"></textarea>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="state" class="form-label">İl</label>
                                    <select id="city" name="city" class="select2 form-select"
                                            onchange="getTown(this.value)">
                                        @foreach($citys as $city)
                                            <option @if($city->id == 34) selected
                                                    @endif value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="zipCode" class="form-label">İlçe</label>
                                    <select id="district" name="district" class="select2 form-select">
                                        @foreach($tows as $item)
                                            <option @if($item->id == 459) selected
                                                    @endif value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="zipCode" class="form-label">Şube</label>
                                    <select id="seller" name="seller_id" class="select2 form-select">
                                        @foreach($sellers as $seller)
                                            <option value="{{$seller->id}}">{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                        <hr class="my-5">
                        <div>
                            <button ng-click="customerSave()" type="button" class="btn btn-danger btn-buy-now">Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('custom-css')
    <style>
        #cont {
            position: relative;

        }

        .son {
            position: absolute;
            top: 0;
            left: 0;

        }


        #control {
            position: absolute;

            left: 0;

            z-index: 50;
            background: HoneyDew;
            opacity: 0.7;
            color: #fff;
            text-align: center;

        }

        #snap {
            background-color: dimgray;

        }

        #retake {
            background-color: coral;

        }

        #close {
            background-color: lightcoral;

        }

        .hov {
            opacity: .8;
            transition: all .5s;
        }

        .hov:hover {
            opacity: 1;

            font-weight: bolder;
        }

        /*#canvas{
          z-index: 1;
        }
        #video{
          z-index: 3;
        }*/

        html:not([dir=rtl]) .modal-simple .btn-close {
            right: -2rem;
        }

        html:not([dir=rtl]) .modal .btn-close {
            transform: translate(23px, -25px);
        }

        .modal-simple .btn-close {
            position: absolute;
            top: -2rem;
        }

        .modal .btn-close {
            background-color: #fff;
            border-radius: 0.5rem;
            opacity: 1;
            padding: 0.635rem;
            box-shadow: 0 0.125rem 0.25rem rgb(161 172 184 / 40%);
            transition: all .23s ease .1s;
        }
    </style>
@endsection
