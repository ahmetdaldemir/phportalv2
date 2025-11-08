<?php

namespace App\Services\Safe;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface SafeService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
