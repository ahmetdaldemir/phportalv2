<?php

namespace App\Services\Color;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface ColorService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
