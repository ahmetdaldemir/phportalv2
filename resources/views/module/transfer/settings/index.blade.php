@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Ayarlar /</span> Ayar listesi</h4>

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{route('settings.update')}}">
                    @csrf
                    <div class="row g-3">

                        @foreach($settings as $setting)
                            <div class="panel-heading">
                                <h4 class="panel-title" style="float: left;width: 50%">
                                    {{$setting->display_name}} <code>setting('{{$setting->category}}.{{$setting->key}}
                                        ')</code>
                                </h4>
                                <div class="panel-actions" style="width: 30%;float: right;text-align: right;">
                                    <i class="bx bxs-trash" data-id="{{$setting->id}}"
                                       data-display-key="{{$setting->category}}.{{$setting->key}}"
                                       data-display-name="{{$setting->display_name}}"></i>
                                </div>
                            </div>
                            <div class="panel-body no-padding-left-right row">
                                <div class="col-md-10 no-padding-left-right">
                                    @if($setting->type == "text_area")
                                        <textarea class="form-control"
                                                  name="{{$setting->category}}.{{$setting->key}}">{{$setting->value}}</textarea>
                                    @endif
                                    @if($setting->type == "text")
                                        <input type="text" class="form-control"
                                               name="{{$setting->category}}.{{$setting->key}}" value="{{$setting->value}}">
                                    @endif
                                    @if($setting->type == "image")
                                        <input type="file" class="form-control"
                                               name="{{$setting->category}}.{{$setting->key}}" value="">
                                    @endif
                                </div>
                                <div class="col-md-2 no-padding-left-right">
                                    <select class="form-control group_select" name="{{$setting->category}}.category"
                                            data-select2-id="25" tabindex="-1" aria-hidden="true">
                                        <option @if($setting->category == 'site') selected @endif value="Site">Site
                                        </option>
                                        <option @if($setting->category == 'admin') selected @endif value="Admin">Admin
                                        </option>
                                        <option @if($setting->category == 'sms') selected @endif value="SMS">SMS
                                        </option>
                                        <option @if($setting->category == 'phone') selected @endif value="Telefon">Telefon
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-2">
                        <button style="    margin: 27px 0 0;" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="clearfix mt-4"></div>

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{route('settings.store')}}">
                    @csrf
                    <div class="col-lg-12 mx-auto">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label" for="key">Key</label>
                                <input type="text" id="key" class="form-control" name="key">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="value">Value</label>
                                <input type="text" id="display_name" class="form-control" name="display_name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="type">Tip</label>
                                <select name="type" class="form-control" required="required">
                                    <option value="">Choose Type</option>
                                    <option value="text">Text Box</option>
                                    <option value="text_area">Text Area</option>
                                    <option value="rich_text_box">Rich Textbox</option>
                                    <option value="markdown_editor">Markdown Editor</option>
                                    <option value="code_editor">Code Editor</option>
                                    <option value="checkbox">Check Box</option>
                                    <option value="radio_btn">Radio Button</option>
                                    <option value="select_dropdown">Select Dropdown</option>
                                    <option value="file">File</option>
                                    <option value="image">Image</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="pincode">Kategori</label>
                                <select class="form-control" name="category">
                                    <option value="sms">SMS</option>
                                    <option value="site">Site</option>
                                    <option value="admin">Admin</option>
                                    <option value="phone">Telefon</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button style="    margin: 27px 0 0;" class="btn btn-success">Kaydet</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <hr class="my-5">
    </div>
@endsection
