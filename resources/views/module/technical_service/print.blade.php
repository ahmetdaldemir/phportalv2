<div class="con-exemple-prompt">
     <div id="barcodeFormSet1">
         <table style="margin: 0px auto;">
            <tbody>
            <tr>
                <td style="height: 52px;">&nbsp;</td>
                <td style="height: 52px; width: 720px;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="text-align: left;"><img alt="" src="Helper/Barcode?barcodeText=17017&amp;width=80&amp;height=22">
                                <div>Form No: PHTEC{{$technical_service->id}}</div>
                            </td>
                            <td style="font-size: 25px; text-align: center;">Phone Hospital - {{$technical_service->seller->name}}</td>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px; text-align: right;">
                                <b>phonehospital.com.tr</b><br> <b>444 23 70</b></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style="height: 52px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="height: 287px;">&nbsp;</td>
                <td style="height: 287px; width: 720px;">
                    <table style="height: 62px; width: 100%; border: 1px solid black; font-size: 14px;">
                        <tbody>
                        <tr>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px;">Müşteri
                                Adı
                            </td>
                            <td style="text-align: center; width: 10px;">:</td>
                            <td class="detay">{{$technical_service->customer->fullname}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px;">Telefon</td>
                            <td style="text-align: center; width: 10px;">:</td>
                            <td class="detay">{{$technical_service->customer->phone1}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px;">Email</td>
                            <td style="text-align: center; width: 10px;">:</td>
                            <td class="detay">{{$technical_service->customer->email}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <table style="height: 208px; width: 100%; border: 1px solid black; font-size: 14px; margin-top: 5px;">
                        <tbody>
                        <tr>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px; height: 21px;"> Marka/Model </td>
                            <td style="text-align: center; width: 10px; height: 21px;">:</td>
                            <td class="detay" style="height: 21px;">{{$technical_service->brand->name ?? 'Buluanmadı'}} - {{$technical_service->version->name  ?? 'Buluanmadı'}}</td>
                            <td style="font-weight: bold; padding-left: 5px; height: 21px; width: 60px;"> IMEI </td>
                            <td style="text-align: center; width: 10px; height: 21px;">:</td>
                            <td style="height: 21px; padding-right: 5px; text-align: right; width: 120px;">{{$technical_service->imei}}</td>
                        </tr>

                        <tr>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px; height: 21px;">
                                Son Durum
                            </td>
                            <td style="text-align: center; width: 10px; height: 21px;">:</td>
                            <td class="detay" style="height: 21px;">{{\App\Models\TechnicalService::STATUS[$technical_service->status??"new"]}}</td>
                            <td style="font-weight: bold; padding-left: 5px; height: 21px; width: 60px;">Fiyat</td>
                            <td style="text-align: center; width: 10px; height: 21px;">:</td>
                            <td style="height: 21px; padding-right: 5px; text-align: right; width: 120px;">{{$technical_service->customer_price}} ₺</td>
                        </tr>
                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;">Cihaz Şifresi  </td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="" valign="top" class="detay"> {{$technical_service->device_password}}</td>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px;">Geliş Tarihi</td>
                            <td style="text-align: center; width: 10px;">:</td>
                            <td class="detay">{{$technical_service->created_at}}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding-left: 5px; width: 120px;"></td>
                            <td style="text-align: center; width: 10px;"></td>
                            <td class="detay"></td>
                            <td style="font-weight: bold; padding-left: 5px; height: 21px; width: 60px;">Ücret</td>
                            <td style="text-align: center; width: 10px; height: 21px;">:</td>
                            <td style="height: 21px; padding-right: 5px; text-align: right; width: 120px;">{{$technical_service->payment_status == 0?"Alınmadı":"Alındı"}}</td>
                        </tr>
                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;">Arıza Bilgisi</td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">{{$technical_service->fault_information}}</td>
                        </tr>

                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;"> </td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">
                                @if($technical_service->fault_category)
                                <?php
                                $x =  \App\Models\TechnicalProcess::whereIn('id',$technical_service->fault_category)->get();
                                $a = $x->pluck('name');
                                $as =json_encode($a,JSON_UNESCAPED_UNICODE);
                                $as = str_replace('["','',$as);
                                $as = str_replace('"]','',$as);
                                $as = str_replace('"','',$as);
                                ?>
                                {!! $as !!}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;">Aksesuar</td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">{{$technical_service->accessories}}</td>
                        </tr>
                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;"> </td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">
                                @if($technical_service->accessory_category)
                                <?php
                                $x =  \App\Models\TechnicalProcess::whereIn('id',$technical_service->accessory_category)->get();
                                $a = $x->pluck('name');
                                        $as = json_encode($a,JSON_UNESCAPED_UNICODE);
                                        $as = str_replace('["','',$as);
                                        $as = str_replace('"]','',$as);
                                        $as = str_replace('"','',$as);
                                ?>
                                    {!! $as !!}

                                @endif

                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;">Fiziksel Durum</td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">
                                @if($technical_service->physically_category)
                                <?php
                                $x =  \App\Models\TechnicalProcess::whereIn('id',$technical_service->physically_category)->get();
                                        $a = $x->pluck('name');
                                        $as = json_encode($a,JSON_UNESCAPED_UNICODE);
                                        $as = str_replace('["','',$as);
                                        $as = str_replace('"]','',$as);
                                        $as = str_replace('"','',$as);
                                ?>
                                    {!! $as !!}

                                @endif

                                </td>
                        </tr>
                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;"> </td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">
                                {{$technical_service->physical_condition}}
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="font-weight: bold; padding-left: 5px; width: 120px;">İşlemler </td>
                            <td valign="top" style="text-align: center; width: 10px;">:</td>
                            <td colspan="4" valign="top" class="detay">
                                @foreach($technical_service_process as $item)
                                <p>{{\App\Models\TechnicalService::STATUS[$item->status]}} {{\Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s')}}</p>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="height: 25px;"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style="height: 287px;">&nbsp;</td>
            </tr>
            <tr>
                <td style="height: 30px;">&nbsp;</td>
                <td style="height: 30px; width: 720px;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="text-align: center;"><b>Teslim Alan / İmza</b></td>
                            <td style="text-align: center;"><b>Teslim Eden / İmza</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">Phone Hospital - {{$technical_service->delivery->name??null}}</td>
                            <td style="text-align: center;">{{$technical_service->customer->fullname}}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="height: 60px;"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="height: 30px;">{{setting('site.adwords')}}</td>

            </tr>
            </tbody>
        </table>
    </div>
    <button onclick="print(1)" style="width: 100%" class="btn btn-danger">Yazdır</button>
</div>
<script>
    function print(id) {

        var divName = 'barcodeFormSet'+id+'';
        var printContents = document.getElementById(divName).innerHTML;
        w = window.open();
        w.document.write(printContents);
        w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
        w.document.close();
        w.focus();
    }

    function getForm(id)
    {

    }
</script>
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #section-to-print, #section-to-print * {
            visibility: visible;
        }

        #section-to-print {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>
