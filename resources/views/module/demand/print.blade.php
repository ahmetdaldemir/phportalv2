<div class="con-exemple-prompt">
    <div id="barcodeFormSet1">

        <table style="width:100%;margin: 0px auto;">
            <thead>

            <tr style="    text-align: center; background: #adadad;">
                <td style="font-weight: bold; padding-left: 5px; width: 120px; height: 21px;">Stok/ Marka/Model </td>
                <td style="text-align: center; width: 10px; height: 21px;">Renk</td>
                <td style="font-weight: bold; padding-left: 5px; height: 21px; width: 60px;">Personel</td>
                <td style="text-align: center; width: 10px; height: 21px;">Açıklama</td>
            </tr>
            </thead>
            <tbody>

            @foreach($demands as $item)
               <?php $stockcard = \App\Models\StockCard::find($item->stock_card_id); ?>
                <tr>
                <td>{{$stockcard->name}}/{{$stockcard->brand->name}}/{{$stockcard->version()}}/{{$stockcard->category->name}}</td>
                <td>{{\App\Models\Color::find($item->color_id)->name}}</td>
                <td style="font-weight: bold; padding-left: 5px; height: 21px; width: 60px;">{{\App\Models\User::find($item->user_id)->name}}</td>
                <td style="text-align: center; width: 10px; height: 21px;">{{$item->description}}</td>
            </tr>
            @endforeach
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
