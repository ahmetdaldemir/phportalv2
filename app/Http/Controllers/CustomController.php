<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Customer;
use App\Models\Demand;
use App\Models\Invoice;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\Town;
use App\Models\Version;
use App\Services\Customer\CustomerService;
use App\Services\Seller\SellerService;
use App\Services\StockCard\StockCardService;
use App\Services\Transfer\TransferService;
use Illuminate\Support\Facades\DB;
use App\Helper\SearchHelper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomController extends Controller
{
    private CustomerService $customerService;
    private SellerService $sellerService;
    private StockCardService $stockCardService;
    private TransferService $transferService;

    protected StockCardMovement $stockCardMovement;

    public function __construct(CustomerService $customerService, SellerService $sellerService, StockCardService $stockCardService, TransferService $transferService)
    {
        $this->customerService = $customerService;
        $this->sellerService = $sellerService;
        $this->stockCardService = $stockCardService;
        $this->transferService = $transferService;
        $this->stockCardMovement = new StockCardMovement();
    }

    public function get_cities(Request $request)
    {
        $town = Town::where('city_id', $request->id)->get();
        return response()->json($town, 200);
    }

    public function customerstore(Request $request)
    {
        // Validate required fields
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone1' => 'required|string|max:20',
            'type' => 'nullable|in:account,customer,siteCustomer'
        ]);

        // Determine the type to use for duplicate check
        $typeToCheck = $request->type ?? 'customer';
        
        // Check if customer with same phone1 and type already exists
        $existingCustomer = Customer::where('phone1', $request->phone1)
            ->where('type', $typeToCheck)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        // If updating, exclude the current record from duplicate check
        if (!empty($request->id)) {
            $existingCustomer = Customer::where('phone1', $request->phone1)
                ->where('type', $typeToCheck)
                ->where('company_id', Auth::user()->company_id)
                ->where('id', '!=', $request->id)
                ->first();
        }

        // If duplicate found, return warning with existing customer data
        if ($existingCustomer) {
            // Parse firstname and lastname from fullname
            $nameParts = explode(' ', $existingCustomer->fullname, 2);
            $existingFirstname = $nameParts[0] ?? '';
            $existingLastname = $nameParts[1] ?? '';
            
            return response()->json([
                'success' => false,
                'message' => 'Bu telefon numarası ve tip kombinasyonu zaten kayıtlı!',
                'warning' => true,
                'customer' => $existingCustomer,
                'existing_customer' => [
                    'id' => $existingCustomer->id,
                    'fullname' => $existingCustomer->fullname,
                    'phone1' => $existingCustomer->phone1,
                    'type' => $existingCustomer->type,
                    'firstname' => $existingFirstname,  // Parsed from fullname
                    'lastname' => $existingLastname     // Parsed from fullname
                ]
            ], 409); // 409 Conflict
        }

        // Prepare data for create/update
        // Combine firstname and lastname into fullname (not stored separately)
        $data = array(
            'fullname' => trim($request->firstname . ' ' . $request->lastname),
            'iban' => $request->iban,
            'tc' => $request->tc,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'email' => $request->email,
            'note' => $request->note,
            'seller_id' => $request->seller_id ?? Auth::user()->seller_id, // Use logged in user's seller_id as default
            'type' => $request->type ?? 'customer', // Default to 'customer'
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::id()
        );

        // Only set code for new customers
        if (empty($request->id)) {
            $data['code'] = Str::uuid();
        }

        try {
            if (empty($request->id)) {
                // Create new customer
                $customer = $this->customerService->create($data);

                // Check if customer creation failed
                if (!$customer || is_array($customer)) {
                    throw new \Exception('Müşteri oluşturulamadı. Lütfen bilgileri kontrol edip tekrar deneyin (1).');
                }
                
                $message = 'Müşteri başarıyla oluşturuldu!';
            } else {
                // Update existing customer
                $updateResult = $this->customerService->update($request->id, $data);
                
                // Check if update failed
                if (is_array($updateResult) && empty($updateResult)) {
                    throw new \Exception('Müşteri güncellenemedi. Lütfen bilgileri kontrol edip tekrar deneyin (2).');
                }
                
                $customer = Customer::find($request->id);
                $message = 'Müşteri başarıyla güncellendi!';
            }

            // Final validation
            if (!$customer) {
                throw new \Exception('Müşteri bilgileri alınamadı.');
            }

            // Parse firstname and lastname from fullname for frontend compatibility
            $nameParts = explode(' ', $customer->fullname, 2);
            $firstname = $nameParts[0] ?? '';
            $lastname = $nameParts[1] ?? '';

            return response()->json([
                'success' => true,
                'message' => $message,
                'customer' => $customer,
                'id' => $customer->id,
                'fullname' => $customer->fullname,
                'firstname' => $firstname,  // Parsed from fullname
                'lastname' => $lastname,    // Parsed from fullname
                'phone1' => $customer->phone1,
                'type' => $customer->type
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    public function customerget(Request $request)
    {
        $data = $this->customerService->find($request->id);
        return response()->json($data, 200);
    }

    public function getStock(Request $request)
    {
        return $this->stockCardMovement->quantityCheck($request->serialCode);
    }

    public function get_version(Request $request)
    {
        $version = [];
        if($request->filled('id'))
        {
            $version = Version::where('brand_id', $request->id)->where('company_id', Auth::user()->company_id)->get();
        }
        return $version;
    }

    public function getStockCard(Request $request)
    {
        $data = [];

        $item = StockCardMovement::where('stock_card_id', $request->id)->where('type', 1);
        if (!Auth::user()->hasRole('super-admin') && !Auth::user()->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {
            $item->where('seller_id', Auth::user()->seller_id);
        }

      $x = $item->orderBy('id', 'desc')->first();
        if ($x) {
            $data['status'] = true;
            $data['sales_price'] = $x;
            return response()->json($data, 200);
        }else{
            $data['status'] = false;
            $data['message'] = "Stok Bulunamadı";
            return response()->json($data, 200);
        }
     }


    public function customers(Request $request)
    {
        $array = [];
        if ($request->type == 'technical') {
            $data = Customer::all();

        } else {
            $data = Customer::where('type', $request->type)->get();

        }
        return response()->json($data, 200);
    }

    public function transferList()
    {
        return response()->json($this->transferService->get(), 200);
    }


    public function searchStockCard(Request $request)
    {
        $keyword = $request->search ?? $request->key;
        
        if (!$keyword || strlen($keyword) < 2) {
            return response()->json([]);
        }
        
        $user = Auth::user();
        $stockcard = StockCard::where('company_id', $user->company_id)
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->with(['brand', 'category'])
            ->limit(10)
            ->get();
            
        if (count($stockcard) == 0) {
            $stockcardmovement = StockCardMovement::where('serial_number', 'LIKE', '%' . $keyword . '%')->first();
            if ($stockcardmovement) {
                $stockcard[] = StockCard::with(['brand', 'category'])->find($stockcardmovement->stock_card_id);
            }
        }
        
        $data = [];
        foreach ($stockcard as $item) {
            if ($item) {
                $data[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'brand_name' => $item->brand->name ?? '',
                    'category_name' => $item->category->name ?? '',
                    'version_name' => $this->getVersionName($item->version_id),
                    'quantity' => $this->getStockQuantity($item->id),
                );
            }
        }

        return response()->json($data, 200);

        //  $data = array(
        //      'stockmovementId' => $item->id,
        //      'stockId' => $item->stock_card_id,
        //      'serialNumber' => $item->serial_number,
        //      'quantity' => $quantity,
        //      'salePrice' => $item->sale_price,
        //      'bestCostPrice' => $item->best_cost_price,
        //  );
        //  return response()->json($data,200);
    }

    public function getVersionName($id)
    {
        $x = "";
        foreach ($id as $item) {
            $x .= "<b>" . Version::find($item)->name . "- <b>";
        }
        return $x;
    }


    public function getStockQuantity($id)
    {
        $request = new \Illuminate\Http\Request();

        $request->replace(['id' => $id]);

        return $this->stockCardMovement->quantityCheck($request);
    }


    public function stockSearch(Request $request)
    {
        if ($request->filled('serialNumber')) {
            $stockcardmovement = Sale::where('serial', $request->serialNumber)->first();
            if ($stockcardmovement) {
                return response()->json(['autoredirect' => false, 'id' => $stockcardmovement->stock_card_id, 'serial' => $request->serialNumber,'message' => 'Seri Numarası Bulunamadı'], 200);;
            }
            $stockcardmovementNew = StockCardMovement::where('serial_number', $request->serialNumber)->first();
            if(!$stockcardmovementNew)
            {
                return response()->json(['autoredirect' => false, 'id' => 0, 'serial' => $request->serialNumber,'message' => 'Seri Numarası Bulunamadı'], 200);;
            }
            if ($stockcardmovementNew->type == 3) // Hasarlı Sorgusu
            {
                return response()->json(['autoredirect' => false, 'id' => $stockcardmovement->stock_card_id, 'serial' => $request->serialNumber,'message' => 'Seri Numarası Hasarlı'], 200);;
            }
            if ($stockcardmovementNew->type == 4) // Transfer Sorgusu
            {
                return response()->json(['autoredirect' => false, 'id' => $stockcardmovementNew->stock_card_id, 'serial' => $request->serialNumber,'message' => 'Seri Numarası Transfer Edilmiş'], 200);;
            }
            if ($stockcardmovementNew->type == 2) // Satılmış Sorgusu
            {
                return response()->json(['autoredirect' => false, 'id' => $stockcardmovementNew->stock_card_id, 'serial' => $request->serialNumber,'message' => 'Seri Numarası Satılmış'], 200);;
            }
            if (!Auth::user()->hasRole('super-admin') && $stockcardmovementNew->seller_id != Auth::user()->seller_id) // HAsarlı Sorgusu
            {
                return response()->json(['autoredirect' => false, 'id' => $stockcardmovementNew->stock_card_id, 'serial' => $request->serialNumber,'message' => 'Farklı Bayiye Ait Ürün'], 200);;
            }
            return response()->json(['autoredirect' => true, 'id' => $stockcardmovementNew->stock_card_id, 'serial' => $request->serialNumber,'message' => 'Seri Numarası Satılabilir'], 200);
        }
    }

    public function version($versions)
    {
        if (gettype($versions) == 'array') {
            $json = $versions;

        } else {
            $json = json_decode($versions, TRUE);

        }
        foreach ($json as $item) {
            $version[] = Version::when($item, function ($query) use ($item) {
                $query->where('id', $item);
            })->first()->name ?? "Bulunamadı";
        }

        return $version;
    }

    public function serialcheck(Request $request)
    {

        $searchInfo = SearchHelper::determineSearchType($request->id);

        if ($searchInfo) {
            if ($searchInfo['type'] === 'barcode') {
                $stockcardmovement = StockCardMovement::where('type', 1)->where('barcode', $searchInfo['value'])->first();
            } else {
                $stockcardmovement = StockCardMovement::where('type', 1)->where('serial_number', $searchInfo['value'])->first();
            }
        }

        if ($stockcardmovement) {
            if (!Auth::user()->hasRole('Depo Sorumlusu') && !Auth::user()->hasRole('super-admin')) {
                if ($stockcardmovement->seller_id != Auth::user()->seller_id) {
                    $data['status'] = false;
                    $data['message'] = "Seri numarası sevk edilmeli";
                    return response()->json($data, 200);
                }
            }

            $stockcardmovementCheck = Sale::where('serial', $request->id)->first();
            if ($stockcardmovementCheck) {
                $data['status'] = false;
                $data['message'] = "Seri numarası satılmıştır";
                return response()->json($data, 200);
            } else {
                $data['status'] = true;
                $data['sales_price'] = $stockcardmovement->sale_price;
                $data['stock_card_id'] = $stockcardmovement->stock_card_id;
                $data['base_cost_price'] = $stockcardmovement->base_cost_price;
                $data['serial_number'] = $stockcardmovement->serial_number;
                return response()->json($data, 200);
            }

        }

    }



    public function getStockCardCategory(Request $request)
    {
        $data = [];
        $newArray = array((int)$request->id);
        $ids = Category::where('parent_id', $request->id)->get()->pluck('id')->toArray();
        $categoryids = array_merge($ids, $newArray);

        $stocks = StockCard::whereIn('category_id', $categoryids)->get();
        if ($stocks) {
            foreach ($stocks as $item) {
                $data[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'barcode' => $item->barcode,
                    'category' => Category::find($item->category_id)->name ?? "Bulunamadı",
                    'quantity' => (new \App\Models\StockCard)->quantityId($item->id),
                    'brand' => Brand::find($item->brand_id)->name ?? "Bulunamadı",
                    'version' => $this->version($item->version_id),
                    'cost_price' => $item->stockCardPrice,
                    'base_cost_price' => $item->stockCardPrice,
                    'sale_price' => $item->stockCardPrice,
                );
            }
        }
        return response()->json($data, 200);
    }

    public function quantity($id)
    {
        $in = StockCardMovement::where('stock_card_id', $id)->where('type', 1)->sum('quantity');
        $out = StockCardMovement::where('stock_card_id', $id)->where('type', 2)->sum('quantity');
        $quantity = $in - $out;
        return $quantity;
    }

    public function getStockSeller(Request $request)
    {
        $StockIn = StockCardMovement::with('seller')->where('stock_card_id', $request->id)->where('type', 1)
            ->selectRaw("SUM(quantity) as quantitya,seller_id")
            ->groupBy('seller_id')
            ->get();

        foreach ($StockIn as $item) {
            $data[] = array(
                'sellerName' => $item->seller->name,
                'quantity' => $item->quantitya - $this->SellerQuantityCalculate($item->seller_id, $request->id, $item->quantitya)
            );
        }
        return $data;
    }

    public function SellerQuantityCalculate($id, $stockId, $quantity)
    {
        return StockCardMovement::where('seller_id', $id)->where('stock_card_id', $stockId)->where('type', 2)->sum('quantity');
    }

    public function demandStore(Request $request)
    {
        $demand = new Demand();
        $demand->company_id = Auth::user()->company_id;
        $demand->color_id = $request->color_id;
        $demand->user_id = Auth::user()->id;
        $demand->stock_card_id = $request->id;
        $demand->seller_id = Auth::user()->seller_id;
        $demand->description = $request->description;
        $demand->save();
        return redirect()->back();
    }

    public function demandStatus(Request $request)
    {
        $demand = Demand::find($request->id);
        $demand->status = 1;
        $demand->save();
        return redirect()->back();
    }

    public function demandList()
    {
        $data['demands'] = Demand::where('company_id',Auth::user()->company_id)->get();
        return view('module.demand.index', $data);
    }

    public function custom_editItem(Request $request)
    {
        $request = Invoice::find($request->id);
        $data = $request->detail[(int)$request->key];
        return response()->json($data, 200);
    }

    public function demandPrint()
    {
        $data['demands'] = Demand::where('is_status', 0)->get();
        return view('module.demand.print', $data);
    }

    public function getTransferSerialCheck(Request $request)
    {
        $stockcardmovement = StockCardMovement::where('type', 1)->where('seller_id',$request->seller_id)->where("serial_number", $request->serial_number)->first();
        if(!$stockcardmovement)
        {
            return response()->json('Transfer Edilemez !',200);
        }
        return response()->json('Yes',200);
    }

    public function cost_update()
    {
        $sales = Sale::all();
        foreach ($sales as $sale)
        {
            if($sale->type == 1)
            {
                $scm = Phone::where('imei',$sale->serial)->first();
                if($scm)
                {
                    $sale->base_cost_price = $scm->cost_price;
                    $sale->save();
                }
            }
          // echo $sale->serial.'/n';

          // $scm = StockCardMovement::where('serial_number',$sale->serial)->first();
          //  if($scm)
          //  {
          //      echo $sale->serial.'-'.$scm->base_cost_price.'/n';

          //      $sale->base_cost_price = $scm->base_cost_price;
          //      $sale->save();
          //  }

        }
    }

}
