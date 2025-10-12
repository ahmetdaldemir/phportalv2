<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Version;
use App\Models\VersionChild;
use App\Services\Customer\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteCustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    protected function index()
    {
        $data['customers'] = $this->customerService->site_customer();
        return view('module.customer.site_index', $data);
    }


    protected function detail(Request $request)
    {
        $data['customer'] =  $this->customerService->find($request->id);
        $data['cities'] =  City::all();
        $data['active'] = 'active';
        return view('module.customer.site_customer_detail', $data);
    }

    protected function profil(Request $request)
    {
        $data['customer'] =  $this->customerService->find($request->id);
        $data['active'] = $request->type;
        return view('module.customer.site_customer_detail', $data);
    }

    protected function create(Request $request)
    {
        $data['brand_id'] = $request->id;
        $data['customerlist'] = Version::where('brand_id', $request->id)->get();
        return view('module.customer.site_form', $data);
    }

    protected function edit(Request $request)
    {
        $data['customers'] = $this->customerService->find($request->id);
        $data['customerlist'] = Version::where('brand_id', $data['customers']->brand_id)->get();
        return view('module.customer.form', $data);
    }

    protected function delete(Request $request)
    {
        $this->customerService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name, 'brand_id' => $request->brand_id, 'image' => $request->file('image'), 'company_id' => Auth::user()->company_id, 'user_id' => Auth::id());
        if (empty($request->id)) {
          $this->customerService->create($data);
            $customer =  Version::latest()->first();

            $customerChild = new VersionChild();
            $customerChild->customer_id = $customer->id;
            $customerChild->name = $customer->name;
            $customerChild->save();

        } else {
            $this->customerService->update($request->id, $data);

            $customerChild = VersionChild::where('customer_id',$request->id)->where('name',$request->name)->first();
            if($customerChild)
            {
                $customerChild->name = $data['name'];
                $customerChild->save();
            }else{
                $customerChild = new VersionChild();
                $customerChild->customer_id = $request->id;
                $customerChild->name = $data['name'];
                $customerChild->save();
            }

        }

        return redirect()->route('customer.create', ['id' => $request->brand_id]);
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->customerService->update($request->id, $data);
    }
}
