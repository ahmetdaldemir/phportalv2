<?php

namespace App\Http\Controllers;

use App\Models\AccountingCategory;
use App\Models\Currency;
use App\Models\FinansTransaction;
use App\Models\PersonalAccountMonth;
use App\Models\Seller;
use App\Models\SellerAccountMonth;
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
        $data['currencies'] = Currency::all();
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
        $data['currencies'] = Currency::all();
        $data['finantransactions'] = FinansTransaction::all();
        $data['currencyDifferences'] = FinansTransaction::calculateCurrencyDifferences();


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
                'salary' => $request->salary ?? 0,
                'overtime' => $request->overtime ?? 0,
                'way' => $request->way ?? 0,
                'meal' => $request->meal ?? 0,
                'bounty' => $request->bounty ?? 0,
                'insurance' => $request->insurance ?? 0,
            ]
        );

    }

    protected function getPerson(Request $request)
    {
      return PersonalAccountMonth::with('userSallary')->where('staff_id',$request->id)->where('mounth',date('m'))->first();
    }



    public function process_store(Request $request)
    {
        $FinansTransaction = new FinansTransaction();
        $FinansTransaction->safe_id = 1;
        $FinansTransaction->currency_id = $request->currency_id;
        $FinansTransaction->rate = Currency::find($request->currency_id)->exchange_rate;
        $FinansTransaction->model_class = ($request->model == 'staff')?"App\Models\User":"App\Models\Seller";
        $FinansTransaction->model_id = $request->model_id;
        $FinansTransaction->price = $request->price;
        $FinansTransaction->process_type = $request->process_type;
        $FinansTransaction->payment_type = $request->payment_type;
        $FinansTransaction->save();
        return redirect()->back();
    }

}
