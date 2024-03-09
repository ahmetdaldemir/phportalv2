<?php

namespace App\Http\Controllers;

use App\Services\Reason\ReasonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReasonController extends Controller
{
    private ReasonService $reasonService;

    public function __construct(ReasonService $reasonService)
    {
        $this->reasonService = $reasonService;
    }

    protected function index()
    {
        $data['reasons'] = $this->reasonService->get();
        return view('module.reason.index',$data);
    }

    protected function create()
    {
        return view('module.reason.form');
    }
    protected function edit(Request $request)
    {
        $data['reasons'] = $this->reasonService->find($request->id);
        return view('module.reason.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->reasonService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
       $data = array('name' => $request->name,'type' => $request->type,'company_id' => Auth::user()->company_id);
       if(empty($request->id))
       {
           $this->reasonService->create($data);
       }else{
           $this->reasonService->update($request->id,$data);
       }
       return redirect()->route('reason.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->reasonService->update($request->id,$data);
    }
}
