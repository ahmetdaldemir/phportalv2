<?php

namespace App\Repositories\Category;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepositoryImplement;
use App\Models\Category;

class CategoryRepositoryImplement extends BaseRepositoryImplement implements CategoryRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->where('company_id', Auth::user()->company_id)->get();
    }

    public function getList($category_id = 2)
    {
        $categories = $this->model->where('company_id', Auth::user()->company_id)->where('parent_id', $category_id)->get();

        foreach ($categories as $category) {
            $data[] = [
                'category_id' => $category->id,
                'name' => $category->name,
                'list' => $this->getCategory($category->id)
            ];
        }
        return $data;
    }

    public function getCategory($category_id)
    {

        $x =  $this->model->where('parent_id', $category_id)->first();
        if($x)
        {
            return  [
                    'category_id' => $x->id,
                    'name' => $x->name,
                    'list' => $this->getCategory($x->id)
                ];
        }
    }

    public function getParentList($category_id=0)
    {
        $data=null;
        $categories = $this->model->where('company_id', Auth::user()->company_id)->where('parent_id', $category_id)->get();
//dd($categories);
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'list' => $this->getParentList($category->id)
            ];
        }
        return $data;
    }

    public function getParentCategory($category_id)
    {

        $x =  $this->model->where('parent_id', $category_id)->first();
        if($x)
        {
            return  [
                'id' => $x->id,
                'list' => $this->getCategory($x->id)
            ];
        }
    }

    public function getAllParentsL($category_id)
    {
        $x =  $this->model->where('parent_id', $category_id)->first();
        if($x)
        {
            return  [
                'id' => $x->id,
                'list' => $this->getCategory($x->id)
            ];
        }
    }

}
