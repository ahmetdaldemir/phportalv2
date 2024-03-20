<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Customer;
use App\Models\Safe;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\TechnicalCustomProducts;
use App\Models\TechnicalCustomService;
use App\Models\TechnicalProcess;
use App\Models\TechnicalServiceProcess;
use App\Models\TechnicalServiceProducts;
use App\Models\Town;
use App\Models\Version;
use App\Services\Brand\BrandService;
use App\Services\Category\CategoryService;
use App\Services\Customer\CustomerService;
use App\Services\Invoice\InvoiceService;
use App\Services\Modules\Sms\SendSms;
use App\Services\Seller\SellerService;
use App\Services\StockCard\StockCardService;
use App\Services\Technical\TechnicalService;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TechnicalServiceController extends Controller
{

    private TechnicalService $technicalService;
    private StockCardService $stockCardService;
    private SellerService $sellerService;
    private CustomerService $customerService;
    private BrandService $brandService;
    private UserService $userService;
    private InvoiceService $invoiceService;

    private CategoryService $categoryService;

    public function __construct(
        TechnicalService $technicalService,
        StockCardService $stockCardService,
        SellerService    $sellerService,
        CustomerService  $customerService,
        BrandService     $brandService,
        CategoryService  $categoryService,
        UserService      $userService,
        InvoiceService   $invoiceService
    )
    {
        $this->technicalService = $technicalService;
        $this->stockCardService = $stockCardService;
        $this->sellerService = $sellerService;
        $this->customerService = $customerService;
        $this->brandService = $brandService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->invoiceService = $invoiceService;

    }

    protected function index(Request $request)
    {
        $user = Cache::get('user_'.\auth()->user()->id);

        $technical_services = \App\Models\TechnicalService::where('company_id', $user->company_id);


        if ($user->hasRole('super-admin') || $user->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {
            if ($request->filled('seller') && $request->seller != 'all') {
                $technical_services->where('seller_id', $request->seller);
            }
        } else {
            $technical_services->where('seller_id', $user->seller_id);
        }


        if ($request->filled('customer')) {
            $x = Customer::where('fullname', 'like', '%' . $request->customer . '%')->get();
            $y = $x->pluck('id');
            $technical_services->whereIn('customer_id', $y);
        }

        if ($request->filled('brand')) {
            $technical_services->where('brand_id', $request->brand);
        }
        if ($request->filled('version')) {
            $technical_services->where('version_id', $request->version);
        }

        if ($request->filled('status')) {
            $technical_services->where('status', $request->status);
        }


        /* COVER   */
        $technical_cover = \App\Models\TechnicalCustomService::where('company_id', $user->company_id);


        if ($user->hasRole('super-admin') || $user->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {
            if ($request->filled('seller') && $request->cover_seller != 'all') {
                $technical_cover->where('seller_id', $request->cover_seller);
            }
        } else {
            $technical_cover->where('seller_id', $user->seller_id);
        }


        if ($request->filled('cover_customer')) {
            $x = Customer::where('fullname', 'like', '%' . $request->cover_customer . '%')->get();
            $y = $x->pluck('id');
            $technical_cover->whereIn('customer_id', $y);
        }

        if ($request->filled('cover_brand')) {
            $technical_cover->where('brand_id', $request->cover_brand);
        }
        if ($request->filled('cover_version')) {
            $technical_cover->where('version_id', $request->cover_version);
        }

        if ($request->filled('tab_type')) {
            if ($request->tab_type == '_technical') {
                $data['_technical'] = "active show";
                $data['_cover'] = "";
            } else {
                $data['_technical'] = "";
                $data['_cover'] = "active show";
            }

        } else {
            $data['_technical'] = "active show";
            $data['_cover'] = "";
        }

        /* Cover END */


        $data['technical_services'] = $technical_services->orderBy('status','asc')->paginate(100);
        $data['technical_covering_services'] = $technical_cover->orderBy('id','desc')->paginate(100);
        $data['brands'] = $this->brandService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['sms'] = Setting::where('category', 'sms')->get();
        $data['users'] = $this->userService->get();

        return view('module.technical_service.index', $data);
    }

    protected function coverings()
    {
        $data['technical_services'] = $this->technicalService->get()->sortBy('status');
        $data['technical_covering_services'] = TechnicalCustomService::all();
        $data['brands'] = $this->brandService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['sms'] = Setting::where('category', 'sms')->get();
        $data['users'] = $this->userService->get();

        return view('module.technical_service.index', $data);
    }

    protected function create()
    {
        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['customers'] = $this->customerService->all();
        $data['brands'] = $this->brandService->get();
        $data['users'] = $this->userService->get();
        $data['citys'] = City::all();
        $data['categories_all'] = TechnicalProcess::all();
        $data['tows'] = Town::where('city_id', 34)->get();

        return view('module.technical_service.form', $data);
    }

    protected function payment(Request $request)
    {
        $technical_service = \App\Models\TechnicalService::find($request->id);
        if ($technical_service->payment_status == 1) {
            return redirect()->back();
        }
        $data['technical_service'] = $technical_service;
        $data['technical_service_process'] = TechnicalServiceProcess::where('technical_service_id', $request->id)->get();

        $data['users'] = $this->userService->get();
        return view('module.technical_service.payment', $data);
    }

    public function paymentcomplate(Request $request)
    {
        DB::beginTransaction();
        try {
            $total = $request->payment_type['cash'] + $request->payment_type['credit_card'] + $request->payment_type['installment'];
            if ($total != $request->totalprice) {
                return redirect()->back()->with(['msg' => 'Tutarlar Eşleşmiyor']);;
            }
            $technicalservice = \App\Models\TechnicalService::find($request->id);

            $technicalserviceproducts = TechnicalServiceProducts::where('technical_service_id', $technicalservice->id)->get();

            if (count($technicalserviceproducts) == 0) {
                return redirect()->back()->with(['msg' => 'Ürün bulunmayan hizmetten ödeme alınamaz']);;
            }

            $data = array(
                'type' => 2,
                'number' => "IN" . rand(1111, 9999) . date("m"),
                'create_date' => Carbon::parse($request->create_date)->format('Y-m-d') ?? null,
                'credit_card' => $request->payment_type['credit_card'],
                'cash' => $request->payment_type['cash'],
                'installment' => $request->payment_type['installment'],
                'description' => "Teknik Servis",
                'is_status' => 1,
                'total_price' => $request->totalprice,
                'tax_total' => 1,
                'discount_total' => 0,
                'staff_id' => $request->payment_person,
                'customer_id' => $technicalservice->customer_id ?? null,
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->company_id,
                'exchange' => 2,
                'tax' => 30,
                'file' => null,
                'paymentStatus' => 'paid',
                'paymentDate' => date('Y-m-d'),
                'paymentStaff' => Auth::user()->id,
                'periodMounth' => Date('m'),
                'periodYear' => Date('Y'),
                'accounting_category_id' => 15,
                'currency' => $request->currency ?? null,
                'safe_id' => null,
                'detail' => null,
            );


            $invoiceID = $this->invoiceService->create($data);
            $i = 0;
            foreach ($technicalserviceproducts as $item) {
                $stockcardmovement = StockCardMovement::where('type', 5)->where('serial_number', $item->serial_number)->first();
                $SaleCheck = Sale::where('serial', $item->serial_number)->first();

                if (!$SaleCheck) {
                    $sale = new Sale();
                    $sale->stock_card_id = $stockcardmovement->stock_card_id;
                    $sale->stock_card_movement_id = $stockcardmovement->id;
                    $sale->invoice_id = $invoiceID->id;
                    $sale->customer_id = $technicalservice->customer_id;
                    $sale->sale_price = $item->sale_price;
                    $sale->customer_price = $stockcardmovement->sale_price - (($stockcardmovement->sale_price * $technicalservice->discount) / 100);
                    $sale->name = StockCard::find($stockcardmovement->stock_card_id)->name;
                    $sale->seller_id = Auth::user()->seller_id;
                    $sale->company_id = Auth::user()->company_id;
                    $sale->user_id = $request->technical_person;
                    $sale->serial = $item->serial_number;
                    $sale->discount = 0;
                    $sale->technical_service_person_id = (int)$request->technical_person;
                    $sale->base_cost_price = $stockcardmovement->base_cost_price;
                    $sale->delivery_personnel = $technicalservice->delivery_staff;
                    $sale->type = 3;
                    $sale->save();
                }
                $stockcardmovement->type =2;
                $stockcardmovement->save();
            }

            $technicalservice->payment_status = 1;
            $technicalservice->status = 6;
            $technicalservice->technical_person = $request->technical_person;
            $technicalservice->save();


            $technicalserviceProcess = new TechnicalServiceProcess();
            $technicalserviceProcess->technical_service_id = $request->id;
            $technicalserviceProcess->company_id = Auth::user()->company_id;
            $technicalserviceProcess->user_id = Auth::user()->id;
            $technicalserviceProcess->status = "5";
            $technicalserviceProcess->save();

            $technicalserviceProcess = new TechnicalServiceProcess();
            $technicalserviceProcess->technical_service_id = $request->id;
            $technicalserviceProcess->company_id = Auth::user()->company_id;
            $technicalserviceProcess->user_id = Auth::user()->id;
            $technicalserviceProcess->status = "6";
            $technicalserviceProcess->save();

            $safe = new Safe();
            $safe->name = "Şirket";
            $safe->company_id = Auth::user()->company_id;
            $safe->user_id = Auth::user()->id;
            $safe->seller_id = Auth::user()->seller_id;
            $safe->type = "in";
            $safe->incash = $request->payment_type['cash'] ?? 0;
            $safe->outcash = "0";
            $safe->amount = $request->total_price ?? 0;
            $safe->invoice_id = $request->id;
            $safe->credit_card = $request->payment_type['credit_card'] ?? 0;
            $safe->installment = 0;
            $safe->description = "Teknik Servis";

            $safe->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
        }
        DB::commit();
        return redirect()->to('technical_service');
    }

    protected function edit(Request $request)
    {
        $data['technical_services'] = $this->technicalService->find($request->id);
        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['customers'] = $this->customerService->all();
        $data['brands'] = $this->brandService->get();
        $data['users'] = $this->userService->get();
        $data['categories_all'] = TechnicalProcess::all();

        $data['citys'] = City::all();
        return view('module.technical_service.form', $data);
    }

    protected function coveredit(Request $request)
    {
        $data['technical_service_cover'] = TechnicalCustomService::find($request->id);
        $data['technical_service_products'] = TechnicalCustomProducts::where('technical_custom_id', $request->id)->get();

        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['customers'] = $this->customerService->all();
        $data['brands'] = $this->brandService->get();
        $data['users'] = $this->userService->get();
        $data['citys'] = City::all();
        $data['tows'] = Town::where('city_id', $data['technical_service_cover']->customer->city)->get();


        return view('module.technical_service.coveredit', $data);
    }

    protected function detail(Request $request)
    {
        $technical_services = $this->technicalService->find($request->id);
        $data['technical_services'] = $technical_services;
        $data['technical_service_products'] = TechnicalServiceProducts::where('technical_service_id', $request->id)->get();
        $data['technical_service_process'] = TechnicalServiceProcess::where('technical_service_id', $request->id)->get();

        $x = [];
        //  $categorylist = Category::where('id', $request->category_id)->orWhere('parent_id', $request->category_id)->get()->pluck('id')->toArray();
        $xxxx = $this->categoryService->getAllParentList(3);
        //dd($xxxx);
        $ids = $this->array_column_recursive($xxxx, 'id');


        $stockcardsList = StockCard::whereIn('category_id', $ids)->orWhere('category_id', 3)->get();

        $data['stocks'] = $stockcardsList;
        $data['sellers'] = $this->sellerService->get();
        $data['customers'] = $this->customerService->all();
        $data['brands'] = $this->brandService->get();
        $data['versions'] = Version::where('brand_id', $technical_services->brand_id)->get();
        $data['users'] = $this->userService->get();
        $data['citys'] = City::all();
        $data['categories_all'] = TechnicalProcess::all();

        return view('module.technical_service.detail', $data);
    }


    function array_column_recursive(array $haystack, $needle)
    {
        $found = [];
        array_walk_recursive($haystack, function ($value, $key) use (&$found, $needle) {
            if ($key == $needle)
                $found[] = $value;
        });
        return $found;
    }

    protected function show(Request $request)
    {
        $data['technical_services'] = $this->technicalService->find($request->id);
        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['customers'] = $this->customerService->all();
        $data['brands'] = $this->brandService->get();
        $data['users'] = $this->userService->get();
        $data['categories_all'] = TechnicalProcess::all();
        $data['tows'] = Town::where('city_id', 34)->get();
        $data['citys'] = City::all();
        return view('module.technical_service.form', $data);
    }

    protected function delete(Request $request)
    {
        $this->technicalService->delete($request->id);
        return redirect()->back();
    }

    protected function categorydelete(Request $request)
    {
        $t = TechnicalProcess::find($request->id);
        $t->delete();
        return redirect()->back();
    }

    protected function detaildelete(Request $request)
    {
        $technicalService = \App\Models\TechnicalService::find($request->technical_service_id);
        if ($technicalService->status == "1") {
            $technicalServiceProduct = TechnicalServiceProducts::find($request->id);
            $technicalServiceProduct->delete();

            $stockcardmovement = StockCardMovement::find($technicalServiceProduct->stock_card_movement_id);
            $stockcardmovement->type = 1;
            $stockcardmovement->save();
        }
        return redirect()->back();
    }


    protected function store(Request $request)
    {


        if (empty($request->id)) {

            $technicalService = \App\Models\TechnicalService::where('created_at', Carbon::now()->format('Y-m-d H:i:s'))->first();
            if ($technicalService) {
                return redirect()->back();
            }
            DB::beginTransaction();
            try {
                $data = array(
                    'customer_id' => $request->customer_id,
                    'physical_condition' => $request->physical_condition,
                    'accessories' => $request->accessories,
                    'fault_information' => $request->fault_information,
                    'products' => '',
                    'accessory_category' => $request->accessory_category,
                    'physically_category' => $request->physically_category,
                    'fault_category' => $request->fault_category,
                    'seller_id' => Auth::user()->seller_id,
                    'brand_id' => $request->brand_id,
                    'version_id' => $request->version_id,
                    'imei' => $request->imei,
                    'total_price' => $request->total_price,
                    'customer_price' => $request->customer_price,
                    'delivery_staff' => $request->delivery_staff,
                    'device_password' => $request->device_password,
                    'company_id' => Auth::user()->company_id,
                    'status' => $request->status,
                    'user_id' => Auth::user()->id
                );
                $tech = $this->technicalService->create($data);
            } catch (\Exception $e) {
                DB::rollBack();
            }
            DB::commit();
        } else {
            $data = array(
                'customer_id' => $request->customer_id,
                'physical_condition' => $request->physical_condition,
                'accessories' => $request->accessories,
                'fault_information' => $request->fault_information,
                'products' => '',
                'accessory_category' => $request->accessory_category,
                'physically_category' => $request->physically_category,
                'fault_category' => $request->fault_category,
                'brand_id' => $request->brand_id,
                'version_id' => $request->version_id,
                'imei' => $request->imei,
                'total_price' => $request->total_price,
                'customer_price' => $request->customer_price,
                'delivery_staff' => $request->delivery_staff,
                'device_password' => $request->device_password,
                'status' => $request->status,
            );
            $tech = $this->technicalService->update($request->id, $data);
            return response()->json(true, 200);
        }
        $technicalserviceProcess = new TechnicalServiceProcess();
        $technicalserviceProcess->technical_service_id = $tech->id;
        $technicalserviceProcess->company_id = Auth::user()->company_id;
        $technicalserviceProcess->user_id = Auth::user()->id;
        $technicalserviceProcess->status = "1";
        $technicalserviceProcess->save();

        return response()->json($tech->id, 200);
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->technicalService->update($request->id, $data);
    }


    protected function detailstore(Request $request)
    {
        $stockcardmovement = StockCardMovement::find($request->stock_card_movement_id);

        $user = TechnicalServiceProducts::firstOrCreate(
            ['stock_card_movement_id' => request('stock_card_movement_id')
                , 'serial_number' => request('serial')
                , 'technical_service_id' => request('id')],
            [
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'stock_card_id' => $stockcardmovement->stock_card_id,
                'quantity' => 1,
                'sale_price' => request('sale_price'),
            ]
        );

        if ($user) {
            $stockcardmovement->type = 5;
            $stockcardmovement->save();
        }
        return redirect()->back();
    }


    protected function coveringdetailstore(Request $request)
    {
        if ($request->filled('stock_card_movement_id')) {
            $stockcardmovement = StockCardMovement::find($request->stock_card_movement_id);
            if ($stockcardmovement) {
                TechnicalCustomProducts::firstOrCreate(
                    ['stock_card_movement_id' => request('stock_card_movement_id')
                        , 'serial_number' => request('serial')
                        , 'technical_custom_id' => request('id')],
                    [
                        'user_id' => Auth::id(),
                        'company_id' => Auth::user()->company_id,
                        'stock_card_id' => $stockcardmovement->stock_card_id,
                        'quantity' => 1,
                        'sale_price' => request('sale_price'),
                    ]
                );
            }
        }

        return redirect()->back();
    }


    protected function sms(Request $request)
    {
        new SendSms($request);
        return redirect()->back();
    }


    protected function category()
    {
        $data['categories_all'] = TechnicalProcess::all();
        return view('module.technical_service.process', $data);
    }

    protected function categorystore(Request $request)
    {
        $technicalprocess = new TechnicalProcess();
        $technicalprocess->name = $request->name;
        $technicalprocess->parent_id = $request->parent_id;
        $technicalprocess->company_id = Auth::user()->company_id;
        $technicalprocess->user_id = Auth::id();
        $technicalprocess->save();
        return redirect()->back();
    }

    public function covering(Request $request)
    {
        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['customers'] = $this->customerService->all();
        $data['brands'] = $this->brandService->get();
        $data['users'] = $this->userService->get();
        $data['citys'] = City::all();
        $data['categories_all'] = TechnicalProcess::all();
        $data['tows'] = Town::where('city_id', 34)->get();
        return view('module.technical_service.covering', $data);
    }

    public function coveringstore(Request $request)
    {

        $tecknicalServiceCheck = TechnicalCustomService::where('created_at', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($tecknicalServiceCheck) {
            return redirect()->back();
        }
        $technical_custom_service = new TechnicalCustomService();
        $technical_custom_service->company_id = Auth::user()->company_id;
        $technical_custom_service->user_id = Auth::user()->id;
        $technical_custom_service->brand_id = $request->brand_id;
        $technical_custom_service->seller_id = Auth::user()->seller_id;
        $technical_custom_service->version_id = $request->version_id;
        $technical_custom_service->customer_id = $request->customer_id;
        $technical_custom_service->total_price = $request->total_price;
        $technical_custom_service->customer_price = 0;
        $technical_custom_service->type = $request->type;
        $technical_custom_service->coating_information = $request->coating_information;
        $technical_custom_service->print_information = $request->print_information;
        $technical_custom_service->delivery_staff = $request->delivery_staff;
        $technical_custom_service->save();
        $id = $technical_custom_service->id;
        return redirect()->route('technical_service.coveredit', ['id' => $id]);
    }

    public function coveringupdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $total = $request->payment_type['cash'] + $request->payment_type['credit_card'];
            if ($total != $request->customer_price) {
                return redirect()->back()->with(['msg' => 'Tutarlar Eşleşmiyor']);;
            }
            $technicalservice = \App\Models\TechnicalCustomService::find($request->id);

            $technicalserviceproducts = TechnicalCustomProducts::where('technical_custom_id', $technicalservice->id)->get();

            if (count($technicalserviceproducts) == 0) {
                return redirect()->back()->with(['msg' => 'Ürün bulunmayan hizmetten ödeme alınamaz']);;
            }

            $data = array(
                'type' => 2,
                'number' => "IN" . rand(1111, 9999) . date("m"),
                'create_date' => Carbon::parse($request->create_date)->format('Y-m-d') ?? null,
                'credit_card' => $request->payment_type['credit_card'],
                'cash' => $request->payment_type['cash'],
                'installment' => $request->payment_type['installment'],
                'description' => "Teknik Servis",
                'is_status' => 1,
                'total_price' => $request->customer_price,
                'tax_total' => 1,
                'discount_total' => 0,
                'staff_id' => $request->delivery_staff,
                'customer_id' => $technicalservice->customer_id ?? null,
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->company_id,
                'exchange' => 2,
                'tax' => 30,
                'file' => null,
                'paymentStatus' => 'paid',
                'paymentDate' => date('Y-m-d'),
                'paymentStaff' => Auth::user()->id,
                'periodMounth' => Date('m'),
                'periodYear' => Date('Y'),
                'accounting_category_id' => 15,
                'currency' => $request->currency ?? null,
                'safe_id' => null,
            );

            $invoiceID = $this->invoiceService->create($data);

            $technical_custom_service = TechnicalCustomService::find($request->id);
            $technical_custom_service->company_id = Auth::user()->company_id;
            $technical_custom_service->user_id = Auth::user()->id;
            $technical_custom_service->brand_id = $request->brand_id;
            $technical_custom_service->version_id = $request->version_id;
            $technical_custom_service->customer_id = $request->customer_id;
            $technical_custom_service->total_price = $request->total_price;
            $technical_custom_service->customer_price = ($request->customer_price != 0)?$request->customer_price:$request->total_price;
            $technical_custom_service->type = $request->type;
            $technical_custom_service->coating_information = $request->coating_information;
            $technical_custom_service->print_information = $request->print_information;
            $technical_custom_service->delivery_staff = $request->delivery_staff;
            $technical_custom_service->seller_id = Auth::user()->seller_id;
            $technical_custom_service->save();


            foreach ($technicalserviceproducts as $item) {

                if ($request->type == 'Kaplama') {
                    $type = 4;
                } else {
                    $type = 5;
                }

                $stockcardmovement = StockCardMovement::where('type', 1)->where('serial_number', $item->serial_number)->first();

                $SaleCheck = Sale::where('serial', $item->serial_number)->first();
                if (!$SaleCheck) {
                    $sale = new Sale();
                    $sale->stock_card_id = $stockcardmovement->stock_card_id;
                    $sale->stock_card_movement_id = $stockcardmovement->id;
                    $sale->invoice_id = $invoiceID->id;
                    $sale->customer_id = $technicalservice->customer_id;
                    $sale->sale_price = $item->sale_price;
                    $sale->customer_price = $stockcardmovement->customer_price;
                    $sale->name = StockCard::find($stockcardmovement->stock_card_id)->name;
                    $sale->seller_id = Auth::user()->seller_id;
                    $sale->company_id = Auth::user()->company_id;
                    $sale->user_id = Auth::user()->id;
                    $sale->serial = $item->serial_number;
                    $sale->technical_service_person_id = $request->delivery_staff;
                    $sale->discount = 0;
                    $sale->base_cost_price = $stockcardmovement->base_cost_price;
                    $sale->type = $type;
                    $sale->save();
                }
                $stockcardmovement->type = 2;
                $stockcardmovement->save();
            }


            $technicalservice->delivery_staff = $request->delivery_staff;
            $technicalservice->payment_status = 1;
            $technicalservice->save();


            $safe = new Safe();
            $safe->name = "Şirket";
            $safe->company_id = Auth::user()->company_id;
            $safe->user_id = Auth::user()->id;
            $safe->seller_id = Auth::user()->seller_id;
            $safe->type = "in";
            $safe->incash = $request->payment_type['cash'] ?? 0;
            $safe->outcash = "0";
            $safe->amount = $request->total_price ?? 0;
            $safe->invoice_id = $request->id;
            $safe->credit_card = $request->payment_type['credit_card'] ?? 0;
            $safe->installment = 0;
            $safe->description = "Kaplama";
            $safe->save();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        DB::commit();

        return redirect()->to('technical_service?tab_type=_cover');
    }


    public
    function coverprint(Request $request)
    {
        $data['cover'] = TechnicalCustomService::find($request->id);
        return view('module.technical_service.coverprint', $data);
    }

    public
    function print(Request $request)
    {
        $technical_serice = \App\Models\TechnicalService::find($request->id);
        if (!$technical_serice->customer) {
            return redirect()->back();
        }

        $data['technical_service'] = $technical_serice;
        $data['technical_service_process'] = TechnicalServiceProcess::where('technical_service_id', $request->id)->get();
        return view('module.technical_service.print', $data);
    }

    public function statusCgange(Request $request)
    {

        if ($request->val == 5) {
            return response()->json('Ödeme Alındı Değiştirilemez', 200);
        }

        if ($request->val == 7 || $request->val == 8 || $request->val == 9 || $request->val == 10) {
            $technical_service_products = TechnicalServiceProducts::where('technical_service_id', $request->id)->get();
            foreach ($technical_service_products as $item) {
                $stcm = StockCardMovement::find($item->stock_card_movement_id);
                $stcm->type = 1;
                $stcm->save();
                $item->delete();
            }
        }

        $techservice = \App\Models\TechnicalService::find($request->id);
        $techservice->status = $request->val;
        $techservice->save();


        $tech = new TechnicalServiceProcess();
        $tech->technical_service_id = $request->id;
        $tech->company_id = Auth::user()->company_id;
        $tech->user_id = Auth::user()->id;
        $tech->status = $request->val;
        $tech->save();

        return response()->json('Güncellendi', 200);

    }

    protected function coverdetaildelete(Request $request)
    {
        $technicalService = \App\Models\TechnicalCustomService::find($request->id);
        if ($technicalService->payment_status == "1") {
            $technicalServiceProduct = TechnicalCustomProducts::find($request->id);
            $technicalServiceProduct->delete();

            $stockcardmovement = StockCardMovement::find($technicalServiceProduct->stock_card_movement_id);
            $stockcardmovement->type = 1;
            $stockcardmovement->save();
        }
        return redirect()->back();
    }
}
