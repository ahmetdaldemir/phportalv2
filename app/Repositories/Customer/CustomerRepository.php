<?php

namespace App\Repositories\Customer;

use LaravelEasyRepository\Repository;

interface CustomerRepository extends Repository{

    public function site_customer();
    public function fileUpload($file);

}
