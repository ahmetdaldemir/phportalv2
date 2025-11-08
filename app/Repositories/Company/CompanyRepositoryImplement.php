<?php

namespace App\Repositories\Company;

use Illuminate\Support\Collection;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Company;

class CompanyRepositoryImplement extends Eloquent implements CompanyRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Company $model)
    {
        $this->model = $model;
    }

}
