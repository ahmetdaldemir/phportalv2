@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kasalar /</span> Kasa listesi</h4>

        <div class="card">
            @role(['Bayi Yetkilisi','super-admin'])
            <div class="card-header">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#referAndEarn">Yeni Kasa Ekle</button>
            </div>
            @endrole
            <div class="card-header">
                <form action="{{route('safe.index')}}" method="get">
                <div class="row">
                    <div class="col-md-6 fv-plugins-icon-container">
                        <label class="form-label" for="formValidationName">Başlangıç - Bitiş Tarihi</label>
                        <input type="text" class="form-control daterangepicker-input" name="daterange" placeholder="YYYY-MM-DD to YYYY-MM-DD" id="date-range" readonly="readonly">
                    </div>

                    <div class="col-md-3 fv-plugins-icon-container">
                        <label class="form-label" for="formValidationName">Şube</label>
                        <select class="form-select"  name="seller" @role(['super-admin']) "" @else disabled @endrole>
                            <option value="">Tümü</option>
                            @foreach($sellers as $seller)
                                <option value="{{$seller->id}}" @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif>{{$seller->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 fv-plugins-icon-container">
                        <label class="form-label" for="formValidationName">İşlem</label>
                        <select class="form-select"  name="process">
                            <option value="">Tümü</option>
                            <option value="in">Giriş</option>
                            <option value="out">Çıkış</option>
                            <option value="Teknik Servis">Teknik</option>
                            <option value="Kaplama">Kaplama</option>
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button style="width: 100%;" type="submit" class="btn btn-danger">Ara</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Kasa Adı</th>
                        <th>Sipariş No</th>
                        <th>Tip</th>
                        <th>Nakit Giriş</th>
                        <th>Nakit Çıkış</th>
                        <th>KK Giriş</th>
                        <th>Taksit</th>
                        <th>Bayi</th>
                        <th>Kayıt Tarihi</th>
                        <th>Açıklama</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($safes as $safe)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i><strong>{{$safe->name}}</strong></td>
                            <td><span class="badge bg-label-primary me-1">{{$safe->invoice_id}}</span></td>
                            <td>{{$safe->type == 'in'?'Giriş':'Çıkış'}}</td>
                            <td>{{$safe->incash??0}} TL</td>
                            <td>{{$safe->outcash??0}} TL</td>
                            <td>{{$safe->credit_card??0}} TL</td>
                            <td>{{$safe->installment??0}} TL</td>
                            <td>{{$safe->seller->name}}</td>
                            <td>{{\Carbon\Carbon::parse($safe->created_at)->format('d-m-Y')}}</td>
                            <td>{{$safe->description}}</td>
                            <td>
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin'))

                                <a href="{{route('safe.delete',['id' => $safe->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bxs-trash"></span>
                                </a>
                                <a href="{{route('safe.edit',['id' => $safe->id])}}"
                                   class="btn btn-icon btn-primary">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot style="order: 2">
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="3"><b>KALAN KASA</b></th>
                    <th rowspan="1" colspan="1">{{$safes->sum('incash') - $safes->sum('outcash')}}</th>
                    </tfoot>
                    <tfoot style="order:1">
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1">{{$safes->sum('incash')}}</th>
                    <th rowspan="1" colspan="1">{{$safes->sum('outcash')}}</th>
                    <th rowspan="1" colspan="1">{{$safes->sum('credit_card')}}</th>
                    <th rowspan="1" colspan="1">{{$safes->sum('installment')}}</th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    <th rowspan="1" colspan="1"></th>
                    </tfoot>
                </table>
            </div>
        </div>
        <hr class="my-5">
        <div class="modal fade" id="referAndEarn" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-refer-and-earn">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h5>Kasa Oluştur</h5>
                        <form class="row g-3" id="id" action="{{route('safe.store')}}" method="post">
                            @csrf
                            <div class="col-lg-10">
                                <label class="form-label" for="modalRnFEmail">Tipi</label>
                                <select class="form-select" name="type">
                                    <option value="in">Giriş</option>
                                    <option value="out">Çıkış</option>
                                </select>
                            </div>
                            <div class="col-lg-10">
                                <label class="form-label" for="modalRnFEmail">Şube</label>
                                <select class="form-select" name="seller_id"  @role(['super-admin']) "" @else disabled @endrole>
                                    @foreach($sellers as $seller)
                                    <option value="{{$seller->id}}"
                                            @if($seller->id ==\Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif>{{$seller->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-10">
                                <label class="form-label" for="modalRnFEmail">Miktar</label>
                                <input type="text"   class="form-control" name="price" >
                            </div>
                            <div class="col-lg-10">
                                <label class="form-label" for="modalRnFEmail">Açıklama</label>
                                <input type="text" name="description" class="form-control" >
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                                <button type="submit" id="saveButton" class="btn btn-primary">Kaydet</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script src="{{asset('assets/js/forms-pickers.js')}}"></script>
    <script>
        $('form#id').submit(function(){
            $("#saveButton").prop( "disabled", true );
        });


    </script>
@endsection
