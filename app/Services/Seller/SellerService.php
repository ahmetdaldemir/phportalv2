<?php

namespace App\Services\Seller;

use Illuminate\Database\Eloquent\Collection;


interface SellerService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
