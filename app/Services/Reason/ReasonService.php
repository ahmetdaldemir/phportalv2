<?php

namespace App\Services\Reason;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface ReasonService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
