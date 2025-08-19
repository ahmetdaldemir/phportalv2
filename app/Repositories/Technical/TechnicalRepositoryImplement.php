<?php

namespace App\Repositories\Technical;

use App\Models\TechnicalService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepositoryImplement;

class TechnicalRepositoryImplement extends BaseRepositoryImplement implements TechnicalRepository{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(TechnicalService $model)
    {
        $this->model = $model;
    }

    // Write something awesome :)
    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }
}
