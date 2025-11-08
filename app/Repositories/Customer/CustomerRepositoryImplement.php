<?php

namespace App\Repositories\Customer;

use Illuminate\Support\Facades\Auth;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Customer;

class CustomerRepositoryImplement extends Eloquent implements CustomerRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function fileUpload($file)
    {
        return $this->model->uploadFile($file);
    }



    public function site_customer()
    {
        return $this->model->where('type','siteCustomer')->get();
    }
}
