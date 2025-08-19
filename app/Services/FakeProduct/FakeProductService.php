<?php

namespace App\Services\FakeProduct;

use Illuminate\Database\Eloquent\Collection;


interface FakeProductService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
