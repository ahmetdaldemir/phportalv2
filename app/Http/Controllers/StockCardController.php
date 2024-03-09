<?php

namespace App\Http\Controllers;

use App\Enums\Unit;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Invoice;
use App\Models\Refund;
use App\Models\Seller;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\StockCardPrice;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Version;
use App\Services\Brand\BrandService;
use App\Services\Category\CategoryService;
use App\Services\Color\ColorService;
use App\Services\FakeProduct\FakeProductService;
use App\Services\Invoice\InvoiceService;
use App\Services\Reason\ReasonService;
use App\Services\Refund\RefundService;
use App\Services\Seller\SellerService;
use App\Services\StockCard\StockCardService;
use App\Services\Transfer\TransferService;
use App\Services\Version\VersionService;
use App\Services\Warehouse\WarehouseService;
use App\Traits\NotifiesOnDelete;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StockCardController extends Controller
{

    private StockCardService $stockcardService;
    private SellerService $sellerService;
    private WarehouseService $warehouseService;
    private BrandService $brandService;
    private CategoryService $categoryService;
    private ColorService $colorService;
    private VersionService $versionService;
    private ReasonService $reasonService;
    private FakeProductService $fakeProductService;
    private TransferService $transferService;
    private InvoiceService $invoiceService;

    protected $a;
    protected $x;


    public function __construct(StockCardService   $stockcardService,
                                SellerService      $sellerService,
                                WarehouseService   $warehouseService,
                                BrandService       $brandService,
                                CategoryService    $categoryService,
                                ColorService       $colorService,
                                VersionService     $versionService,
                                ReasonService      $reasonService,
                                FakeProductService $fakeProductService,
                                TransferService    $transferService,
                                RefundService      $refundService,
                                InvoiceService     $invoiceService
    )
    {
        $this->stockcardService = $stockcardService;
        $this->refundService = $refundService;
        $this->sellerService = $sellerService;
        $this->warehouseService = $warehouseService;
        $this->brandService = $brandService;
        $this->versionService = $versionService;
        $this->colorService = $colorService;
        $this->categoryService = $categoryService;
        $this->reasonService = $reasonService;
        $this->fakeProductService = $fakeProductService;
        $this->transferService = $transferService;
        $this->invoiceService = $invoiceService;
        $this->a = [];
        $this->x = [];
        $this->message = '';


    }

    protected function index(Request $request)
    {
        $data = [];
        $stockcards = $this->stockcardService->stockSearch($request);
        $data['stockcards'] = $stockcards['stockList'];
        /* if ($stockcards) {
             $data['stockcards'] = $stockcards;
         } else {
             $stockcards = StockCard::where('company_id',Auth::user()->company_id)->get();
             foreach ($stockcards as $item) {
                 $data[] = array(
                     'test' => $item->version_id,
                     'id' => $item->id,
                     'name' => $item->name,
                     'sku' => $item->sku,
                     'barcode' => $item->barcode,
                     'is_status' => $item->is_status,
                     'category' => $this->category($item->category_id) ?? "Bulunamadı",
                     'quantity' => (new \App\Models\StockCard)->quantityId($item->id),
                     'brand' => Brand::find($item->brand_id)->name ?? "Bulunamadı",
                     'version' => $this->version($item->version_id) ?? [],
                     'cost_price' => $item->stockCardPrice->cost_price,
                     'base_cost_price' => $item->stockCardPrice->cost_price,
                     'sale_price' => $item->stockCardPrice->cost_price,
                 );
             }
             $data['stockcards'] = $data;
         } */
        $data['sellers'] = $this->sellerService->get();
        $data['brands'] = $this->brandService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['categories'] = $this->getCategoryPathList();
        $data['links'] = $stockcards['stockLink'];

        return view('module.stockcard.index', $data);
    }

    public function category($categories)
    {
        if (gettype($categories) == 'array') {
            $json = $categories;
        } else {
            $json = json_decode($categories, TRUE);
        }
        foreach ($json as $item) {
            $category[] = Category::when($item, function ($query) use ($item) {
                $query->where('id', $item);
            })->first()->name ?? "Bulunamadı";
        }


        return $category;
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

    protected function create(Request $request)
    {
        $this->authorize('create-accessory');

        $data['brands'] = $this->brandService->get();
        $data['versions'] = $this->versionService->get();
        //$categories = $this->categoryService->getAllParentsL();
        $categories =
            DB::select("WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and company_id = " . Auth::user()->company_id . " and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        //dd($categories);
        //  $a = $this->categoryService->getList($request->category);
        $data['categories'] = $categories;//$this->listenke($categories, $request->category);
        $data['fakeproducts'] = StockCard::select('name')->distinct()->get();
        $data['units'] = Unit::Unit()->value;
        $data['request'] = $request;


        return view('module.stockcard.form', $data);
    }

    public function get_category_select($parent_id = 2, $separator = '&nbsp;&nbsp;', $alt = 0)
    {
        $out = [];
        $new_alt = $alt;
        foreach ($this->get_category_parent($parent_id) as $row) {
            if ($new_alt >= $alt) {
                $out[] = $row->name;
                $new_alt = $alt + 1;

                $this->get_category_select($row->id, $separator, $new_alt);
            } else {
                $this->get_category_select($row->id, $separator, $alt + 1);
            }

        }
        return $out;
    }

    public function get_category_parent($parent_id)
    {
        return Category::where('parent_id', $parent_id)->get();
    }

    protected function listenke($array, $parent, $derinlik = -1, &$html = array())
    {
        ++$derinlik;
        foreach ($array as $row) {
            if ($row->parent_id == $parent) {
                $html[$row->id] = str_repeat('--', $derinlik) . $row->name;
                $this->listenke($array, $row->id, $derinlik, $html);
            }
        }
        return $html;
    }

    protected function edit(Request $request)
    {
        $this->authorize('create-accessory');

        $data['stockcards'] = $this->stockcardService->find($request->id);
        $data['sellers'] = $this->sellerService->get();
        $data['brands'] = $this->brandService->get();
        $data['versions'] = $this->versionService->get();
        //$categories = $this->categoryService->getAllParentsL();
        $categories =
            DB::select("WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and company_id = " . Auth::user()->company_id . " and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        //dd($categories);
        //  $a = $this->categoryService->getList($request->category);
        $data['categories'] = $categories;//$this->listenke($categories, $request->category);
        $data['fakeproducts'] = $this->fakeProductService->get();
        $data['units'] = Unit::Unit()->value;
        return view('module.stockcard.form', $data);
    }

    protected function movement(Request $request)
    {
        $data['warehouses'] = $this->warehouseService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['stock_card_id'] = $request->id;
        $data['movements'] = StockCardMovement::where('stock_card_id', $request->id)->get();
        return view('module.stockcard.movement', $data);
    }

    protected function movementdelete(Request $request)
    {

        $stoc = new StockCardMovement();
        $stoc->addMessage($request->note);

        $user = StockCardMovement::find($request->stock_card_movement_id);
        $user->delete();

        return redirect()->back();
    }

    protected function barcode(Request $request)
    {
        $data = $this->stockData($request->ids[0]);
        return view('module.stockcard.barcode', compact('data'));
    }

    protected function barcodes(Request $request)
    {

        $y = StockCardMovement::whereIn('id', $request->selected)->get();

        $data = [];
        foreach ($y as $item) {
            $vesions = '';
            foreach ($item->stock->version_id as $key) {
                $vesions .= \App\Models\Version::find($key)->name ?? "Bulunamadı" . "</br>";

            }
            $data[] = array('serial_number' => $item->serial_number,
                'id' => $item->id,
                'stock_name' => $item->stock->name,
                'category_sperator_name' => $this->categorySeperator($item->testParent($item->stock->category->id)),
                'category_name' => $item->stock->category->name,
                'brand_name' => $item->stock->brand->name,
                'sale_price' => number_format($item->sale_price ?? 0, 2),
                'color_name' => $item->color->name,
                'versions' => $vesions,
                'assigned_device' => $item->assigned_device == 1 ? 'Temlikli Cihaz' : '',
                'assigned_accessory' => $item->assigned_accessory == 1 ? 'Temlikli Aksesuar' : '',
                'seller_name' => $item->seller->name,
            );
        }

        return view('module.stockcard.barcode', compact('data'));
    }

    protected function sevk(Request $request)
    {
        if ($request->type != 'phone') {
            $serial_stock_card_movement = StockCardMovement::where('serial_number', $request->serial_number)->first();

            if (is_null($serial_stock_card_movement) || $serial_stock_card_movement->quantityCheck($request->serial_number) <= 0) {
                return response()->json("Seri Numarası Bulunamadı Veya Stok Yetersiz", 400);
            }

            $transfer = Transfer::whereJsonContains('serial_list', $request->serial_number)->whereNull('comfirm_id')->whereNull('comfirm_date')->first();
            if ($transfer) {
                return response()->json("Transferi kabul edilmemiş", 400);
            }
        }


        $stock = ['stock_card_id' => $request->stock_card_id];
        $serialList[$request->stock_card_id] = array($request->serial_number);

        $data = array(
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::user()->id,
            'is_status' => 1,
            'main_seller_id' => Auth::user()->seller_id,
            'delivery_id' => User::where('seller_id', $request->seller_id)->first()->id ?? 1,
            'description' => $request->description,
            'number' => $request->number ?? rand(333333, 999999),
            'stocks' => $request->serial_number,
            'type' => $request->type,
            'serial_list' => $request->serial_number,
            'delivery_seller_id' => $request->seller_id,
            'reason_id' => $request->reason_id,

        );

        $transfer = $this->transferService->create($data);

        return response()->json($transfer, 200);

    }

    protected function delete(Request $request)
    {
        $stoc = new StockCardMovement();
        $stoc->addMessage($request->note);

        $stocs = new StockCard();
        $stocs->addMessage($request->note);

        $this->stockcardService->delete($request->stock_card_id);
        $stockcardmovements = StockCardMovement::where('stock_card_id', $request->stock_card_id)->get();
        foreach ($stockcardmovements as $stockcardmovement) {
            $stockcardmovement->delete();
        }
        return redirect()->back();
    }

    protected function store(Request $request)
    {
        $this->authorize('create-accessory');

        if (is_null($request->name)) {
            $name = $request->fakeproduct;
        } else {
            $name = $request->name;
        }
        $data = array(
            'name' => $name,
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::user()->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'version_id' => $request->version_id,
            'sku' => "PH" . rand(0001, 9999) . date('Y'),
            'barcode' => "PH" . rand(0001, 9999) . date('Y'),
            'tracking' => $request->tracking == 'on' ? '1' : '0',
            'unit' => $request->unit_id,
            'tracking_quantity' => $request->tracking_quantity,
            'is_status' => 1,
        );

        if (empty($request->id)) {
            $stock = $this->stockcardService->create($data);
            $id = $stock->id;
        } else {
            $this->stockcardService->update($request->id, $data);
            $id = $request->id;
        }

        return redirect()->route('invoice.create', ['id' => $id]);
    }

    protected function update(Request $request)
    {
        $data = array('is_status' => $request->is_status);
        return $this->stockcardService->update($request->id, $data);
    }

    protected function show(Request $request)
    {
        $data['stockcardsmovement'] = StockCardMovement::where('stock_card_id', $request->id)->get();
        return view('module.stockcard.show', $data);
    }

    public function walk_recursive_remove(array $array, callable $callback)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = $this->walk_recursive_remove($v, $callback);
            } else {
                if ($callback($v, $k)) {
                    unset($array[$k]);
                }
            }
        }
        return $array;
    }

    public function array_column_recursive(array $haystack, $needle)
    {
        $found = [];
        array_walk_recursive($haystack, function ($value, $key) use (&$found, $needle) {
            if ($key == $needle)
                $found[] = $value;
        });
        return $found;
    }

    public function list(Request $request)
    {

         $this->NewSerialNumber = 'undefined';
         $this->colorCode       = 'undefined';
         $this->sellerCode      = 'undefined';

        if($request->filled('page'))
        {
            $cashdata = 'list_'.$request->category_id.'_'.$request->page;
        }else{
            $cashdata = 'list_'.$request->category_id;
        }


        $x = [];
        $xxxx = $this->categoryService->getAllParentList($request->category_id);
        $ids = $this->array_column_recursive($xxxx, 'id');
        array_push($ids, $request->category_id);

        $stockcardsList = StockCard::whereIn('category_id', $ids);

        if ($request->filled('brand')) {
            $stockcardsList->where('brand_id', $request->brand);
        }

        if ($request->filled('category')) {
            $stockcardsList->where('category_id', $request->category);
        }

        if ($request->filled('version')) {
            $stockcardsList->whereJsonContains('version_id', $request->version);
        }

        if ($request->filled('stockName')) {

            $stockcardsList->where('name', 'like', '%' . $request->stockName);
        }
        if ($request->filled('serialNumber') || $request->filled('seller') || $request->filled('color')) {
              $this->NewSerialNumber  = ($request->serialNumber != ''?$request->serialNumber : 'undefined');
              $this->colorCode        =  ($request->color != '' ? $request->color : 'undefined');
              $this->sellerCode       = ($request->seller != '' ? $request->seller : 'undefined');

            $stocklist = $stockcardsList->get();
            $abc = $stocklist->toArray();
            $stocklistasd = array_group_by($abc, "name", "category_id", "brand_id");
            $links = 1;
        } else {
            $stocklist = $stockcardsList->orderBy('name', 'asc')->paginate(100);
            $abc = $stocklist->toArray();
            $stocklistasd = array_group_by($abc['data'], "name", "category_id", "brand_id");
            $links = $stocklist->appends(['category_id' => $request->category_id])->links();

            $this->NewSerialNumber = 'undefined';
            $this->colorCode       = 'undefined';
            $this->sellerCode      = 'undefined';
        }

        foreach ($stocklistasd as $aaaa => $values) {
            $ad1 = array_keys($values);
            $ad2 = array_keys($values['' . $ad1[0] . '']);
            $content = $values['' . $ad1[0] . '']['' . $ad2[0] . ''];
            $stockCardIds = $this->array_column_recursive($content, 'id');
            if ($stockCardIds == 324) {
                dd("d");
            }
            $x[] = array(
                'id' => rand(0, 45353),
                'ids' => array($stockCardIds),
                'stock_name' => $aaaa,
                'category_sperator_name' => $this->categorySeperator($this->testParent($ad1[0])),
                'category_name' => Category::find($ad1[0])->name,
                'brand_name' => Brand::find($ad2[0])->name,
                'stockData' => $this->getStockquantity($stockCardIds, $request->serialNumber, $request->seller, $request->color),
            );
        }

        /* if(!Cache::has($cashdata.'_data') && !Cache::has($cashdata.'_link'))
            {
                foreach ($stocklistasd as $aaaa => $values) {
                    $ad1 = array_keys($values);
                    $ad2 = array_keys($values['' . $ad1[0] . '']);
                    $content = $values['' . $ad1[0] . '']['' . $ad2[0] . ''];
                    $stockCardIds = $this->array_column_recursive($content, 'id');
                    if ($stockCardIds == 324) {
                        dd("d");
                    }
                    $x[] = array(
                        'id' => rand(0, 45353),
                        'ids' => array($stockCardIds),
                        'stock_name' => $aaaa,
                        'category_sperator_name' => $this->categorySeperator($this->testParent($ad1[0])),
                        'category_name' => Category::find($ad1[0])->name,
                        'brand_name' => Brand::find($ad2[0])->name,
                       // 'stockData' => $this->stockData($stockCardIds, $request->serialNumber, $request->seller, $request->color),
                    );
                }

                $putData = [$cashdata.'_data' => $x];
                Cache::putMany($putData,5);

                if(is_object($links))
                {
                    $putLink = $links->render();

                }else{
                    $putLink =$links;
                }

                Cache::put($cashdata.'_link',$putLink,5);
                $myData = Cache::many([$cashdata.'_data']);
                $myLink = Cache::get($cashdata.'_link');
            }else{
                $myData = Cache::many([$cashdata.'_data']);
                $myLink = Cache::get($cashdata.'_link');
            } */
        $data['stockcards'] = $x;
        $data['links'] = $links;

        /**/
        //$data['stockcards'] = $t->groupBy('serial_number')->having(DB::raw('count(serial_number)'), 1)->orderBy('id', 'desc')->get();
        $data['category'] = $request->category_id;
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] =  $this->colorService->get();
        $data['brands'] =  $this->brandService->get();

        $data['serialNumber'] = $this->NewSerialNumber;
        $data['colorCode'] =    $this->colorCode;
        $data['sellerCode'] =   $this->sellerCode;

        // $data['categories'] = $this->categoryService->get();
        $categories = DB::select("WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and company_id = " . Auth::user()->company_id . " and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        //dd($categories);
        //  $a = $this->categoryService->getList($request->category);
        $data['categories'] = $categories;//$this->listenke($categories, $request->category);
        //dd($data['stockcards']);
        return view('module.stockcard.list', $data);

    }

    public function testParent($category_id = 0)
    {
        $x = Category::find($category_id);

        $data = null;
        $categories = Category::where('id', $x->parent_id)->get();
//dd($categories);
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'list' => $this->testParent($category->id),
                'name' => $category->name
            ];
        }
        return $data;
    }

    public function stockData($id, ...$arg)
    {
        $y = StockCardMovement::whereIn('stock_card_id', $id)->where('company_id', Auth::user()->company_id)->whereIn('type', [1, 3, 4]);

        if (isset($arg[0])) {

            $y->where('serial_number', $arg[0]);
        }else{
            if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
            {

                if (isset($arg[1]) && $arg[1] != 'all') {
                    $y->where('seller_id', $arg[1]);
                }

            } else {
                if (isset($arg[1]) and $arg[1] != 'all') {
                    $y->where('seller_id', Auth::user()->seller_id);
                }

                if (!isset($arg[1])) {
                    $y->where('seller_id', Auth::user()->seller_id);
                }
            }

            if (isset($arg[2])) {

                $y->where('color_id', $arg[2]);
            }
        }

        $a = $y->get();

        $data = [];
        foreach ($a as $item) {
            $vesions = [];
            foreach ($item->stock->version_id as $key) {
                $vesions[] = \App\Models\Version::find($key)->name ?? "Bulunamadı";
            }
            $data[] = array('serial_number' => $item->serial_number,
                'test' => $item->stock->version_id,
                'id' => $item->id,
                'stock_name' => $item->stock->name,
                'category_name' => $item->stock->category->name,
                'category_sperator_name' => $this->categorySeperator($item->testParent($item->stock->category->id)),
                'brand_name' => $item->stock->brand->name,
                'sale_price' => number_format($item->sale_price ?? 0, 2),
                'cost_price' => number_format($item->cost_price ?? 0, 2),
                'base_cost_price' => number_format($item->base_cost_price ?? 0, 2),
                'color_name' => $item->color->name,
                'color_id' => $item->color->id,
                'versions' => implode("/", $vesions),
                'assigned_device' => $item->assigned_device == 1 ? 'Temlikli Cihaz' : '',
                'assigned_accessory' => $item->assigned_accessory == 1 ? 'Temlikli Aksesuar' : '',
                'seller_name' => $item->seller->name,
                'quantity' => $item->quantityCheckDataNew(),
                'type' => $item->type,
            );
        }

        return $this->sortByField($data, 'quantity', SORT_DESC);
    }

    public function sortByField($multArray, $sortField, $desc = true)
    {
        $tmpKey = '';
        $ResArray = array();

        $maIndex = array_keys($multArray);
        $maSize = count($multArray) - 1;

        for ($i = 0; $i < $maSize; $i++) {

            $minElement = $i;
            $tempMin = $multArray[$maIndex[$i]][$sortField];
            $tmpKey = $maIndex[$i];

            for ($j = $i + 1; $j <= $maSize; $j++)
                if ($multArray[$maIndex[$j]][$sortField] < $tempMin) {
                    $minElement = $j;
                    $tmpKey = $maIndex[$j];
                    $tempMin = $multArray[$maIndex[$j]][$sortField];

                }
            $maIndex[$minElement] = $maIndex[$i];
            $maIndex[$i] = $tmpKey;
        }

        if ($desc)
            for ($j = 0; $j <= $maSize; $j++)
                $ResArray[$maIndex[$j]] = $multArray[$maIndex[$j]];
        else
            for ($j = $maSize; $j >= 0; $j--)
                $ResArray[$maIndex[$j]] = $multArray[$maIndex[$j]];

        return $ResArray;
    }

    public function categorySeperator($data)
    {
        if (!empty($data)) {
            return implode("/", $this->array_column_recursive($data, 'name')) . " /";
        }
        return "";
    }

    public function priceupdate(Request $request)
    {
        $stockcardMovement = StockCardMovement::where('stock_card_id', (int)$request->stock_card_id)->first();
        if ($stockcardMovement->base_cost_price > $request->sale_price) {
            return response()->json('Destekli fiyattan küçük olamaz', 200);
        }
        $stockcardprice = new StockCardPrice();
        $stockcardprice->stock_card_id = $request->stock_card_id;
        $stockcardprice->user_id = Auth::id();
        $stockcardprice->company_id = Auth::user()->company_id;
        $stockcardprice->cost_price = $stockcardMovement->cost_price;
        $stockcardprice->base_cost_price = $stockcardMovement->base_cost_price;
        $stockcardprice->sale_price = $request->sale_price;
        $stockcardprice->save();

        $stockcardmovement = StockCardMovement::where('stock_card_id', $request->stock_card_id)->where('type', 1)->get();
        foreach ($stockcardmovement as $item) {
            $item->sale_price = $request->sale_price;
            $item->save();
        }
        return response()->json('Kayıt Güncellendi', 200);
    }

    function sanitize($price) {
        return floatval(preg_replace('/[^0-9.]/', '', $price));
    }


    public function singlepriceupdate(Request $request)
    {
        $stockcardmovement = StockCardMovement::where('id', $request->stock_card_id)->where('type', 1)->first();

        if ($this->sanitize($stockcardmovement->base_cost_price) > $this->sanitize($request->sale_price)) {
            return response()->json('Satış Fiyatı maliyetten küçük olamaz', 200);
        }

        $stockcardmovement->sale_price = $request->sale_price;
        $stockcardmovement->save();
        return response()->json('Kayıt Güncellendi', 200);
    }


    public function multiplepriceupdate(Request $request)
    {
        $ids = explode(",", $request->stock_card_id_multiple);
        foreach ($ids as $item) {
            $stockcardmovement = StockCardMovement::find($item);

            if ($this->sanitize($stockcardmovement->base_cost_price) > $this->sanitize($request->sale_price)) {

                return response()->json('Satış Fiyatı maliyetten küçük olamaz', 200);
            }
            if($request->filled('base_cost_price'))
            {
                $stockcardmovement->base_cost_price = $request->base_cost_price;
            }
            if($request->filled('cost_price'))
            {
                $stockcardmovement->cost_price = $request->cost_price;
            }
            if($request->filled('sale_price'))
            {
                $stockcardmovement->sale_price = $request->sale_price;
            }
            $stockcardmovement->save();
        }
        return response()->json('Kayıt Güncellendi', 200);
    }

    public function multiplesaleupdate(Request $request)
    {
        $ids = explode(",", $request->stock_card_id_multiple);
        foreach ($ids as $item) {
            $stockcardmovement = StockCardMovement::find($item);

            if ($this->sanitize($stockcardmovement->base_cost_price) > $this->sanitize($request->sale_price)) {

                return response()->json('Satış Fiyatı maliyetten küçük olamaz', 200);
            }
            $stockcardmovement->sale_price = $request->sale_price;
            $stockcardmovement->save();
        }
        return response()->json('Kayıt Güncellendi', 200);
    }

    public function singleserialprint(Request $request)
    {
        $movements = StockCardMovement::find($request->id);


        $data[] = array(
            'title' => 'Barkod',
            'id' => $movements->id,
            'serial_number' => $movements->serial_number,
            'sale_price' => $movements->sale_price,
            'brand_name' => $movements->stockcard()->brand->name,
            'name' => $movements->stockcard()->name,
            'version' => $this->getVersionMap($movements->stockcard()->version()),
        );


        $pdf = PDF::loadView('module.stockcard.print', ['data' => $data]);
        return $pdf->stream('codesolutionstuff.pdf');

    }

    public function getVersionMap($map)
    {
        $datas = json_decode($map, TRUE);
        foreach ($datas as $mykey => $myValue) {
            return "$myValue,";
        }
    }

    public function refund(Request $request)
    {

        $stockcard_id = $request->stock_id;
        if ($request->filled('serial_number')) {
            $stockcardmovemet = StockCardMovement::where('serial_number', $request->serial_number)->where('seller_id', Auth::user()->seller_id)->first();
            if (!$stockcardmovemet) {
                return response()->json('Stock Bulunamadı', 400);
            }
            $stockcard_id = $stockcardmovemet->stock_card_id;
        }

        $refund = new Refund();
        $refund->stock_card_id = $stockcard_id;
        $refund->company_id = Auth::user()->company_id;
        $refund->seller_id = Auth::user()->seller_id;
        $refund->user_id = Auth::user()->id;
        $refund->color_id = $request->color_id;
        $refund->reason_id = $request->reason_id;
        $refund->serial_number = $request->serial_number;
        $refund->description = $request->description;
        $refund->save();
    }

    public function refundlist(Request $request)
    {
        $refunds = collect($this->refundService->get());

        $x = Refund::where('company_id', Auth::user()->company_id);

        if ($request->filled('brand')) {
            $x->whereHas('stock', function ($q) use ($request) {
                $q->where('brand_id', $request->brand);
            });
        }

        if ($request->filled('version')) {
            $x->whereHas('version', function ($q) use ($request) {
                $q->whereJsonContains('version_id', $request->version);
            });
        }

        if ($request->filled('color')) {
            $x->where('color_id', $request->color);
        }

        if ($request->filled('seller')) {
            $x->where('seller_id', $request->seller);
        }
        if ($request->filled('reason')) {
            $x->where('reason_id', $request->reason);
        }

        if ($request->filled('serial_number')) {
            $x->where('serial_number', $request->serial_number);
        }

        $data['refunds'] = $x->get();
        $data['brands'] = $this->brandService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['stocks'] = $this->stockcardService->all();

        return view('module.refund.index', $data);
    }

    public function refundcomfirm(Request $request)
    {
        $refund = Refund::find($request->id);
        if ($request->type == 'service_send') {
            $refund->service_send_date = Carbon::now()->format('Y-m-d H:i:s');
            $refund->status = 5;
            $refund->save();
        }

        if ($request->type == 'service_return') {
            $refund->service_return_date = Carbon::now()->format('Y-m-d H:i:s');
            $refund->status = 6;
            $refund->save();
        }

        if ($request->type == 'refund') {
            $refund->status = 3;
            $refund->save();

            $stockcardmovement = StockCardMovement::where('serial_number', $refund->serial_number)->first();
            $stockcardmovement->type = 3;
            $stockcardmovement->save();
        }
        if ($request->type == 'delivered') {
            $refund->status = 4; // Teslim Edildi
            $refund->save();
        }

        return redirect()->back();
    }

    public function refundreturn(Request $request)
    {
        $refund = Refund::find($request->id);

        $stockcardmovement = "";
        if (!empty($refund->serial_number)) {
            $stockcardmovement = StockCardMovement::where('serial_number', $refund->serial_number)->where('type', 1)->first();
        }

        $data = array(
            'type' => 1,
            'number' => null,
            'create_date' => Carbon::now()->format('Y-m-d'),
            'credit_card' => 0,
            'cash' => 0,
            'installment' => 0,
            'description' => "İADE DÖNÜŞ",
            'is_status' => 1,
            'total_price' => 0,
            'tax_total' => 0,
            'discount_total' => 0,
            'staff_id' => Auth::user()->id,
            'customer_id' => 1,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
            'exchange' => null,
            'tax' => null,
            'file' => null,
            'paymentStatus' => 'paid',
            'paymentDate' => null,
            'paymentStaff' => null,
            'periodMounth' => null,
            'periodYear' => null,
            'accounting_category_id' => 8,
            'currency' => null,
            'safe_id' => null,
        );
        $invoiceID = $this->invoiceService->create($data);
        return redirect()->route('invoice.stockcardmovementformrefund', ['id' => $invoiceID->id, 'refund_id' => $request->id]);
    }

    public function category_id(Request $request)
    {
        $category = Category::where('parent_id', $request->id)->get();
        return response()->json($category, 200);
    }

    public function refunddetail(Request $request)
    {
        return Refund::find($request->id);
    }

    public function refunddetailStore(Request $request)
    {
        $refund = Refund::find($request->id);
        $refund->description = $request->description;
        $refund->save();

        return $refund;
    }

    public function newSale(Request $request)
    {
        $refund = Refund::find($request->id);
        $stockcard = StockCard::find($refund->stock_card_id);
        if (!$stockcard) {
            return response()->json(['data' => 'ürün Buluanmadı', 'status' => false], 200);
        }
        if ($refund->serial_number) {
            $stock_card_movement = StockCardMovement::where('serial_number', $refund->serial_number)->first();
            if ($stock_card_movement) {
                return response()->json(['data' => $stock_card_movement, 'status' => true], 200);
            }
            $stock_card_movement = StockCardMovement::where('stock_card_id', $refund->stock_card_id)->orderBy('id', 'desc')->first();
            if ($stock_card_movement) {
                return response()->json(['data' => $stock_card_movement, 'status' => true], 200);
            }
        }
        return response()->json(['data' => 'Ürün Fiyatını Belirleyiniz', 'status' => false], 200);
    }

    public function newSaleStore(Request $request)
    {
        $refund = Refund::find($request->id);

        $data = array(
            'type' => 1,
            'number' => "IN" . rand(1111, 9999) . date("m"),
            'create_date' => Carbon::now()->format('Y-m-d'),
            'credit_card' => 0,
            'cash' => 0,
            'installment' => 0,
            'description' => "İADE DÖNÜŞ",
            'is_status' => 1,
            'total_price' => 0,
            'tax_total' => 0,
            'discount_total' => 0,
            'staff_id' => Auth::user()->id,
            'customer_id' => 1,
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->company_id,
            'exchange' => null,
            'tax' => 18,
            'file' => null,
            'paymentStatus' => 'paid',
            'paymentDate' => null,
            'paymentStaff' => null,
            'periodMounth' => null,
            'periodYear' => null,
            'accounting_category_id' => 8,
            'currency' => null,
            'safe_id' => null,
        );
        $invoiceID = $this->invoiceService->create($data);


        $a = 0;
        foreach ($request->stock_card_id as $item) {
            $stockcardlist[$a]['stockcardid'] = $item;
            $stockcardlist[$a]['user_id'] = Auth::user()->id;
            $stockcardlist[$a]['invoice_id'] = $invoiceID->id;
            $stockcardlist[$a]['color_id'] = $request->color_id[$a];
            $stockcardlist[$a]['warehouse_id'] = 1;
            $stockcardlist[$a]['seller_id'] = 1;
            $stockcardlist[$a]['reason_id'] = 10;
            $stockcardlist[$a]['type'] = 1;
            $stockcardlist[$a]['quantity'] = 1;
            $stockcardlist[$a]['imei'] = null;
            $stockcardlist[$a]['assigned_accessory'] = 0;
            $stockcardlist[$a]['assigned_device'] = 0;
            $stockcardlist[$a]['tax'] = 18;
            $stockcardlist[$a]['cost_price'] = str_replace(",", ".", $request->cost_price[$a]);
            $stockcardlist[$a]['base_cost_price'] = str_replace(",", ".", $request->base_cost_price[$a]);
            $stockcardlist[$a]['sale_price'] = str_replace(",", ".", $request->sale_price[$a]);
            $stockcardlist[$a]['description'] = null;
            $stockcardlist[$a]['discount'] = null;


            for ($i = 0; $i < 1; $i++) {

                $timer = date('');
                $newSerial = strtoupper(rand(101, 999) . substr($timer, 3, 5) . substr(time(), -2) . rand(10, 99));

                $stockcardmovement = new StockCardMovement();
                $stockcardmovement->stock_card_id = $item;
                $stockcardmovement->user_id = Auth::user()->id;
                $stockcardmovement->invoice_id = $invoiceID->id;
                $stockcardmovement->color_id = $request->color_id[$a];
                $stockcardmovement->warehouse_id = 1;
                $stockcardmovement->seller_id = 1;
                $stockcardmovement->reason_id = 10;
                $stockcardmovement->type = 1;
                $stockcardmovement->quantity = 1;
                $stockcardmovement->imei = null;
                $stockcardmovement->assigned_accessory = 0;
                $stockcardmovement->assigned_device = 0;
                $stockcardmovement->serial_number = $newSerial;
                $stockcardmovement->tax = 18;
                $stockcardmovement->cost_price = str_replace(",", ".", $request->cost_price[$a]);
                $stockcardmovement->base_cost_price = str_replace(",", ".", $request->base_cost_price[$a]);
                $stockcardmovement->sale_price = str_replace(",", ".", $request->sale_price[$a]);
                $stockcardmovement->description = null;
                $stockcardmovement->discount = 0;
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

        $refund->status = 1;
        $refund->save();

    }

    public function deleted()
    {
        $data['stockCardMovement'] = StockCardMovement::onlyTrashed()->where('company_id',Auth::user()->company_id)->orderBy('deleted_at', 'desc')->paginate(250);
        return view('module.stockcard.deleted', $data);
    }


    public function serialList(Request $request)
    {
        if($request->filled('serialNumber'))
        {
            $data['stockcards'] = StockCardMovement::where('serial_number',$request->serialNumber)->get();
            $data['links'] = 1;

        }else{
            $data['stockcards'] = StockCardMovement::where('company_id',Auth::user()->company_id)->orderBy('deleted_at', 'desc')->paginate(100);
            $data['links'] = $data['stockcards']->appends(['category_id' => $request->category_id])->links();

        }
        $data['category'] = $request->category_id;
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['brands'] = $this->brandService->get();

        return view('module.stockcard.serialList', $data);
    }


    public function getStockMovementList(Request $request)
    {

        $type = array('1,3,4,5');
        $user = Cache::get('user_'.\auth()->user()->id);
$ids = explode(',',$request->id);
        $y = StockCardMovement::whereIn('stock_card_id', $ids)->where('company_id', $user->company_id)->whereIn('type', ['1','3','4','5']);

        if ($request->serialNumber != 'undefined') {

            $y->where('serial_number', $request->serialNumber);
        }
        if ($user->hasRole('super-admin') || $user->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {

            if ($request->seller != 'undefined' && $request->seller != 'all') {
                $y->where('seller_id', $request->seller);
            }

        } else {
            if ($request->seller != 'undefined' && $request->seller != 'all') {
                $y->where('seller_id', $user->seller_id);
            }

            if ($request->seller == 'undefined') {
                $y->where('seller_id', $user->seller_id);
            }
        }

         if ($request->color != 'undefined') {

            $y->where('color_id', $request->color);
        }
        $a = $y->get();

        $data = [];
        foreach ($a as $item) {
            $vesions = [];
            foreach ($item->stock->version_id as $key) {
                $vesions[] = \App\Models\Version::find($key)->name ?? "Bulunamadı";
            }
            $data[] = array('serial_number' => $item->serial_number,
                //'test' => $item->stock->version_id,
                'id' => $item->id,
                'stock_name' => $item->stock->name,
                'category_name' => $item->stock->category->name,
                'category_sperator_name' => '',// $this->categorySeperator($item->testParent($item->stock->category->id)),
                'brand_name' => $item->stock->brand->name,
                'sale_price' => number_format($item->sale_price ?? 0, 2),
                'cost_price' => number_format($item->cost_price ?? 0, 2),
                'base_cost_price' => number_format($item->base_cost_price ?? 0, 2),
                'color_name' => $item->color->name,
                'color_id' => $item->color->id,
                'versions' => implode("/", $vesions),
                'assigned_device' => $item->assigned_device == 1 ? 'Temlikli Cihaz' : '',
                'assigned_accessory' => $item->assigned_accessory == 1 ? 'Temlikli Aksesuar' : '',
                'seller_name' => $item->seller->name,
                'quantity' => 1,//$item->quantityCheckDataNew(),
                'type' => $item->type,
            );
        }

        $array['data'] = $this->sortByField($data, 'quantity', SORT_DESC);
        $array['ids'] = array_column($data,'id');

        return response()->json($array,200);
    }

    public function getStockquantity($id, ...$arg)
    {
        $user = Cache::get('user_'.\auth()->user()->id);

        $type = array('1,3,4,5');

        $y = StockCardMovement::whereIn('stock_card_id', $id)->where('company_id', $user->company_id)->whereIn('type', ['1','3','4','5']);

        if (isset($arg[0])) {

            $y->where('serial_number', $arg[0]);
        }
        if ($user->hasRole('super-admin') || $user->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {

            if (isset($arg[1]) && $arg[1] != 'all') {
                $y->where('seller_id', $arg[1]);
            }

        } else {
            if (isset($arg[1]) and $arg[1] != 'all') {
                $y->where('seller_id',  $arg[1] );
            }

            if (!isset($arg[1])) {
                $y->where('seller_id',$user->seller_id);
            }
        }

        if (isset($arg[2])) {

            $y->where('color_id', $arg[2]);
        }
        $a = $y->count();



        return $a;
    }

    public function stockforserial(Request $request)
    {
        if($request->filled('id'))
        {
            $data['stockcards'] = StockCardMovement::where('stock_card_id',$request->id)->orderBy('type','asc')->get();
            $data['links'] = 1;

        }else{

            return  redirect()->back();

        }
        $data['category'] = $request->category_id;
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['brands'] = $this->brandService->get();

        return view('module.stockcard.serialList', $data);


    }

    public function all_price()
    {

    }



}
