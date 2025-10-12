<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Sale;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\StockTraking;
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
use App\Services\User\UserService;
use App\Services\Version\VersionService;
use App\Services\Warehouse\WarehouseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StocktakingController extends Controller
{

    private SellerService $sellerService;
    private BrandService $brandService;
    private CategoryService $categoryService;
    private ColorService $colorService;
    private VersionService $versionService;


    public function __construct(
        SellerService   $sellerService,
        BrandService    $brandService,
        CategoryService $categoryService,
        ColorService    $colorService,
        VersionService  $versionService,
        UserService     $userService,
    )
    {
        $this->sellerService = $sellerService;
        $this->brandService = $brandService;
        $this->versionService = $versionService;
        $this->colorService = $colorService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
    }

    protected function index(Request $request)
    {
        $reportdata = [];
        $data['sellers'] = $this->sellerService->get();
        return view('module.report.stocktaking', $data);
    }

    public function stocktakingcheck(Request $request)
    {
        $xxx =  StockCardMovement::select('stock_card_id',DB::raw('SUM(quantity) AS sum_of_quantity'))->where('seller_id', $request->seller_id)->whereIn('serial_number', $request->sevkList)->groupBy('stock_card_id')->get();


        foreach ($xxx as $item)
        {
            $xxxss= StockCardMovement::select('stock_card_id',DB::raw('SUM(quantity) AS sum_of_quantity'))->where('stock_card_id',$item->stock_card_id)->where('seller_id', $request->seller_id)->get();
            $sell = Sale::where('stock_card_id',$item->stock_card_id)->where('seller_id', $request->seller_id)->count();
            $data[] = array(
            'name' =>$item->stock->name,
            'found_in_stock' => $item->sum_of_quantity,
            'realy_stock'=> $xxxss[0]->sum_of_quantity,
            'sell_stock'=> $sell,
            'remaining_stock'=> $xxxss[0]->sum_of_quantity - $item->sum_of_quantity,
            );
        }
        return response()->json($data,200);
    }

    public function stocktakingserialcheck(Request $request)
    {
        $stockcardmovement = StockCardMovement::where('serial_number', $request->serial)->where('seller_id', $request->seller_id)->first();
        if ($stockcardmovement) {
            $stocktraking = new StockTraking();
            $stocktraking->user_id = Auth::user()->id;
            $stocktraking->company_id = Auth::user()->company_id;
            $stocktraking->process_seller_id = $request->seller_id;
            $stocktraking->stock_seller_id = $stockcardmovement->seller_id;
            $stocktraking->serial_number = $request->serial;
            $stocktraking->stock_card_id = $stockcardmovement->stock_card_id;
            $stocktraking->save();

            return response()->json(['status' => 'success'], 200);
        } else {
            $stockcardmovementnew = StockCardMovement::where('serial_number', $request->serial)->first();
            $stocktraking = new StockTraking();
            $stocktraking->user_id = Auth::user()->id;
            $stocktraking->company_id = Auth::user()->company_id;
            $stocktraking->process_seller_id = $request->seller_id;
            $stocktraking->stock_seller_id = $stockcardmovementnew->seller_id;
            $stocktraking->serial_number = $request->serial;
            $stocktraking->stock_card_id = $stockcardmovementnew->stock_card_id;
            $stocktraking->save();
            return response()->json(['status' => 'failure', 'seller' => $stockcardmovementnew->seller->name], 200);
        }
    }


}
