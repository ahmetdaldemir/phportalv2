<?php

namespace App\Services\Customer;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface CustomerService extends BaseService{

    public function all(): ?Collection;
     public function site_customer(): ?Collection;
}
