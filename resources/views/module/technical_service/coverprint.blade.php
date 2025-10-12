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
                            <td style="text-align: left;"><img alt=""
                                                               src="Helper/Barcode?barcodeText=17017&amp;width=80&amp;height=22">
                                <div>Form No: PHCOV{{date('Y')}}{{$cover->id}}</div>
                            </td>
                            <td style="font-size: 25px; text-align: center;">Phone Hospital - {{$cover->seller->name}}
                            </td>
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
                            <td>
                                <table>
                                    <tr>
                                        <td style="font-weight: bold; padding-left: 5px; width: 120px;">Müşteri Adı</td>
                                        <td style="text-align: center; width: 10px;">:</td>
                                        <td class="detay">{{$cover->customer->fullname}}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; padding-left: 5px; width: 120px;">Telefon</td>
                                        <td style="text-align: center; width: 10px;">:</td>
                                        <td class="detay">{{$cover->customer->phone1}}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; padding-left: 5px; width: 120px;">Email</td>
                                        <td style="text-align: center; width: 10px;">:</td>
                                        <td class="detay">{{$cover->customer->email}}</td>
                                    </tr>
                                </table>
                            </td>
                            <td>-</td>
                        </tr>

                        </tbody>
                    </table>
                    <table
                        style="height: 208px; width: 100%; border: 1px solid black; font-size: 14px; margin-top: 5px;">
                        <tbody>
                        <tr>
                            <td style="width: 50%;vertical-align: text-top;">
                                <table>
                                    <tr>
                                        <td>Marka/Model : {{$cover->brand->name}} {{$cover->version->name}}</td>
                                    </tr>

                                    <tr>
                                        <td>Hizmet Tipi : {{$cover->type}}</td>
                                    </tr>
                                    <tr>
                                        <td>Kaplama Bilgisi :{{$cover->coating_information}}
                                            /{{$cover->print_information}}</td>
                                    </tr>
                                    <tr>
                                        <td>Fiyat : {{number_format($cover->customer_price,2) }} ₺</td>
                                    </tr>
                                    <tr>
                                        <td>İşlem Zamanı : {{$cover->created_at}}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%;vertical-align: text-top;">
                                {{setting('site.cover')}}
                            </td>
                        </tr>
                    </table>
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
                        <td style="text-align: center;"><b>Teslim Eden / İmza</b></td>
                        <td style="text-align: center;"><b>Teslim Alan / İmza</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">Phone Hospital - {{$cover->delivery->name}}</td>
                        <td style="text-align: center;">{{$cover->customer->fullname}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 60px;"></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        </tbody>
        </table>
    </div>
    <button onclick="print(1)" style="width: 100%" class="btn btn-danger">Yazdır</button>
</div>
<script>
    function print(id) {

        var divName = 'barcodeFormSet' + id + '';
        var printContents = document.getElementById(divName).innerHTML;
        w = window.open();
        w.document.write(printContents);
        w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
        w.document.close();
        w.focus();
    }

    function getForm(id) {

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
