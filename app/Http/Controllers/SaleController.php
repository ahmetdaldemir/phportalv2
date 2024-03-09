<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Phone;
use App\Models\Safe;
use App\Models\Sale;
use App\Models\StockCardMovement;
use App\Services\AccountingCategory\AccountingCategoryService;
use App\Services\Brand\BrandService;
use App\Services\Category\CategoryService;
use App\Services\Color\ColorService;
use App\Services\Customer\CustomerService;
use App\Services\Invoice\InvoiceService;
use App\Services\Reason\ReasonService;
use App\Services\Safe\SafeService;
use App\Services\Seller\SellerService;
use App\Services\StockCard\StockCardService;
use App\Services\User\UserService;
use App\Services\Version\VersionService;
use App\Services\Warehouse\WarehouseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private InvoiceService $invoiceService;
    private SellerService $sellerService;
    private WarehouseService $warehouseService;
    private BrandService $brandService;
    private CategoryService $categoryService;
    private ColorService $colorService;
    private VersionService $versionService;
    private ReasonService $reasonService;
    private CustomerService $customerService;
    private UserService $userService;
    private StockCardService $stockCardService;
    private Currency $currency;
    private AccountingCategoryService $accountingCategoryService;
    private SafeService $safeService;


    public function __construct(InvoiceService            $invoiceService,
                                SellerService             $sellerService,
                                WarehouseService          $warehouseService,
                                BrandService              $brandService,
                                CategoryService           $categoryService,
                                ColorService              $colorService,
                                VersionService            $versionService,
                                ReasonService             $reasonService,
                                CustomerService           $customerService,
                                UserService               $userService,
                                StockCardService          $stockCardService,
                                Currency                  $currency,
                                AccountingCategoryService $accountingCategoryService,
                                SafeService               $safeService
    )
    {
        $this->invoiceService = $invoiceService;
        $this->sellerService = $sellerService;
        $this->warehouseService = $warehouseService;
        $this->brandService = $brandService;
        $this->versionService = $versionService;
        $this->colorService = $colorService;
        $this->categoryService = $categoryService;
        $this->reasonService = $reasonService;
        $this->customerService = $customerService;
        $this->userService = $userService;
        $this->stockCardService = $stockCardService;
        $this->currency = $currency;
        $this->accountingCategoryService = $accountingCategoryService;
        $this->safeService = $safeService;
        setlocale(LC_TIME, 'Turkish');  // ya da tr_TR.utf8

    }

    protected function index(Request $request)
    {
        $query = Sale::where('company_id', Auth::user()->company_id);
        if ($request->filled('daterange')) {
            $daterange = explode("to", $request->daterange);
            if (isset($daterange[1])) {
                 $query->whereDate('created_at','>=',trim($daterange[0]) .' 00:00:00')->where('created_at', '<=', trim($daterange[1]). ' 23:59:59');
            } else {
                $query->whereDate('created_at', trim($daterange[0]));
            }
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        if($request->filled('seller'))
        {
            if (\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin')) {
                $query->where('seller_id', $request->seller);
            }else{
                $query->where('seller_id', Auth::user()->seller_id);
            }
        }else{
            if (!\Illuminate\Support\Facades\Auth::user()->hasRole('super-admin')) {
                $query->where('seller_id', Auth::user()->seller_id);
            }
        }
        if($request->filled('brand'))
        {
            $query->whereHas('stock_card',function (Builder $query) use ($request) {
                $query->where('brand_id',$request->brand);
            });
        }

        if($request->filled('category'))
        {
            $query->whereHas('stock_card',function (Builder $query) use ($request) {
                $query->where('category_id',$request->category);
            });
        }

        if($request->filled('version'))
        {
            $query->whereHas('stock_card',function (Builder $query) use ($request) {
                $query->whereJsonContains('version_id',$request->version);
            });
        }


        if($request->filled('serialNumber'))
        {
            $query->whereHas('stock_card_movement',function (Builder $query) use ($request) {
                $query->where('serial_number',$request->serialNumber);
            });
        }


        if ($request->filled('stockName')) {
            $query->where('name', 'like', '%' . $request->stockName . '%');
        }


                    $invoices = $query->get()->groupBy('invoice_id');
                    $profits = 0;
                    $x = 0;
                    $totalPriceInvoices = 0;
                    foreach($invoices as $key => $value)
                    {
                        $invoice = \App\Models\Invoice::find($key);
                        if($invoice)
                        {
                            $totalPriceInvoices += $invoice->total_price;

                            $sale = Sale::where('invoice_id', $invoice->id)->get();
                            foreach ($sale as $item) {
                                if($item->type == 1)
                                {
                                    $x += Phone::where('id', $item->stock_card_movement_id)->first()->cost_price??0;
                                }else{
                                    $x += StockCardMovement::where('id', $item->stock_card_movement_id)->first()->base_cost_price??0;
                                }
                            }
                         }
                    }
            $profits +=  $totalPriceInvoices -$x;




        $data['profits'] = $profits;
        $data['invoices'] = $invoices;
        $data['brands'] = $this->brandService->get();
        $categories =
            DB::select("WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        //dd($categories);
        //  $a = $this->categoryService->getList($request->category);
        $data['categories'] = $categories;//$this->listenke($categories, $request->category);
        $data['sellers'] = $this->sellerService->get();
        return view('module.sale.index', $data);
    }

    protected function create()
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['customers'] = $this->customerService->all();
        $data['citys'] = City::all();
        $data['stocks'] = $this->stockCardService->all();
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        $data['taxs'] = ['0' => '%0', '1' => '%1', '8' => '%8', '18' => '%18'];
        return view('module.sale.form', $data);
    }

    protected function edit(Request $request)
    {
        $data['invoices'] = $this->invoiceService->find($request->id);
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['customers'] = $this->customerService->all();
        $data['citys'] = City::all();
        $data['stocks'] = $this->stockCardService->all();
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        $data['taxs'] = ['0' => '%0', '1' => '%1', '8' => '%8', '18' => '%18'];

        return view('module.sale.form', $data);
    }

    protected function delete(Request $request)
    {
        $this->invoiceService->delete($request->id);
        $collection = StockCardMovement::where('invoice_id', $request->id)->get(['id']);
        StockCardMovement::destroy($collection->toArray());
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $data = array(
            'type' => $request->type,
            'number' => $request->number ?? null,
            'create_date' => Carbon::parse($request->create_date)->format('Y-m-d') ?? null,
            'payment_type' => $request->payment_type,
            'description' => $request->description ?? null,
            'is_status' => 1,
            'total_price' => 1,
            'tax_total' => 1,
            'discount_total' => 1,
            'staff_id' => $request->staff_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
            'exchange' => $request->exchange ?? null,
            'tax' => $request->tax ?? null,
            'file' => $request->file ?? null,
            'paymentStatus' => $request->paymentStatus ?? null,
            'paymentDate' => $request->paymentDate ?? null,
            'paymentStaff' => $request->paymentStaff ?? null,
            'periodMounth' => $request->periodMounth ?? null,
            'periodYear' => $request->periodYear ?? null,
            'accounting_category_id' => $request->accounting_category_id ?? null,
            'currency' => $request->currency ?? null,
            'safe_id' => $request->safe_id ?? null,
        );

        if (empty($request->id)) {
            $invoiceID = $this->invoiceService->create($data);
        } else {
            $this->invoiceService->update($request->id, $data);
            $invoiceID = $this->invoiceService->find($request->id);
        }

        if (isset($request->group_a)) {

            $this->stockCardService->add_movement($request->group_a, $invoiceID, $request->type);
            $total = 0;
            $taxtotal = 0;
            $discount_total = 0;

            foreach ($request->group_a as $item) {
                $total += $item['cost_price'] + (($item['cost_price'] * $item['tax']) / 100) * $item['quantity'];
                $taxtotal += (($item['cost_price'] * $item['tax']) / 100) * $item['quantity'];
                $discount_total += (($item['cost_price'] * $item['discount'] ?? 0) / 100) * $item['quantity'];
            }
            $totalprice = $total - $discount_total;

            $newdata = array(
                'total_price' => $totalprice,
                'discount_total' => $discount_total,
                'taxtotal' => $taxtotal,
            );

            $this->invoiceService->update($invoiceID->id, $newdata);
        }
        return response()->json('Kaydedildi', 200);
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->invoiceService->update($request->id, $data);
    }
}
