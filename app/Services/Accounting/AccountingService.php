<?php

namespace App\Services\Accounting;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface AccountingService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
