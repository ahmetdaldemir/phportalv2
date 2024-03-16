<?php

namespace App\Repositories\Seller;

use Illuminate\Support\Facades\Auth;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Seller;

class SellerRepositoryImplement extends Eloquent implements SellerRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Seller $model)
    {
        $this->model = $model;
    }

    // Write something awesome :)


    public function get()
    {
        return $this->model->where('company_id', Auth::user()->company_id)->orderBy('id', 'desc')->get();
    }
}
