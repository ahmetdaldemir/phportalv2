@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Stok Kart /</span> @if(isset($stockcards))
                {{$stockcards->name}}
            @endif</h4>
        <form action="{{route('stockcard.store')}}" method="post">
            @csrf
            <input type="hidden" name="id" @if(isset($stockcards)) value="{{$stockcards->id}}" @endif />
             <div class="card">
                <h5 class="card-header">Stok Kart Bilgileri</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6 col-md-8 col-sm-9 col-12 fv-plugins-icon-container">
                            <label for="defaultFormControlInput" class="form-label">Stok Adı</label>
                            <input type="text" class="form-control" id="name"
                                   @if(isset($stockcards)) value="{{$stockcards->name}}" @endif  name="name" aria-describedby="name">
                            <div id="name" class="form-text">
                                <select name="fakeproduct" class="form-select select2">
                                    <option value="">Seçiniz</option>
                                    @foreach($fakeproducts as $fakeproduct)
                                        <option value="{{$fakeproduct->name}}">{{$fakeproduct->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- div class="col-xl-2 col-md-4 col-sm-5 col-12 fv-plugins-icon-container">
                            <label for="defaultFormControlInput" class="form-label">Barkod</label>
                            <input type="text" class="form-control" id="barcode"
                                   @if(isset($stockcards)) value="{{$stockcards->barcode}}" @endif  name="barcode"
                                   aria-describedby="barcode">
                            <div id="barcode" class="form-text">
                                We'll never share your details with anyone else.
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-5 col-12 fv-plugins-icon-container">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku"
                                   @if(isset($stockcards)) value="{{$stockcards->sku}}" @endif  name="sku"
                                   aria-describedby="sku">

                        </div -->
                        <div class="col-xl-2 col-md-3 col-sm-6 col-12 fv-plugins-icon-container">
                            <label for="defaultFormControlInput" class="form-label">Stok Takibi</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="tracking"  id="flexSwitchCheckChecked"/>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4  mb-4">
                <h5 class="card-header">Stok Ayarları</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Stok Takip Miktarı</label>
                                <input type="text" class="form-control" id="tracking_quantity"
                                       @if(isset($stockcards)) value="{{$stockcards->tracking_quantity}}"
                                       @endif  name="tracking_quantity"
                                       aria-describedby="tracking_quantity">

                            </div>



                            <div>
                                <label for="defaultFormControlInput" class="form-label">Kategori </label>
                                <select class="select2" name="category_id">
                                    @foreach($categories as $value)
                                        <option @if(isset($stockcards) && $stockcards->category_id == $value->id) selected @endif  value="{{$value->id}}">{{$value->path}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div>
                                <label for="brand_id" class="form-label">Marka</label>
                                <select name="brand_id" id="brand_id" onchange="getVersion(this.value)"
                                        class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($brands as $brand)
                                        <option
                                            @if(isset($stockcards) and ($brand->id == $stockcards->brand_id))  selected
                                            @endif value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Model</label>
                                <select name="version_id[]" @if(isset($stockcards)) @if(!is_null($stockcards->version_id)) data-version="{{implode(",",$stockcards->version_id)}}" @endif  @endif id="version_id" class="form-control select2" required  multiple></select>
                            </div>
                            <div>
                                <label for="defaultFormControlInput" class="form-label">Birim</label>
                                <select name="unit_id" class="form-control">
                                    @foreach($units as $key => $value)
                                        <option @if(isset($stockcards))
                                                    {{ $stockcards->hasSeller($key) ? 'selected' : '' }}
                                                @endif  value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <hr class="my-5">

                    </div>
                    <hr class="my-5">
                    <div>
                        <button type="submit" class="btn btn-danger btn-buy-now">Kaydet</button>
                    </div>
                </div>
            </div>
        </form>
        <hr class="my-5">
    </div>
    <style>
        .controls {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #fff;
            z-index: 1;
            padding: 6px 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        button {
            border: 0px;
            color: #e13300;
            margin: 4px;
            padding: 4px 12px;
            cursor: pointer;
            background: transparent;
        }

        button.active,
        button.active:hover {
            background: #e13300;
            color: #fff;
        }

        button:hover {
            background: #efefef;
        }

        input[type=checkbox] {
            vertical-align: middle !important;
        }

        h1 {
            font-size: 3em;
            font-weight: lighter;
            color: #fff;
            text-align: center;
            display: block;
            padding: 40px 0px;
            margin-top: 40px;
        }

        .tree {
            margin: 2% auto;
            width: 80%;
        }

        .tree ul {
            margin: 4px auto;
            margin-left: 6px;
            border-left: 1px dashed #dfdfdf;
        }


        .tree li {
            padding: 12px 18px;
            cursor: pointer;
            vertical-align: middle;
            background: #fff;
        }

        .tree li:first-child {
            border-radius: 3px 3px 0 0;
        }

        .tree li:last-child {
            border-radius: 0 0 3px 3px;
        }

        .tree .active,
        .active li {
            background: #efefef;
        }

        .tree label {
            cursor: pointer;
        }

        .tree input[type=checkbox] {
            margin: -2px 6px 0 0px;
        }

        .has > label {
            color: #000;
        }

        .tree .total {
            color: #e13300;
        }
    </style>
@endsection

@section('custom-js')
    <script>
        "use strict";
        !function () {
            var e = document.querySelectorAll(".invoice-item-price"),
                t = document.querySelectorAll(".invoice-item-qty"), n = document.querySelectorAll(".date-picker");
            e && e.forEach(function (e) {
                new Cleave(e, {delimiter: "", numeral: !0})
            }), t && t.forEach(function (e) {
                new Cleave(e, {delimiter: "", numeral: !0})
            }), n && n.forEach(function (e) {
                e.flatpickr({monthSelectorType: "static"})
            })
        }(), $(function () {
            var n, o, a, i, l, r, e = $(".btn-apply-changes"), t = $(".source-item"), c = {
                "App Design": "Designed UI kit & app pages.",
                "App Customization": "Customization & Bug Fixes.",
                "ABC Template": "Bootstrap 4 admin template.",
                "App Development": "Native App Development."
            };

            function p(e, t) {
                e.closest(".repeater-wrapper").find(t).text(e.val())
            }

            $(document).on("click", ".tax-select", function (e) {
                e.stopPropagation()
            }), e.length && $(document).on("click", ".btn-apply-changes", function (e) {
                var t = $(this);
                l = t.closest(".dropdown-menu").find("#taxInput1"), r = t.closest(".dropdown-menu").find("#taxInput2"), i = t.closest(".dropdown-menu").find("#discountInput"), o = t.closest(".repeater-wrapper").find(".tax-1"), a = t.closest(".repeater-wrapper").find(".tax-2"), n = $(".discount"), null !== l.val() && p(l, o), null !== r.val() && p(r, a), i.val().length && t.closest(".repeater-wrapper").find(n).text(i.val() + "%")
            }), t.length && (t.on("submit", function (e) {
                e.preventDefault()
            }), t.repeater({
                show: function () {
                    $(this).slideDown(), [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (e) {
                        return new bootstrap.Tooltip(e)
                    })
                }, hide: function (e) {
                    $(this).slideUp()
                }
            })), $(document).on("change", ".item-details", function () {
                var e = $(this), t = c[e.val()];
                e.next("textarea").length ? e.next("textarea").val(t) : e.after('<textarea class="form-control" rows="2">' + t + "</textarea>")
            })
        });
    </script>
<script>
    $(document).on('click', '.tree label', function(e) {
        $(this).next('ul').fadeToggle();
        e.stopPropagation();
    });

    $(document).on('change', '.tree input[type=checkbox]', function(e) {
        $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
        $(this).parentsUntil('.tree').children("input[type='checkbox']").prop('checked', this.checked);
        e.stopPropagation();
    });

    $(document).on('click', 'button', function(e) {
        switch ($(this).text()) {
            case 'Collepsed':
                $('.tree ul').fadeOut();
                break;
            case 'Expanded':
                $('.tree ul').fadeIn();
                break;
            case 'Checked All':
                $(".tree input[type='checkbox']").prop('checked', true);
                break;
            case 'Unchek All':
                $(".tree input[type='checkbox']").prop('checked', false);
                break;
            default:
        }
    });
</script>
@endsection
