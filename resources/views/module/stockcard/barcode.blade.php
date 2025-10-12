<html>
<head>
    <title>Aksesuarlar | PhoneHospital</title>

    <style>
        body > div {
            padding: 5px 20px;
        }
    </style>
</head>
<body>
 @foreach($data as $value)
 <div id="printableArea{{$value['id']}}" style="width: 390px; height: 170px;padding: 24px 12px 15px 12px;">
    <div style="width: 15%; float: left;">
        <img src="{{asset('img/147836.png')}}" class="logoClass" style="rotate:-90deg;margin-top: 23px;width: 130px;margin-left: -35px;" alt=""></div>
    <div style="width: 85%; float: right;    text-transform: uppercase;">
        <div style="font-size: 1rem; width:85%;text-align: center; font-weight: bold;margin-right: 40px;">
            <div style="font-size: 17px;">{!! $value['stock_name'] !!} {!! $value['brand_name'] !!} {!! $value['versions'] !!} - {!! $value['color_name'] !!}</div>
            <div style="font-size: 15px;"> {{$value['category_sperator_name']}} {{$value['category_name']}}</div>
        </div>
        <div style="position: relative; float: left; width: 100%; display: block;">
            <div style="width: 85%; float: left; margin-top: 4px;">
                <div style="width: 100px;height: 50px">
                    <?php
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    echo '<img style="width: 250px;height: 47px;margin-left: 12px;" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value['serial_number'], $generator::TYPE_CODE_128)) . '">';
                    ?>
                </div>
                <div style="text-align: center; display: block; font-size: 21px;">{{$value['serial_number']}}</div>
            </div>
            <div  style="transform: rotate(90deg); position: absolute; right: -21px; top: 0px; font-weight: bold; font-size:26px;">

                {{number_format(str_replace(",","",$value['sale_price']),2)}} TL
            </div>
        </div>
    </div>
</div>
@endforeach

</body>
</html>
