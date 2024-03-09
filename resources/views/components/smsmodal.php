
<div class="modal fade" id="smsModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-edit-user">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Yeni Mesaj Gönder</h3>
                </div>
                <form action="javascript():;" method="post" id="smsForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id"/>
                    <div class="card-body">
                         <div class="form-group">
                             <select class="form-control">
                                 @foreach($sms as $item)
                                 <option></option>
                                 @endforeach
                             </select>
                         </div>
                        <div>
                            <button onclick="customerSave()" type="button" class="btn btn-danger btn-buy-now">Kaydet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('custom-css')
    <style>
        #cont{
            position: relative;

        }
        .son{
            position: absolute;
            top:0;
            left:0;

        }




        #control{
            position:absolute;

            left:0;

            z-index: 50;
            background: HoneyDew ;
            opacity:0.7;
            color:#fff;
            text-align: center;

        }
        #snap{
            background-color: dimgray ;

        }
        #retake{
            background-color: coral ;

        }

        #close{
            background-color: lightcoral ;

        }
        .hov{
            opacity:.8;
            transition: all .5s;
        }
        .hov:hover{
            opacity:1;

            font-weight: bolder;
        }
        /*#canvas{
          z-index: 1;
        }
        #video{
          z-index: 3;
        }*/

        html:not([dir=rtl]) .modal-simple .btn-close {
            right: -2rem;
        }

        html:not([dir=rtl]) .modal .btn-close {
            transform: translate(23px, -25px);
        }

        .modal-simple .btn-close {
            position: absolute;
            top: -2rem;
        }

        .modal .btn-close {
            background-color: #fff;
            border-radius: 0.5rem;
            opacity: 1;
            padding: 0.635rem;
            box-shadow: 0 0.125rem 0.25rem rgb(161 172 184 / 40%);
            transition: all .23s ease .1s;
        }
    </style>
@endsection
