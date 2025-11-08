<?php

namespace App\Services\Demand;

use LaravelEasyRepository\Service;
use App\Repositories\Demand\DemandRepository;

class DemandServiceImplement extends Service implements DemandService{

     /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
     protected $mainRepository;

    public function __construct(DemandRepository $mainRepository)
    {
      $this->mainRepository = $mainRepository;
    }

    // Define your custom methods :)
}
