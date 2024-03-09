<?php

namespace App\Http\Controllers;

use App\Services\AccountingCategory\AccountingCategoryService;
 use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingCategoryController extends Controller
{
    private AccountingCategoryService $accountingCategory;

    public function __construct(AccountingCategoryService $accountingCategory)
    {
        $this->accountingCategory = $accountingCategory;
    }

    protected function index()
    {
        $data['accounting_categories'] = $this->accountingCategory->get();
        return view('module.accounting_category.index',$data);
    }

    protected function create()
    {
        return view('module.accounting_category.form');
    }
    protected function edit(Request $request)
    {
        $data['accounting_category'] = $this->accountingCategory->find($request->id);
        return view('module.accounting_category.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->accountingCategory->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'category' => $request->category,
            'company_id' => Auth::user()->company_id,'user_id' => Auth::id());
        if(empty($request->id))
        {
            $this->accountingCategory->create($data);
        }else{
            $this->accountingCategory->update($request->id,$data);
        }

        return redirect()->route('accounting_category.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->accountingCategory->update($request->id,$data);
    }
}
