<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\Transfer;
use App\Models\User;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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
        $this->middleware('auth');


    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $data['stocks'] = $this->stockCardService->get();
        $data['sellers'] = $this->sellerService->get();
        $data['colors'] = $this->colorService->get();
        $data['reasons'] = $this->reasonService->get();
        $data['brands'] = $this->brandService->get();
        $data['versions'] = $this->versionService->get();
        $data['categories'] = $this->categoryService->get();
        $data['transfers'] = Transfer::where('delivery_seller_id', Auth::user()->seller_id)->get();
        $data['stockTracks'] = $this->stockTraking();

        //$data['salesDaily'] = $this->dailyCalculate();
        $data['salesMonth'] = $this->mounthCalculate();


        return view('home', $data);
    }


    public function dailyCalculate()
    {
        return DB::select('SELECT sales.id,sum(sales.customer_price) as total,sales.user_id,sales.type,users.name as username from sales
                    LEFT JOIN users on users.id = sales.user_id
                    where DATE(sales.created_at) = CURDATE() and company_id = '.Auth::user()->company_id.'
                    GROUP BY sales.user_id,sales.type order by  total desc');
    }

    public function mounthCalculate()
    {
        return DB::select('SELECT sum(sales.customer_price) as total,sales.user_id,sales.type,users.name as username from sales
                    LEFT JOIN users on users.id = sales.user_id
                    where MONTH(sales.created_at) = MONTH(now()) and company_id = '.Auth::user()->company_id.'
                    GROUP BY sales.user_id,sales.type order by  total desc');


    }

    public function monthPhone()
    {
        return DB::select('SELECT sum(sales.customer_price)as total,users.name as username,sales.user_id,categories.name as categpryName from
                                            sales
                                            LEFT JOIN stock_cards on stock_cards.id = sales.stock_card_id
                                            INNER JOIN categories on stock_cards.category_id = categories.id
                                            INNER JOIN users on sales.user_id = users.id
                                            where MONTH(sales.created_at) = MONTH(now()) and
                                                sales.type =  1 and company_id = '.Auth::user()->company_id.'
                                            GROUP BY sales.user_id');


    }

    public function stockTraking()
    {
        $data = [];

        /*
        if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('Depo Sorumlusu')) {
            $stockcards = StockCard::where("tracking", 1)->get();
            foreach ($stockcards as $item) {
                if ($item->quantity() <= $item->tracking_quantity) {
                    $data[] = array(
                        'id' => $item->id,
                        'name' => $item->name,
                        'quantity' => $item->quantity(),
                        'brand' => $item->brand->name,
                        'version' => $item->version(),
                        'tracking_quantity' => $item->tracking_quantity,
                    );
                }
            }
        }else{
            $stockcards = DB::select("SELECT sum(scm.quantity),sc.`name`,brand_id,sc.version_id,scm.tracking_quantity
            from stock_cards as sc
            left JOIN stock_card_movements as scm  on sc.id = scm.stock_card_id
            where scm.tracking_quantity != 0 and sc.tracking = '1' and scm.type=1 and scmçseller_id = Auth::user()->seller_id
            GROUP BY scm.color_id");

                foreach ($stockcards as $item) {
                    if ($item->quantity() <= $item->tracking_quantity) {
                        $data[] = array(
                            'id' => $item->stock->id,
                            'name' => $item->stock->name,
                            'quantity' => $item->stock->quantity(),
                            'brand' => $item->stock->brand->name,
                            'version' => $item->stock->version(),
                            'tracking_quantity' => $item->tracking_quantity,
                        );
                    }
            }
        }
*/
        $stockcards = StockCard::where("tracking", 1)->get();
        foreach ($stockcards as $item) {
            if ($item->quantity() <= $item->tracking_quantity) {
                $data[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity(),
                    'brand' => $item->brand->name,
                    'version' => $item->version(),
                    'tracking_quantity' => $item->tracking_quantity,
                );
            }
        }
        return $data;
    }


    public function getParentList($category_id = 0)
    {
        $user = Cache::get('user_'.\auth()->user()->id);

        $data = null;
        $categories = Category::where('company_id', $user->company_id)->where('parent_id', $category_id)->get();
//dd($categories);
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'list' => $this->getParentList($category->id)
            ];
        }
        return $data;
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

    public function dashboardReport()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();
        $cover = [];
        $begin = new \DateTime(date('Y-m-d', strtotime($start)));
        $end = new \DateTime(date('Y-m-d', strtotime($end . '+1 days')));
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        foreach ($period as $dt) {

            $dates[] = strip_tags($dt->format("Y-m-d"));
            $alldata[] = DB::select("SELECT DATE(created_at) AS sadece_tarih, SUM(sale_price) AS veri_sayisi
                    FROM sales where created_at LIKE '" . strip_tags($dt->format("Y-m-d")) . "%'
                    GROUP BY DATE(created_at)");
        }

$data = [];
        $aksesuar = DB::table('sales')->where('type', 2)->whereDate('sales.created_at', Carbon::today())
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->groupBy('sales.user_id')
            ->get(['users.name as username', DB::raw('sum(sales.customer_price) as total')])->pluck('total', 'username');

        foreach ($aksesuar as $key => $value) {
            $data['username'][] = '' . $key . '-' . round($value) . ' ₺';
            $data['total'][] = round($value);
        }

        $phone = DB::table('sales')->where('type', 1)->whereDate('sales.created_at', Carbon::today())
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->groupBy('sales.user_id')
            ->get(['users.name as username', DB::raw('sum(sales.sale_price) as total')])->pluck('total', 'username');
        $phones = [];
        if ($phone) {
            foreach ($phone as $key => $value) {
                $phones['username'][] = '' . $key . '-' . round($value) . ' ₺';
                $phones['total'][] = round($value) ?? 0;
            }
        } else {
            $phones['username'][] = 'Boş';
            $phones['total'][] = 0;
        }


        foreach ($alldata as $item) {
            {
                foreach ($item as $value) {
                    $aasd['total'][] = round($value->veri_sayisi);
                }
            }
        }

$cover=[];
        $kaplama = DB::table('sales')->where('type', 4)->whereDate('sales.created_at', Carbon::today())
            ->join('users', 'users.id', '=', 'sales.technical_service_person_id')
            ->groupBy('sales.technical_service_person_id')
            ->get(['users.name as username', DB::raw('sum(sales.sale_price) as total')])->pluck('total', 'username');

        foreach ($kaplama as $key => $value) {
            $cover['username'][] = '' . $key . '-' . round($value) . ' ₺';
            $cover['total'][] = round($value);
        }

$technicals = [];
        $technical = DB::table('sales')->where('type', 3)->whereDate('sales.created_at', Carbon::today())
            ->join('users', 'users.id', '=', 'sales.technical_service_person_id')
            ->groupBy('sales.technical_service_person_id')
            ->get(['users.name as username', DB::raw('sum(sales.sale_price) as total')])->pluck('total', 'username');

        foreach ($technical as $key => $value) {
            $technicals['username'][] = '' . $key . '-' . round($value) . ' ₺';
            $technicals['total'][] = round($value);
        }

        arsort($data);

        return response()->json([
            'dates' => $dates,
            'aksesuar' => $data,
            'phone' => $phones,
            'alldata' => $aasd,
            'cover' => $cover,
            'technical' => $technicals,
        ], 200);

    }

    public function dailyTypeCalculate($type, $dates)
    {
        dd($type, $dates);
        return DB::select('SELECT sales.id,sum(sales.customer_price) as total,sales.user_id,sales.type,users.name as username from sales
                    LEFT JOIN users on users.id = sales.user_id
                    where created_at IN (' . $dates . ') and sales.type=' . $type . '
                    GROUP BY sales.user_id,sales.type order by  total desc');
    }


    public function dashboardNewReport(Request $request)
    {
        $data = [];
        $userss = Cache::get('user_'.\auth()->user()->id);

        $users = User::where('is_status',1)->where('personel',1)->where('company_id', $userss->company_id)->get();
        foreach ($users as $user) {

            $data['users'][] = $user->name;
            $data['aksesuar'][] = $this->getAccesuaries($user->id);
            $data['teknikservis'][] = $this->getTechnical($user->id, $request);
            $data['kaplama'][] = $this->getCover($user->id,$request);
            $data['telefon'][] = $this->getPhone($user->id);
        }


        return [
            'data' => $data,
        ];
    }

    public function getAccesuaries($user)
    {
        return Sale::where('user_id', $user)->where('type', 2)->whereDate('created_at', Carbon::today())->sum('sale_price') ?? 0;
    }

    public function getPhone($user)
    {
        return Sale::where('user_id', $user)->where('type', 1)->whereDate('created_at', Carbon::today())->sum('sale_price') ?? 0;
    }


    public function getTechnical($user, $request)
    {
        $personData = [];
        $date1 = Carbon::today()->format('Y-m-d H:i:s');
        $date2 = Carbon::today()->format('Y-m-d H:i:s');

        if (!$request->filled('person')) {
            $add_db = 'users';
            $add_db_id = 'user_id';
        } else {

            $add_db = 'sellers';
            $add_db_id = 'seller_id';
        }

        $technicalReport = DB::select('SELECT u.name as userName,ts.' . $add_db_id . ',sum(salesproduct.base_cost_price) as bTotal,ts.user_id from technical_services ts
		left join ' . $add_db . ' u on u.id = ts.' . $add_db_id . '
	    left join (select tsp.technical_service_id,s.base_cost_price from sales s left join technical_service_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id) salesproduct on salesproduct.technical_service_id = ts.id
		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.' . $add_db_id . '');

        $a = [];
        $b = [];
        foreach ($technicalReport as $item) {
            $a[$item->{$add_db_id}]['price'] = $item->bTotal ?? NULL;
        }

        /*

        $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.'.$add_db_id.' from technical_custom_services ts
		left join '.$add_db.' u on u.id = ts.'.$add_db_id.'
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.'.$add_db_id.'');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->{$add_db_id}]['name'] = $item1->Username ?? NULL;
            $b[$item1->{$add_db_id}]['price'] = $item1->CTotal ?? NULL;
        }
*/
        return $b[$user]['price'] ?? 0;
     }


    public function getCover($user, $request)
    {
        $personData = [];
        $date1 = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $date2 = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');

        $add_db = 'users';
        $add_db_id = 'user_id';
      /*  if (!$request->filled('person')) {
            $add_db = 'users';
            $add_db_id = 'user_id';
        } else {

            $add_db = 'sellers';
            $add_db_id = 'seller_id';
        }*/

        $b = [];




        $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.user_id from technical_custom_services ts
		left join '.$add_db.' u on u.id = ts.user_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '" and ts.user_id = '.$user.' GROUP BY ts.user_id');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->user_id]['price'] = $item1->CTotal ?? NULL;
        }

        return $b[$user]['price'] ?? 0;
    }

}




