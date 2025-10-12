<?php

namespace App\Http\Controllers;

use App\Services\Category\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    protected function index()
    {
        $data['categories'] = $this->categoryService->get();
        return view('module.category.index',$data);
    }

    protected function create()
    {
        $data['categories_all'] = $this->categoryService->get();
        return view('module.category.form',$data);
    }
    protected function edit(Request $request)
    {
        $data['categories'] = $this->categoryService->find($request->id);
        $data['categories_all'] = $this->categoryService->get();
        return view('module.category.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->categoryService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
         $data = array('name' => $request->name,'parent_id' => $request->parent_id,'company_id' => Auth::user()->company_id,'user_id' => Auth::id());
        if(empty($request->id))
        {
            $this->categoryService->create($data);
        }else{
            $this->categoryService->update($request->id,$data);
        }

        return redirect()->route('category.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->categoryService->update($request->id,$data);
    }
}
