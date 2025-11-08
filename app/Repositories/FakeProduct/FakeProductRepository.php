<?php

namespace App\Repositories\FakeProduct;

use LaravelEasyRepository\Repository;

interface FakeProductRepository extends Repository{

    public function get();
}
