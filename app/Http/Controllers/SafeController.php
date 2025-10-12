<?php

namespace App\Http\Controllers;

use App\Models\Safe;
use App\Services\Safe\SafeService;
use App\Services\Seller\SellerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SafeController extends Controller
{
    private SafeService $safeService;
    private SellerService $sellerService;

    public function __construct(SafeService $safeService,SellerService $sellerService)
    {
        $this->safeService = $safeService;
        $this->sellerService = $sellerService;
    }

    protected function index(Request $request)
    {
        $query = Safe::orderBy('id', 'desc');
        if($request->filled('daterange'))
        {
            $daterange = explode("to",$request->daterange);
            if(isset($daterange[1]))
            {
                $query->whereBetween('created_at',[trim($daterange[0]), trim($daterange[1])]);

            }else{
                $query->whereDate('created_at',trim($daterange[0]));
            }
        }else{
            $query->whereDate('created_at',Carbon::today());
        }
        if($request->filled('seller'))
        {
            $query->where('seller_id',$request->seller);
        }

        if($request->filled('process'))
        {
            if($request->process == 'in')
            {
                $query->where('type','in');
            }else if($request->process == 'out')
            {
                $query->where('type','out');
            }else{
                $query->where('description',$request->process);
            }
        }


        if (Auth::user()->hasRole('super-admin')) // HAsarlı Sorgusu
        {
            if($request->filled('seller') && $request->seller != 'all')
            {
                $query->where('seller_id', $request->seller);
            }
        }else{
            $query->where('seller_id', Auth::user()->seller_id);
        }

        $safes = $query->get();
        $data['safes'] = $safes;
        $data['sellers'] = $this->sellerService->get();
        return view('module.safe.index',$data);
    }

    protected function create()
    {
        return view('module.safe.form');
    }
    protected function edit(Request $request)
    {
        $data['safes'] = $this->safeService->find($request->id);
        return view('module.safe.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->safeService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        DB::beginTransaction();
        try {
         if($request->type == 'in')
        {
            $incash = $request->price;
            $outcash = 0;
        }else{
            $incash = 0;
            $outcash = $request->price;
        }
        $safe = new Safe();
        $safe->name = "Şirket";
        $safe->company_id = Auth::user()->company_id;
        $safe->user_id = Auth::user()->id;
        $safe->seller_id = $request->seller_id??Auth::user()->seller_id;
        $safe->type = $request->type;
        $safe->incash = $incash;
        $safe->outcash = $outcash;
        $safe->amount = $total ?? 0;
        $safe->invoice_id = 99999999;
        $safe->credit_card = 0;
        $safe->installment = 0;
        $safe->description = $request->description;;
        $safe->save();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        DB::commit();
        return redirect()->route('safe.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->safeService->update($request->id,$data);
    }
}
