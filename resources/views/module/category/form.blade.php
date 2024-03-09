@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kategoriler /</span> @if(isset($categorys))
                {{$categorys->name}}
            @endif</h4>
        <div class="card  mb-4">
            <h5 class="card-header">Kategori Bilgileri</h5>
            <form action="{{route('category.store')}}" method="post">
                @csrf
                <input type="hidden" name="id" @if(isset($categories)) value="{{$categories->id}}" @endif />
                <div class="card-body">
                    <div>
                        <label for="defaultFormControlInput" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="name"
                               @if(isset($categories)) value="{{$categories->name}}" @endif  name="name"
                               aria-describedby="name">

                    </div>
                    <div>
                        <label>KATEGORİLER</label>
                        <ul class="tree">
                            <li class="has">
                              <input  style="width: 20px;height: 20px" type="radio" name="parent_id" value="0">  <label>Üst Kategori</label>
                            </li>
                            @foreach($categories_all as $category)
                                @if($category->parent_id == 0)
                                    <li class="has">
                                        <input style="width: 20px;height: 20px" type="radio" name="parent_id" value="{{$category->id}}">
                                        <label>{{$category->name}}</label>
                                        <ul>
                                            @foreach($categories_all as $categorya)
                                                @if($categorya->parent_id == $category->id)
                                                    <li class="">
                                                        <input  style="width: 20px;height: 20px" type="radio" name="parent_id"
                                                               value="{{$categorya->id}}">
                                                        <label>{{$categorya->name}}</label>
                                                    </li>

                                                    @foreach($categories_all as $categoryaa)
                                                        @if($categorya->id == $categoryaa->parent_id)
                                                           <li class=""><div style="    float: left;
                                                            line-height: 1.5;
                                                            margin-left: 11px;
                                                            padding-right: 10px;
                                                        }"> --</div>
                                                               <input  style="width: 20px;height: 20px" type="radio" name="parent_id"
                                                                        value="{{$categoryaa->id}}">
                                                                <label>{{$categoryaa->name}}</label>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

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
            margin: 5px 5px;
            width: 50%;
            border: 1px solid #ccc;
        }

        .tree ul {
            display: none;
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
        $(document).on('click', '.tree label', function (e) {
            $(this).next('ul').fadeToggle();
            e.stopPropagation();
        });

        $(document).on('change', '.tree input[type=checkbox]', function (e) {
            $(this).siblings('ul').find("input[type='checkbox']").prop('checked', this.checked);
            $(this).parentsUntil('.tree').children("input[type='checkbox']").prop('checked', this.checked);
            e.stopPropagation();
        });

        $(document).on('click', 'button', function (e) {
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
