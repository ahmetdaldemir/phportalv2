<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\Company\CompanyService;
use App\Services\Seller\SellerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{

    private SellerService $sellerService;
    private CompanyService $companyService;

    public function __construct(SellerService $sellerService,CompanyService $companyService)
    {
        $this->sellerService = $sellerService;
        $this->companyService = $companyService;
    }

    protected function index()
    {
        $data['sellers'] = $this->sellerService->get();
        if ((!Auth::user()->hasRole('super-admin') || !Auth::user()->hasRole('Depo Sorumlusu')) && Auth::user()->company_id != 1) // HAsarlı Sorgusu
        {
            $data['companys'] = Company::where('id',Auth::user()->company_id)->get();
        }else{
            $data['companys'] = $this->companyService->all();
        }
        return view('module.seller.index',$data);
    }

    protected function create()
    {
        if ((!Auth::user()->hasRole('super-admin') || !Auth::user()->hasRole('Depo Sorumlusu')) && Auth::user()->company_id != 1) // HAsarlı Sorgusu
        {
            $data['companys'] = Company::where('id',Auth::user()->company_id)->get();
        }else{
            $data['companys'] = $this->companyService->all();
        }
        return view('module.seller.form',$data);
    }
    protected function edit(Request $request)
    {
        $data['sellers'] = $this->sellerService->find($request->id);
        $data['companys'] = $this->companyService->all();
        return view('module.seller.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->sellerService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'phone' => $request->phone,'company_id' => $request->company_id,'user_id' => Auth::user()->id);
        if(empty($request->id))
        {
            $this->sellerService->create($data);
        }else{
            $this->sellerService->update($request->id,$data);
        }

        return redirect()->route('seller.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->sellerService->update($request->id,$data);
    }
}
