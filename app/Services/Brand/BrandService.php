<?php

namespace App\Services\Brand;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface BrandService extends BaseService{

    public function get(): ?Collection;
    public function all(): ?Collection;
}
