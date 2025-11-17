<?php

namespace App\Http\Controllers;

use App\Enums\Unit;
use App\Helper\BarcodeHelper;
use App\Helper\SearchHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Refund;
use App\Models\Sale;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockCardController extends Controller
{
    private StockCardService $stockcardService;
    private RefundService $refundService;
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

    public function __construct(
        StockCardService $stockcardService,
        RefundService $refundService,
        SellerService $sellerService,
        WarehouseService $warehouseService,
        BrandService $brandService,
        ColorService $colorService,
        CategoryService $categoryService,
        VersionService $versionService,
        ReasonService $reasonService,
        FakeProductService $fakeProductService,
        TransferService $transferService,
        InvoiceService $invoiceService
    ) {
        $this->stockcardService = $stockcardService;
        $this->refundService = $refundService;
        $this->sellerService = $sellerService;
        $this->warehouseService = $warehouseService;
        $this->brandService = $brandService;
        $this->colorService = $colorService;
        $this->categoryService = $categoryService;
        $this->versionService = $versionService;
        $this->reasonService = $reasonService;
        $this->fakeProductService = $fakeProductService;
        $this->transferService = $transferService;
        $this->invoiceService = $invoiceService;
        $this->a = [];
        $this->x = [];
    }

    public function index(Request $request)
    {
        // Normal view isteği - sayfa açılışında verileri yükle
        $data['category'] = $request->category_id ?? 0;

        // Filter verilerini yükle
        $data['brands'] = app(BrandService::class)->get();
        $data['colors'] = app(ColorService::class)->get();
        $data['sellers'] = app(SellerService::class)->all();
        $data['reasons'] = app(ReasonService::class)->get();

        // Categories - recursive query
        $categories = DB::select('WITH RECURSIVE category_path (id, name, parent_id, path) AS
        (
          SELECT id, name, parent_id, name as path
          FROM categories
          WHERE parent_id = 0 and company_id = ' . Auth::user()->company_id . " and deleted_at is null
          UNION ALL
          SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
          FROM category_path cp JOIN categories k
          ON cp.id = k.parent_id Where deleted_at is null
        )
        SELECT * FROM category_path ORDER BY path;");

        $data['categories'] = $categories;

        return view('module.stockcard.index', $data);
    }

    /**
     * Stok kartlarını API olarak döndür
     */
    public function getStockCardsData(Request $request)
    {
        try {
            // Stok kartlarını filtrele
            $query = StockCard::with(['category', 'brand', 'color', 'warehouse', 'seller', 'company', 'user'])
                ->where('company_id', Auth::user()->company_id);

            // Filtreleri uygula
            if ($request->filled('stockName')) {
                $query->where('name', 'like', '%' . $request->stockName . '%');
            }

            if ($request->filled('brand')) {
                $query->where('brand_id', $request->brand);
            }

            if ($request->filled('version')) {
                $query->whereJsonContains('version_id', $request->version);
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Pagination ile stok kartlarını al
            $perPage = $request->get('per_page', 15);
            $stockCards = $query->orderBy('name')->paginate($perPage);

            // Verileri formatla
            $formattedData = [];
            foreach ($stockCards as $card) {
                // Devir hızını hesapla
                $turnoverRate = $this->calculateTurnoverRate($card->id, Auth::user()->company_id);
                
                $formattedData[] = [
                    'id' => $card->id,
                    'name' => $card->name,
                    'category' => $card->category->name ?? 'Belirtilmedi',
                    'category_sperator_name' => $this->categorySeperator($card->category_id ?? 0) ?? '',
                    'brand' => $card->brand->name ?? 'Belirtilmedi',
                    'version' => (function($v){
                        if (empty($v)) return '';
                        if (is_string($v)) {
                            $ids = json_decode($v, true);
                            if ($ids === null) { $ids = [$v]; }
                        } elseif (is_array($v)) {
                            $ids = $v;
                        } else {
                            $ids = [$v];
                        }
                        $names = \App\Models\Version::whereIn('id', $ids)->pluck('name')->toArray();
                        return implode(', ', $names);
                    })($card->version_id),
                    'barcode' => $card->barcode ?? '',
                    'is_status' => $card->is_status ?? 0,
                    'quantity' => $card->quantity() ?? 0,
                    'turnover_rate' => $turnoverRate,
                    'turnover_status' => $this->getTurnoverStatus($turnoverRate),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $stockCards->currentPage(),
                    'last_page' => $stockCards->lastPage(),
                    'per_page' => $stockCards->perPage(),
                    'total' => $stockCards->total(),
                    'from' => $stockCards->firstItem(),
                    'to' => $stockCards->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Stok kartları yüklenirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok kartları yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchStocksAjax(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([], 401);
            }

            $companyId = Auth::user()->company_id;
            $barcode = trim((string) $request->get('barcode', ''));
            $query = trim((string) $request->get('q', ''));

            $formatStock = function (StockCard $stock) {
                $stock->loadMissing(['brand', 'category']);

                $versionNames = '';
                try {
                    $versionJson = $stock->versionNames();
                    if ($versionJson) {
                        $decoded = json_decode($versionJson, true);
                        if (is_array($decoded)) {
                            $versionNames = implode(', ', array_filter($decoded));
                        }
                    }
                } catch (\Throwable $e) {
                    $versionNames = '';
                }

                return [
                    'id'            => $stock->id,
                    'text'          => $stock->name,
                    'brand_name'    => $stock->brand->name ?? '',
                    'version_names' => $versionNames,
                    'category_name' => $stock->category->name ?? '',
                    'sku'           => $stock->sku,
                    'barcode'       => $stock->barcode,
                ];
            };

            if ($barcode !== '') {
                $normalizedBarcode = BarcodeHelper::formatBarcode($barcode);
                $candidates = array_unique(array_filter([
                    $barcode,
                    $normalizedBarcode,
                    BarcodeHelper::formatSerialNumber($barcode),
                    BarcodeHelper::formatSerialNumber($normalizedBarcode)
                ]));

                $stock = StockCard::with('brand')
                    ->where('company_id', $companyId)
                    ->where(function ($q) use ($barcode, $normalizedBarcode, $candidates) {
                        $q->where('barcode', $barcode)
                          ->orWhere('barcode', $normalizedBarcode)
                          ->orWhere(function ($inner) use ($candidates) {
                              foreach ($candidates as $value) {
                                  $inner->orWhere('barcode', $value)
                                        ->orWhere('sku', $value);
                              }
                          })
                          ->orWhere('name', 'like', '%' . $barcode . '%');
                    })
                    ->orderBy('name')
                    ->first();

                if (!$stock) {
                    $movementQuery = StockCardMovement::with(['stock.brand', 'stock.stockCardPrice'])
                        ->where(function ($q) use ($candidates) {
                            foreach ($candidates as $value) {
                                $q->orWhere('barcode', $value)
                                  ->orWhere('serial_number', $value);
                            }
                        })
                        ->where('stock_card_movements.company_id', $companyId)
                        ->orderByDesc('id');

                    $movement = $movementQuery->first();

                    if ($movement && $movement->stock) {
                        $stock = $movement->stock;
                    }
                }

                if ($stock) {
                    $formatted = $formatStock($stock);
                    if (isset($movement) && $movement) {
                        $formatted['stock_card_movement_id'] = $movement->id;
                        $formatted['warehouse_id'] = $movement->warehouse_id;
                        $formatted['seller_id'] = $movement->seller_id;
                        $formatted['cost_price'] = $movement->cost_price;
                        $formatted['base_cost_price'] = $movement->base_cost_price;
                        $formatted['sale_price'] = $movement->sale_price;
                        $formatted['tax'] = $movement->tax;
                        $formatted['stockcardid'] = $movement->stock_card_id;
                    }
                    return response()->json(['stock' => $formatted]);
                }

                return response()->json(['stock' => null]);
            }

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $stocks = StockCard::with('brand')
                ->where('company_id', $companyId)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('sku', 'like', '%' . $query . '%')
                      ->orWhere('barcode', 'like', '%' . $query . '%');
                })
                ->orderBy('name')
                ->limit(20)
                ->get();

            if ($stocks->isEmpty()) {
                $stocks = StockCardMovement::with('stock.brand')
                    ->where('serial_number', 'like', '%' . $query . '%')
                    ->limit(20)
                    ->get()
                    ->pluck('stock')
                    ->filter()
                    ->unique('id')
                    ->values();
            }

            $formatted = collect($stocks)->filter()->map($formatStock)->values();

            return response()->json($formatted);
        } catch (\Exception $e) {
            Log::error('Stock search ajax error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Stok kart hareketlerini getir (modal için)
     */
    public function getMovements(Request $request)
    {
        try {
            $stockCardIds = $request->get('stock_card_ids');

            if (!$stockCardIds) {
                return response()->json(['error' => "Stok kart ID'leri gerekli"], 400);
            }

            // StockCardMovement modelini kullanarak hareketleri getir
            $query = StockCardMovement::whereIn('type', [1, 3, 4, 5])
                ->where('stock_card_id', $stockCardIds)
                ->with([
                    'stockCard',
                    'stockCard.brand:id,name',
                    'stockCard.category:id,name',
                    'stockCard.color:id,name',
                ]);

            if ($request->filled('seller')){
                $query->where('seller_id', $request->seller);
            }
            // Pagination ekle
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $movements = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

            // Veriyi modal için formatla
            $formattedMovements = $movements->items();
            $formattedMovements = collect($formattedMovements)->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'serial_number' => $movement->serial_number,
                    'cost_price' => $movement->cost_price,
                    'base_cost_price' => $movement->base_cost_price,
                    'sale_price' => $movement->sale_price,
                    'quantity' => $movement->quantity,
                    'type' => $movement->type,
                    'type_name' => $this->getTypeName($movement->type),
                    'color_name' => $movement->color->name ?? 'N/A',
                    'brand_name' => $movement->stockCard->brand->name ?? 'N/A',
                    // 'versions' => $movement->stockCard->version ?? 'N/A',
                    'category_sperator_name' => '',
                    'category_name' => $movement->stockCard->category->name ?? 'N/A',
                    'seller_name' => $movement->seller->name ?? 'N/A',
                    'stock_name' => $movement->stockCard->stock_name,
                    'created_at' => $movement->created_at->format('d.m.Y H:i'),
                    'barcode' => $movement->barcode ?? 'N/A',
                    'imei' => $movement->imei ?? 'N/A',
                    'assigned_accessory' => $movement->assigned_accessory ?? 'N/A',
                    'assigned_device' => $movement->assigned_device ?? 'N/A',
                    'reason_name' => $movement->reason->name ?? 'N/A',
                    'invoice' => $movement->invoice_id ?? 'N/A',
                ];
            });

            return response()->json([
                'data' => $formattedMovements->values(),
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'per_page' => $movements->perPage(),
                'total' => $movements->total(),
                'from' => $movements->firstItem(),
                'to' => $movements->lastItem()
            ]);
        } catch (\Exception $e) {
            Log::error('StockCard movements error: ' . $e->getMessage());
            return response()->json(['error' => 'Stok hareketleri yüklenemedi'], 500);
        }
    }

    /**
     * Hareket tipi adını getir
     */
    private function getTypeName($type)
    {
        $types = [
            1 => 'Satışta',
            2 => 'Satıldı',
            3 => 'Hasarlı',
            4 => 'Transfer Sürecinde',
            5 => 'Teknik Servis Sürecinde'
        ];

        return $types[$type] ?? 'Bilinmiyor';
    }

    /**
     * AJAX endpoint for stockcard search - performans optimizasyonu
     */
    public function searchAjax(Request $request)
    {
        try {
            $stockcards = $this->stockcardService->stockSearch($request);
            return response()->json([
                'stockList' => $stockcards['stockList'],
                'stockLink' => $stockcards['stockLink']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Stok kartları yüklenemedi'], 500);
        }
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
            })->first()->name ?? 'Bulunamadı';
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
            })->first()->name ?? 'Bulunamadı';
        }

        return $version;
    }

    protected function create(Request $request)
    {
        $this->authorize('create-accessory');

        // Performans optimizasyonu - sadece gerekli verileri yükle
        $data['brands'] = collect([]);  // Boş collection - AJAX ile yüklenecek
        $data['versions'] = collect([]);  // Boş collection - AJAX ile yüklenecek
        $data['categories'] = [];  // Boş array - AJAX ile yüklenecek
        $data['fakeproducts'] = collect([]);  // Boş collection - gerektiğinde yüklenecek
        $data['units'] = Unit::Unit()->value;
        $data['request'] = $request;

        return view('module.stockcard.form', $data);
    }

    /**
     * AJAX endpoint for brands - performans optimizasyonu
     */
    public function getBrandsAjax()
    {
        try {
            $brands = $this->brandService->get();
            return response()->json($brands);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Markalar yüklenemedi'], 500);
        }
    }

    /**
     * AJAX endpoint for versions - performans optimizasyonu
     */
    public function getVersionsAjax(Request $request)
    {
        try {
            $brandId = $request->get('brand_id');
            if (!$brandId) {
                return response()->json([]);
            }

            $versions = $this->versionService->getByBrand($brandId);
            return response()->json($versions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Modeller yüklenemedi'], 500);
        }
    }

    /**
     * AJAX endpoint for categories - performans optimizasyonu
     */
    public function getCategoriesAjax()
    {
        try {
            $categories = $this->getCategoryPathList();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kategoriler yüklenemedi'], 500);
        }
    }

    /**
     * AJAX endpoint for stock name autocomplete - performans optimizasyonu
     */
    public function getStockNamesAjax(Request $request)
    {
        try {
            // Authentication check
            if (!Auth::check()) {
                return response()->json([], 401);
            }
            
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $stockNames = StockCard::where('company_id', Auth::user()->company_id)
                ->where('name', 'like', '%' . $query . '%')
                ->select('name')
                ->distinct()
                ->limit(10)
                ->get()
                ->pluck('name')
                ->values(); // Re-index array

            return response()->json($stockNames);
        } catch (\Exception $e) {
            \Log::error('Stock names ajax error: ' . $e->getMessage());
            return response()->json([], 500);
        }
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
        // $categories = $this->categoryService->getAllParentsL();
        $categories =
            DB::select('WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and company_id = ' . Auth::user()->company_id . " and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        // dd($categories);
        //  $a = $this->categoryService->getList($request->category);
        $data['categories'] = $categories;  // $this->listenke($categories, $request->category);
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
        $ids = $request->ids;
        // ids string olarak geliyorsa array'e çevir
        if (is_string($ids)) {
            $ids = [$ids];
        }

        try {
            $data = $this->stockData($ids);

            // Eğer data boşsa debug için göster
            if (empty($data)) {
                dd([
                    'message' => 'Veri bulunamadı',
                    'ids' => $ids,
                    'user_company_id' => Auth::user()->company_id,
                    'movements_count' => StockCardMovement::whereIn('stock_card_id', $ids)->count()
                ]);
            }

            return view('module.stockcard.barcode', compact('data'));
        } catch (\Exception $e) {
            dd([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'ids' => $ids
            ]);
        }
    }

    protected function barcodes(Request $request)
    {
        $y = StockCardMovement::whereIn('id', $request->selected)->get();

        $data = [];
        foreach ($y as $item) {
            $vesions = '';
            foreach ($item->stock->version_id as $key) {
                $vesions .= \App\Models\Version::find($key)->name ?? 'Bulunamadı' . '</br>';
            }
            $data[] = array(
                'serial_number' => $item->serial_number,
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
                return response()->json('Seri Numarası Bulunamadı Veya Stok Yetersiz', 400);
            }

            $transfer = Transfer::whereJsonContains('serial_list', $request->serial_number)->whereNull('comfirm_id')->whereNull('comfirm_date')->first();
            if ($transfer) {
                return response()->json('Transferi kabul edilmemiş', 400);
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
        Cache::delete('stock_cards_all');

        $data = array(
            'name' =>  Str::upper($request->name),
            'company_id' => Auth::user()->company_id,
            'user_id' => Auth::user()->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'version_id' => $request->version_id,
            'sku' => 'PH' . rand(01, 9999) . date('Y'),
            'barcode' => $request->barcode ? BarcodeHelper::formatBarcode($request->barcode) : BarcodeHelper::generateBarcode('PH', 4) . date('Y'),
            'tracking' => $request->tracking == 'on' ? '1' : '0',
            'unit' => $request->unit_id,
            'tracking_quantity' => $request->tracking_quantity,
            'is_status' => 1,
        );

        if (empty($request->id)) {
            // Yeni kayıt eklenmeden önce aynı özelliklere sahip stok kartı var mı kontrol et
            $existingStock = StockCard::where('company_id', Auth::user()->company_id)
                ->where('name', Str::upper($request->name))
                ->where('brand_id', $request->brand_id)
                ->where('category_id', $request->category_id)
                ->where('version_id', $request->version_id)
                ->first();
            
            if ($existingStock) {
                // Aynı özelliklere sahip stok kartı bulundu
                Log::warning('Duplicate stock card attempt', [
                    'user_id' => Auth::user()->id,
                    'existing_stock_id' => $existingStock->id,
                    'stock_name' => $request->name,
                    'brand_id' => $request->brand_id,
                    'category_id' => $request->category_id
                ]);
                
                return redirect()
                    ->route('invoice.create', ['id' => $existingStock->id])
                    ->with('warning', 'Bu özelliklere sahip bir stok kartı zaten mevcut. Mevcut stok kartına yönlendirildiniz.');
            }
            
            $stock = $this->stockcardService->create($data);
            $id = $stock->id;
        } else {
            // Düzenleme işlemi - aynı ID hariç kontrol et
            $existingStock = StockCard::where('company_id', Auth::user()->company_id)
                ->where('name', Str::upper($request->name))
                ->where('brand_id', $request->brand_id)
                ->where('category_id', $request->category_id)
                ->where('version_id', $request->version_id)
                ->where('id', '!=', $request->id)
                ->first();
            
            if ($existingStock) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Bu özelliklere sahip başka bir stok kartı zaten mevcut (ID: ' . $existingStock->id . ')');
            }
            
            $this->stockcardService->update($request->id, $data);
            $id = $request->id;
        }
        Cache::set('stock_cards_all', StockCard::all());
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
        // Normal view isteği - Vue.js component'inden yüklenecek
        $data['category'] = $request->category_id ?? 0;

        // Categories - recursive query
        $categories = DB::select('WITH RECURSIVE category_path (id, name, parent_id, path) AS
        (
          SELECT id, name, parent_id, name as path
          FROM categories
          WHERE parent_id = 0 and company_id = ' . Auth::user()->company_id . " and deleted_at is null
          UNION ALL
          SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
          FROM category_path cp JOIN categories k
          ON cp.id = k.parent_id Where deleted_at is null
        )
        SELECT * FROM category_path ORDER BY path;");

        $data['categories'] = $categories;

        return view('module.stockcard.list', $data);
    }
    /**
     * List sayfası için API data
     */
    protected function getListData(Request $request)
    {
        try {
            // Kategori filtreleme
            $categoryId = $request->category ?? $request->category_id;
            $categoryIds = [$categoryId];

            if ($categoryId > 0) {
                $subCategories = app(CategoryService::class)->getAllParentList($categoryId);
                $subCategoryIds = $this->array_column_recursive($subCategories, 'id');
                $categoryIds = array_merge($categoryIds, $subCategoryIds);
            }

            // Base query
            $query = StockCard::with(['category', 'brand', 'color','movements'])
                ->whereIn('category_id', $categoryIds)
                ->where('company_id', Auth::user()->company_id);

            // Filtreler
            if ($request->filled('stockName')) {
                $query->where('name', 'like', '%' . $request->stockName . '%');
            }

            if ($request->filled('brand')) {
                $query->where('brand_id', $request->brand);
            }

            if ($request->filled('version')) {
                $query->whereJsonContains('version_id', $request->version);
            }


            if ($request->filled('seller')) {
                $seller = $request->seller;
                $companyId = Auth::user()->company_id;
                $query->whereIn('id', function ($sub) use ($seller, $companyId) {
                    $sub->select('stock_card_id')
                        ->from('stock_card_movements')
                        ->where('seller_id', $seller)
                        ->where('company_id', $companyId)
                        ->whereNull('deleted_at');
                });
            }

            // Seri numarası, renk, şube filtresi varsa detaylı arama
            if ($request->filled('serialNumber') || $request->filled('seller') || $request->filled('color')) {
                // Bu durumda tüm kayıtları getir, frontend'de filtrele
                $stockcards = $query->orderBy('name', 'asc')->get();
            } else {
                // Normal pagination
                $perPage = $request->get('per_page', default: 20);
                $stockcards = $query->orderBy('name', 'asc')->paginate($perPage);
            }

            // Verileri grupla ve formatla
            $groupedData = [];
            $stockcardsData = $stockcards instanceof \Illuminate\Pagination\LengthAwarePaginator ? $stockcards->items() : $stockcards;

            foreach ($stockcardsData as $stockcard) {
                $key = $stockcard->name . '_' . $stockcard->category_id . '_' . $stockcard->brand_id;

                if (!isset($groupedData[$key])) {
                    // Devir hızını hesapla
                    $turnoverRate = $this->calculateTurnoverRate($stockcard->id, Auth::user()->company_id);
                    
                    $groupedData[$key] = [
                        'id' => $stockcard->id,
                        'stock_name' => $stockcard->name,
                        'category_separator_name' => $this->categorySeperator($stockcard->category_id),
                        'category_name' => $stockcard->category->name ?? 'Belirtilmedi',
                        'brand_name' => $stockcard->brand->name ?? 'Belirtilmedi',
                        'quantity' => $stockcard->quantity() ?? 0,
                        'turnover_rate' => $turnoverRate,
                        'turnover_status' => $this->getTurnoverStatus($turnoverRate),
                        'stockData' => []
                    ];
                } else {
                    $groupedData[$key]['ids'][] = $stockcard->id;
                }

                // Stok detaylarını ekle
                $groupedData[$key]['stockData'][] = [
                    'id' => $stockcard->id,
                    'quantity' => $stockcard->quantity() ?? 0,
                    'barcode' => $stockcard->barcode ?? '',
                    'is_status' => $stockcard->is_status ?? 0
                ];
            }

            $response = [
                'success' => true,
                'data' => (function($g){
                    $arr = array_values($g);
                    usort($arr, function($a, $b) {
                        return ($b['quantity'] ?? 0) <=> ($a['quantity'] ?? 0);
                    });
                    return $arr;
                })($groupedData),
                'pagination' => null
            ];

            // Pagination bilgisi varsa ekle
            if ($stockcards instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $response['pagination'] = [
                    'current_page' => $stockcards->currentPage(),
                    'last_page' => $stockcards->lastPage(),
                    'per_page' => $stockcards->perPage(),
                    'total' => $stockcards->total(),
                    'from' => $stockcards->firstItem(),
                    'to' => $stockcards->lastItem(),
                    'total_quantity' => $stockcards->sum(function($item) {
                        return $item->quantity() ?? 0;
                    })
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Stok kart listesi yüklenirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok kart listesi yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    public function listOld(Request $request)
    {
        $this->NewSerialNumber = 'undefined';
        $this->colorCode = 'undefined';
        $this->sellerCode = 'undefined';

        if ($request->filled('page')) {
            $cashdata = 'list_' . $request->category_id . '_' . $request->page;
        } else {
            $cashdata = 'list_' . $request->category_id;
        }

        $x = [];
        $xxxx = app(CategoryService::class)->getAllParentList($request->category_id);
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
            $this->NewSerialNumber = ($request->serialNumber != '' ? $request->serialNumber : 'undefined');
            $this->colorCode = ($request->color != '' ? $request->color : 'undefined');
            $this->sellerCode = ($request->seller != '' ? $request->seller : 'undefined');

            $stocklist = $stockcardsList->get();
            $abc = $stocklist->toArray();
            $stocklistasd = array_group_by($abc, 'name', 'category_id', 'brand_id');
            $links = 1;
        } else {
            $stocklist = $stockcardsList->orderBy('name', 'asc')->paginate(100);
            $abc = $stocklist->toArray();
            $stocklistasd = array_group_by($abc['data'], 'name', 'category_id', 'brand_id');
            $links = $stocklist->appends(['category_id' => $request->category_id])->links();

            $this->NewSerialNumber = 'undefined';
            $this->colorCode = 'undefined';
            $this->sellerCode = 'undefined';
        }

        foreach ($stocklistasd as $aaaa => $values) {
            $ad1 = array_keys($values);
            $ad2 = array_keys($values['' . $ad1[0] . '']);
            $content = $values['' . $ad1[0] . '']['' . $ad2[0] . ''];
            $stockCardIds = $this->array_column_recursive($content, 'id');
            if ($stockCardIds == 324) {
                dd('d');
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
        // $data['stockcards'] = $t->groupBy('serial_number')->having(DB::raw('count(serial_number)'), 1)->orderBy('id', 'desc')->get();
        $data['category'] = $request->category_id;
        $data['sellers'] = app(SellerService::class)->get();
        $data['colors'] = app(ColorService::class)->get();
        $data['brands'] = app(BrandService::class)->get();

        $data['serialNumber'] = $this->NewSerialNumber;
        $data['colorCode'] = $this->colorCode;
        $data['sellerCode'] = $this->sellerCode;

        // $data['categories'] = $this->categoryService->get();
        $categories = DB::select('WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and company_id = ' . Auth::user()->company_id . " and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        // dd($categories);
        //  $a = $this->categoryService->getList($request->category);
        $data['categories'] = $categories;  // $this->listenke($categories, $request->category);
        // dd($data['stockcards']);
        return view('module.stockcard.list', $data);
    }
    public function testParent($category_id = 0)
    {
        $x = Category::find($category_id);

        $data = null;
        $categories = Category::where('id', $x->parent_id)->get();
        // dd($categories);
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
        // $id array değilse array'e çevir
        if (!is_array($id)) {
            $id = [$id];
        }

        $y = StockCardMovement::whereIn('stock_card_id', $id)->where('company_id', Auth::user()->company_id)->whereIn('type', [1, 3, 4]);

        if (isset($arg[0])) {
            $y->where('serial_number', $arg[0]);
        } else {
            if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('Depo Sorumlusu'))  // HAsarlı Sorgusu
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
                $vesions[] = \App\Models\Version::find($key)->name ?? 'Bulunamadı';
            }
            $data[] = array(
                'serial_number' => $item->serial_number,
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
                'versions' => implode('/', $vesions),
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
        if (!empty($data) && is_array($data)) {
            return implode('/', $this->array_column_recursive($data, 'name')) . ' /';
        } elseif (!empty($data) && is_numeric($data)) {
            // Eğer $data bir ID ise, kategoriyi bul ve path'i oluştur
            $category = \App\Models\Category::find($data);
            if ($category) {
                return $this->getCategoryPath($category);
            }
        }
        return '';
    }
    /**
     * Kategori path'ini oluştur
     */
    private function getCategoryPath($category)
    {
        $path = [];
        $current = $category;

        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }

        return implode(' / ', $path) . ' /';
    }
    public function priceupdate(Request $request)
    {
        $stockcardMovement = StockCardMovement::where('stock_card_id', (int) $request->stock_card_id)->first();
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
    function sanitize($price)
    {
        return floatval(preg_replace('/[^0-9.]/', '', $price));
    }
    public function singlepriceupdate(Request $request)
    {

        $stockcardmovement = StockCardMovement::where('id', $request->stock_card_id)->where('type', 1)->first();

        if (!$stockcardmovement) {
            return response()->json(['error' => 'Stok kartı bulunamadı'], 404);
        }

        // Validate using new base_cost_price if provided, otherwise use existing
        $baseToCheck = $request->filled('base_cost_price') ? $request->base_cost_price : $stockcardmovement->base_cost_price;

// Ensure numeric values
        if (!is_numeric($baseToCheck) || !is_numeric($request->sale_price)) {
            return response()->json(['error' => 'Fiyatlar geçerli sayı olmalı'], 400);
        }

        if ($this->sanitize($baseToCheck) > $this->sanitize($request->sale_price)) {
            return response()->json(['error' => 'Satış Fiyatı maliyetten küçük olamaz'], 400);
        }

        $stockcardmovement->sale_price = $request->sale_price;
        $stockcardmovement->cost_price = $request->cost_price;
// Sadece gönderilmişse base_cost_price güncelle
        if ($request->filled('base_cost_price')) {
            $stockcardmovement->base_cost_price = $request->base_cost_price;
        }

        $stockcardmovement->save();
        
        return response()->json(['success' => true, 'message' => 'Kayıt Güncellendi'], 200);
    }

    public function multiplepriceupdate(Request $request)
    {
        // Aynı stock_card_id'ye sahip tüm StockCardMovement kayıtlarını bul
        if($request->filled('barcode')){
            $stockcardmovements = StockCardMovement::where('barcode', $request->barcode)->get();
        }else{
            $stockcardmovements = StockCardMovement::where('stock_card_id', $request->stock_card_id_multiple)->get();
        }
        
        if ($stockcardmovements->isEmpty()) {
            return response()->json('Bu stok kartına ait hareket bulunamadı', 400);
        }
        
        $updatedCount = 0;
        $errors = [];
        
        foreach ($stockcardmovements as $stockcardmovement) {
            try {
                // Satış fiyatı kontrolü (sadece sale_price verilmişse)
                if ($request->filled('sale_price')) {
                    if ($this->sanitize($stockcardmovement->base_cost_price) > $this->sanitize($request->sale_price)) {
                        $errors[] = "Seri {$stockcardmovement->serial_number}: Satış Fiyatı maliyetten küçük olamaz";
                        continue;
                    }
                }
                
                // Fiyatları güncelle
                if ($request->filled('base_cost_price')) {
                    $stockcardmovement->base_cost_price = $request->base_cost_price;
                }
                if ($request->filled('cost_price')) {
                    $stockcardmovement->cost_price = $request->cost_price;
                }
                if ($request->filled('sale_price')) {
                    $stockcardmovement->sale_price = $request->sale_price;
                }
                
                $stockcardmovement->save();
                $updatedCount++;
                
            } catch (\Exception $e) {
                $errors[] = "Seri {$stockcardmovement->serial_number}: " . $e->getMessage();
            }
        }
        
        // Sonuç mesajı
        if ($updatedCount > 0) {
            $message = "{$updatedCount} hareket güncellendi";
            if (!empty($errors)) {
                $message .= ". Hatalar: " . implode(', ', $errors);
            }
            return response()->json($message, 200);
        } else {
            $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Hiçbir hareket güncellenemedi';
            return response()->json($errorMessage, 400);
        }
    }

    public function multiplesaleupdate(Request $request)
    {
        $ids = explode(',', $request->stock_card_id_multiple);
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
        $movement = StockCardMovement::with(['stockCard.brand', 'stock'])->find($request->id);
        if (!$movement) {
            abort(404);
        }

        // Stock relation may be defined as `stock` or `stockCard`
        $stock = $movement->relationLoaded('stock') && $movement->stock ? $movement->stock : $movement->stockCard;

        $brandName = $stock && $stock->relationLoaded('brand') ? optional($stock->brand)->name : ($stock->brand->name ?? null);
        $versionJson = $stock && method_exists($stock, 'versionNames') ? $stock->versionNames() : null;

        $data[] = [
            'title'         => 'Barkod',
            'id'            => $movement->id,
            'serial_number' => $movement->serial_number,
            'sale_price'    => $movement->sale_price,
            'brand_name'    => $brandName,
            'name'          => $stock->name ?? '',
            'version'       => $versionJson ? $this->getVersionMap($versionJson) : '',
        ];

        $pdf = PDF::loadView('module.stockcard.print', ['data' => $data]);
        return $pdf->stream('codesolutionstuff.pdf');
    }


    public function singleserialprintrefresh(Request $request)
    {
        $movement = StockCardMovement::with(['stockCard.brand', 'stock'])->find($request->id);
        if (!$movement) {
            abort(404);
        }

        $newbarcode =  BarcodeHelper::generateSerialNumber();
        $movement->serial_number = $newbarcode;
        $movement->save();


        // Stock relation may be defined as `stock` or `stockCard`
        $stock = $movement->relationLoaded('stock') && $movement->stock ? $movement->stock : $movement->stockCard;

        $brandName = $stock && $stock->relationLoaded('brand') ? optional($stock->brand)->name : ($stock->brand->name ?? null);
        $versionJson = $stock && method_exists($stock, 'versionNames') ? $stock->versionNames() : null;

        $data[] = [
            'title'         => 'Barkod',
            'id'            => $movement->id,
            'serial_number' => $newbarcode,
            'sale_price'    => $movement->sale_price,
            'brand_name'    => $brandName,
            'name'          => $stock->name ?? '',
            'version'       => $versionJson ? $this->getVersionMap($versionJson) : '',
        ];

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

        BarcodeHelper::formatSerialNumber($request->serial_number);
        if ($request->filled('serial_number')) {
            $serialNumber =  explode('-', $request->serial_number);
            if($serialNumber[0] == 'B'){
                $stockcardmovemet = StockCardMovement::where('barcode', $request->serial_number)->first();
                if (!$stockcardmovemet) {
                    return response()->json('Stock Bulunamadı', 400);
                }
            }else{
                $stockcardmovemet = StockCardMovement::where('serial_number', $request->serial_number)->first();
                if (!$stockcardmovemet) {
                    return response()->json('Stock Bulunamadı', 400);
                }
            }
         }

        $stockcard_id = $stockcardmovemet->stock_card_id;

        $refund                = new Refund();
        $refund->stock_card_id = $stockcard_id;
        $refund->company_id    = Auth::user()->company_id;
        $refund->seller_id     = Auth::user()->seller_id;
        $refund->user_id       = Auth::user()->id;
        $refund->color_id      = $request->color_id;
        $refund->reason_id     = $request->reason_id;
        $refund->serial_number = BarcodeHelper::formatSerialNumber($request->serial_number);
        $refund->description   = $request->description;
        $refund->save();

        if($request->reason_id == 7){
            $stockcardmovement = StockCardMovement::find($stockcardmovemet->id);
            $stockcardmovement->type = 1;
            $stockcardmovement->save();
        }
    }

    public function refundlist(Request $request)
    {
        $x = Refund::with('stock','brand')->where('company_id', Auth::user()->company_id);

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

        $data['refunds'] = $x->orderBy('id', 'desc')->get();
        $data['brands'] = $this->brandService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['stocks'] = $this->stockcardService->all();

        return view('module.refund.index', $data);
    }

    public function getRefundsData(Request $request)
    {
        $query = Refund::with([
            'stock.brand',
            'color',
            'reason',
            'brand',
        ])->where('company_id', Auth::user()->company_id);

        if ($request->filled('brand')) {
            $query->whereHas('stock', function ($q) use ($request) {
                $q->where('brand_id', $request->brand);
            });
        }

        if ($request->filled('version')) {
            $query->whereHas('stock', function ($q) use ($request) {
                $q->whereJsonContains('version_id', $request->version);
            });
        }

        if ($request->filled('color')) {
            $query->where('color_id', $request->color);
        }

        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }

        if ($request->filled('reason')) {
            $query->where('reason_id', $request->reason);
        }

        $serialNumber = $request->input('serial_number') ?: $request->input('barcode');
        if (!empty($serialNumber)) {
            $query->where('serial_number', $serialNumber);
        }

        $refunds = $query->orderByDesc('id')->get();

        return response()->json([
            'refunds' => $refunds,
            'filters' => [
                'brands' => $this->brandService->get(),
                'sellers' => $this->sellerService->get(),
                'colors' => $this->colorService->get(),
                'reasons' => $this->reasonService->get(),
                'stocks' => $this->stockcardService->all(),
            ],
        ]);
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

        if ($request->type == 'normal_refund') {
            $refund->status = 1;
            $refund->save();

            $stockcardmovement = StockCardMovement::where('serial_number', $refund->serial_number)->first();
            if (!$stockcardmovement) {
                return response()->json(['error' => 'Stock hareketi bulunamadı'], 404);
            }

            $stockcardmovement->type = 1;

            $sale = Sale::where('stock_card_movement_id', $stockcardmovement->id)->first();

            if ($sale) {
                $sameInvoiceCount = Sale::where('invoice_id', $sale->invoice_id)->count();

                // Eğer bu invoice'a ait yalnızca bu sale varsa faturayı sil
                DB::transaction(function () use ($sale, $sameInvoiceCount) {
                    if ($sale->invoice_id && $sameInvoiceCount <= 1) {
                        Invoice::find($sale->invoice_id)?->delete();
                    }
                    $sale->delete();
                });
            }

            $stockcardmovement->save();
        }


        
        if ($request->type == 'delivered') {
            $refund->status = 4;  // Teslim Edildi
            $refund->save();
        }

        return redirect()->back();
    }

    public function refundreturn(Request $request)
    {
        $refund = Refund::find($request->id);

        $stockcardmovement = '';
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
            'description' => 'İADE DÖNÜŞ',
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
            'number' => 'IN' . rand(1111, 9999) . date('m'),
            'create_date' => Carbon::now()->format('Y-m-d'),
            'credit_card' => 0,
            'cash' => 0,
            'installment' => 0,
            'description' => 'İADE DÖNÜŞ',
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
            $stockcardlist[$a]['cost_price'] = str_replace(',', '.', $request->cost_price[$a]);
            $stockcardlist[$a]['base_cost_price'] = str_replace(',', '.', $request->base_cost_price[$a]);
            $stockcardlist[$a]['sale_price'] = str_replace(',', '.', $request->sale_price[$a]);
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
                $stockcardmovement->serial_number = BarcodeHelper::formatSerialNumber($newSerial);
                $stockcardmovement->tax = 18;
                $stockcardmovement->cost_price = str_replace(',', '.', $request->cost_price[$a]);
                $stockcardmovement->base_cost_price = str_replace(',', '.', $request->base_cost_price[$a]);
                $stockcardmovement->sale_price = str_replace(',', '.', $request->sale_price[$a]);
                $stockcardmovement->description = null;
                $stockcardmovement->discount = 0;
                $stockcardmovement->save();

                $stockcardprice = new StockCardPrice();
                $stockcardprice->company_id = Auth::user()->company_id;
                $stockcardprice->user_id = Auth::user()->id;
                $stockcardprice->stock_card_id = $request->stock_card_id[$a];
                $stockcardprice->cost_price = str_replace(',', '.', $request->cost_price[$a]);
                $stockcardprice->base_cost_price = str_replace(',', '.', $request->base_cost_price[$a]);
                $stockcardprice->sale_price = str_replace(',', '.', $request->sale_price[$a]);
                $stockcardprice->save();
            }

            $a++;
        }

        $invoiceID->detail = $stockcardlist;
        $invoiceID->save();

        $refund->status = 1;
        $refund->save();
    }

    public function deleted(Request $request)
    {
        // AJAX isteği için
        if ($request->ajax() || $request->wantsJson()) {
            $query = StockCardMovement::onlyTrashed()
                ->with(['stock', 'seller'])
                ->where('company_id', Auth::user()->company_id);
            
            // Search filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('serial_number', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%")
                      ->orWhereHas('stock', function($stockQuery) use ($search) {
                          $stockQuery->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            $stockCardMovements = $query->orderBy('deleted_at', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $stockCardMovements
            ]);
        }
        
        // Normal view request
        return view('module.stockcard.deleted');
    }
    
    public function restore(Request $request)
    {
        try {
            $movement = StockCardMovement::onlyTrashed()->findOrFail($request->id);
            
            // Check if user has permission to restore
            if ($movement->company_id !== Auth::user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu hareketi geri alma yetkiniz yok'
                ], 403);
            }
            
            $movement->restore();
            
            return response()->json([
                'success' => true,
                'message' => 'Hareket başarıyla geri alındı'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hareket geri alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }


    public function serialList(Request $request)
    {
        // AJAX isteği kontrolü
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getSerialListAjax($request);
        }

        // Normal sayfa yükleme
        $data = [];

        if ($request->filled('serialNumber')) {
            if($request->serialNumber == 'undefined') {
                return redirect()->back();
            }

            $searchInfo = SearchHelper::determineSearchType($request->serialNumber);
            
            if ($searchInfo) {
                $query = StockCardMovement::with(['stock.brand', 'stock.category', 'color', 'seller'])
                    ->where('company_id', Auth::user()->company_id);

                if ($searchInfo['type'] === 'barcode') {
                    $query->where('barcode', $searchInfo['value']);
                } else {
                    $query->where('serial_number', $searchInfo['value']);
                }

                $data['stockcards'] = $query->limit(50)->get();
                $data['search_type'] = $searchInfo['type'];
                $data['search_value'] = $searchInfo['value'];
            } else {
                $data['stockcards'] = collect([]);
            }
            
            $data['links'] = 1;
        } else {
            // Genel liste - pagination ile optimize edilmiş
            $data['stockcards'] = StockCardMovement::with(['stock.brand', 'stock.category', 'color', 'seller'])
                ->where('company_id', Auth::user()->company_id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            $data['links'] = $data['stockcards']->appends(['category_id' => $request->category_id])->links();
        }

        $data['category'] = $request->category_id;

        // AJAX ile yüklenecek veriler - performans optimizasyonu
        $data['sellers'] = collect([]);
        $data['colors'] = collect([]);
        $data['brands'] = collect([]);

        return view('module.stockcard.serialList', $data);
    }

    /**
     * AJAX endpoint for serial list - Vue.js için
     */
    public function getSerialListAjax(Request $request)
    {
        try {
            if($request->filled('type')) {
                $query = StockCardMovement::with(['stock.brand', 'stock.category', 'color', 'seller', 'sale.user'])
                    ->where('company_id', Auth::user()->company_id)
                    ->where('type', $request->type);
            } else {
                $query = StockCardMovement::with(['stock.brand', 'stock.category', 'color', 'seller', 'sale.user'])
                    ->where('company_id', Auth::user()->company_id);
            }
     

            // Seri numarası/barkod filtresi
            if ($request->filled('serialNumber')) {
                $searchInfo = SearchHelper::determineSearchType($request->serialNumber);
                
                if ($searchInfo) {
                    if ($searchInfo['type'] === 'barcode') {
                        $query->where('barcode', $searchInfo['value']);

                    } else {
                        // Seri numarası araması - LIKE ile arama
                        $query->where('serial_number', 'like', '%' . $searchInfo['value'] . '%');
                    }
                }
            }

            // Pagination
            $page = $request->get('page', 1);
            $perPage = 20;

            $stockCards = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Veriyi Vue.js için formatla
            $formattedData = $stockCards->map(function ($item) {
                return [
                    'id' => $item->id,
                    'serial_number' => $item->serial_number,
                    'invoice_id' => $item->invoice_id,
                    'barcode' => $item->barcode,
                    'cost_price' => $item->cost_price,
                    'base_cost_price' => $item->base_cost_price,
                    'sale_price' => $item->sale_price,
                    'quantity' => $item->quantity,
                    'type' => $item->type,
                    'color' => $item->color,
                    'seller' => $item->seller,
                    'stock' => $item->stock,
                    'sale' => $item->sale,
                    'categoryPath' => $this->categorySeperator($this->testParent($item->stock->category->id ?? 0))
                ];
            });

            return response()->json([
                'stockcards' => $formattedData,
                'pagination' => [
                    'current_page' => $stockCards->currentPage(),
                    'last_page' => $stockCards->lastPage(),
                    'per_page' => $stockCards->perPage(),
                    'total' => $stockCards->total(),
                    'from' => $stockCards->firstItem(),
                    'to' => $stockCards->lastItem()
                ],
                'search_info' => $request->filled('serialNumber') ? SearchHelper::determineSearchType($request->serialNumber) : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Stok kartları yüklenemedi: ' . $e->getMessage()], 500);
        }
    }

    // AJAX endpoint for sellers - performans optimizasyonu
    public function getSellersAjax()
    {
        try {
            $sellers = $this->sellerService->get();
            return response()->json($sellers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Satıcılar yüklenemedi'], 500);
        }
    }

    // AJAX endpoint for colors - performans optimizasyonu
    public function getColorsAjax()
    {
        try {
            $colors = $this->colorService->get();
            return response()->json($colors);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Renkler yüklenemedi'], 500);
        }
    }

    public function getStockMovementList(Request $request)
    {
        $user = Cache::get('user_' . \auth()->user()->id);
        $ids = explode(',', $request->id);

        $query = StockCardMovement::whereIn('stock_card_id', $ids)
            ->where('company_id', $user->company_id)
            ->whereIn('type', ['1', '3', '4', '5'])
            ->with(['stock.category', 'stock.brand', 'color', 'seller']);

        if ($request->serialNumber != 'undefined') {
            $searchInfo = SearchHelper::determineSearchType($request->serialNumber);
            
            if ($searchInfo) {
                if ($searchInfo['type'] === 'barcode') {
                    // Barkod araması - StockCard tablosunda ara
                    $query->whereHas('stock', function($q) use ($searchInfo) {
                        $q->where('barcode', $searchInfo['value']);
                    });
                } else {
                    // Seri numarası araması
                    $query->where('serial_number', $searchInfo['value']);
                }
            }
        }

        if ($user->hasRole('super-admin') || $user->hasRole('Depo Sorumlusu')) {
            if ($request->seller != 'undefined' && $request->seller != 'all') {
                $query->where('seller_id', $request->seller);
            }
        } else {
            $query->where('seller_id', $user->seller_id);
        }

        if ($request->color != 'undefined') {
            $query->where('color_id', $request->color);
        }

        $movements = $query->get();

        // Tüm version_id değerlerini topluyoruz
        $allVersionIds = $movements->flatMap(function ($item) {
            return $item->stock->version_id;  // JSON formatında saklanmış array'i decode ediyoruz
        })->unique()->toArray();  // Benzersiz ID'leri alıyoruz

        $versions = \App\Models\Version::whereIn('id', $allVersionIds)->pluck('name', 'id');

        $data = $movements->map(function ($item) use ($versions) {
            $versionIds = $item->stock->version_id;  // version_id'yi array olarak çözüyoruz
            $versionNames = collect($versionIds)->map(function ($id) use ($versions) {
                return $versions[$id] ?? 'Bulunamadı';  // Version ismini array'den alıyoruz
            })->implode('/');

            return [
                'serial_number' => $item->serial_number,
                'id' => $item->id,
                'stock_name' => $item->stock->name,
                'category_name' => $item->stock->category->name,
                'category_sperator_name' => '',
                'brand_name' => $item->stock->brand->name,
                'sale_price' => number_format($item->sale_price ?? 0, 2),
                'cost_price' => number_format($item->cost_price ?? 0, 2),
                'base_cost_price' => number_format($item->base_cost_price ?? 0, 2),
                'color_name' => $item->color->name,
                'color_id' => $item->color->id,
                'versions' => $versions,
                'assigned_device' => $item->assigned_device == 1 ? 'Temlikli Cihaz' : '',
                'assigned_accessory' => $item->assigned_accessory == 1 ? 'Temlikli Aksesuar' : '',
                'seller_name' => $item->seller->name,
                'quantity' => 1,
                'type' => $item->type,
            ];
        })->toArray();

        $array['data'] = $this->sortByField($data, 'quantity', SORT_DESC);
        $array['ids'] = array_column($data, 'id');

        return response()->json($array, 200);
    }

    public function getStockquantity($id, ...$arg)
    {
        $user = Auth::user();

        $type = array('1,3,4,5');

        $y = StockCardMovement::whereIn('stock_card_id', $id)->where('company_id', $user->company_id)->whereIn('type', ['1', '3', '4', '5']);

        if (isset($arg[0])) {
            $y->where('serial_number', $arg[0]);
        }
        if ($user->hasRole('super-admin') || $user->hasRole('Depo Sorumlusu'))  // HAsarlı Sorgusu
        {
            if (isset($arg[1]) && $arg[1] != 'all') {
                $y->where('seller_id', $arg[1]);
            }
        } else {
            if (isset($arg[1]) and $arg[1] != 'all') {
                $y->where('seller_id', $arg[1]);
            }

            if (!isset($arg[1])) {
                $y->where('seller_id', $user->seller_id);
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
        if ($request->filled('id')) {
            // id virgülle ayrılmış geliyorsa array'e çevir
            $ids = $request->id;
            if (is_string($ids) && strpos($ids, ',') !== false) {
                $ids = explode(',', $ids);
            } elseif (is_string($ids)) {
                $ids = [$ids];
            }

            $data['stockcards'] = StockCardMovement::whereIn('stock_card_id', $ids)->orderBy('type', 'asc')->get();
            $data['links'] = 1;
        } else {
            return redirect()->back();
        }
        $data['category'] = $request->category_id ?? 0;
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['brands'] = $this->brandService->get();

        return view('module.stockcard.serialList', $data);
    }

    public function all_price() {}

    /**
     * AJAX endpoint for stock card movements
     */
    public function getMovementsAjax(Request $request)
    {

        try {
            $stockCardId = $request->get('stock_card_id');
            if (!$stockCardId) {
                return response()->json(['error' => 'Stok kartı ID gerekli'], 400);
            }

            // Stok hareketlerini yükle
            $movements =  StockCardMovement::where('stock_card_id', $stockCardId)
                ->where('company_id', Auth::user()->company_id)
                ->orderBy('created_at', 'desc')
                ->get();


            // Hareketleri formatla
            $formattedMovements = $movements->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'serial_number' => $movement->serial_number,
                    'quantity' => $movement->quantity,
                    'type' => $movement->type,
                    'type_name' => $this->getMovementTypeName($movement->type),
                    'description' => $movement->description,
                    'created_at' => $movement->created_at,
                    'user_name' => $movement->user->name ?? 'Sistem'
                ];
            });

            return response()->json(['movements' => $formattedMovements]);
        } catch (\Exception $e) {
            Log::error('Stok hareketleri yüklenirken hata: ' . $e->getMessage());
            return response()->json(['error' => 'Hareketler yüklenemedi'], 500);
        }
    }

    /**
     * Hareket tipi adını getir
     */
    private function getMovementTypeName($type)
    {
        switch ($type) {
            case 1:
                return 'Giriş';
            case 2:
                return 'Çıkış';
            case 3:
                return 'Transfer';
            case 4:
                return 'İade';
            case 5:
                return 'Düzeltme';
            default:
                return 'Diğer';
        }
    }

    /**
     * Get stock price data for API
     */
    public function getStockPriceApi($id)
    {
        try {
            $stockPrice = StockCardPrice::where('stock_card_id', $id)
                ->orderBy('id', 'desc')
                ->first();

            if ($stockPrice) {
                return response()->json([
                    'cost_price' => $stockPrice->cost_price,
                    'base_cost_price' => $stockPrice->base_cost_price,
                    'sale_price' => $stockPrice->sale_price
                ]);
            }

            return response()->json([
                'cost_price' => 0,
                'base_cost_price' => 0,
                'sale_price' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Stock price not found'], 404);
        }
    }

    /**
     * Stok devir hızını hesapla
     */
    private function calculateTurnoverRate($stockCardId, $companyId)
    {
        try {
            $query = "
                SELECT 
                    COUNT(s.id) as total_sold,
                    AVG(DATEDIFF(s.created_at, scm.created_at)) as avg_days_to_sell
                FROM stock_card_movements scm
                LEFT JOIN sales s ON s.stock_card_movement_id = scm.id
                WHERE scm.stock_card_id = ?
                    AND scm.type = 1
                    AND s.id IS NOT NULL
            ";

            $result = DB::select($query, [$stockCardId]);
            
            // Debug log ekle
            Log::info("Turnover calculation for stock {$stockCardId}: " . json_encode($result));
            
            if (!empty($result) && $result[0]->total_sold > 0) {
                $rate = round($result[0]->avg_days_to_sell, 1);
                Log::info("Calculated turnover rate: {$rate} days for stock {$stockCardId}");
                return $rate;
            }
            
            Log::info("No sales found for stock {$stockCardId}");
            return 0; // Satış yoksa 0 döndür
        } catch (\Exception $e) {
            Log::error('Turnover rate calculation error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Devir hızı durumunu belirle
     */
    private function getTurnoverStatus($turnoverRate)
    {
        if ($turnoverRate == 0) {
            return [
                'status' => 'no_sales',
                'label' => 'Satış Yok',
                'class' => 'bg-secondary',
                'description' => 'Son 90 günde satış yok'
            ];
        } elseif ($turnoverRate <= 7) {
            return [
                'status' => 'excellent',
                'label' => 'Çok Hızlı',
                'class' => 'bg-success',
                'description' => $turnoverRate . ' günde bir satılıyor'
            ];
        } elseif ($turnoverRate <= 15) {
            return [
                'status' => 'good',
                'label' => 'Hızlı',
                'class' => 'bg-info',
                'description' => $turnoverRate . ' günde bir satılıyor'
            ];
        } elseif ($turnoverRate <= 30) {
            return [
                'status' => 'fair',
                'label' => 'Orta',
                'class' => 'bg-warning',
                'description' => $turnoverRate . ' günde bir satılıyor'
            ];
        } else {
            return [
                'status' => 'poor',
                'label' => 'Yavaş',
                'class' => 'bg-danger',
                'description' => $turnoverRate . ' günde bir satılıyor'
            ];
        }
    }

    /**
     * Export stock cards to Excel
     */
    public function exportToExcel(Request $request)
    {
        try {
            Log::info('StockCard Excel export started', ['request_params' => $request->all()]);
            
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            // Get filtered stock cards data
            $query = StockCard::with(['brand', 'category', 'color', 'seller'])
                ->where('company_id', Auth::user()->company_id);

            // Apply filters
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('barcode', 'like', "%{$searchTerm}%")
                        ->orWhere('serial_number', 'like', "%{$searchTerm}%");
                });
            }
            if ($request->filled('brand')) {
                $query->where('brand_id', $request->brand);
            }
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }
            if ($request->filled('version')) {
                $query->where('version_id', $request->version);
            }
            if ($request->filled('color')) {
                $query->where('color_id', $request->color);
            }
            if ($request->filled('seller')) {
                $query->where('seller_id', $request->seller);
            }
            if ($request->filled('status')) {
                $query->where('is_status', $request->status);
            }

            // Get all data (no pagination for export)
            $stockCards = $query->orderBy('id', 'desc')->get();
            
            Log::info('StockCard Excel export - Found stock cards: ' . $stockCards->count());

            // Prepare Excel data
            $excelData = [];
            $excelData[] = [
                'ID',
                'Ürün Adı',
                'Barkod',
                'Seri No',
                'Marka',
                'Kategori',
                'Renk',
                'Satışçı',
                'Stok Miktarı',
                'Satış Fiyatı',
                'Maliyet Fiyatı',
                'Durum',
                'Oluşturma Tarihi'
            ];

            foreach ($stockCards as $stockCard) {
                try {
                    Log::info('Processing stock card: ' . $stockCard->id);
                    
                    // Safe conversion for all fields
                    $id = (string)($stockCard->id ?? '');
                    $name = (string)($stockCard->name ?? '');
                    $barcode = (string)($stockCard->barcode ?? '');
                    $serialNumber = (string)($stockCard->serial_number ?? '');
                    $brandName = $stockCard->brand ? (string)$stockCard->brand->name : '';
                    $categoryName = $stockCard->category ? (string)$stockCard->category->name : '';
                    $colorName = $stockCard->color ? (string)$stockCard->color->name : '';
                    $sellerName = $stockCard->seller ? (string)$stockCard->seller->name : '';
                    $quantity = (string)($stockCard->tracking_quantity ?? 0);
                    $salePrice = number_format($stockCard->sale_price ?? 0, 2, ',', '.');
                    $costPrice = number_format($stockCard->cost_price ?? 0, 2, ',', '.');
                    $status = $stockCard->is_status ? 'Aktif' : 'Pasif';
                    $createdAt = $stockCard->created_at ? $stockCard->created_at->format('d.m.Y H:i') : '';
                    
                    $excelData[] = [
                        $id,
                        $name,
                        $barcode,
                        $serialNumber,
                        $brandName,
                        $categoryName,
                        $colorName,
                        $sellerName,
                        $quantity,
                        $salePrice,
                        $costPrice,
                        $status,
                        $createdAt
                    ];
                    
                    Log::info('Successfully processed stock card: ' . $stockCard->id);
                    
                } catch (\Exception $e) {
                    Log::error('Error processing stock card ' . $stockCard->id . ': ' . $e->getMessage());
                    Log::error('Stock card data: ' . json_encode($stockCard->toArray()));
                    throw $e;
                }
            }

            // Generate Excel file
            $filename = 'stok_kartlari_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function() use ($excelData) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for proper encoding
                fwrite($file, "\xEF\xBB\xBF");
                
                foreach ($excelData as $row) {
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('StockCard Excel export error: ' . $e->getMessage());
            return response()->json(['error' => 'Excel dosyası oluşturulurken hata oluştu.'], 500);
        }
    }
}
