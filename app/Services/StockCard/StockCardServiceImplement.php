<?php

namespace App\Services\StockCard;

use App\Models\Brand;
use App\Models\Category;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\StockCardPrice;
use App\Models\Version;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SN;
use LaravelEasyRepository\Service;
use App\Repositories\StockCard\StockCardRepository;

class StockCardServiceImplement extends Service implements StockCardService
{

    /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
    protected StockCardRepository $mainRepository;

    public function __construct(StockCardRepository $mainRepository)
    {
        $this->mainRepository = $mainRepository;
    }

    public function all(): ?Collection
    {
        try {
            return $this->mainRepository->all();
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function get(): ?Collection
    {
        try {
            return $this->mainRepository->get();
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }


    public function find($id)
    {
        try {
            return $this->mainRepository->find($id);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function delete($id)
    {
        try {
            return $this->mainRepository->delete($id);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function update($id, $data)
    {
        try {
            return $this->mainRepository->update($id, $data);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function create($data)
    {

        try {
            return $this->mainRepository->create($data);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function add_movement($request, $invoiceID, $type)
    {
        $stockmovents = StockCardMovement::where('invoice_id', $invoiceID->id)->first();
        if ($stockmovents) {
            StockCardMovement::where('invoice_id', $invoiceID->id)->delete();
        }
        foreach ($request as $item) {
            $stockcard = StockCardMovement::where('serial_number', $item['serial'])->where('type', 1)->orderBy('id', 'desc')->first();
            for ($i = 0; $i < $item['quantity']; $i++) {
                $data = array(
                    'stock_card_id' => $item['stock_card_id'],
                    'user_id' => Auth::user()->id,
                    'invoice_id' => $invoiceID->id,
                    'color_id' => $item['color_id'] ?? $stockcard->color_id,
                    'warehouse_id' => $item['warehouse_id'] ?? $stockcard->warehouse_id,
                    'seller_id' => $item['seller_id'] ?? $stockcard->seller_id,
                    'reason_id' => $item['reason_id'],
                    'type' => $type,
                    'quantity' => 1,
                    'imei' => $item['imei'] ?? null,
                    'assigned_accessory' => isset($item['assigned_accessory']) and $item['assigned_accessory'] == 'on' ? 1 : 0,
                    'assigned_device' => isset($item['assigned_device']) and $item['assigned_device'] == 'on' ? 1 : 0,
                    'serial_number' => $item['serial'] ?? SN::generate(),
                    'tax' => $item['tax'] ?? null,
                    'cost_price' => str_replace(",", ".", $item['cost_price'] ?? $stockcard->cost_price),
                    'base_cost_price' => str_replace(",", ".", $item['base_cost_price'] ?? $stockcard->base_cost_price),
                    'sale_price' => str_replace(",", ".", $item['sale_price']),
                    'description' => $item['description'] ?? null,
                    'discount' => $item['discount'] ?? null,
                );
                if (empty($request->id) || isset($request->id)) {
                    StockCardMovement::create($data);
                } else {
                    StockCardMovement::update($request->id, $data);
                }
            }

            $stockcardprice = new StockCardPrice();
            $stockcardprice->company_id = Auth::user()->company_id;
            $stockcardprice->user_id = Auth::user()->id;
            $stockcardprice->stock_card_id = $item['stock_card_id'];
            $stockcardprice->cost_price = str_replace(",", ".", $item['cost_price'] ?? $stockcard->cost_price);
            $stockcardprice->base_cost_price = str_replace(",", ".", $item['base_cost_price'] ?? $stockcard->base_cost_price);
            $stockcardprice->sale_price = str_replace(",", ".", $item['sale_price']);
            $stockcardprice->save();
        }
        return response()->json("Kayıt Başarılı", 200);
    }

    public function add_movementupdate($request, $invoiceID, $type)
    {
        foreach ($request as $item) {
            $x = StockCardMovement::find($item['stockcardmovementid']);
            $x->stock_card_id = $item['stock_card_id'];
            $x->user_id = Auth::user()->id;
            $x->invoice_id = $invoiceID->id;
            $x->color_id = $item['color_id'];
            $x->warehouse_id = $item['warehouse_id'];
            $x->seller_id = $item['seller_id'];
            $x->reason_id = $item['reason_id'];
            $x->type = $type;
            $x->quantity = 1;
            $x->imei = $item['imei'];
            $x->assigned_accessory = isset($item['assigned_accessory']) and $item['assigned_accessory'] == 'on' ? 1 : 0;
            $x->assigned_device = isset($item['assigned_device']) and $item['assigned_device'] == 'on' ? 1 : 0;
            $x->serial_number = $item['serial'];
            $x->tax = $item['tax'];
            $x->cost_price = str_replace(",", ".", $item['cost_price'] ?? $item->cost_price);
            $x->base_cost_price = str_replace(",", ".", $item['base_cost_price'] ?? $item->base_cost_price);
            $x->sale_price = str_replace(",", ".", $item['sale_price']);
            $x->description = $item['description'] ?? null;
            $x->discount = $item['discount'] ?? null;
            $x->save();


            $stockcardprice = new StockCardPrice();
            $stockcardprice->company_id = Auth::user()->company_id;
            $stockcardprice->user_id = Auth::user()->id;
            $stockcardprice->stock_card_id = $item['stock_card_id'];
            $stockcardprice->cost_price = str_replace(",", ".", $item['cost_price'] ?? $item->cost_price);
            $stockcardprice->base_cost_price = str_replace(",", ".", $item['base_cost_price'] ?? $item->base_cost_price);
            $stockcardprice->sale_price = str_replace(",", ".", $item['sale_price']);
            $stockcardprice->save();
        }
        return response()->json("Kayıt Başarılı", 200);
    }


    public function add_movement_sales(array $request, $invoiceID, $type)
    {
        foreach ($request as $item) {
            $stockcard = StockCardMovement::where('serial_number', $item['serial'])->where('type', 1)->orderBy('id', 'desc')->first();
            $data = array(
                'stock_card_id' => $item['stock_card_id'],
                'user_id' => Auth::user()->id,
                'invoice_id' => $invoiceID->id,
                'reason_id' => $item['reason_id'],
                'color_id' => $stockcard->color_id ?? 1,
                'seller_id' => $stockcard->seller_id,
                'warehouse_id' => $stockcard->warehouse_id,
                'assigned_accessory' => $stockcard->assigned_accessory,
                'assigned_device' => $stockcard->assigned_device,
                'type' => $type,
                'quantity' => 1,
                'imei' => $item['imei'] ?? null,
                'serial_number' => $item['serial'] ?? SN::generate(),
                'description' => $item['description'] ?? null,
                'discount' => $item['discount'] ?? null,
                'cost_price' => $stockcard->cost_price,
                'base_cost_price' => $stockcard->base_cost_price,
                'sale_price' => str_replace(",", ".", $item['sale_price']),
            );
            if (empty($request->id) || isset($request->id)) {
                StockCardMovement::create($data);
            } else {
                StockCardMovement::update($request->id, $data);
            }
        }
        return response()->json("Kayıt Başarılı", 200);
    }

    public function add_movement_update(array $request, $invoiceID, $type)
    {
        foreach ($request as $item) {
            $stockcard = StockCardMovement::where('serial_number', $item['serial'])->where('type', 1)->orderBy('id', 'desc')->first();
            $stockmovementupdate = StockCardMovement::find($item['stockcardmovementid']);
            $stockmovementupdate->stock_card_id = $item['stock_card_id'];
            $stockmovementupdate->user_id = Auth::user()->id;
            $stockmovementupdate->invoice_id = $invoiceID->id;
            $stockmovementupdate->reason_id = $item['reason_id'];
            $stockmovementupdate->color_id = $stockcard->color_id ?? 1;
            $stockmovementupdate->seller_id = $stockcard->seller_id;
            $stockmovementupdate->warehouse_id = $stockcard->warehouse_id;
            $stockmovementupdate->type = $type;
            $stockmovementupdate->quantity = 1;
            $stockmovementupdate->assigned_accessory = $stockcard->assigned_accessory;
            $stockmovementupdate->assigned_device = $stockcard->assigned_device;
            $stockmovementupdate->imei = $item['imei'] ?? null;
            $stockmovementupdate->serial_number = $item['serial'] ?? SN::generate();
            $stockmovementupdate->description = $item['description'] ?? null;
            $stockmovementupdate->discount = $item['discount'] ?? null;
            $stockmovementupdate->cost_price = $stockcard->cost_price;
            $stockmovementupdate->base_cost_price = $stockcard->base_cost_price;
            $stockmovementupdate->sale_price = str_replace(",", ".", $item['sale_price']);
            $stockmovementupdate->save();

        }
        return response()->json("Kayıt Başarılı", 200);
    }

    public function filter($arg)
    {
        return $this->mainRepository->filter($arg);
    }

    public function getInvoiceForSerial($arg)
    {
        return $this->mainRepository->getInvoiceForSerial($arg);
    }


    public function getStockData($arg)
    {
        return $this->mainRepository->getStockData($arg);
    }

    public function stockSearch($request)
    {
        $data = [];
        $ssss = [];
        $stock =  StockCard::whereNull('deleted_at');
         if ($request->filled('stockName')) {
            $stock->where('name', 'like', '%' . $request->stockName . '%');
        }
        if ($request->filled('category')) {
            $categorylist = Category::where('parent_id', $request->category)->pluck('id')->toArray();
            $categorylist[] = $request->category;
            $stock->whereIn('category_id', $categorylist);
        }

        if ($request->filled('brand')) {
            $stock->where('brand_id', $request->brand);
        }

        if ($request->filled('version')) {
            $stock = $stock->whereJsonContains('version_id', $request->version);
        }

        // $stocks =  array_merge((array)$stock->get(), (array)$stock1->get());
        $stocks = $stock->groupBy('category_id')->orderBy('id', 'desc')->paginate(20);

        $data['stockLink'] = $stocks->links();

        foreach ($stocks as $stock) {

            $ssss[] = array(
                'id' => $stock->id,
                'name' => $stock->name,
                'brand' => Brand::find($stock->brand_id)->name ?? "Bulunamadı",
                'version' => $this->version($stock->version_id),
                'category_sperator_name' => $this->categorySeperator($this->testParent($stock->category_id)),
                'category' => Category::find($stock->category_id)->name ?? "Bulunamadı",
                'stockData' => $this->stockData($stock->category_id)

            );
        }
        $data['stockList'] = $ssss;
        return $data;
    }

    public function categorySeperator($data)
    {
        if (!empty($data)) {
            return implode("/", $this->array_column_recursive($data, 'name')) . " /";
        }
        return "";
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

    public function testParent($category_id = 0)
    {
        $data = null;

        $x = Category::find($category_id);
        if ($x) {
            $categories = Category::where('id', $x->parent_id)->get();
//dd($categories);
            foreach ($categories as $category) {
                $data[] = [
                    'id' => $category->id,
                    'list' => $this->testParent($category->id),
                    'name' => $category->name
                ];
            }
        }


        return $data;
    }

    public function stockData($name)
    {
        $stocks = DB::table('stock_cards')->where('category_id', $name)->whereNull('deleted_at')->get();
        foreach ($stocks as $item) {
            $data[] = array(
                'test' => $item->version_id,
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'is_status' => $item->is_status,
                'barcode' => $item->barcode,
                'category_sperator_name' => $this->categorySeperator($this->testParent($item->category_id)),
                'category' => Category::find($item->category_id)->name ?? "Bulunamadı",
                'quantity' => (new \App\Models\StockCard)->quantityId($item->id),
                'brand' => Brand::find($item->brand_id)->name ?? "Bulunamadı",
                'version' => $this->version($item->version_id),
                'cost_price' => StockCard::getStockCardPrice($item->id)->cost_price ?? 0,
                'base_cost_price' => StockCard::getStockCardPrice($item->id)->base_cost_price ?? 0,
                'sale_price' => StockCard::getStockCardPrice($item->id)->sale_price ?? 0,
            );
        }
        return $data;
    }

    public function category($categories)
    {
        $category = [];
        if (gettype($categories) == 'array') {
            $json = $categories;
        } else {
            $json = json_decode($categories, TRUE);
        }

        foreach ($json as $item) {
            $category[] = Category::find($item)->name ?? "Bulunamadı";
        }
        return implode(",", $category);
    }

    public function version($versions)
    {

        if(gettype($versions) != 'array')
        {
            $json = json_decode($versions, TRUE);
        }else{
            $json = $versions;
        }


        foreach ($json as $item) {
            $version[] = Version::when($item, function ($query) use ($item) {
                $query->where('id', $item);
            })->first()->name ?? "Bulunamadı";
        }

        return $version;
    }
}
