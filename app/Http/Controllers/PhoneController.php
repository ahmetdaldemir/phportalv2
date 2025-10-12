<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Phone;
use App\Models\Safe;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Services\Brand\BrandService;
use App\Services\Invoice\InvoiceService;
use App\Services\Seller\SellerService;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\BarcodeHelper;

class PhoneController extends Controller
{

    private InvoiceService $invoiceService;
    private UserService $userService;
    private SellerService $sellerService;

    public function __construct(InvoiceService $invoiceService, UserService $userService,SellerService $sellerService)
    {
        $this->invoiceService = $invoiceService;
        $this->userService = $userService;
        $this->sellerService = $sellerService;

    }

    protected function index(Request $request)
    {

        $stockcardsList = Phone::orderBy('status', 'asc')->where('company_id', Auth::user()->company_id);

        if ($request->filled('brand')) {
            $stockcardsList->where('brand_id', $request->brand);
        }

        if ($request->filled('version')) {
            $stockcardsList->where('version_id', $request->version);
        }

        if ($request->filled('color')) {
            $stockcardsList->where('color_id', $request->color);
        }

        if ($request->filled('barcode')) {
            $stockcardsList->where('barcode', $request->barcode);
        }
        if ($request->filled('imei')) {
            $stockcardsList->where('imei', $request->imei);
        }
        if ($request->filled('status')) {
            $stockcardsList->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $stockcardsList->where('type', $request->type);
        }


        if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {
            if ($request->filled('seller') && $request->seller != 'all') {
                $stockcardsList->where('seller_id', $request->seller);
            }
        } else {
            $stockcardsList->where('seller_id', Auth::user()->seller_id);
        }


        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sellers'] = $this->sellerService->get();
        $data['phones'] = $stockcardsList->paginate(20);
        return view('module.phone.index', $data);
    }


    public function list(Request $request)
    {
        $stockcardsList = Phone::where('company_id', Auth::user()->company_id);

        if ($request->filled('brand')) {
            $stockcardsList->where('brand_id', $request->brand);
        }

        if ($request->filled('version')) {
            $stockcardsList->where('version_id', $request->version);
        }

        if ($request->filled('color')) {
            $stockcardsList->where('color_id', $request->color);
        }

        if ($request->filled('barcode')) {
            $stockcardsList->where('barcode', $request->barcode);
        }
        if ($request->filled('imei')) {
            $stockcardsList->where('imei', $request->imei);
        }

        if ($request->filled('status')) {
            $stockcardsList->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $stockcardsList->where('type', $request->type);
        }

        if ($request->filled('seller')) {
            if ($request->seller != 'all') {
                $stockcardsList->where('seller_id', $request->seller);
            }
        } else {
            $stockcardsList->where('seller_id', Auth::user()->seller_id);
        }


        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sellers'] = $this->sellerService->get();
        $data['phones'] = $stockcardsList->orderBy('status', 'asc')->paginate(50);
        return view('module.phone.index', $data);
    }

    protected function create()
    {
        $this->authorize('create-phone');
        $data['brands'] = Brand::where('company_id', Auth::user()->company_id)->get();
        $data['colors'] = Color::where('company_id', Auth::user()->company_id)->get();
        $data['sellers'] = $this->sellerService->get();
        $data['citys'] = City::all();

        return view('module.phone.form', $data);
    }

    protected function edit(Request $request)
    {
        $this->authorize('create-phone');

        $data['phone'] = Phone::find($request->id);
        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sellers'] = $this->sellerService->get();
        $data['citys'] = City::all();
        return view('module.phone.edit', $data);
    }

    protected function show(Request $request)
    {
        $data['phone'] = Phone::find($request->id);
        $data['brands'] = Brand::all();
        $data['colors'] = Color::all();
        $data['sellers'] = $this->sellerService->get();
        $data['citys'] = City::all();
        return view('module.phone.show', $data);
    }

    protected function sale(Request $request)
    {

        if (\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin')) {
            $data['phone'] = Phone::find($request->id);
        } else {
            $phone = Phone::where('id', $request->id)->where('seller_id', Auth::user()->seller_id)->first();
            if ($phone) {
                $data['phone'] = $phone;
            } else {
                return redirect()->back()->withErrors(['msg' => 'Sadece kendi bayinize ait ürün satışı yapabilirsiniz']);
            }
        }


        $data['citys'] = City::all();
        $data['sellers'] = $this->sellerService->get();
        $data['users'] = $this->userService->get()->where('is_status', 1);
        return view('module.phone.sale', $data);
    }

    protected function barcode(Request $request)
    {
        $data['phone'] = Phone::find($request->id);
        return view('module.phone.barcode', $data);
    }

    protected function delete(Request $request)
    {
        $phone = Phone::find($request->id);
        $phone->delete();
        return redirect()->back();
    }

    protected function store(Request $request)
    {

        if($request->type != 'old')
        {
            if ($request->has('is_warranty')) {
                $warranty = 2;
            } else {
                $warranty = $request->warranty;
            }
        }else{
            if ($request->has('is_warranty')) {
                $warranty = null;
            } else {
                $warranty = $request->warranty;
            }
        }

        if($request->type == 'new')
        {
            if($request->filled('warranty') || !$request->has('is_warranty'))
            {
                $warranty = 1;
            }
        }



        $this->authorize('create-phone');

        if($request->filled('id'))
        {
            $phone =  Phone::find($request->id);
            $phone->imei = $request->imei;
            $phone->user_id = $request->user_id;
            $phone->company_id = $request->company_id;
            $phone->brand_id = $request->brand_id;
            $phone->version_id = $request->version_id;
            $phone->color_id = $request->color_id;
            $phone->seller_id = $request->seller_id??Auth::user()->seller_id;
            $phone->quantity = $request->quantity;
            $phone->type = $request->type;
            $phone->description = $request->description;
            $phone->cost_price = $request->cost_price;
            $phone->sale_price = $request->sale_price;
            $phone->customer_id = $request->customer_id;
            $phone->altered_parts = $request->altered_parts;
            $phone->physical_condition = $request->physical_condition;
            $phone->memory = $request->memory;
            $phone->batery = $request->batery;
            $phone->warranty = $warranty;
            $phone->save();
        }else{
            $phone = new Phone();
            $phone->imei = $request->imei;
            $phone->user_id = $request->user_id;
            $phone->company_id = $request->company_id;
            $phone->brand_id = $request->brand_id;
            $phone->version_id = $request->version_id;
            $phone->color_id = $request->color_id;
            $phone->seller_id = $request->seller_id??Auth::user()->seller_id;
            $phone->quantity = $request->quantity;
            $phone->type = $request->type;
            $phone->barcode = BarcodeHelper::generateBarcode('PH', 7);
            $phone->description = $request->description;
            $phone->cost_price = $request->cost_price;
            $phone->sale_price = $request->sale_price;
            $phone->customer_id = $request->customer_id;
            $phone->altered_parts = $request->altered_parts;
            $phone->physical_condition = $request->physical_condition;
            $phone->memory = $request->memory;
            $phone->batery = $request->batery;
            $phone->warranty = $warranty;
            $phone->save();
        }



        return redirect()->route('phone.index');
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->brandService->update($request->id, $data);
    }

    protected function confirm(Request $request)
    {
        $phone = Phone::find($request->id);
        $phone->is_confirm = 1;
        $phone->save();
        return redirect()->back();
    }

    protected function printconfirm(Request $request)
    {
        $data['phone'] = Phone::find($request->id);
        return view('module.phone.printconfirm', $data);
    }

    public function salestore(Request $request)
    {
         $phone = Phone::find($request->phone_id);

        $salePrice = $request->payment_type['credit_card'] + $request->payment_type['cash'] + $request->payment_type['installment'];
        if ($salePrice < $phone->sale_price) {
            return redirect()->back()->withErrors(['msg' => 'Satış fiyatından düşük satılamaz']);
        }


        $data = array(
            'type' => 2,
            'number' => null,
            'create_date' => date('Y-m-d'),
            'credit_card' => $request->payment_type['credit_card'] ?? 0,
            'cash' => $request->payment_type['cash'] ?? 0,
            'installment' => $request->payment_type['installment'] ?? 0,
            'description' => $request->description ?? null,
            'is_status' => 1,
            'total_price' => $request->payment_type['credit_card'] + $request->payment_type['cash'] + $request->payment_type['installment'],
            'tax_total' => 18,
            'discount_total' => $request->discount_total,
            'staff_id' => Auth::user()->id,
            'customer_id' => $request->customer_id ?? null,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
            'exchange' => null,
            'tax' => 18,
            'file' => null,
            'paymentStatus' => 1,
            'paymentDate' => date('Y-m-d'),
            'paymentStaff' => Auth::user()->id,
            'periodMounth' => date('m'),
            'periodYear' => date('Y'),
            'accounting_category_id' => 9999999,
            'currency' => null,
            'safe_id' => null,
        );

        $invoiceID = $this->invoiceService->create($data);

        $safe = new Safe();
        $safe->name = "Şirket";
        $safe->company_id = Auth::user()->company_id;
        $safe->user_id = Auth::user()->id;
        $safe->seller_id = $phone->seller_id;
        $safe->type = "in";
        $safe->incash = $request->payment_type['cash'] ?? 0;
        $safe->outcash = "0";
        $safe->amount = $request->payment_type['cash'] ?? 0 + $request->payment_type['credit_card'] ?? 0 + $request->payment_type['installment'] ?? 0;
        $safe->invoice_id = $invoiceID->id;
        $safe->credit_card = $request->payment_type['credit_card'] ?? 0;
        $safe->installment = $request->payment_type['installment'] ?? 0;
        $safe->description = "TELEFON";
        $safe->save();


        $phone->invoice_id = $invoiceID->id;
        $phone->status = 1;
        $phone->sales_person = $request->sales_person;
        $phone->save();

        $sale = new Sale();
        $sale->stock_card_id = $phone->id;
        $sale->stock_card_movement_id = $phone->id;
        $sale->invoice_id = $invoiceID->id;
        $sale->customer_id = $request->customer_id ?? null;
        $sale->sale_price = $phone->sale_price;
        $sale->customer_price = $request->payment_type['cash'] ?? 0 + $request->payment_type['credit_card'] ?? 0 + $request->payment_type['installment'] ?? 0;
        $sale->name = $phone->brand->name . '/' . $phone->version->name;
        $sale->seller_id = $phone->seller_id;
        $sale->company_id = Auth::user()->company_id;
        $sale->user_id = $request->sales_person;
        $sale->serial = $phone->imei;
        $sale->discount = 0;
        $sale->type = 1;
        $sale->base_cost_price = $phone->cost_price;
        $sale->save();


        return redirect()->to('phone');
    }
    
    /**
     * AJAX endpoint for getting phones
     */
    public function getPhonesAjax(Request $request)
    {
        try {
            $query = Phone::with(['brand', 'version', 'color', 'seller'])
                ->where('company_id', Auth::user()->company_id);
            
            // Apply filters
            if ($request->filled('brand')) {
                $query->where('brand_id', $request->brand);
            }
            
            if ($request->filled('version')) {
                $query->where('version_id', $request->version);
            }
            
            if ($request->filled('color')) {
                $query->where('color_id', $request->color);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            
            if ($request->filled('seller') && $request->seller !== 'all') {
                $query->where('seller_id', $request->seller);
            }
            
            if ($request->filled('barcode')) {
                $query->where('barcode', 'like', '%' . $request->barcode . '%');
            }
            
            if ($request->filled('imei')) {
                $query->where('imei', 'like', '%' . $request->imei . '%');
            }
            
            $phones = $query->orderBy('status', 'asc')->get();
            
            // Format phone data
            $formattedPhones = $phones->map(function ($phone) {
                return [
                    'id' => $phone->id,
                    'imei' => $phone->imei,
                    'barcode' => $phone->barcode,
                    'brand' => $phone->brand,
                    'version' => $phone->version,
                    'color' => $phone->color,
                    'seller' => $phone->seller,
                    'type' => $phone->type,
                    'type_text' => \App\Models\Phone::TYPE[$phone->type] ?? 'Bilinmiyor',
                    'memory' => $phone->memory,
                    'battery' => $phone->batery,
                    'battery_text' => $phone->batery == 0 ? 'Bilinmiyor' : '% ' . $phone->batery,
                    'warranty' => $phone->warranty,
                    'warranty_text' => $this->getWarrantyText($phone->warranty),
                    'status' => $phone->status,
                    'is_confirm' => $phone->is_confirm,
                    'cost_price' => $phone->cost_price,
                    'cost_price_formatted' => number_format($phone->cost_price, 2),
                    'sale_price' => $phone->sale_price,
                    'sale_price_formatted' => number_format($phone->sale_price, 2),
                ];
            });
            
            return response()->json([
                'phones' => $formattedPhones,
                'total' => $formattedPhones->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Telefonlar yüklenemedi: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * AJAX endpoint for getting versions by brand
     */
    public function getVersionsAjax(Request $request)
    {
        try {
            $brandId = $request->brand_id;
            
            if (!$brandId) {
                return response()->json(['versions' => []]);
            }
            
            $versions = \App\Models\Version::where('brand_id', $brandId)->get();
            
            return response()->json([
                'versions' => $versions
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Versiyonlar yüklenemedi: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Get warranty text
     */
    private function getWarrantyText($warranty)
    {
        if ($warranty == null) {
            return 'Garantisiz';
        } elseif ($warranty == '2') {
            return \App\Models\Phone::WARRANTY[$warranty] ?? 'Garantisiz';
        } elseif ($warranty == 1) {
            return 'Garantili';
        } else {
            return \Carbon\Carbon::parse($warranty)->format('d-m-Y');
        }
    }
}
