@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Sevkler /</span> Sevk listesi</h4>


        <div class="card">
            @if($errors->any())
                <div class="card-header">
                    <div class="alert alert-warning">
                        <h4>{{$errors->first()}}</h4>
                    </div>
                </div>
            @endif

            @role(['Depo Sorumlusu','super-admin','Bayi Yetkilisi'])
            <div class="card-header">
                <a href="{{route('transfer.create')}}" class="btn btn-primary float-end">Yeni Sevk Ekle</a>
            </div>
            @endrole

            <div class="card-body">
                <form action="{{route('transfer.index')}}" id="stockSearch" method="get">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="multicol-username">Stok</label>
                            <input type="text" class="form-control" value="{{old('stockName')}}" name="stockName">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="multicol-email">Marka</label>
                            <div class="input-group input-group-merge">
                                <select type="text" name="brand" class="form-select" onchange="getVersion(this.value)"
                                        style="width: 100%">
                                    <option value="">Tümü</option>
                                    @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Model</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" id="version_id" name="version" class="form-select"
                                            style="width: 100%">
                                        <option value="">Tümü</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <style>
                            .position-relative {
                                width: 100%;
                            }
                        </style>
                        <div class="col-md-3">
                            <label class="form-label" for="multicol-email">Kategori</label>
                            <div class="input-group input-group-merge w-100">
                                <select name="category" class="select2">
                                    <option value="">Tümü</option>
                                    @foreach($categories as $value)
                                        <option value="{{$value->id}}">{{$value->path}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Renk</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="color" class="form-select" style="width: 100%">
                                        <option value="">Tümü</option>
                                        @foreach($colors as $color)
                                            <option value="{{$color->id}}">{{$color->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-password">Şube</label>
                                <div class="input-group input-group-merge">
                                    <select type="text" name="seller" class="form-select" style="width: 100%"
                                            @role(['Depo Sorumlusu','super-admin'])
                                    ""
                                    @else
                                        disabled
                                        @endrole
                                        >
                                        <option value="all">Tümü</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{$seller->id}}"
                                                    @if($seller->id == \Illuminate\Support\Facades\Auth::user()->seller_id) selected @endif>{{$seller->name}}</option>
                                            @endforeach
                                            </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-password-toggle">
                                <label class="form-label" for="multicol-confirm-password">Seri Numarası</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" name="serialNumber" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
                    </div>
                </form>

            </div>

            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">Gelen
                            Sevkler
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                data-bs-target="#navs-top-profile" aria-controls="navs-top-profile"
                                aria-selected="false">
                            Yapılan Sevkler
                        </button>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="navs-top-home" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table" data-detail-view="true">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Tip</th>
                                    <th>Gönderici Bayi</th>
                                    <th>Oluşturma Zamanı</th>
                                    <th>Alıcı Bayi</th>
                                    <th>Gönderen</th>
                                    <th>Teslim Alan</th>
                                    <th>Durum</th>
                                    <th>Teslim Tarihi</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0" style="font-size: 13px;">
                                @foreach($transfers as $transfer)
                                    <tr>
                                        <td>
                                            <a href="{{route('transfer.show',['id'=>$transfer->id])}}">{{$transfer->number}}</a>
                                        </td>
                                        <td>{{($transfer->type == 'phone')?'TELEFON':'DİĞER'}}</td>
                                        <td>{{$transfer->seller($transfer->main_seller_id)->name??null}}</td>
                                        <td>{{\Carbon\Carbon::parse($transfer->created_at)->format('d-m-Y')}}</td>
                                        <td>{{$transfer->seller($transfer->delivery_seller_id)->name??null}}</td>
                                        <td>{{$transfer->user($transfer->user_id)->name??null}}</td>
                                        <td>
                                                <?php
                                                if (!is_null($transfer->comfirm_date) and !is_null($transfer->comfirm_id)) {
                                                    echo $transfer->user($transfer->comfirm_id)->name;
                                                }
                                                ?>
                                        </td>
                                        <td><span
                                                    class="badge bg-label-{{\App\Models\Transfer::STATUS_COLOR[$transfer->is_status]}}">{{\App\Models\Transfer::STATUS[$transfer->is_status]}}</span>
                                        </td>
                                        <td>{{$transfer->comfirm_date}}</td>
                                        <td>
                                            @role(['Depo Sorumlusu','super-admin'])
                                            @if($transfer->is_status == 2 && $transfer->delivery_seller_id == 1)
                                                <a onclick="return confirm('Onaylamak istediğinizden eminmisiniz?');"
                                                   href="{{route('transfer.update',['id' => $transfer->id,'is_status' => 3])}}"
                                                   class="btn btn-icon btn-success">
                                                    <span class="bx bx-navigation"></span>
                                                </a>
                                                <a onclick="return confirm('Reddetmek istediğinizden eminmisiniz?');"
                                                   href="{{route('transfer.update',['id' => $transfer->id,'is_status' => 4])}}"
                                                   class="btn btn-icon btn-danger">
                                                    <span class="bx bx-sad"></span>
                                                </a>
                                            @endif
                                            @endrole
                                            @role(['Depo Sorumlusu','super-admin'])
                                            @if($transfer->is_status == 1)
                                                <a onclick="return confirm('Onaylamak istediğinizden eminmisiniz?');"
                                                   href="{{route('transfer.update',['id' => $transfer->id,'is_status' => 2])}}"
                                                   class="btn  btn-sm btn-secondary">
                                                    ÖN ONAY
                                                </a>
                                                <a onclick="return confirm('Reddetmek istediğinizden eminmisiniz?');"
                                                   href="{{route('transfer.update',['id' => $transfer->id,'is_status' => 4])}}"
                                                   class="btn btn-danger">
                                                    <span class="bx  btn-sm  bx-sad"></span>
                                                </a>
                                            @endif
                                            @endrole
                                            @role(['Bayi Yetkilisi'])
                                            @if($transfer->is_status == 2)
                                                <a onclick="return confirm('Onaylamak istediğinizden eminmisiniz?');"
                                                   href="{{route('transfer.update',['id' => $transfer->id,'is_status' => 3])}}"
                                                   class="btn btn-icon btn-success">
                                                    <span class="bx bx-navigation"></span>
                                                </a>
                                                <a onclick="return confirm('Reddetmek istediğinizden eminmisiniz?');"
                                                   href="{{route('transfer.update',['id' => $transfer->id,'is_status' => 4])}}"
                                                   class="btn btn-icon btn-danger">
                                                    <span class="bx bx-sad"></span>
                                                </a>
                                            @endif
                                            @endrole
                                            @role(['Depo Sorumlusu|super-admin'])
                                            <!-- a onclick="return confirm('Silmek istediğinizden eminmisiniz?');"
                                       href="{{route('transfer.delete',['id' => $transfer->id])}}"
                                       class="btn btn-icon btn-primary">
                                        <span class="bx bxs-trash"></span>
                                    </a -->
                                            @if($transfer->is_status == 1)
                                                <a href="{{route('transfer.edit',['id' => $transfer->id])}}"
                                                   class="btn btn-icon btn-primary">
                                                    <span class="bx bx-edit-alt"></span>
                                                </a>
                                            @endif
                                            @endrole
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{$transfers->links()}}
                    </div>
                    <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table" data-detail-view="true">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Tip</th>
                                    <th>Gönderici Bayi</th>
                                    <th>Oluşturma Zamanı</th>
                                    <th>Alıcı Bayi</th>
                                    <th>Gönderen</th>
                                    <th>Teslim Alan</th>
                                    <th>Durum</th>
                                    <th>Teslim Tarihi</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0" style="font-size: 13px;">
                                @foreach($onlyTransfers as $transfer)
                                    <tr>
                                        <td>
                                            <a href="{{route('transfer.show',['id'=>$transfer->id])}}">{{$transfer->number}}</a>
                                        </td>
                                        <td>{{($transfer->type == 'phone')?'TELEFON':'DİĞER'}}</td>
                                        <td>{{$transfer->seller($transfer->main_seller_id)->name??null}}</td>
                                        <td>{{\Carbon\Carbon::parse($transfer->created_at)->format('d-m-Y')}}</td>
                                        <td>{{$transfer->seller($transfer->delivery_seller_id)->name??null}}</td>
                                        <td>{{$transfer->user($transfer->user_id)->name??null}}</td>
                                        <td>
                                                <?php
                                                if (!is_null($transfer->comfirm_date) and !is_null($transfer->comfirm_id)) {
                                                    echo $transfer->user($transfer->comfirm_id)->name;
                                                }
                                                ?>
                                        </td>
                                        <td><span
                                                    class="badge bg-label-{{\App\Models\Transfer::STATUS_COLOR[$transfer->is_status]}}">{{\App\Models\Transfer::STATUS[$transfer->is_status]}}</span>
                                        </td>
                                        <td>{{$transfer->comfirm_date}}</td>
                                        <td>

                                            @role(['Depo Sorumlusu|super-admin'])
                                            @if($transfer->is_status == 1)
                                                <a href="{{route('transfer.edit',['id' => $transfer->id])}}"
                                                   class="btn btn-icon btn-primary">
                                                    <span class="bx bx-edit-alt"></span>
                                                </a>
                                            @endif
                                            @endrole
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <hr class="my-5">
    </div>
@endsection

@section('custom-js')
    <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table-locale-all.min.js"></script>
    <script
            src="https://unpkg.com/bootstrap-table@1.21.4/dist/extensions/export/bootstrap-table-export.min.js"></script>

    <script>
        var $table = $('#table')
        var $remove = $('#remove')
        var selections = []

        function getIdSelections() {
            return $.map($table.bootstrapTable('getSelections'), function (row) {
                return row.id
            })
        }

        function responseHandler(res) {
            $.each(res.rows, function (i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        function detailFormatter(index, row) {
            var html = []
            $.each(row, function (key, value) {
                html.push('<p><b>' + key + ':</b> ' + value + '</p>')
            })
            return html.join('')
        }

        function operateFormatter(value, row, index) {
            return [
                '<a class="like" href="javascript:void(0)" title="Like">',
                '<i class="fa fa-heart"></i>',
                '</a>  ',
                '<a class="remove" href="javascript:void(0)" title="Remove">',
                '<i class="fa fa-trash"></i>',
                '</a>'
            ].join('')
        }


        function mainSellerName(id) {
            <?php if (isset($transfer)){ ?>
            console.log("da", id);
            return {{$transfer->seller("+@id+")}}
                <?php } ?>
        }

        window.operateEvents = {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row))
            },
            'click .remove': function (e, value, row, index) {
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [row.id]
                })
            }
        }

        function totalTextFormatter(data) {
            return 'Total'
        }

        function totalNameFormatter(data) {
            return data.length
        }

        function totalPriceFormatter(data) {
            var field = this.field
            return '$' + data.map(function (row) {
                return +row[field].substring(1)
            }).reduce(function (sum, i) {
                return sum + i
            }, 0)
        }

        function initTable() {
            $table.bootstrapTable('destroy').bootstrapTable({
                height: 550,
                locale: $('#locale').val(),
                columns: [
                    [{
                        field: 'id',
                        checkbox: true,
                        rowspan: 2,
                        align: 'center',
                        valign: 'middle'
                    }, {
                        title: 'Gönderici Bayi',
                        field: 'main_seller_id',
                        rowspan: 2,
                        align: 'center',
                        valign: 'middle',
                        sortable: true,
                        footerFormatter: mainSellerName
                    }, {
                        title: 'Item Detail',
                        colspan: 3,
                        align: 'center'
                    }],
                    [{
                        field: 'delivery_seller_id',
                        title: 'Item Name',
                        sortable: true,
                        // footerFormatter: totalNameFormatter,
                        align: 'center'
                    }, {
                        field: 'delivery_id',
                        title: 'Item Price',
                        sortable: true,
                        align: 'center',
                        // footerFormatter: totalPriceFormatter
                    }, {
                        field: 'status_id',
                        title: 'Item Operate',
                        align: 'center',
                        clickToSelect: false,
                        events: window.operateEvents,
                        // formatter: operateFormatter
                    }]
                ]
            })
            $table.on('check.bs.table uncheck.bs.table ' +
                'check-all.bs.table uncheck-all.bs.table',
                function () {
                    $remove.prop('disabled', !$table.bootstrapTable('getSelections').length)

                    // save your data, here just save the current page
                    selections = getIdSelections()
                    // push or splice the selections if you want to save all data selections
                })
            $table.on('all.bs.table', function (e, name, args) {
                console.log(name, args)
            })
            $remove.click(function () {
                var ids = getIdSelections()
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: ids
                })
                $remove.prop('disabled', true)
            })
        }

        $(function () {
            initTable()

            $('#locale').change(initTable)
        })
    </script>
@endsection

@section('custom-css')
    <link href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css" rel="stylesheet">
    <style>
        .select,
        #locale {
            width: 100%;
        }

        .like {
            margin-right: 10px;
        }
    </style>

@endsection

