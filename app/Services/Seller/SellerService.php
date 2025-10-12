<?php

namespace App\Services\Seller;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface SellerService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
