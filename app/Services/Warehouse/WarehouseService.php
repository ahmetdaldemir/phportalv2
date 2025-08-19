<?php

namespace App\Services\Warehouse;

use Illuminate\Database\Eloquent\Collection;


interface WarehouseService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
