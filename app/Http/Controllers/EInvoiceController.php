<?php

namespace App\Http\Controllers;

use App\Jobs\ElogoCreateInvoice;
use App\Models\City;
use App\Models\Company;
use App\Models\Customer;
use App\Models\StockCard;
use App\Services\Brand\BrandService;
use App\Services\Category\CategoryService;
use App\Services\Color\ColorService;
use App\Services\Customer\CustomerService;
use App\Services\Invoice\InvoiceService;
use App\Services\Modules\Elogo\CreateInvoice;
use App\Services\Reason\ReasonService;
use App\Services\Seller\SellerService;
use App\Services\StockCard\StockCardService;
use App\Services\User\UserService;
use App\Services\Version\VersionService;
use App\Services\Warehouse\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EInvoiceController extends Controller
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
    private $createinvoice;


    public function __construct(InvoiceService   $invoiceService,
                                SellerService    $sellerService,
                                WarehouseService $warehouseService,
                                BrandService     $brandService,
                                CategoryService  $categoryService,
                                ColorService     $colorService,
                                VersionService   $versionService,
                                ReasonService    $reasonService,
                                CustomerService  $customerService,
                                UserService      $userService,
                                StockCardService $stockCardService
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
        $this->createinvoice = new CreateInvoice();

    }

    public function create(Request $request)
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['customers'] = $this->customerService->all();
        $data['citys'] = City::all();
        $data['stocks'] = $this->stockCardService->all();
        $data['request'] = $request;
        $data['product'] = $this->stockCardService->filter($request->sell);
        return view('module.einvoice.form', $data);
    }

    public function e_invoice_create(Request $request)
    {

        $customer = Customer::find($request->customer_id);
        $componies = Company::find(Auth::user()->company_id);
        $kdvtutar = 0;
        $geneltoplam = 0;
        $toplamtutar = 0;
        $products = $request->group_a;
        $a = [];
        foreach ($products as $product) {
            $a[] = array(
                'name' => StockCard::find($product['stock_card_id'])->name,
                'quantity' => $product['quantity'],
                'price' => $product['sale_price'],
                'total_price' => $product['sale_price'] - (($product['sale_price'] * $product['tax']) / 100),
                'taxPrice' => ($product['sale_price'] * $product['tax']) / 100,
                'tax' => $product['tax'],
            );
            $kdvtutar += ($product['sale_price'] * $product['tax']) / 100;
            $geneltoplam += $product['sale_price'] + ($product['sale_price'] * $product['tax']) / 100;
            $toplamtutar += $product['sale_price'] - ($product['sale_price'] * $product['tax']) / 100;
        }

        $x = array(
            'Uuid' => Str::uuid(),
            'InvoiceType' => 'IADE',
            'IssueDate' => $request->create_date,
            'InvoiceTotal' => $geneltoplam,
            'SupplierVknTckn' => $customer->tc,
            'SupplierPartyName' => $customer->fullname,
            'CustomerPartyName' => $componies->company_name,
            'CustomerVknTckn' => $componies->tax_number,
            'Description' => $request->description,
            'ProfileID' => 'TEMELFATURA',
            'CurrencyUnit' => 'TRY',
            'TaxAmount' => $kdvtutar,
            'PayableAmount' => $geneltoplam,
            'AllowanceTotalAmount' => 0,
            'TaxInclusiveAmount' => $geneltoplam,
            'TaxExclusiveAmount' => $toplamtutar,
            'LineExtensionAmount' => $toplamtutar,
            'CurrentDate' => $request->create_date,
            'invoiceStatus' => 'Waiting',
        );
        $einvoice = $this->createinvoice->store($x, Auth::user()->company_id, Auth::user()->id, 'IN');
        $this->createinvoice->store_detail($einvoice, $a);
    }
}
