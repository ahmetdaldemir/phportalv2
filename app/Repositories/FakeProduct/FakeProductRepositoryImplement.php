<?php

namespace App\Repositories\FakeProduct;

use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepositoryImplement;
use App\Models\FakeProduct;

class FakeProductRepositoryImplement extends BaseRepositoryImplement implements FakeProductRepository{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(FakeProduct $model)
    {
        $this->model = $model;
    }

    // Write something awesome :)
    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }
}
