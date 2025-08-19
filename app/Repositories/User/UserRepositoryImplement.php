<?php

namespace App\Repositories\User;

use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepositoryImplement;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRepositoryImplement extends BaseRepositoryImplement implements UserRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }

    public function role($id,$data)
    {
        $user = $this->model->find($id);
        $user->syncRoles($data);
    }


    public function lastInsertId()
    {
        return $this->model->latest()->first()->id;
    }
}
