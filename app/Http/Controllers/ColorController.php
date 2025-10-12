<?php

namespace App\Http\Controllers;

use App\Services\Color\ColorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColorController extends Controller
{
    private ColorService $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;
    }

    protected function index()
    {
        $data['colors'] = $this->colorService->get();
        return view('module.color.index',$data);
    }

    protected function create()
    {
        return view('module.color.form');
    }
    protected function edit(Request $request)
    {
        $data['colors'] = $this->colorService->find($request->id);
        return view('module.color.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->colorService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'company_id' => Auth::user()->company_id);
        if(empty($request->id))
        {
            $this->colorService->create($data);
        }else{
            $this->colorService->update($request->id,$data);
        }

        return redirect()->route('color.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->colorService->update($request->id,$data);
    }
}
