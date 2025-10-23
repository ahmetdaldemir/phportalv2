<?php

namespace App\Http\Controllers;

use App\Jobs\SendTransferInfo;
use App\Models\Phone;
use App\Models\StockCardMovement;
use App\Models\Transfer;
use App\Services\Brand\BrandService;
use App\Services\Color\ColorService;
use App\Services\Reason\ReasonService;
use App\Services\Seller\SellerService;
use App\Services\StockCard\StockCardService;
use App\Services\Transfer\TransferService;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{

    private TransferService $transferService;
    private SellerService $sellerService;
    private StockCardService $stockCardService;
    private ReasonService $reasonService;
    private UserService $userService;
    private ColorService $colorService;
    private BrandService $brandService;

    public function __construct(TransferService  $transferService,
                                SellerService    $sellerService,
                                StockCardService $stockCardService,
                                UserService      $userService,
                                ReasonService    $reasonService,
                                ColorService     $colorService,
                                BrandService     $brandService,
    )
    {
        $this->transferService = $transferService;
        $this->sellerService = $sellerService;
        $this->stockCardService = $stockCardService;
        $this->reasonService = $reasonService;
        $this->userService = $userService;
        $this->colorService = $colorService;
        $this->brandService = $brandService;

    }

    protected function index(Request $request)
    {
        $this->authorize('view-all-dispatches');
        $transfers = Transfer::where('company_id', Auth::user()->company_id);

        if (!Auth::user()->hasRole('super-admin') && !Auth::user()->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
        {
            $transfers->where('delivery_seller_id', Auth::user()->seller_id);
        }
//
//        if (Auth::user()->hasRole('super-admin') && Auth::user()->hasRole('Depo Sorumlusu') && $request->filled('seller_id')) // HAsarlı Sorgusu
//        {
//            $transfers->where('delivery_seller_id', $request->seller_id);
//        }


        if ($request->filled('serialNumber')) {
            $transfers->whereJsonContains('serial_list', $request->serialNumber);
        }

        $x = $transfers->orderBy('created_at', 'desc')->paginate(50);

        $onlyTransfer = Transfer::where('company_id', Auth::user()->company_id)->where('main_seller_id', Auth::user()->seller_id)->orderBy('id','desc')->limit(50)->get();


        $data['brands'] = $this->brandService->get();
        $data['sellers'] = $this->sellerService->all();
        $data['stocks'] = $this->stockCardService->all();
        $data['reasons'] = $this->reasonService->get();
        $data['users'] = $this->userService->get();
        $data['colors'] = $this->colorService->get();
        $data['transfers'] = $x;
        $data['onlyTransfers'] = $onlyTransfer;
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
        return view('module.transfer.index', $data);
    }

    protected function create(Request $request)
    {
        $this->authorize('create-dispatch');
        $data['sellers'] = $this->sellerService->all();
        $data['stocks'] = $this->stockCardService->all();
        $data['reasons'] = $this->reasonService->get();
        $data['users'] = $this->userService->get();
        $data['colors'] = $this->colorService->get();
        $data['request'] = $request;
        return view('module.transfer.form', $data);
    }

    protected
    function edit(Request $request)
    {
        $data['sellers'] = $this->sellerService->all();
        $data['stocks'] = $this->stockCardService->all();
        $data['reasons'] = $this->reasonService->get();
        $data['users'] = $this->userService->get();
        $data['colors'] = $this->colorService->get();
        $data['transfers'] = $this->transferService->find($request->id);
        return view('module.transfer.form', $data);
    }

    protected
    function delete(Request $request)
    {
        $this->transferService->delete($request->id);
        return redirect()->back();
    }

    public function store(Request $request)
    {
        Log::info('Transfer store method called', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);
        
        DB::beginTransaction();
        try {
            Log::info('Starting authorization check');
            $this->authorize('create-dispatch');
            Log::info('Authorization passed');
            if (Auth::user()->hasRole('Depo Sorumlusu') || Auth::user()->hasRole('super-admin')) {
                $status = 2;
            } else {
                $status = 1;
            }
            if ($request->delivery_seller_id == 1) {
                $status = 2;
            }

            foreach ($request->sevkList as $item) {
                
                // Handle barcode transfer with quantity
                if ($request->is_barcode_transfer && is_array($item)) {
                    $barcode = $item['barcode'];
                    $quantity = $item['quantity'] ?? 1;
                    
                    // Check if enough stock available for this barcode
                    $availableStock = StockCardMovement::where('barcode', $barcode)
                        ->where('seller_id', $request->main_seller_id)
                        ->where('type', '!=', 4) // Not already transferred
                        ->count();
                    
                    if ($availableStock < $quantity) {
                        return response()->json("Yetersiz stok! {$barcode} barkodlu ürün için {$availableStock} adet mevcut, {$quantity} adet talep edildi.", 200);
                    }
                    
                    // Get stock details for detail array
                    $stockcheck = StockCardMovement::where('barcode', $barcode)
                        ->where('seller_id', $request->main_seller_id)
                        ->first();
                    
                    if (!$stockcheck) {
                        return response()->json("Barkod bulunamadı: {$barcode}", 200);
                    }
                    
                    // Add to detail array with quantity
                    $versionData = $stockcheck->stock->version;
                    $datas = [];
                    if ($versionData && isset($versionData->version)) {
                        $datas = json_decode($versionData->version, TRUE) ?? [];
                    }
                    $a = [];
                    foreach ($datas as $mykey => $myValue) {
                        $a[] = $myValue;
                    }
                    
                    $detail[] = array(
                        'name' => $stockcheck->stock->name,
                        'serial' => $barcode,
                        'brand' => $stockcheck->stock->brand->name,
                        'version' => json_encode($a),
                        'category' => $this->categorySeperator1($this->testParentS($stockcheck->stock->category->id)),
                        'quantity' => $quantity,
                        'color' => $stockcheck->color->name,
                    );
                    
                    // Mark items as transferred (type = 4)
                    StockCardMovement::where('barcode', $barcode)
                        ->where('seller_id', $request->main_seller_id)
                        ->where('type', '!=', 4)
                        ->limit($quantity)
                        ->update(['type' => 4]);
                    
                    continue; // Skip to next item
                }

                if ($request->type == 'phone') {
                    $phone = Phone::where('barcode', $item);
                    if (!Auth::user()->hasRole('super-admin') && !Auth::user()->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
                    {
                        $phone->where('seller_id', Auth::user()->seller_id);
                    }
                    $phonecheck = $phone->first();
                    if (!$phonecheck) {
                        return response()->json('Sevk yapılamaz. Seçilen Telefon aynı bayiye ait olmalı', 200);
                    } else {
                        $phonecheck->status = 2;
                        $phonecheck->save();
                    }

                    $detail[] = array(
                        'name' => $phonecheck->brand->name . ' ' . $phonecheck->version->name . ' ' . Phone::TYPE[$phonecheck->type],
                        'serial' => $phonecheck->barcode,
                        'brand' => $phonecheck->brand->name,
                        'version' => $phonecheck->version->name,
                        'category' => 'Telefon',
                        'quantity' => '1',
                        'color' => $phonecheck->color->name,
                    );

                } else {

                    // Check if this is barcode transfer
                    if ($request->is_barcode_transfer) {
                        $stock = StockCardMovement::where('barcode', $item);
                    } else {
                        $stock = StockCardMovement::where('serial_number', $item);
                    }

                    if (!Auth::user()->hasRole('super-admin') && !Auth::user()->hasRole('Depo Sorumlusu')) // HAsarlı Sorgusu
                    {
                        $stock->where('seller_id', Auth::user()->seller_id);
                    }
                    $stockcheck = $stock->first();

                    if (!$stockcheck) {
                        $itemType = $request->is_barcode_transfer ? 'barkod' : 'seri numarası';
                        return response()->json("Sevk yapılamaz. Seçilen {$itemType} aynı bayiye ait olmalı.", 200);
                    }

                    $a = Transfer::whereJsonContains('serial_list', $item)->where('is_status', 1)->get();
                    if (count($a) > 0) {
                        $itemType = $request->is_barcode_transfer ? 'barkod' : 'seri numarası';
                        return response()->json($item . " {$itemType} ürün Sevk sürecinde", 200);
                    }


                    $versionData = $stockcheck->stock->version;
                    $datas = [];
                    if ($versionData && isset($versionData->version)) {
                        $datas = json_decode($versionData->version, TRUE) ?? [];
                    }
                    foreach ($datas as $mykey => $myValue) {
                        $a[] = $myValue;
                    }


                    $detail[] = array(
                        'name' => $stockcheck->stock->name,
                        'serial' => $request->is_barcode_transfer ? $stockcheck->barcode : $stockcheck->serial_number,
                        'brand' => $stockcheck->stock->brand->name,
                        'version' => json_encode($a),
                        'category' => $this->categorySeperator1($this->testParentS($stockcheck->stock->category->id)),
                        'quantity' => '1',
                        'color' => $stockcheck->color->name,
                    );
                }
            }

            if ($request->type == 'other' && !$request->is_barcode_transfer) {
                foreach ($request->sevkList as $item) {
                    $stock = StockCardMovement::where('serial_number', $item)->first();
                    $stock->type = 4;
                    $stock->save();
                }
            }

            $data = array(
                'company_id' => Auth::user()->company_id,
                'user_id' => Auth::user()->id,
                'is_status' => $status,
                'main_seller_id' => $request->main_seller_id ?? Auth::user()->seller_id,
                'description' => $request->description,
                'number' => $request->number ?? null,
                'stocks' => array_unique($request->sevkList),
                'serial_list' => array_unique($request->sevkList),
                'comfirm_id' => 1,
                'type' => $request->type,
                'detail' => $detail,
                'delivery_seller_id' => $request->delivery_seller_id,
            );

            if (empty($request->id)) {
                $transfer = $this->transferService->create($data);
            } else {
                $transfer = $this->transferService->update($request->id, $data);
            }
            //$this->dispatch(new SendTransferInfo($transfer));

        } catch (\Exception $e) {
            Log::error('Transfer store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            DB::rollBack();
        }
        DB::commit();
        return response()->json('Transfer Gerçekleşti', 200);
    }

    protected function update(Request $request)
    {

        $this->authorize('accept-dispatch');

        $transfer = $this->transferService->find($request->id);

        if ($transfer->type == 'phone') {
            foreach ($transfer->serial_list as $key => $value) {
                Phone::where('barcode', $value)->update(['status' => 0, 'seller_id' => $transfer->delivery_seller_id]);
            }
            $data = array('is_status' => $request->is_status, 'comfirm_id' => Auth::user()->id, 'comfirm_date' => Carbon::now());
            $this->transferService->update($request->id, $data);
            return redirect()->back();
        }
        if ($transfer->serial_list) {
            foreach ($transfer->serial_list as $key => $value) {
                if($request->is_status == 4) //RED
                {
                    StockCardMovement::where('serial_number', $value)->update(['type' => 1]);
                }else if($request->is_status == 3){
                    StockCardMovement::where('serial_number', $value)->update(['type' => 1, 'seller_id' => $transfer->delivery_seller_id]);
                }
            }
            $data = array('is_status' => $request->is_status, 'comfirm_id' => Auth::user()->id, 'comfirm_date' => Carbon::now());
            $this->transferService->update($request->id, $data);
            return redirect()->back();
        }
        return redirect()->back()->withErrors(['msg' => 'Seri Numaraları Seçilmedi']);
    }

    protected
    function show(Request $request)
    {
        $data['transfer'] = $this->transferService->find($request->id);
        return view('module.transfer.show', $data);
    }

    public
    function getSerialList($stockCardId, $quantity, $color_id)
    {
        return StockCardMovement::select('serial_number')->where('stock_card_id', $stockCardId)->where('color_id', $color_id)->pluck('serial_number')->take($quantity);
    }
    
    /**
     * AJAX endpoint for incoming transfers - Vue.js için
     */
    public function getIncomingTransfersAjax(Request $request)
    {
        try {
            $query = Transfer::with(['main_seller', 'delivery_seller', 'user', 'confirm_user'])
                ->where('company_id', Auth::user()->company_id);

            // Filtreler
            if ($request->filled('stockName')) {
                $query->where('stocks', 'like', '%' . $request->stockName . '%');
            }
            
            if ($request->filled('serialNumber')) {
                $query->whereJsonContains('serial_list', $request->serialNumber);
            }
            
            if ($request->filled('seller') && $request->seller !== 'all') {
                $query->where('delivery_seller_id', $request->seller);
            }

            // Pagination
            $page = $request->get('page', 1);
            $perPage = 20;
            
            $transfers = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
            
            // Veriyi Vue.js için formatla
            $formattedData = $transfers->map(function ($item) {
                return [
                    'id' => $item->id,
                    'number' => $item->number,
                    'type' => $item->type,
                    'is_status' => $item->is_status,
                    'created_at' => $item->created_at,
                    'comfirm_date' => $item->comfirm_date,
                    'main_seller' => $item->main_seller,
                    'delivery_seller' => $item->delivery_seller,
                    'user' => $item->user,
                    'confirm_user' => $item->confirm_user
                ];
            });
            
            return response()->json([
                'transfers' => $formattedData,
                'pagination' => [
                    'current_page' => $transfers->currentPage(),
                    'last_page' => $transfers->lastPage(),
                    'per_page' => $transfers->perPage(),
                    'total' => $transfers->total(),
                    'from' => $transfers->firstItem(),
                    'to' => $transfers->lastItem()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gelen sevkler yüklenemedi: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * AJAX endpoint for outgoing transfers - Vue.js için
     */
    public function getOutgoingTransfersAjax(Request $request)
    {
        try {
            $query = Transfer::with(['main_seller', 'delivery_seller', 'user', 'confirm_user'])
                ->where('company_id', Auth::user()->company_id)
                ->where('main_seller_id', Auth::user()->seller_id);

            // Filtreler
            if ($request->filled('stockName')) {
                $query->where('stocks', 'like', '%' . $request->stockName . '%');
            }
            
            if ($request->filled('serialNumber')) {
                $query->whereJsonContains('serial_list', $request->serialNumber);
            }

            // Pagination
            $page = $request->get('page', 1);
            $perPage = 20;
            
            $transfers = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
            
            // Veriyi Vue.js için formatla
            $formattedData = $transfers->map(function ($item) {
                return [
                    'id' => $item->id,
                    'number' => $item->number,
                    'type' => $item->type,
                    'is_status' => $item->is_status,
                    'created_at' => $item->created_at,
                    'comfirm_date' => $item->comfirm_date,
                    'main_seller' => $item->main_seller,
                    'delivery_seller' => $item->delivery_seller,
                    'user' => $item->user,
                    'confirm_user' => $item->confirm_user
                ];
            });
            
            return response()->json([
                'transfers' => $formattedData,
                'pagination' => [
                    'current_page' => $transfers->currentPage(),
                    'last_page' => $transfers->lastPage(),
                    'per_page' => $transfers->perPage(),
                    'total' => $transfers->total(),
                    'from' => $transfers->firstItem(),
                    'to' => $transfers->lastItem()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Yapılan sevkler yüklenemedi: ' . $e->getMessage()], 500);
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
     * Get transfer details as JSON - Vue.js modal için
     */
    public function getTransferJson($id)
    {
        try {
            $transfer = Transfer::with(['main_seller', 'delivery_seller', 'user', 'confirm_user'])
                ->where('company_id', Auth::user()->company_id)
                ->findOrFail($id);
            
            // Format response
            $response = [
                'id' => $transfer->id,
                'number' => $transfer->number,
                'type' => $transfer->type,
                'is_status' => $transfer->is_status,
                'created_at' => $transfer->created_at,
                'comfirm_date' => $transfer->comfirm_date,
                'description' => $transfer->description,
                'serial_list' => $transfer->serial_list,
                'detail' => $transfer->detail,
                'main_seller' => $transfer->main_seller,
                'delivery_seller' => $transfer->delivery_seller,
                'user' => $transfer->user,
                'confirm_user' => $transfer->confirm_user
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transfer bulunamadı: ' . $e->getMessage()], 404);
        }
    }
}
