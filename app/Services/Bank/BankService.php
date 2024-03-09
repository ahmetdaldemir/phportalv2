<?php

namespace App\Services\Bank;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface BankService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
