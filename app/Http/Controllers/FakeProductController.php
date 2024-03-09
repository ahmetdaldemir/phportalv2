<?php

namespace App\Http\Controllers;

use App\Services\FakeProduct\FakeProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakeProductController extends Controller
{
    private FakeProductService $fakeProductService;

    public function __construct(FakeProductService $fakeProductService)
    {
        $this->fakeProductService = $fakeProductService;
    }

    protected function index()
    {
        $data['fakeproducts'] = $this->fakeProductService->get();
        return view('module.fakeproduct.index',$data);
    }

    protected function create()
    {
        return view('module.fakeproduct.form');
    }
    protected function edit(Request $request)
    {
        $data['fakeproducts'] = $this->fakeProductService->find($request->id);
        return view('module.fakeproduct.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->fakeProductService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'company_id' => Auth::user()->company_id,'user_id' => Auth::user()->id);
        if(empty($request->id))
        {
            $this->fakeProductService->create($data);
        }else{
            $this->fakeProductService->update($request->id,$data);
        }

        return redirect()->route('fakeproduct.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->fakeProductService->update($request->id,$data);
    }
}
