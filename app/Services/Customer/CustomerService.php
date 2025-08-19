<?php

namespace App\Services\Customer;

use Illuminate\Database\Eloquent\Collection;


interface CustomerService {

    public function all(): ?Collection;
     public function site_customer(): ?Collection;
}
