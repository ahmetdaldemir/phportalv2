<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\Company\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    protected function index()
    {
        $data['companies'] = $this->companyService->all();
        return view('module.company.index',$data);
    }

    protected function create()
    {
        return view('module.company.form');
    }
    protected function edit(Request $request)
    {
        $data['companies'] = $this->companyService->find($request->id);
        return view('module.company.form',$data);
    }

    protected function delete(Request $request)
    {
       $this->companyService->delete($request->id);
       return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'phone' => $request->phone,'authorized' => $request->authorized);
        if(empty($request->id))
        {
          $a =  $this->companyService->create($data);
        }else{
            $this->companyService->update($request->id,$data);
        }


        return redirect()->route('company.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->companyService->update($request->id,$data);
    }

    protected function new(Request $request)
    {
        $data['companies'] = $this->companyService->find($request->id);
        return view('module.company.form',$data);
    }

}
