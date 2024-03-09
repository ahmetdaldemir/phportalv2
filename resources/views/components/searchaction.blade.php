<form action="{{route('stockcard.index')}}" id="stockSearch" method="get">
    @csrf
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label" for="multicol-username">Stok</label>
            <input type="text" class="form-control" placeholder="············" name="stockName">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="multicol-email">Marka</label>
            <div class="input-group input-group-merge">
                <select type="text" name="brand" class="form-select" onchange="getVersion(this.value)" style="width: 100%">
                    <option value="">Tümü</option>
                    @foreach($brands as $brand)
                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-password-toggle">
                <label class="form-label" for="multicol-password">Model</label>
                <div class="input-group input-group-merge">
                    <select type="text" id="version_id" name="version" class="form-select" style="width: 100%">
                        <option value="">Tümü</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-password-toggle">
                <label class="form-label" for="multicol-password">Kategori</label>
                <div class="input-group input-group-merge">
                    <select type="text" name="category" class="form-select" style="width: 100%">
                        <option value="">Tümü</option>
                        @foreach($categories as $category)
                            @if($category->parent_id == 0)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-password-toggle">
                <label class="form-label" for="multicol-confirm-password">Seri Numarası</label>
                <div class="input-group input-group-merge">
                    <input type="text" name="serialNumber" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <button   type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
    </div>
</form>
