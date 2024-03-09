<?php

namespace App\Http\Controllers;

use App\Enums\Tax;
use App\Models\AccountingCategory;
use App\Models\City;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Phone;
use App\Models\Refund;
use App\Models\Safe;
use App\Models\Sale;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\StockCardPrice;
use App\Models\Transfer;
use App\Services\AccountingCategory\AccountingCategoryService;
use App\Services\Brand\BrandService;
use App\Services\Category\CategoryService;
use App\Services\Color\ColorService;
use App\Services\Customer\CustomerService;
use App\Services\Reason\ReasonService;
use App\Services\Safe\SafeService;
use App\Services\Seller\SellerService;
use App\Services\Invoice\InvoiceService;
use App\Services\StockCard\StockCardService;
use App\Services\User\UserService;
use App\Services\Version\VersionService;
use App\Services\Warehouse\WarehouseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use elogo_api\elogo_api;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Picqer\Barcode\BarcodeGeneratorHTML;
use SN;

class InvoiceController extends Controller
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
        $data['invoices'] = $this->invoiceService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['type'] = $request->type;
        return view('module.invoice.index', $data);
    }

    protected function create(Request $request)
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
        $data['stock_card_id'] = $request->id;
        $data['last_price'] = StockCardPrice::where('stock_card_id', $request->id)->orderBy('id', 'desc')->first();
        // return view('module.invoice.form', $data);
        return view('module.invoice.newinvoiceform', $data);
    }

    protected function fast()
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['customers'] = $this->customerService->all();
        $data['citys'] = City::all();
        $data['currencies'] = $this->currency->all();
        $data['taxs'] = ['0' => '%0', '1' => '%1', '8' => '%8', '18' => '%18'];
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        return view('module.invoice.fast', $data);
    }

    protected function personal()
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['customers'] = $this->customerService->all();
        $data['citys'] = City::all();
        $data['currencies'] = $this->currency->all();
        $data['taxs'] = ['0' => '%0', '1' => '%1', '8' => '%8', '18' => '%18'];
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        return view('module.invoice.personal', $data);
    }

    protected function bank()
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        $data['currencies'] = $this->currency->all();
        return view('module.invoice.bank', $data);
    }

    protected function tax()
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['currencies'] = $this->currency->all();
        $data['taxs'] = ['0' => '%0', '1' => '%1', '8' => '%8', '18' => '%18'];
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        return view('module.invoice.tax', $data);
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
        return view('module.invoice.form', $data);
    }

    protected function show(Request $request)
    {
        $data['invoice'] = $this->invoiceService->find($request->id);
        return view('module.invoice.show', $data);
    }

    protected function movement(Request $request)
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['stock_card_id'] = $request->id;
        $data['movements'] = InvoiceMovement::where('stock_card_id', $request->id)->get();
        return view('module.invoice.movement', $data);
    }

    protected function sevk(Request $request)
    {
        $serial_stock_card_movement = InvoiceMovement::where('serial_number', $request->serial_number)->first();

        if (is_null($serial_stock_card_movement) || $serial_stock_card_movement->quantityCheck($request->serial_number) >= 0) {
            return response()->json("Seri Numarası Bulunamadı Veya Stok Yetersiz", 400);
        }
        if ($serial_stock_card_movement->transfer->is_status == 1) {
            return response()->json("Beklemede olan sevk işlemi var", 400);
        }

        $transfer = new Transfer();
        $transfer->stock_card_id = $request->stock_card_id;
        $transfer->serial_number = $request->serial_number;
        $transfer->stock_card_movement_id = $serial_stock_card_movement->id;
        $transfer->user_id = Auth::user()->id;
        $transfer->is_status = 1;
        $transfer->save();
        return response()->json("Sevk Başlatıldı", 200);
    }

    protected function delete(Request $request)
    {
        $this->invoiceService->delete($request->id);
        return redirect()->back();
    }

    protected function store(Request $request)
    {

        $data = array(
            'type' => $request->type,
            'number' => $request->number ?? "IN" . rand(1111, 9999) . date("m"),
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

        //  if (isset($request->group_a)) {

        //      if (empty($request->id)) {
        //          $this->stockCardService->add_movement($request->group_a, $invoiceID, $request->type);
        //      } else {
        //          $this->stockCardService->add_movementupdate($request->group_a, $invoiceID, $request->type);
        //      }

        //      $total = 0;
        //      $taxtotal = 0;
        //      $discount_total = 0;

        //      foreach ($request->group_a as $item) {
        //          $costprice = str_replace(",", ".", $item['cost_price']);
        //          $total += $costprice + (($costprice * $item['tax']) / 100) * $item['quantity'];
        //          $taxtotal += (($costprice * $item['tax']) / 100) * $item['quantity'];
        //          $discount_total += (($costprice * $item['discount'] ?? 0) / 100) * $item['quantity'];
        //      }
        //      $totalprice = $total - $discount_total;

        //      $newdata = array(
        //          'total_price' => $totalprice,
        //          'discount_total' => $discount_total,
        //          'taxtotal' => $taxtotal,
        //      );

        //      $this->invoiceService->update($invoiceID->id, $newdata);
        //  }

        //  $total = $request->payment_type['cash'] + $request->payment_type['credit_card'];
        //  $safe = new Safe();
        //  $safe->name = "Şirket";
        //  $safe->company_id = Auth::user()->company_id;
        //  $safe->user_id = Auth::user()->id;
        //  $safe->seller_id = Auth::user()->seller_id;
        //  $safe->type = "out";
        //  $safe->incash = $request->payment_type['cash'] ?? 0;
        //  $safe->outcash = "0";
        //  $safe->amount = $total ?? 0;
        //  $safe->invoice_id = $invoiceID->id;
        //  $safe->credit_card = $request->payment_type['credit_card'] ?? 0;
        //  $safe->installment = 0;
        //  $safe->description = AccountingCategory::find($request->accounting_category_id)->name;
        //  $safe->save();

        return response()->json($invoiceID->id, 200);
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->invoiceService->update($request->id, $data);
    }

    public function einvoice()
    {
        $elogo_username = '3600330874';
        $elogo_password = 'Erktelekom123*';

        $elogo = new elogo_api($elogo_username, $elogo_password, false);
        $elogo->invoce_prefix = 'ERK'; //FATURA NUMARASI OLUŞTURMASI İÇİN EN FAZLA 3 KARAKTER

        $result = $elogo->get_documents_list();
        dd($result['message']->Document);
        //E-ARŞİV FATURASI BİLGİSİ ALMA
        // $result = $elogo->get_document_info('1dfe9cfa-2c86-4e28-b7f5-5104faf00197', 'EARCHIVE');
        // print_r($result);
        // //E-ARŞİV FATURASI BİLGİSİ ALMA

        // //E-FATURA BİLGİSİ ALMA
        // $result = $elogo->get_document_info('1dfe9cfa-2c86-4e28-b7f5-5104faf00197', 'EINVOICE');
        // print_r($result);
        // //E-FATURA BİLGİSİ ALMA

    }

    public function serialprint(Request $request)
    {
        $data = [];
        $movements = $this->stockCardService->getInvoiceForSerial($request->id);
        foreach ($movements as $item) {
            $data[] = [
                'id' => $item->id,
                'serial_number' => $item->serial_number,
                'sale_price' => $item->sale_price,
                'brand_name' => $item->stockcard()->brand->name,
                'stock_name' => $item->stockcard()->name,
                'color_name' => $item->color->name,
                'category_sperator_name' => $this->categorySeperator($item->testParent($item->stockcard()->category->id)),
                'category_name' => $item->stockcard()->category->name ?? 'Bulunamadı',
                'versions' => $this->getVersionMap($item->stockcard()->version()),
            ];
        }
        return view('module.stockcard.barcode', compact('data'));
        // $pdf = PDF::loadView('module.stockcard.print', ['data' => $data]);
        // return $pdf->stream('codesolutionstuff.pdf');
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

    public function categorySeperator($data)
    {
        if (!empty($data)) {
            return implode("/", $this->array_column_recursive($data, 'name')) . " /";
        }
        return "";
    }


    public function sales(Request $request)
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get()->where('is_status', 1);
        $data['reasons'] = $this->reasonService->get();
        $data['customers'] = $this->customerService->all();
        $data['citys'] = City::all();
        $data['stocks'] = $this->stockCardService->all();
        $data['categories'] = $this->accountingCategoryService->all();
        $data['safes'] = $this->safeService->all();
        $data['taxs'] = ['0' => '%0', '1' => '%1', '8' => '%8', '18' => '%18'];
        $data['request'] = $request;
        $product = $this->stockCardService->getStockData($request);
        if (empty($product['stock_card'])) {
            return redirect()->back();
        }
        $data['product'] = $this->stockCardService->getStockData($request);
        return view('module.invoice.sales', $data);
    }

    public function salesedit(Request $request)
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
        $data['request'] = $request;

        $invoice = Invoice::find($request->id);
        if ($invoice->accounting_category_id === 9999999) {
            $data['invoice'] = Invoice::find($request->id);
            $data['phone'] = Phone::where('invoice_id', $request->id)->first();
            return view('module.phone.saleedit', $data);
        } else {
            $data['invoice'] = Invoice::find($request->id);
            $data['stockcardmovements'] = StockCardMovement::where('invoice_id', $request->id)->get();
            return view('module.invoice.salesedit', $data);
        }

    }

    public function salesstore(Request $request)
    {

        DB::beginTransaction();
        try {
            $totalSalePrice = 0;
            $x = array_count_values($request->serial);
            $x = array_values($x);
            foreach ($x as $i) {
                if ($i > 1) {
                    return response()->json("Aynı seri numarası eklenemez", 405);
                }
            }
            $total = $request->payment_type['credit_card'] + $request->payment_type['cash'] + $request->payment_type['installment'];


            $i = 0;
            foreach ($request->stock_card_id as $item) {
                $totalSalePrice += $request->sale_price[$i];
                $i++;
            }

            /*
            foreach ($request->serial as $item) {

                $stockcard = StockCardMovement::where('serial_number', $item)->where('type', 1)->orderBy('id', 'desc')->first();
                if (!$stockcard) {
                       return false;
                }

                $totalSalePrice += $stockcard->sale_price;
               // return response()->json($totalSalePrice, 405);
            }
            */
            if (number_format($total, 2) != number_format($totalSalePrice, 2)) {
                return response()->json("Ürün fiyatı toplam fiyata eşit değil", 405);
            }
            $data = array(
                'type' => $request->type,
                'number' => $request->number ?? null,
                'create_date' => Carbon::parse($request->create_date)->format('Y-m-d') ?? null,
                'credit_card' => $request->payment_type['credit_card'],
                'cash' => $request->payment_type['cash'],
                'installment' => $request->payment_type['installment'],
                'description' => $request->description ?? null,
                'is_status' => 1,
                'total_price' => $totalSalePrice,
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

            if ($request->filled('serial')) {
                // $this->stockCardService->add_movement_sales($request->group_a, $invoiceID, $request->type);
                $total = 0;
                $taxtotal = 0;
                $discount_total = 0;

                $i = 0;
                foreach ($request->stock_card_id as $item) {
                    $total += $request->sale_price[$i];
                    $taxtotal += (($request->sale_price[$i] * 18) / 100) * 1;
                    $discount_total += (($request->sale_price[$i] * $request->discount[$i] ?? 0) / 100) * 1;


                    $stockcardmovement = StockCardMovement::where('type', 1)->where('serial_number', $request->serial[$i])->first();

                    $SaleCheck = Sale::where('serial', $request->serial[$i])->first();
                    if (!$SaleCheck) {
                        $sale = new Sale();
                        $sale->stock_card_id = $item;
                        $sale->stock_card_movement_id = $stockcardmovement->id;
                        $sale->invoice_id = $invoiceID->id;
                        $sale->customer_id = $request->customer_id;
                        $sale->sale_price = $request->sale_price[$i];
                        $sale->customer_price = $stockcardmovement->sale_price - (($stockcardmovement->sale_price * $request->discount[$i]) / 100);
                        $sale->name = StockCard::find($item)->name;
                        $sale->seller_id = $stockcardmovement->seller_id;
                        $sale->company_id = Auth::user()->company_id;
                        $sale->user_id = $request->staff_id;
                        $sale->serial = $request->serial[$i];
                        $sale->discount = $request->discount[$i];
                        $sale->base_cost_price = $stockcardmovement->base_cost_price;
                        $sale->save();
                    }
                    $stockcardmovement->type = 2;
                    $stockcardmovement->save();
                    $i++;
                }

                // $totalprice = $total - $discount_total;

                $newdata = array(
                    // 'total_price' => $totalprice,
                    'discount_total' => $discount_total,
                    'taxtotal' => $taxtotal,
                );

                $this->invoiceService->update($invoiceID->id, $newdata);
            }

            $safe = new Safe();
            $safe->name = "Şirket";
            $safe->company_id = Auth::user()->company_id;
            $safe->user_id = Auth::user()->id;
            $safe->type = "in";
            $safe->incash = $request->payment_type['cash'];
            $safe->outcash = "0";
            $safe->amount = $request->payment_type['cash'] + $request->payment_type['credit_card'] + $request->payment_type['installment'];
            $safe->invoice_id = $invoiceID->id;
            $safe->credit_card = $request->payment_type['credit_card'];
            $safe->installment = $request->payment_type['installment'];
            $safe->description = "SATIŞ";
            $safe->seller_id = Auth::user()->seller_id;
            $safe->save();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        DB::commit();

        return response()->json('Kaydedildi', 200);
    }

    public function salesupdate(Request $request)
    {

        foreach ($request->group_a as $item) {
            $stockcard = StockCardMovement::where('serial_number', $item['serial'])->where('type', 2)->orderBy('id', 'desc')->first();
            if (!$stockcard) {
                return false;
            }
        }

        $data = array(
            'type' => $request->type,
            'number' => $request->number ?? null,
            'create_date' => Carbon::parse($request->create_date)->format('Y-m-d') ?? null,
            'credit_card' => $request->payment_type['credit_card'],
            'cash' => $request->payment_type['cash'],
            'installment' => $request->payment_type['installment'],
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
        $this->invoiceService->update($request->id, $data);
        $invoiceID = $this->invoiceService->find($request->id);
        if (isset($request->group_a)) {
            $this->stockCardService->add_movement_update($request->group_a, $invoiceID, $request->type);
            $total = 0;
            $taxtotal = 0;
            $discount_total = 0;

            foreach ($request->group_a as $item) {
                $total += $item['sale_price'] + (($item['sale_price'] * 18) / 100) * 1;
                $taxtotal += (($item['sale_price'] * 18) / 100) * 1;
                $discount_total += (($item['sale_price'] * $item['discount'] ?? 0) / 100) * 1;
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

    public function stockcardmovementform(Request $request)
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['invoice'] = Invoice::find($request->id);
        $data['reasons'] = $this->reasonService->get();
        $data['citys'] = City::all();
        $data['invoice_id'] = $request->id;
        $data['stock_card_id'] = "";
        $data['stock_card_movements'] = StockCardMovement::selectRaw('*,count(stock_card_id) as quant')->where('invoice_id', $request->id)->groupBy('stock_card_id')->get();
        return view('module.invoice.stockcardmovementform', $data);
    }

    public function stockcardmovementstore(Request $request)
    {

        $data = array(
            'type' => $request->type,
            'number' => $request->number ?? "IN" . rand(1111, 9999) . date("m"),
            'create_date' => Carbon::parse($request->create_date)->format('Y-m-d') ?? null,
            //'payment_type' => $request->payment_type,
            'description' => null,
            'is_status' => 1,
            'total_price' => 1,
            'tax_total' => 1,
            'discount_total' => 1,
            'staff_id' => $request->staff_id ?? null,
            'customer_id' => $request->customer_id ?? null,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
            'exchange' => $request->exchange ?? null,
            'tax' => null,
            'file' => $request->file ?? null,
            'paymentStatus' => "unpaid",
            'paymentDate' => $request->paymentDate ?? null,
            'paymentStaff' => $request->paymentStaff ?? null,
            'periodMounth' => $request->periodMounth ?? null,
            'periodYear' => $request->periodYear ?? null,
            'accounting_category_id' => 1,
            'currency' => $request->currency ?? null,
            'safe_id' => $request->safe_id ?? null,
        );

        if ($request->id == 0) {
            $invoiceID = $this->invoiceService->create($data);
        } else {
            Invoice::where('id', $request->id)->update($data);
            $invoiceID = Invoice::find($request->id);
        }

        StockCardMovement::where('invoice_id', $invoiceID->id)->where('type', 1)->delete();

        $a = 0;
        foreach ($request->stock_card_id as $item) {
            $stockcardlist[$a]['stockcardid'] = $item;
            $stockcardlist[$a]['user_id'] = Auth::user()->id;
            $stockcardlist[$a]['company_id'] = Auth::user()->company_id;
            $stockcardlist[$a]['invoice_id'] = $invoiceID->id;
            $stockcardlist[$a]['color_id'] = $request->color_id[$a];
            $stockcardlist[$a]['warehouse_id'] = $request->warehouse_id[$a] ?? 1;
            $stockcardlist[$a]['seller_id'] = $request->seller_id[$a];
            $stockcardlist[$a]['reason_id'] = $request->reason_id[$a];
            $stockcardlist[$a]['type'] = 1;
            $stockcardlist[$a]['quantity'] = $request->quantity[$a];
            $stockcardlist[$a]['imei'] = $request->imei[$a] ?? null;
            $stockcardlist[$a]['assigned_accessory'] = isset($request->assigned_accessory[$a]) and $item->assigned_accessory[$a] == 'on' ? 1 : 0;
            $stockcardlist[$a]['assigned_device'] = isset($request->assigned_device[$a]) and $item->assigned_device[$a] == 'on' ? 1 : 0;
            $stockcardlist[$a]['tax'] = $request->tax[$a] ?? null;
            $stockcardlist[$a]['cost_price'] = str_replace(",", ".", $request->cost_price[$a]);
            $stockcardlist[$a]['base_cost_price'] = str_replace(",", ".", $request->base_cost_price[$a]);
            $stockcardlist[$a]['sale_price'] = str_replace(",", ".", $request->sale_price[$a]);
            $stockcardlist[$a]['description'] = $request->description[$a] ?? null;
            $stockcardlist[$a]['discount'] = $request->discount[$a] ?? null;
            $stockcardlist[$a]['tracking_quantity'] = $request->tracking_quantity[$a] ?? 0;


            $stockmovementcount = StockCardMovement::where('invoice_id', $invoiceID->id)->whereNot('type', 1)->count();

            $newQuantity = $request->quantity[$a] - $stockmovementcount;

            for ($i = 0; $i < $newQuantity; $i++) {

                $timer = date('');

                $newSerial = $this->newSerialNumberCreate();


                $stockcardmovement = new StockCardMovement();
                $stockcardmovement->stock_card_id = $item;
                $stockcardmovement->user_id = Auth::user()->id;
                $stockcardmovement->company_id = Auth::user()->company_id;
                $stockcardmovement->invoice_id = $invoiceID->id;
                $stockcardmovement->color_id = $request->color_id[$a];
                $stockcardmovement->warehouse_id = $request->warehouse_id[$a] ?? 1;
                $stockcardmovement->seller_id = $request->seller_id[$a];
                $stockcardmovement->reason_id = $request->reason_id[$a];
                $stockcardmovement->type = 1;
                $stockcardmovement->quantity = 1;
                $stockcardmovement->imei = $request->imei[$a] ?? null;
                $stockcardmovement->assigned_accessory = isset($request->assigned_accessory[$a]) and $item->assigned_accessory[$a] == 'on' ? 1 : 0;
                $stockcardmovement->assigned_device = isset($request->assigned_device[$a]) and $item->assigned_device[$a] == 'on' ? 1 : 0;
                $stockcardmovement->serial_number = $request->serial[$a] ?? $newSerial;
                $stockcardmovement->tax = $request->tax[$a] ?? null;
                $stockcardmovement->cost_price = str_replace(",", ".", $request->cost_price[$a]);
                $stockcardmovement->base_cost_price = str_replace(",", ".", $request->base_cost_price[$a]);
                $stockcardmovement->sale_price = str_replace(",", ".", $request->sale_price[$a]);
                $stockcardmovement->description = $request->description[$a] ?? null;
                $stockcardmovement->discount = $request->discount[$a] ?? null;
                $stockcardmovement->tracking_quantity = $request->tracking_quantity[$a] ?? 0;
                $stockcardmovement->save();

                $stockcardprice = new StockCardPrice();
                $stockcardprice->company_id = Auth::user()->company_id;
                $stockcardprice->user_id = Auth::user()->id;
                $stockcardprice->stock_card_id = $request->stock_card_id[$a];
                $stockcardprice->cost_price = str_replace(",", ".", $request->cost_price[$a]);
                $stockcardprice->base_cost_price = str_replace(",", ".", $request->base_cost_price[$a]);
                $stockcardprice->sale_price = str_replace(",", ".", $request->sale_price[$a]);
                $stockcardprice->save();
            }


            $a++;
        }

        $invoiceID->detail = $stockcardlist;
        $invoiceID->save();
        return redirect()->route('invoice.serialprint', ['id' => $invoiceID->id]);

    }

    public function stockmovementdelete(Request $request)
    {
        StockCardMovement::where('stock_card_id', $request->id)->where('type', 1)->delete();
        return redirect()->back();
    }

    public function pdf(Request $request)
    {
        $data = [];
        $movements = $this->stockCardService->getInvoiceForSerial($request->id);
        foreach ($movements as $item) {
            $data[] = [
                'title' => 'Welcome to CodeSolutionStuff.com',
                'id' => $item->id,
                'serial_number' => $item->serial_number,
                'sale_price' => $item->sale_price,
                'brand_name' => $item->stockcard()->brand->name,
                'name' => $item->stockcard()->name,
                'version' => $this->getVersionMap($item->stockcard()->version()),
            ];
        }


        $pdf = PDF::loadView('module.stockcard.print', ['data' => $data]);

        return $pdf->stream('codesolutionstuff.pdf');
    }

    public function getVersionMap($map)
    {

        $datas = json_decode($map, TRUE);
        $x = "";
        foreach ($datas as $mykey => $myValue) {
            $x .= $myValue . ",";
        }
        return $x;
    }

    public function stockcardmovementformrefund(Request $request)
    {
        $refund = Refund::find($request->refund_id);
        $stockcardmovements = StockCardMovement::where('serial_number', $refund->serial_number)->first();

        if ($stockcardmovements) {
            $stockcardmovement = new StockCardMovement();
            $stockcardmovement->stock_card_id = $stockcardmovements->stock_card_id;
            $stockcardmovement->user_id = Auth::user()->id;
            $stockcardmovement->invoice_id = $stockcardmovements->invoice_id;
            $stockcardmovement->color_id = $stockcardmovements->color_id;
            $stockcardmovement->warehouse_id = $stockcardmovements->warehouse_id;
            $stockcardmovement->seller_id = $stockcardmovements->seller_id;
            $stockcardmovement->reason_id = $stockcardmovements->reason_id;
            $stockcardmovement->type = 1;
            $stockcardmovement->quantity = 1;
            $stockcardmovement->imei = $stockcardmovements->imei ?? null;
            $stockcardmovement->assigned_accessory = $stockcardmovements->assigned_accessory;
            $stockcardmovement->assigned_device = $stockcardmovements->assigned_device;
            $stockcardmovement->serial_number = $stockcardmovements->serial;
            $stockcardmovement->tax = $stockcardmovements->tax;
            $stockcardmovement->cost_price = $stockcardmovements->cost_price;
            $stockcardmovement->base_cost_price = $stockcardmovements->base_cost_price;
            $stockcardmovement->sale_price = $stockcardmovements->sale_price;
            $stockcardmovement->description = $stockcardmovements->description;
            $stockcardmovement->discount = $stockcardmovements->discount;
            $stockcardmovement->tracking_quantity = $stockcardmovements->tracking_quantity ?? 0;
            $stockcardmovement->save();

            $stockcardprice = new StockCardPrice();
            $stockcardprice->company_id = Auth::user()->company_id;
            $stockcardprice->user_id = Auth::user()->id;
            $stockcardprice->stock_card_id = $stockcardmovement->stock_card_id;
            $stockcardprice->cost_price = $stockcardmovement->cost_price;
            $stockcardprice->base_cost_price = $stockcardmovement->base_cost_price;
            $stockcardprice->sale_price = $stockcardmovement->sale_price;
            $stockcardprice->save();

            $refund->status = 2;
            $refund->save();

            return redirect()->back();
        }

        $data['warehouses'] = $this->warehouseService->get();
        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['invoice'] = Invoice::find($request->id);
        $data['reasons'] = $this->reasonService->get();
        $data['citys'] = City::all();
        $data['invoice_id'] = $request->id;
        $data['refund'] = $refund;
        $data['stock_card_movements'] = StockCardMovement::selectRaw('*,count(stock_card_id) as quant')->where('invoice_id', $request->id)->groupBy('stock_card_id')->get();
        return view('module.invoice.stockcardmovementformrefund', $data);
    }


    public function itemSave(Request $request)
    {
        $invoice = Invoice::find($request->id);
        $detail = $invoice->detail;

        // {"stockcardid":"48","user_id":1,"invoice_id":20,"color_id":"1","warehouse_id":"1","seller_id":"1","reason_id":"9","type":1,"quantity":"10","imei":null,"assigned_accessory":false,"assigned_device":false,"tax":"18","cost_price":"1","base_cost_price":"1","sale_price":"1","description":null,"discount":"0"}]
    }

    /* public function newSerialNumberCreate()
     {
       //  $timer = date('');

         $today = date('md');
         $rand = strtoupper(substr(uniqid(sha1(time())), 0, 4));
         $newSerial = $today.$rand;

 //        $newSerial = strtoupper(rand(101, 999) . substr($timer, 3, 5) . substr(time(), -2) . rand(10, 99));

         $newSerialNumberCheck = StockCardMovement::where('serial_number',$newSerial)->first();
         if($newSerialNumberCheck)
         {
             $this->newSerialNumberCreate();
         }
         return $newSerial;
     }
    */
    function newSerialNumberCreate($lenght = 7)
    {

        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        $newSerial = mb_strtoupper(substr(bin2hex($bytes), 0, $lenght), "UTF-8");
        $newSerialNumberCheck = StockCardMovement::where('serial_number', $newSerial)->first();
        if ($newSerialNumberCheck) {
            $this->newSerialNumberCreate();
        }
        return $newSerial;
    }

}
