<?php

namespace App\Services\Technical;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface TechnicalService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
