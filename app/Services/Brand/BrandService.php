<?php

namespace App\Services\Brand;

use Illuminate\Database\Eloquent\Collection;


interface BrandService {

    public function get(): ?Collection;
    public function all(): ?Collection;
}
