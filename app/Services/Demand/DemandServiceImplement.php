<?php

namespace App\Services\Demand;

use App\Services\BaseService;
use App\Repositories\Demand\DemandRepository;

class DemandServiceImplement extends BaseService implements DemandService{

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
