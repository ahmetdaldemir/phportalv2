<?php

namespace App\Repositories\Version;

use Illuminate\Support\Facades\Auth;
use App\Repositories\BaseRepositoryImplement;
use App\Models\Version;

class VersionRepositoryImplement extends BaseRepositoryImplement implements VersionRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Version $model)
    {
        $this->model = $model;
    }

    public function fileUpload($file)
    {
       return $this->model->uploadFile($file);
    }

    public function get()
    {
        return $this->model->where('company_id',Auth::user()->company_id)->get();
    }
}
