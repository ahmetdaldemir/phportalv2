<div class="modal fade modal-lg" id="checkoutModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>Tahsilat</h3>
                </div>
                <form  class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework" onsubmit="return false" novalidate="novalidate">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label" for="fullname">Ödemeyi Alan</label>
                            <select id="payment_person" name="payment_person" class="select2 form-select">
                                @foreach($users as $user)
                                    <option @if(isset($technical_services) && $technical_services->payment_person == $user->id) selected
                                            @endif  value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                         </div>
                        <div class="col-md-6">
                            <label class="form-label" for="fullname">Teknik Personel</label>
                            <select id="technical_person" name="technical_person" class="select2 form-select">
                                @foreach($users as $user)
                                    <option @if(isset($technical_services) && $technical_services->technical_person == $user->id) selected @endif  value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr class="my-4 mx-n4">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label" for="fullname">Kredi Kartı</label>
                            <input type="text" name="payment_type[credit_card]" id="credit_card" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="fullname">Nakit</label>
                            <input type="text" name="payment_type[cash]" id="money_order" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="fullname">Taksitli</label>
                            <input type="text" name="payment_type[installment]" id="installment"  class="form-control">
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3">Submit</button>
                        <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                    <input type="hidden"></form>
            </div>
        </div>
    </div>
</div>
