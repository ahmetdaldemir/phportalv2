<?php

namespace App\Http\Controllers;

use App\Services\Customer\CustomerService;
use App\Services\Seller\SellerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    private CustomerService $customerService;
    private SellerService $sellerService;

    public function __construct(CustomerService $customerService,SellerService $sellerService)
    {
        $this->customerService = $customerService;
        $this->sellerService = $sellerService;
    }

    protected function index(Request $request)
    {
        $data['customers'] = $this->customerService->all();
        $data['request'] = $request;
        if($request->type == 'customer')
        {
            return view('module.customer.index',$data);
        }else{
            return view('module.customer.account',$data);
        }
    }

    protected function create()
    {
        $data['sellers'] = $this->sellerService->all();
        return view('module.customer.form',$data);
    }
    protected function edit(Request $request)
    {
        $data['customers'] = $this->customerService->find($request->id);
        $data['sellers'] = $this->sellerService->get();

        return view('module.customer.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->customerService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array(
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'fullname' => $request->firstname.' '.$request->lastname,
            'iban' => $request->iban,
            'code' => Str::uuid(),
            'tc' => $request->tc,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'email' => $request->email,
            'note' => $request->note,
            'seller_id' => $request->seller_id,
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id()
        );
        if(empty($request->id))
        {
            $this->customerService->create($data);
        }else{
            $this->customerService->update($request->id,$data);
        }

        return redirect()->route('customer.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->customerService->update($request->id,$data);
    }

    protected function updateDanger(Request $request)
    {
        $data = array('is_danger' => $request->is_danger);
        return $this->customerService->update($request->id,$data);
    }

    /**
     * Get customers for API
     */
    public function getCustomersApi(Request $request)
    {
        try {
            $query = $this->customerService->all();
            
            if ($request->has('type')) {
                $query = $query->where('type', $request->type);
            }
            
            return response()->json($query);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Customers not found'], 404);
        }
    }
}
