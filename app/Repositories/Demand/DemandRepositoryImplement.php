<?php

namespace App\Repositories\Demand;

use App\Repositories\BaseRepositoryImplement;
use App\Models\Demand;

class DemandRepositoryImplement extends BaseRepositoryImplement implements DemandRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Demand $model)
    {
        $this->model = $model;
    }

    // Write something awesome :)
}
