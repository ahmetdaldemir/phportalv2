<?php

namespace App\Http\Controllers;

use App\Models\AccountingCategory;
use App\Models\PersonalAccountMonth;
use App\Models\Seller;
use App\Models\SellerAccountMonth;
use App\Models\FinansTransaction;
use App\Models\User;
use Carbon\Carbon;
 use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalculationController extends Controller
{


    public function __construct()
    {
        //
    }

    protected function index()
    {
        $data['brands'] = 1;
        return view('calculation.index', $data);
    }

    protected function categories()
    {
        $data['accounting_categories'] = AccountingCategory::all();
        return view('calculation.categories', $data);
    }

    protected function accounting()
    {
        $data['accounting_categories'] = AccountingCategory::all();
        return view('calculation.accounting', $data);
    }

    protected function selected(Request $request)
    {
        if ($request->id == 'seller') {
            return response()->json(Seller::all());
        } else if ($request->id == 'staff') {
            return response()->json(User::all());
        }
    }


    protected function getCategories(Request $request)
    {
        if ($request->id == 'income') {
            return response()->json(AccountingCategory::where('category','gelir')->get());
        } else if ($request->id == 'expense') {
            return response()->json(AccountingCategory::where('category','gider')->get());
        }
    }


    protected function seller()
    {
        $data['sellers'] = Seller::all();
        return view('calculation.seller', $data);
    }

    protected function staff()
    {
        $data['staffs'] = User::all()->where('company_id', Auth::user()->company_id);
        return view('calculation.staff', $data);
    }

    protected function saveSeller(Request $request)
    {
        SellerAccountMonth::updateOrCreate(
            [
                'seller_id' => $request->seller_id,
                'mounth' => date('m'),
            ],
            [
                'rent' => $request->rent,
                'invoice' => $request->invoice,
                'tax' => $request->tax,
                'additional_expense' => $request->additional_expense
            ]
        );

    }

    protected function saveStaff(Request $request)
    {
        PersonalAccountMonth::updateOrCreate(
            [
                'staff_id' => $request->staff_id,
                'mounth' => date('m'),
            ],
            [
                'sallary' => $request->sallary ?? 0,
                'overtime' => $request->overtime ?? 0,
                'way' => $request->way ?? 0,
                'meal' => $request->meal ?? 0,
                'bounty' => $request->bounty ?? 0,
                'insurance' => $request->insurance ?? 0,
            ]
        );

    }



    public function process_store(Request $request)
    {
        $transaction = new FinansTransaction();
        $transaction->user_id = Auth::user()->id;
        $transaction->safe_id = $request->safe_id;
        $transaction->model_class = ($request->model == 'staff')?"App\Models\User":"App\Models\Seller";
        $transaction->model_id = $request->model_id;
        $transaction->price = $request->price;
        $transaction->payment_type = $request->payment_type;
        $transaction->process_type = $request->process_type;
        $transaction->save();
        return redirect()->back();
    }

}
