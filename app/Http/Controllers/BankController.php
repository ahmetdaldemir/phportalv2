<?php

namespace App\Http\Controllers;

use App\Services\Bank\BankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    private BankService $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    protected function index()
    {
        $data['banks'] = $this->bankService->get();
        return view('module.bank.index',$data);
    }

    protected function create()
    {
        return view('module.bank.form');
    }
    protected function edit(Request $request)
    {
        $data['banks'] = $this->bankService->find($request->id);
        return view('module.bank.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->bankService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'iban' => $request->iban,'company_id' => Auth::user()->company_id,'user_id' => Auth::id());
        if(empty($request->id))
        {
            $this->bankService->create($data);
        }else{
            $this->bankService->update($request->id,$data);
        }

        return redirect()->route('bank.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->bankService->update($request->id,$data);
    }
}
