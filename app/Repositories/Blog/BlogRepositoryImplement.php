<?php

namespace App\Repositories\Blog;

use App\Repositories\BaseRepositoryImplement;
use App\Models\Blog;

class BlogRepositoryImplement extends BaseRepositoryImplement implements BlogRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected $model;

    public function __construct(Blog $model)
    {
        $this->model = $model;
    }

    // Write something awesome :)
}
