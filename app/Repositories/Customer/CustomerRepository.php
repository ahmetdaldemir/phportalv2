<?php

namespace App\Repositories\Customer;

use App\Repositories\BaseRepository;

interface CustomerRepository extends BaseRepository{

    public function site_customer();
    public function fileUpload($file);

}
