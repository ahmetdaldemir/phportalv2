<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\Brand\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{

    private BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    protected function index()
    {
        $data['brands'] = $this->brandService->get();
        return view('module.brand.index',$data);
    }

    protected function create()
    {
        return view('module.brand.form');
    }
    protected function edit(Request $request)
    {
        $data['brands'] = $this->brandService->find($request->id);
        return view('module.brand.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->brandService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'company_id' => Auth::user()->company_id,'user_id' => Auth::id());
        if(empty($request->id))
        {
            $this->brandService->create($data);
        }else{
            $this->brandService->update($request->id,$data);
        }

        return redirect()->route('brand.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->brandService->update($request->id,$data);
    }

    protected function technical(Request $request)
    {

        $brand = Brand::find($request->id);
        $brand->technical = $request->technical;
        $brand->save();
    }
}
