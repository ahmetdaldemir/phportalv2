<?php

namespace App\Http\Controllers;

use App\Models\SiteTechnicalServiceCategory;
use Illuminate\Http\Request;

class SiteTechnicalServiceCategoryController extends Controller
{


    protected function index()
    {
        $data['categories'] = [];
        return view('site.technical_service_category.index', $data);
    }

    protected function create(Request $request)
    {
        $data['brand_id'] = $request->id;
        $data['versionlist'] = SiteTechnicalServiceCategory::where('brand_id', $request->id)->get();
        return view('site.technical_service_category.form', $data);
    }

    protected function get()
    {
        $json['data']  = SiteTechnicalServiceCategory::all();
         return response()->json($json,200);
    }

    protected function edit(Request $request)
    {
        $data = SiteTechnicalServiceCategory::find($request->id);
        return response()->json($data,200);
    }

    protected function delete(Request $request)
    {
        $this->versionService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        if($request->id != 0)
        {
            $siteTechnicalServiceCategory = SiteTechnicalServiceCategory::find($request->id);
        }else{
            $siteTechnicalServiceCategory = new SiteTechnicalServiceCategory();
        }

        $siteTechnicalServiceCategory->category = $request->category;
        $siteTechnicalServiceCategory->sort_description = $request->sort_description;
        $siteTechnicalServiceCategory->title = $request->title;
        $siteTechnicalServiceCategory->price = $request->price;
        $siteTechnicalServiceCategory->description = $request->description;
        $siteTechnicalServiceCategory->save();
        return redirect()->back();
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->versionService->update($request->id, $data);
    }
}
