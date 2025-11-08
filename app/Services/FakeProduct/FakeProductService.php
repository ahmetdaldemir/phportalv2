<?php

namespace App\Services\FakeProduct;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface FakeProductService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
