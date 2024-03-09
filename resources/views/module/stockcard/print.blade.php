@foreach($data as $value)
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
            .logoClass{
                rotate: -90deg;
            }
        }
    </style>
</head>
<body>

 <div id="printableArea{{$value['id']}}" style="width: 350px; height: 150px;">
    <div style="width: 18%; float: left;">
        <img src="{{asset('img/147836.png')}}" class="logoClass" style="transform: translate(0px,-5px)rotate(-90deg);
                            -webkit-transform: translate(0px,-5px)rotate(-90deg);
                            -o-transform: translate(0px,-5px)rotate(-90deg);
                            -moz-transform: translate(0px,-5px)rotate(-90deg);rotate: 90deg;margin-top: 23px;width: 114px;margin-left: -10px;" alt=""></div>
    <div style="width: 75%; float: right;">
        <div style="font-size: 1rem; text-align: center; font-weight: bold;">
            <div style="font-size: 15px;"> {{$value['brand_name']}}
                {{$value['version']}}</div>
            <div style="font-size: 15px;">{{$value['name']}}</div>
        </div>
        <div style="position: relative; float: left; width: 100%; display: block;">
            <div style="width: 80%; float: left; margin-top: 4px;">
                <div style="width: 100px;height: 50px">
                    <?php
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    echo '<img style="width: 145px;height: 47px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value['serial_number'], $generator::TYPE_CODE_128)) . '">';
                    ?>
                </div>
                <div style="text-align: center; display: block; font-size: 15px;">{{$value['serial_number']}}</div>
            </div>
            <div  style="transform: rotate(90deg); position: absolute; right: 0px; top: 20px; font-weight: bold; font-size:20px;">
               {{$value['sale_price']}} TL
            </div>
        </div>
    </div>
</div>

</body>
</html>
@endforeach
