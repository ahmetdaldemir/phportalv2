<html>
<head>
    <title>Aksesuarlar | PhoneHospital</title>

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <script>
        function printDiv() {
            var divName = 'printableArea';
            var printContents = document.getElementById(divName).innerHTML;
            w = window.open();
            w.document.write(printContents);
            w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
            w.document.close(); // necessary for IE >= 10
            w.focus(); // necessary for IE >= 10
        }
    </script>
</head>
<body>
<div  id="printableArea">
<table  style="width: 700px;border:1px solid #ccc;font-size: 14px">
    <tr style="height: 400px;border-bottom: 1pc solid #000">
        <td colspan="2"></td>
    </tr>
    <tr><td colspan="2" style="text-align: center;font-weight: 700">CİHAZ ALIŞ TAAHHÜTNAMESİ</td></tr>
    <tr border="1">
        <td colspan="2">
            Şahsıma ait olan {{$phone->imei}} IMEI no’lu {{$phone->brand->name}} Marka {{$phone->version->name}} Model cihazımı ERK TELEKOM
            NAKLİYAT PETROL TİC. LTD. ŞTİ. ünvanlı firmaya kendi rızam ile aşağıdaki 15 maddeyi kontrol ederek sattım. Mezkur
            telefonun aşağıda belirttiğim tarihten önce şahsıma ait olduğunu, kayıp, çalıntı, kaçak ve bunlarla sınırlı olmamak
            kaydıyla herhangi bir yasadışı unsurunun olmadığını, her ne ad altında olursa olsun meydana gelebilecek maddi ve
            /veya cezai her türlü talep karşısında sorumluluğun şahsıma ait olduğunu, bu nedenle teslim alan ERK TELEKOM
            NAKLİYAT PETROL TİC. LTD. ŞTİ. ünvanlı bayinin ve /veya 3. Şahısların uğrayabileceği her türlü zararı başlıca hüküm
            istihsaline gerek olmaksızın, tarafımın bildirim yapılmasını mütakip derhal ve nakden ve defaten tazmin edeceğimi
            gayri kabili rücu ve koşulsuz olarak kabul, beyan ve taahhüt ederim.
        </td>
    </tr>
    <tr>
        <table style="width: 700px;margin-top: 20px;margin-bottom: 30px">
            <tr>
                <td>Tarih : {{\Carbon\Carbon::parse($phone->created_at)->format('d-m-Y')}}</td>
                <td>Adı Soyadı {{$phone->customer->fullname??"Genel Müşteri"}}</td>
            </tr>
            <tr>
                <td>GSM {{$phone->customer->phone1??"000 000 00 00"}}</td>
                <td>İmza :</td>
            </tr>
            <tr>
                <td colspan="2">Adres</td>
             </tr>
        </table>
    </tr>
    <tr style="margin-top: 20px">
        <table style="width: 700px;margin-top: 20px;margin-bottom: 30px">
            <tr>
                <td colspan="2" style="text-align: center;font-weight: 700"">2.EL CİHAZ KONTROL UNSUZLARI</td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" /> ROOT KONTROLÜ<br>
                    <input type="checkbox" /> IMEİ KONTROLÜ<br>
                    <input type="checkbox" /> SENSÖR<br>
                    <input type="checkbox" /> KASA KONTROLÜ<br>
                    <input type="checkbox" /> AÇILIYOR MU ?<br>
                    <input type="checkbox" /> SES KONTROLÜ (ARAMA YAPILARAK)<br>
                    <input type="checkbox" /> TİTREŞİM<br>
                    <input type="checkbox" /> BLUETOOTH KONTROLÜ<br>
                    <input type="checkbox" /> İNTERNET KONTROLÜ<br>
                    <input type="checkbox" /> EKRAN PİKSEL KONTROLÜ<br>
                    <input type="checkbox" /> SD KART KONTROLÜ<br>
                    <input type="checkbox" /> WİRELEES KONTROLÜ TESLİM ALAN ;<br>
                    <input type="checkbox" /> KAMERA KONTROLÜ ADI SOYADI :<br>
                    <input type="checkbox" /> ŞARJ KONTROLÜ İMZA :<br>
                    <input type="checkbox" /> FACE ID KONTROLÜ<br>
                </td>
                <td style="width: 300px">
                    TESLİM ALAN ;<br>
                    ADI SOYADI :<br>
                    İMZA :<br>
                </td>
            </tr>

        </table>
    </tr>
</table>
</div>
<button id="printableAreaButton" onclick="printDiv()">YAZDIR</button>
</body>
</html>
