<?php

namespace App\Repositories\Transfer;

use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepositoryImplement;
use App\Models\Transfer;

class TransferRepositoryImplement extends BaseRepositoryImplement implements TransferRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Transfer $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }
}
