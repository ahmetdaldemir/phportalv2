<?php

namespace App\Http\Controllers;

use App\Services\Seller\SellerService;
use App\Services\Warehouse\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{

    private WarehouseService $warehouseService;
    private SellerService $sellerService;

    public function __construct(WarehouseService $warehouseService, SellerService $sellerService)
    {
        $this->warehouseService = $warehouseService;
        $this->sellerService = $sellerService;
    }

    protected function index()
    {
        $data['warehouses'] = $this->warehouseService->get();
        return view('module.warehouse.index',$data);
    }

    protected function create()
    {
        $data['sellers'] = $this->sellerService->get();
        return view('module.warehouse.form',$data);
    }
    protected function edit(Request $request)
    {
        $data['warehouses'] = $this->warehouseService->find($request->id);
        $data['sellers'] = $this->sellerService->get();
        return view('module.warehouse.form',$data);
    }

    protected function delete(Request $request)
    {
        $this->warehouseService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array('name' => $request->name,'seller_id' => $request->seller_id,'company_id' => Auth::user()->company_id,'user_id' => Auth::user()->id);
        if(empty($request->id))
        {
            $this->warehouseService->create($data);
        }else{
            $this->warehouseService->update($request->id,$data);
        }

        return redirect()->route('warehouse.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->warehouseService->update($request->id,$data);
    }
}
