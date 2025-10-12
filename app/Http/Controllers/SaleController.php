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
use Illuminate\Support\Facades\Log;

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
    
    /**
     * AJAX endpoint for sales - Vue.js için
     */
    public function getSalesAjax(Request $request)
    {
        try {
            set_time_limit(30);
            
            // Debug logs removed to prevent class not found error
            
            if (!Auth::check()) {
                // User not authenticated
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $user = Auth::user();
            // User authenticated
            
            // Step 1: Get invoices directly with filters (much faster)
            $invoiceQuery = Invoice::where('company_id', $user->company_id)
                ->with(['account:id,fullname', 'staff:id,name']);

            // Invoice query initialized

            // Date filter on invoices
            if ($request->filled('daterange')) {
                $daterange = explode(" to ", $request->daterange);
                if (isset($daterange[1])) {
                    $startDate = Carbon::createFromFormat('Y-m-d', trim($daterange[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('Y-m-d', trim($daterange[1]))->endOfDay();
                    $invoiceQuery->whereBetween('created_at', [$startDate, $endDate]);
                } else {
                    $date = Carbon::createFromFormat('Y-m-d', trim($daterange[0]));
                    $invoiceQuery->whereDate('created_at', $date);
                }
            } else {
                $invoiceQuery->whereDate('created_at', Carbon::today());
            }

            // Only get invoices that have sales
            $invoiceQuery->whereHas('sales');

            // Pagination on invoice level
            $invoices = $invoiceQuery->paginate(50);
            
            // Step 2: Get sales count for each invoice (single query)
            $invoiceIds = $invoices->pluck('id')->toArray();
            $salesCounts = DB::table('sales')
                ->select('invoice_id', DB::raw('COUNT(*) as sales_count'))
                ->whereIn('invoice_id', $invoiceIds)
                ->groupBy('invoice_id')
                ->pluck('sales_count', 'invoice_id');

            // Step 3: Format invoice data for display
            $formattedInvoices = $invoices->map(function ($invoice) use ($salesCounts) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number ?? $invoice->id,
                    'created_at' => $invoice->created_at->format('d.m.Y H:i'),
                    'customer_name' => $invoice->account->fullname ?? 'Genel Cari',
                    'staff_name' => $invoice->staff->name ?? 'Sistem',
                    'credit_card' => $invoice->credit_card ?? 0,
                    'cash' => $invoice->cash ?? 0,
                    'installment' => $invoice->installment ?? 0,
                    'total_price' => $invoice->total_price ?? 0,
                    'tax_total' => $invoice->tax_total ?? 0,
                    'discount_total' => $invoice->discount_total ?? 0,
                    'sales_count' => $salesCounts[$invoice->id] ?? 0,
                    'payment_status' => $invoice->paymentStatus ?? 'paid',
                    'type' => $invoice->type,
                    'type_name' => $invoice->type == 1 ? 'Gelen Fatura' : 'Giden Fatura'
                ];
            });

            return response()->json([
                'invoices' => $formattedInvoices,
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                    'total' => $invoices->total(),
                    'per_page' => $invoices->perPage(),
                    'from' => $invoices->firstItem(),
                    'to' => $invoices->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Data loading failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get sales details for a specific invoice (for modal)
     */
    public function getInvoiceSalesDetails($invoiceId)
    {
        try {
            $sales = Sale::where('invoice_id', $invoiceId)
                ->with([
                    'stockCard:id,name,barcode',
                    'stockCard.brand:id,name',
                    'stockCard.category:id,name', 
                    'stockCardMovement:id,serial_number,cost_price,base_cost_price',
                    'seller:id,name',
                    'user:id,name'
                ])
                ->get();

            $formattedSales = $sales->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'stock_name' => $sale->stockCard->name ?? $sale->name ?? 'N/A',
                    'brand_name' => $sale->stockCard->brand->name ?? 'N/A',
                    'category_name' => $sale->stockCard->category->name ?? 'N/A',
                    'serial_number' => $sale->stockCardMovement->serial_number ?? $sale->serial ?? 'N/A',
                    'type' => $sale->type,
                    'type_name' => Sale::STATUS[$sale->type] ?? 'Bilinmiyor',
                    'sale_price' => $sale->sale_price ?? 0,
                    'cost_price' => $sale->stockCardMovement->cost_price ?? 0,
                    'base_cost_price' => $sale->stockCardMovement->base_cost_price ?? $sale->base_cost_price ?? 0,
                    'profit' => ($sale->sale_price ?? 0) - ($sale->stockCardMovement->base_cost_price ?? $sale->base_cost_price ?? 0),
                    'seller_name' => $sale->seller->name ?? 'N/A',
                    'user_name' => $sale->user->name ?? 'N/A',
                    'created_at' => $sale->created_at->format('d.m.Y H:i')
                ];
            });

            // Calculate totals for this invoice
            $totals = [
                'total_sale_price' => $sales->sum('sale_price'),
                'total_cost_price' => $sales->sum('base_cost_price'),
                'total_profit' => $sales->sum('sale_price') - $sales->sum('base_cost_price'),
                'items_count' => $sales->count()
            ];

            return response()->json([
                'sales' => $formattedSales,
                'totals' => $totals
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Sales details not found: ' . $e->getMessage()], 404);
        }
    }

    /**
     * Get async totals calculation
     */
    public function calculateTotalsAsync(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Build same query as main listing
            $invoiceQuery = Invoice::where('company_id', $user->company_id);

            // Apply same filters
            if ($request->filled('daterange')) {
                $daterange = explode(" to ", $request->daterange);
                if (isset($daterange[1])) {
                    $startDate = Carbon::createFromFormat('Y-m-d', trim($daterange[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('Y-m-d', trim($daterange[1]))->endOfDay();
                    $invoiceQuery->whereBetween('created_at', [$startDate, $endDate]);
                } else {
                    $date = Carbon::createFromFormat('Y-m-d', trim($daterange[0]));
                    $invoiceQuery->whereDate('created_at', $date);
                }
            } else {
                $invoiceQuery->whereDate('created_at', Carbon::today());
            }

            // Calculate totals using single aggregation query
            $totals = $invoiceQuery->selectRaw('
                COUNT(*) as total_invoices,
                SUM(credit_card) as total_credit_card,
                SUM(cash) as total_cash,
                SUM(installment) as total_installment,
                SUM(total_price) as total_revenue,
                SUM(tax_total) as total_tax,
                SUM(discount_total) as total_discount
            ')->first();

            // Calculate profit using efficient join
            $profitData = DB::table('invoices as i')
                ->join('sales as s', 'i.id', '=', 's.invoice_id')
                ->where('i.company_id', $user->company_id);

            // Apply same date filter
            if ($request->filled('daterange')) {
                $daterange = explode(" to ", $request->daterange);
                if (isset($daterange[1])) {
                    $startDate = Carbon::createFromFormat('Y-m-d', trim($daterange[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('Y-m-d', trim($daterange[1]))->endOfDay();
                    $profitData->whereBetween('i.created_at', [$startDate, $endDate]);
                } else {
                    $date = Carbon::createFromFormat('Y-m-d', trim($daterange[0]));
                    $profitData->whereDate('i.created_at', $date);
                }
            } else {
                $profitData->whereDate('i.created_at', Carbon::today());
            }

            $profitCalc = $profitData->selectRaw('
                SUM(i.total_price) as total_revenue,
                SUM(s.base_cost_price) as total_cost
            ')->first();

            $totalProfit = ($profitCalc->total_revenue ?? 0) - ($profitCalc->total_cost ?? 0);

            return response()->json([
                'totals' => [
                    'total_invoices' => $totals->total_invoices ?? 0,
                    'credit_card' => $totals->total_credit_card ?? 0,
                    'cash' => $totals->total_cash ?? 0,
                    'installment' => $totals->total_installment ?? 0,
                    'gross_total' => $totals->total_revenue ?? 0,
                    'tax_total' => $totals->total_tax ?? 0,
                    'discount_total' => $totals->total_discount ?? 0,
                    'profit' => $totalProfit
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Totals calculation failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * AJAX endpoint for versions - Vue.js için
     */
    public function getVersionsAjax(Request $request)
    {
        try {
            $brandId = $request->get('brand_id');
            if (!$brandId) {
                return response()->json([]);
            }
            
            $versions = \App\Models\Version::where('brand_id', $brandId)->get();
            return response()->json($versions);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Versiyonlar yüklenemedi'], 500);
        }
    }

    /**
     * Sale index page
     */
    public function index()
    {
        try {
            // Load filter data
            $brands = $this->brandService->get();
            $categories = $this->categoryService->get();
            $sellers = $this->sellerService->get();

            return view('module.sale.index', compact('brands', 'categories', 'sellers'));
        } catch (\Exception $e) {
            Log::error('Sale index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sayfa yüklenirken bir hata oluştu.');
        }
    }
}
