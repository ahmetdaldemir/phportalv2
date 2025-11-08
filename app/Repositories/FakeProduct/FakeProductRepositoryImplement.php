<?php

namespace App\Repositories\FakeProduct;

use Illuminate\Support\Facades\Auth;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\FakeProduct;

class FakeProductRepositoryImplement extends Eloquent implements FakeProductRepository{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected FakeProduct $model;

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
