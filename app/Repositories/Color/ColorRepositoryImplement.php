<?php

namespace App\Repositories\Color;

use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepositoryImplement;
use App\Models\Color;

class ColorRepositoryImplement extends BaseRepositoryImplement implements ColorRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Color $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }
}
