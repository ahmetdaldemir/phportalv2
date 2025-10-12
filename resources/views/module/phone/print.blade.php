


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



    @foreach($movements as $value)
        <script>
            $(document).ready(function() {
                var divName = 'printableArea{{$value->id}}';
                var printContents = document.getElementById(divName).innerHTML;
                w = window.open();
                w.document.write(printContents);
                w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');
                w.document.close(); // necessary for IE >= 10
                w.focus(); // necessary for IE >= 10
            });
        </script>
    @endforeach
</head>
<body>
@foreach($movements as $value)
    <div id="printableArea{{$value->id}}" style="width: 350px; height: 150px;">
        <div style="width: 18%; float: left;">
            <img src="{{asset('img/147836.png')}}" style="rotate: 90deg;
    margin-top: 23px;
    width: 114px;
    margin-left: -10px;" alt=""></div>
        <div style="width: 75%; float: right;">
            <div style="font-size: 1rem; text-align: center; font-weight: bold;">
                <div style="font-size: 15px;"> {{$value->stockcard()->brand->name}}
                        <?php $datas = json_decode($value->stockcard()->version(), TRUE);
                        foreach ($datas as $mykey => $myValue) {
                            echo "$myValue,";
                        }
                        ?></div>
                <div style="font-size: 15px;">{{$value->stockcard()->name}}</div>
            </div>
            <div style="position: relative; float: left; width: 100%; display: block;">
                <div style="width: 80%; float: left; margin-top: 4px;">
                    <div style="width: 100px;height: 50px">
                            <?php
                            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                            echo '<img style="width: 145px;height: 47px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->serial_number, $generator::TYPE_CODE_128)) . '">';
                            ?>
                    </div>
                    <div style="text-align: center; display: block; font-size: 15px;">{{$value->serial_number}}</div>
                </div>
                <div
                    style="transform: rotate(90deg); position: absolute; right: 0px; top: 20px; font-weight: bold; font-size:20px;">
                    {{$value->sale_price}} ₺
                </div>
            </div>
        </div>
    </div>
    <button style="display: none" id="printableAreaButton" onclick="printDiv()">YAZDIR</button>
@endforeach


</body>
</html>
