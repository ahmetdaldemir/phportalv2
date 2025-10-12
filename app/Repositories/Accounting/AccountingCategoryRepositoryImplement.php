<?php

namespace App\Repositories\Accounting;

use App\Models\AccountingCategory;
use Illuminate\Support\Facades\Auth;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Accounting;

class AccountingRepositoryImplement extends Eloquent implements AccountingRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(AccountingCategory $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }
}
