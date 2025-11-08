<?php

namespace App\Services\Refund;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface RefundService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
