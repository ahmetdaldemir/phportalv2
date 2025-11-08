<?php

namespace App\Services\Warehouse;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface WarehouseService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
