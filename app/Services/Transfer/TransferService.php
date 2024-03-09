<?php

namespace App\Services\Transfer;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface TransferService extends BaseService{

    public function all(): ?Collection;
}
