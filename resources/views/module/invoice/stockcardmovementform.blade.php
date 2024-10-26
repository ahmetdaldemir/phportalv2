@extends('layouts.admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-add">
            <div class="col-lg-9 col-12 mb-lg-0 mb-4">
                <div class="card invoice-preview-card">

                    <!-- route('invoice.stockcardmovementstore') -->
                     <!--form id="invoiceForm" method="post" action="#" -->
                        @csrf
                        <input type="hidden" name="id" @if(isset($invoice_id)) value="{{$invoice_id}}" @endif />
                        <input type="hidden" name="type" value="1"/>
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                                <div class="col-md-6 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                        <label for="selectCustomer" class="form-label">Cari Seçiniz</label>
                                        <div class="col-md-9">
                                            <select id="selectCustomer" class="w-100 select2" data-style="btn-default" name="customer_id" ng-init="getCustomers()">
                                                <option value="0">Genel Cari</option>
                                                <option ng-repeat="customer in customers"
                                                        ng-if="customer.type == 'account'"
                                                        data-value="@{{customer.id}}"
                                                        ng-selected="customer.id == {{$invoice->customer_id}}"
                                                        @if(isset($invoice) && '@{{customer.id}}' == $invoice->customer_id) selected
                                                        @endif data-value="@{{customer.id}}" value="@{{customer.id}}"> @{{customer.fullname}}
                                                </option>
                                            </select>
                                        </div>
                                        <!-- div class="col-md-3">
                                            <button class="btn btn-secondary btn-primary" tabindex="0" data-bs-toggle="modal" data-bs-target="#editUser" type="button">
                                                <span><i class="bx bx-plus me-md-1"></i></span>
                                            </button>
                                        </div -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-2">
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="h4 text-capitalize mb-0 text-nowrap">Invoice #</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control" name="number"  @if(isset($invoice)) value="{{$invoice->number}}" @endif  id="invoiceId">
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tarihi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                                <input type="text" class="form-control datepicker flatpickr-input" name="create_date"  @if(isset($invoice)) value="{{$invoice->create_date}}"
                                                       @else  value="{{date('d-m-Y')}}" @endif  />
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            <hr class="mx-n4">

                        </div>
                        <div class="card-body">
                            <div class="mb-3">


                                @if(!empty($invoice->detail))
                                    @foreach($invoice->detail as $item)
                                    <div id="test" class="pt-0 pt-md-4">
                                        <div class="cloneBox"></div>
                                        <div class="border rounded position-relative pe-0">
                                            <div class="row w-100 m-0 p-3">
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Stok</p>
                                                    <select name="stock_card_id[]"
                                                            class="form-select item-details select2 mb-2">
                                                        @foreach($stocks as $stock)
                                                            <option value="{{$stock->id}}" @if($stock->id == $item['stockcardid']) selected @endif >
                                                                {{$stock->name}} -
                                                                <small> {{$stock->brand->name}}</small> - <b>
                                                                        <?php  $datas = json_decode($stock->version(), TRUE);
                                                                        foreach ($datas as $mykey => $myValue) {
                                                                            echo "$myValue,";
                                                                        }
                                                                        ?></b>
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Seri No</p>
                                                    <input type="text" class="form-control" name="serial[]" readonly />
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Renk</p>
                                                    <select name="color_id[]"
                                                            class="form-select item-details select2 mb-2">
                                                        @foreach($colors as $color)
                                                            <option @if($color->id == $item['color_id']) selected @endif  value="{{$color->id}}">{{$color->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Stok Takibi</p>
                                                    <input type="text" class="form-control"  value="{{$item['tracking_quantity']}}" name="tracking_quantity[]" required />
                                                </div>
                                                <div class="col-md-1 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Prefix</p>
                                                    <input type="text" class="form-control" @if(isset($item['prefix'])) value="{{$item['prefix']}}"  @endif name="prefix[]" required />
                                                </div>
                                                <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Neden</p>
                                                    <select name="reason_id[]"
                                                            class="form-select item-details select2 mb-2">
                                                        @foreach($reasons as $reason)
                                                            @if($reason->type == 5)
                                                                <option @if($reason->id == $item['reason_id']) selected @endif  value="{{$reason->id}}">{{$reason->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div -->
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Gerçek Maliyet</p>
                                                    <input type="text" class="form-control invoice-item-price" value="{{$item['cost_price']}}"
                                                    name="cost_price[]" required/>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Maliyet</p>
                                                    <input type="text" class="form-control invoice-item-price"  value="{{$item['base_cost_price']}}"
                                                           name="base_cost_price[]" required/>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                    <input type="text" class="form-control invoice-item-price"  value="{{$item['sale_price']}}"
                                                           name="sale_price[]" required/>
                                                </div>
                                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Adet</p>
                                                    <input type="number" class="form-control invoice-item-qty" value="{{$item['quantity']}}"
                                                           name="quantity[]" min="1" max="5000" required>
                                                </div>

                                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="taxInput1" class="form-label">Şube</label>
                                                    <select name="seller_id[]"  id="taxInput1"
                                                            class="form-select tax-select">
                                                        @foreach($sellers as $seller)
                                                            <option @if($item['seller_id'] == $seller->id) selected @endif
                                                                value="{{$seller->id}}">{{$seller->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="taxInput2" class="form-label">Depo</label>
                                                    <select name="warehouse_id[]" id="taxInput2"
                                                            class="form-select tax-select">
                                                        @foreach($warehouses as $warehouse)
                                                            <option @if($item['warehouse_id'] == $warehouse->id) selected @endif
                                                                value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="taxInput1" class="form-label">KDV</label>
                                                    <select name="tax[]" id="taxInput1"
                                                            class="form-select tax-select">
                                                        <option @if($item['tax'] == 0) selected @endif value="0">0%</option>
                                                        <option @if($item['tax'] == 1) selected @endif value="1">1%</option>
                                                        <option @if($item['tax'] == 8) selected @endif value="8">10%</option>
                                                        <option @if($item['tax'] == 18) selected @endif value="18" selected>18%</option>
                                                    </select>
                                                </div -->
                                                <!-- div class="col-md-12 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Açıklama</p>
                                                    <textarea class="form-control" rows="2" name="description[]" id="description">{{$item['description']}}</textarea>
                                                </div -->
                                                <input type="hidden" name="reason_id[]" value="9" />
                                                <input type="hidden" name="description[]" value="" />
                                                <input type="hidden" name="tax[]" value="20" />
                                                <input type="hidden" name="discount[]" value="0" />
                                            </div>
                                        </div>
                                        <hr class="mx-n4">
                                    </div>
                                    @endforeach
                                @else
                                    <div id="test" class="pt-0 pt-md-4">
                                        <div class="cloneBox"></div>
                                        <div class="border rounded position-relative pe-0">
                                            <div class="row w-100 m-0 p-3">
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Stok</p>
                                                    <select name="stock_card_id[]"
                                                            class="form-select item-details select2 mb-2">
                                                        @foreach($stocks as $stock)
                                                            <option value="{{$stock->id}}">
                                                                {{$stock->name}} -
                                                                <small> {{$stock->brand->name}}</small> - <b>
                                                                        <?php  $datas = json_decode($stock->version(), TRUE);
                                                                        foreach ($datas as $mykey => $myValue) {
                                                                            echo "$myValue,";
                                                                        }
                                                                        ?></b>
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Seri No</p>
                                                    <input type="text" class="form-control" name="serial[]" readonly />
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Renk</p>
                                                    <select name="color_id[]"
                                                            class="form-select item-details select2 mb-2">
                                                        @foreach($colors as $color)
                                                            <option value="{{$color->id}}">{{$color->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Neden</p>
                                                    <select name="reason_id[]"
                                                            class="form-select item-details select2 mb-2">
                                                        @foreach($reasons as $reason)
                                                            @if($reason->type == 5)
                                                                <option value="{{$reason->id}}">{{$reason->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div -->
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Gerçek Maliyet</p>
                                                    <input type="text" class="form-control invoice-item-price" name="cost_price[]" required/>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Maliyet</p>
                                                    <input type="text" class="form-control invoice-item-price" name="base_cost_price[]" required/>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                                    <input type="text" class="form-control invoice-item-price" name="sale_price[]" required/>
                                                </div>
                                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Adet</p>
                                                    <input type="number" class="form-control invoice-item-qty" name="quantity[]" min="1" max="5000" required>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Stok Takibi</p>
                                                    <input type="text" class="form-control"  value="0" name="tracking_quantity[]" />
                                                </div>
                                                <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="discountInput"
                                                           class="form-label">İndirim (%)</label>
                                                    <input type="number" class="form-control"
                                                           id="discountInput"
                                                           min="0" max="100" name="discount[]" -->
                                                </div>
                                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="taxInput1" class="form-label">Şube</label>
                                                    <select name="seller_id[]"  id="taxInput1"
                                                            class="form-select tax-select">
                                                        @foreach($sellers as $seller)
                                                            <option value="{{$seller->id}}">{{$seller->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="taxInput2" class="form-label">Depo</label>
                                                    <select name="warehouse_id[]" id="taxInput2"
                                                            class="form-select tax-select">
                                                        @foreach($warehouses as $warehouse)
                                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <label for="taxInput1" class="form-label">KDV</label>
                                                    <select name="tax[]" id="taxInput1"
                                                            class="form-select tax-select">
                                                        <option value="0">0%</option>
                                                        <option value="1">1%</option>
                                                        <option value="8">10%</option>
                                                        <option value="18" selected>18%</option>
                                                    </select>
                                                </div -->
                                                <!-- div class="col-md-12 col-12 mb-md-0 mb-3 ps-md-0">
                                                    <p class="mb-2 repeater-title">Açıklama</p>
                                                    <textarea class="form-control" rows="2" name="description[]" id="description"></textarea>
                                                </div -->

                                                <input type="hidden" name="reason_id[]" value="9" />
                                                <input type="hidden" name="description[]" value="" />
                                                <input type="hidden" name="tax[]" value="20" />
                                                <input type="hidden" name="discount[]" value="0" />

                                            </div>
                                        </div>
                                        <hr class="mx-n4">
                                    </div>
                                @endif
                                <div id="myList1">

                                </div>
                                <!-- button type="button" onclick="myFunction()" class="btn btn-secondary clon">EKLE</button -->

                            </div>
                            <!-- div class="col-md-12">
                                <button style="width: 100%;" type="submit" class="btn btn-danger">Kaydet</button>
                            </div -->

                        </div>
                    </form>



                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td style="font-size: 13px">Toplam Maliyet</td>
                                <td style="font-size: 13px;text-align: center">{{$invoice->totalCost()}} TL</td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px">Toplam Dest. Sat. Tutarı</td>
                                <td style="font-size: 13px;text-align: center">{{$invoice->totalBaseCost()}} TL</td>
                            </tr>
                            <tr>
                                <td style="font-size: 13px">Toplam Satış Tutarı</td>
                                <td style="font-size: 13px;text-align: center">{{$invoice->totalSale()}} TL</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body" style="display: none">
                        <div>
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]" value="{{$invoice->credit_card}}" id="credit_card"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order" value="{{$invoice->cash}}"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Taksit</label>
                            <input type="text" name="payment_type[installment]" value="{{$invoice->installment}}" id="installment"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]" value="0" id="credit_card"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order" value="0"
                                   class="form-control">
                        </div>
                        <div>
                            <label class="form-label" for="fullname">Taksit</label>
                            <input type="text" name="payment_type[installment]" value="0" id="installment"
                                   class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td>Stok Adı</td>
                                <td>Adet</td>
                                <td>Renk</td>
                                <td>Maliyet</td>
                                <td>Destekli Satış Fiyatı</td>
                                <td>Satış Fiyatı</td>
                                <td>İşlemler</td>
                            </tr>

                            @if(isset($invoice) and !is_null($invoice->detail))
                                @foreach($invoice->detail as $key => $item)
                                    <tr>
                                        <td>{{\App\Models\StockCard::find($item['stockcardid'])->name}}</td>
                                        <td>{{$item['quantity']}}</td>
                                        <td>{{\App\Models\Color::find($item['color_id'])->name}}</td>
                                        <td>@if($item['cost_price'] != "") {{number_format($item['cost_price'],2)}} @else Fiyat Yok @endif </td>
                                        <td>@if($item['cost_price'] != "") {{number_format($item['base_cost_price'],2)}} @else  Fiyat Yok @endif </td>
                                        <td>@if($item['cost_price'] != "") {{number_format($item['sale_price'],2)}} @else  Fiyat Yok @endif </td>
                                        <!-- td>
                                             <a href="{{route('invoice.stockmovementdelete',['id' => $item['stockcardid']])}}" class="btn btn-danger">Sil</a>
                                        </td -->
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>


        @endsection
        @include('components.customermodal')


        <div class="modal fade" id="editItem" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3>Fatura Maddesi</h3>
                        </div>
                        <form ng-submit="editItemSave()"   method="post" id="smsForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="@{{itemData.invoice_id}}" name="id"/>
                            <input type="hidden" value="@{{key}}" name="key"/>
                            <div class="card-body">
                                <div class="row w-100 m-0 p-3">
                                    <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Stok</p>
                                        <select name="stock_card_id"  class="form-select item-details select2 mb-2">
                                            @foreach($stocks as $stock)
                                                <option value="{{$stock->id}}" ng-selected="{{$stock->id}} == itemData.stock_card_id">
                                                    {{$stock->name}} -
                                                    <small> {{$stock->brand->name}}</small> - <b>
                                                            <?php  $datas = json_decode($stock->version(), TRUE);
                                                            foreach ($datas as $mykey => $myValue) {
                                                                echo "$myValue,";
                                                            }
                                                            ?></b>
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Seri No</p>
                                        <input type="text" class="form-control" name="serial"  placeholder="11111111"/>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Renk</p>
                                        <select name="color_id"
                                                class="form-select item-details select2 mb-2">
                                            @foreach($colors as $color)
                                                <option  ng-selected="itemData.color_id == {{$color->id}}"  value="{{$color->id}}">{{$color->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Neden</p>
                                        <select name="reason_id"
                                                class="form-select item-details select2 mb-2">
                                            @foreach($reasons as $reason)
                                                @if($reason->type == 5)
                                                    <option    ng-selected="itemData.reason_id == {{$reason->id}}" value="{{$reason->id}}">{{$reason->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div -->
                                    <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Gerçek Maliyet</p>
                                        <input type="text" class="form-control invoice-item-price" value="@{{itemData.cost_price}}"
                                               name="cost_price"  />
                                    </div>

                                    <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Maliyet</p>
                                        <input type="text" class="form-control invoice-item-price" value="@{{itemData.base_cost_price}}"
                                               name="base_cost_price" />
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Satış Fiyatı</p>
                                        <input type="text" class="form-control invoice-item-price"
                                               name="sale_price" value="@{{itemData.quantity}}"/>
                                    </div>
                                    <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Adet</p>
                                        <input type="number" class="form-control invoice-item-qty"
                                               name="quantity" value="@{{itemData.quantity}}" min="1" max="5000">
                                    </div>
                                    <input type="hidden" name="tax[]" value="20" />
                                    <input type="hidden" name="description[]" value="" />
                                    <input type="hidden" name="discount[]" value="" />
                                    <input type="hidden" name="reason_id[]" value="9" />

                                    <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <label for="discountInput"
                                               class="form-label">İndirim (%)</label>
                                        <input type="number" class="form-control"
                                               id="discountInput"
                                               min="0" max="100" value="@{{itemData.discount}}" name="discount">
                                    </div -->
                                    <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                        <label for="taxInput1" class="form-label">Şube</label>
                                        <select name="seller_id" id="taxInput1"
                                                class="form-select tax-select">
                                            @foreach($sellers as $seller)
                                                <option  ng-selected="itemData.seller_id == {{$seller->id}}"
                                                         value="{{$seller->id}}">{{$seller->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-12 mb-md-0 mb-3 ps-md-0">
                                        <label for="taxInput2" class="form-label">Depo</label>
                                        <select name="warehouse_id" id="taxInput2"
                                                class="form-select tax-select">
                                            @foreach($warehouses as $warehouse)
                                                <option  ng-selected="itemData.warehouse_id == {{$warehouse->id}}" value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- div class="col-md-3 col-12 mb-md-0 mb-3 ps-md-0">
                                        <label for="taxInput1" class="form-label">KDV - @{{itemData.tax}}</label>
                                        <select name="tax]" id="taxInput1" class="form-select tax-select">
                                            <option ng-selected="itemData.tax == 0" value="0">0%</option>
                                            <option ng-selected="itemData.tax == 1" value="1">1%</option>
                                            <option ng-selected="itemData.tax == 8" value="8">10%</option>
                                            <option ng-selected="itemData.tax == 18" value="18">18%</option>
                                        </select>
                                    </div -->
                                    <!-- div class="col-md-12 col-12 mb-md-0 mb-3 ps-md-0">
                                        <p class="mb-2 repeater-title">Açıklama</p>
                                        <textarea class="form-control" rows="2" name="description"  id="description">@{{itemData.description}}</textarea>
                                    </div -->
                                </div>
                                <div class="w-100 m-0 p-3">
                                    <button type="submit" class="btn btn-danger btn-buy-now w-100">Kaydet</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @section('custom-js')

            <script>


                function save() {
                    var postUrl = window.location.origin + '/invoice/store';   // Returns base URL (https://example.com)
                    $.ajax({
                        type: "POST",
                        url: postUrl,
                        data: $("#invoiceForm").serialize(),
                        dataType: "json",
                        encode: true,
                        beforeSend: function () {
                            $('#loader').removeClass('display-none')
                        },
                        success: function (data) {
                            window.location.href = "{{route('invoice.stockcardmovementform')}}?id=" + data + "";
                        },
                        error: function (xhr) { // if error occured
                            alert("Error occured.please try again");
                            $(placeholder).append(xhr.statusText + xhr.responseText);
                            $(placeholder).removeClass('loading');
                        }

                    });
                }


            </script>

            <script>
                app.controller("mainController", function ($scope, $http, $httpParamSerializerJQLike, $window) {
                    $scope.getCustomers = function () {
                        var postUrl = window.location.origin + '/customers?type=account';   // Returns base URL (https://example.com)
                        $http({
                            method: 'GET',
                            //url: './comment/change_status?id=' + id + '&status='+status+'',
                            url: postUrl,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function successCallback(response) {
                            $scope.customers = response.data;
                        });
                    }
                    $scope.customerSave = function () {
                        var postUrl = window.location.origin + '/custom_customerstore';   // Returns base URL (https://example.com)
                        var formData = $("#customerForm").serialize();

                        $http({
                            method: 'POST',
                            url: postUrl,
                            data: formData,
                            dataType: "json",
                            encode: true,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function successCallback(response) {
                            $scope.getCustomers();
                            $(".customerinformation").html('<p className="mb-1">\'+data.address+\'</p>\n' + '<p className="mb-1">\'+data.phone1+\'</p>');
                            $('#selectCustomer option:selected').val(response.data.id);
                            var modalDiv = $("#editUser");
                            modalDiv.modal('hide');
                            modalDiv
                                .find("input,textarea,select")
                                .val('')
                                .end()
                                .find("input[type=checkbox], input[type=radio]")
                                .prop("checked", "")
                                .end();
                        });
                    }

                    $scope.editItem = function (id,key) {
                        var postUrl = window.location.origin + '/custom_editItem?id='+id+'&key='+key;   // Returns base URL (https://example.com)
                        $http({
                            method: 'POST',
                            url: postUrl,
                            dataType: "json",
                            encode: true,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function successCallback(response) {
                            $("#editItem").modal('show');
                            console.log(response);
                            $scope.key = key;
                            $scope.itemData = response.data;
                        });
                    }

                    $scope.editItemSave = function () {
                        var postUrl = window.location.origin + '/invoice/itemSave';   // Returns base URL (https://example.com)
                        var formData = $("#smsForm").serialize();

                        $http({
                            method: 'POST',
                            url: postUrl,
                            data: formData,
                            dataType: "json",
                            encode: true,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }).then(function successCallback(response) {
                            window.location.reload();
                        });
                    }

                });
            </script>


            <script>

                function myFunction() {
                    $("#test").find(".select2").each(function (index) {
                        $("select.select2-hidden-accessible").select2('destroy');
                    });
                    const node = document.getElementById("test");
                    const clone = node.cloneNode(true);
                    document.getElementById("myList1").appendChild(clone);
                    $("select.select2").select2();

                }
            </script>
            <script>
                $('#invoiceForm').on('keyup', '#description', function (e) {
                    var key = e.which;
                    switch (key) {
                        case 9: // enter
                            alert('Enter key pressed.');
                            break;
                        default:
                            break;
                    }
                });
            </script>

@endsection

