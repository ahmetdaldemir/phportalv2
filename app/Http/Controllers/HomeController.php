<?php

namespace App\Http\Controllers;

use App\Helper\SearchHelper;
use App\Models\Category;
use App\Models\Currency;
use App\Models\DeletedAtSerialNumber;
use App\Models\Invoice;
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
use Illuminate\Support\Facades\Log;

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
        // Maksimum performans optimizasyonu - sadece gerekli verileri yükle
        $data['stocks'] = collect([]); // Boş collection - gerektiğinde AJAX ile yüklenecek
        $data['colors'] = collect([]); // Boş collection - gerektiğinde AJAX ile yüklenecek
        $data['reasons'] = collect([]); // Boş collection - gerektiğinde AJAX ile yüklenecek
        $data['stockTracks'] = [];
        $data['sellers'] = $this->sellerService->get(); // Sellers for dashboard filters

        return view('home', $data);
    }

    /**
     * AJAX endpoint for stocks - performans optimizasyonu
     */
    public function getStocksAjax()
    {
        try {
            $stocks = $this->stockCardService->get()->take(50); // Sadece 50 stok kartı
            return response()->json($stocks);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Stoklar yüklenemedi'], 500);
        }
    }

    /**
     * AJAX endpoint for colors - performans optimizasyonu
     */
    public function getColorsAjax()
    {
        try {
            $colors = $this->colorService->get();
            return response()->json($colors);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Renkler yüklenemedi'], 500);
        }
    }

    /**
     * AJAX endpoint for reasons - performans optimizasyonu
     */
    public function getReasonsAjax()
    {
        try {
            $reasons = $this->reasonService->get();
            return response()->json($reasons);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Nedenler yüklenemedi'], 500);
        }
    }

    /**
     * Stok kontrolü - Seri numarası veya barkod ile
     * Bayinin stoğunda var mı kontrol et
     */

    public function checkStock(Request $request)
    {
        try {
            $search = $request->input('search');
            
            if (empty($search)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen seri numarası veya barkod giriniz'
                ]);
            }

            $user = Auth::user();
            $searchInfo = SearchHelper::determineSearchType($search);
            
            if (!$searchInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz arama formatı'
                ]);
            }

            if ($searchInfo['type'] === 'barcode') {
                // Barkod araması - StockCard tablosunda ara
                $stockCard = StockCardMovement::where('company_id', $user->company_id)
                    ->where('barcode', $searchInfo['value'])
                    ->first();

                if ($stockCard) {
                    // Barkod bulundu, stokta var mı kontrol et
                    $hasStock =  $stockCard->where('type', 1)->exists();

                    if ($hasStock) {
                        return response()->json([
                            'success' => true,
                            'exists' => true,
                            'stock_id' => $stockCard->id,
                            'barcode' => $stockCard->barcode,
                            'stock_name' => $stockCard->name,
                            'search_type' => 'barcode',
                            'message' => 'Barkod ile stok bulundu'
                        ]);
                    } else {
                        return response()->json([
                            'success' => true,
                            'exists' => false,
                            'message' => 'Ürün kayıtlı ancak stoklarınızda bulunmuyor'
                        ]);
                    }
                }
            } else {
                // Seri numarası araması - StockCardMovement tablosunda ara
                $stockMovement = StockCardMovement::where('company_id', $user->company_id)
                    ->where('serial_number', $searchInfo['value'])
                    ->whereIn('type', ['1', '3', '4', '5']) // Stokta olan tipleri
                    ->with(['stock'])
                    ->first();

                if ($stockMovement) {
                    // Seri numarası bulundu
                    return response()->json([
                        'success' => true,
                        'exists' => true,
                        'stock_id' => $stockMovement->stock_card_id,
                        'serial_number' => $stockMovement->serial_number,
                        'stock_name' => $stockMovement->stock->name ?? 'Bilinmeyen',
                        'search_type' => 'serial',
                        'message' => 'Seri numarası ile stok bulundu'
                    ]);
                }
            }

            // Hiçbir şey bulunamadı
            return response()->json([
                'success' => true,
                'exists' => false,
                'search_type' => $searchInfo['type'],
                'message' => 'Bu ' . ($searchInfo['type'] === 'barcode' ? 'barkod' : 'seri numarası') . ' sistemde bulunamadı'
            ]);

        } catch (\Exception $e) {
            Log::error('Stock check error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Stok kontrolü sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dailyCalculate()
    {
        return DB::select('SELECT sales.id,sum(sales.customer_price) as total,sales.user_id,sales.type,users.name as username from sales
                    LEFT JOIN users on users.id = sales.user_id
                    where DATE(sales.created_at) = CURDATE()  and sales.company_id = ' . Auth::user()->company_id . '
                    GROUP BY sales.user_id,sales.type order by  total desc');
    }

    public function mounthCalculate()
    {
        return DB::select('SELECT sum(sales.customer_price) as total,sales.user_id,sales.type,users.name as username from sales
                    LEFT JOIN users on users.id = sales.user_id
                    where MONTH(sales.created_at) = MONTH(now())    and sales.company_id = ' . Auth::user()->company_id . '
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
                                                sales.type =  1 and sales.company_id = ' . Auth::user()->company_id . '
                                            GROUP BY sales.user_id');


    }

    public function stockTraking()
    {
        $data = [];
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
        $user = Cache::get('user_' . \auth()->user()->id);

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
        $dates = [];
        $alldata = [];
        $aasd = ['total' => []];
        
        $begin = new \DateTime(date('Y-m-d', strtotime($start)));
        $end = new \DateTime(date('Y-m-d', strtotime($end . '+1 days')));
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        
        foreach ($period as $dt) {
            $dates[] = strip_tags($dt->format("Y-m-d"));
            $alldata[] = DB::select("SELECT DATE(created_at) AS sadece_tarih, SUM(sale_price) AS veri_sayisi
                    FROM sales where created_at LIKE '" . strip_tags($dt->format("Y-m-d")) . "%'
                    and company_id = '" . Auth::user()->company_id . "'
                    GROUP BY DATE(created_at)");
        }

        foreach ($alldata as $item) {
            foreach ($item as $value) {
                $aasd['total'][] = round($value->veri_sayisi);
            }
        }
        
        return response()->json([
            'dates' => $dates,
            'alldata' => $aasd,
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
        $userss = Cache::get('user_' . \auth()->user()->id);

        $users = User::where('is_status', 1)->where('personel', 1)->where('company_id', auth()->user()->company_id)->get();


        // Tüm kullanıcıların aksesuar satışlarını ve diğer bilgilerini almak
        $usersWithSales = $users->map(function ($user) use ($request) {
            return [
                'name' => $user->name,
                'aksesuar' => Sale::totalMonthlySales($user->id, 2, 'daily'),
                'teknikservis' => Sale::getTechnical($user->id, $request, false),
                'kaplama' => Sale::getCover($user->id, $request, false),
                'telefon' => Sale::totalMonthlySales($user->id, 1, 'daily'),
            ];
        });

// Kullanıcıları aksesuar satışlarına göre azalan sırayla sıralamak
        $sortedUsers = $usersWithSales->sortByDesc('aksesuar');

// Sıralanmış kullanıcıları işlemek
        $data = ['users' => [], 'aksesuar' => [], 'teknikservis' => [], 'kaplama' => [], 'telefon' => []];

        foreach ($sortedUsers as $user) {
            $data['users'][] = $user['name'];
            $data['aksesuar'][] = $user['aksesuar'];
            $data['teknikservis'][] = $user['teknikservis'];
            $data['kaplama'][] = $user['kaplama'];
            $data['telefon'][] = $user['telefon'];
        }



         return [
            'data' => $data,
        ];
    }

    public function dashboardMounthNewReport(Request $request)
    {
        $data = [];
        $userss = Cache::get('user_' . \auth()->user()->id);

        $users = User::where('is_status', 1)->where('personel', 1)->where('company_id', auth()->user()->company_id)->get();



        // Tüm kullanıcıların aksesuar satışlarını ve diğer bilgilerini almak
        $usersWithSales = $users->map(function ($user) use ($request) {
            return [
                'name' => $user->name,
                'aksesuar' => Sale::totalMonthlySales($user->id, 2, 'monthly'),
                'teknikservis' => Sale::getTechnical($user->id, $request, true),
                'kaplama' => Sale::getCover($user->id, $request, true),
                'telefon' => Sale::totalMonthlySales($user->id, 1, 'monthly'),
            ];
        });

// Kullanıcıları aksesuar satışlarına göre azalan sırayla sıralamak
        $sortedUsers = $usersWithSales->sortByDesc('aksesuar');

// Sıralanmış kullanıcıları işlemek
        $data = ['users' => [], 'aksesuar' => [], 'teknikservis' => [], 'kaplama' => [], 'telefon' => []];

        foreach ($sortedUsers as $user) {
            $data['users'][] = $user['name'];
            $data['aksesuar'][] = $user['aksesuar'];
            $data['teknikservis'][] = $user['teknikservis'];
            $data['kaplama'][] = $user['kaplama'];
            $data['telefon'][] = $user['telefon'];
        }

 
        return [
            'data' => $data,
        ];
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
		left join ' . $add_db . ' u on u.id = ts.user_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '" and ts.delivery_staff = ' . $user . ' and ts.company_id = "' . Auth::user()->company_id . '" GROUP BY ts.user_id');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->user_id]['price'] = $item1->CTotal ?? NULL;
        }

        return $b[$user]['price'] ?? 0;
    }


    public function deleted_at_serial_number_store(Request $request)
    {
        DeletedAtSerialNumber::firstOrCreate(['serial_number' => $request->serial_number]);
        return redirect()->back();
    }
    
    /**
     * Personele göre satış grafiği verisi
     */
    public function getSalesByStaff(Request $request)
    {
        try {
            // Debug: Authentication check
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'debug' => [
                        'headers' => $request->headers->all(),
                        'session_id' => session()->getId(),
                    ]
                ], 401);
            }
            $period = $request->get('period', 'daily');
            $companyId = Auth::user()->company_id;
            
            if ($period === 'daily') {
                $date = $request->get('date', date('Y-m-d'));
                $sales = Invoice::select('staff_id', DB::raw('SUM(total_price) as total_sales'))
                    ->with('staff:id,name')
                    ->where('company_id', $companyId)
                    ->where('free_sale', 0)
                    ->where('type', 2)
                    ->whereDate('created_at', $date)
                    ->groupBy('staff_id')
                    ->get();
            } else {
                $month = $request->get('month', date('Y-m'));
                $sales = Invoice::select('staff_id', DB::raw('SUM(total_price) as total_sales'))
                    ->with('staff:id,name')
                    ->where('company_id', $companyId)
                    ->where('free_sale', 0)
                    ->where('type', 2)
                    ->whereYear('created_at', substr($month, 0, 4))
                    ->whereMonth('created_at', substr($month, 5, 2))
                    ->groupBy('staff_id')
                    ->get();
            }
            
            $formattedData = $sales->map(function($sale) {
                return [
                    'staff_id' => $sale->staff_id,
                    'staff_name' => $sale->staff->name ?? 'Bilinmiyor',
                    'total_sales' => $sale->total_sales
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Grafik verisi yüklenemedi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Stok devir hızı
     * Hesaplama: Stok girişinden satışa kadar geçen ortalama gün sayısı
     */
    /**
     * AI Analizi - Stok devir hızı yapay zeka insights
     */
    public function getStockTurnoverAI(Request $request)
    {
        try {
            $companyId = Auth::user()->company_id;
            $sellerId = $request->get('seller_id');
            
            $aiService = app(\App\Services\StockTurnoverAIService::class);
            $analysis = $aiService->analyzeStockPerformance($companyId, $sellerId);
            
            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI analiz hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'AI analizi yapılamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getStockTurnover(Request $request)
    {
        try {
            $companyId = Auth::user()->company_id;
            $days = $request->get('days', 90); // Son 90 gün
            $sellerId = $request->get('seller_id'); // Bayi filtresi
            
            // Base query
            $query = "
                SELECT 
                    sc.id,
                    sc.name as stock_name,
                    c.name as category,
                    sel.name as seller_name,
                    COUNT(s.id) as total_sold,
                    AVG(DATEDIFF(s.created_at, scm.created_at)) as avg_days_to_sell,
                    SUM(CASE WHEN scm_current.quantity > 0 AND scm_current.type = 1 THEN scm_current.quantity ELSE 0 END) as current_stock
                FROM sales s
                INNER JOIN stock_card_movements scm ON s.stock_card_movement_id = scm.id
                INNER JOIN stock_cards sc ON scm.stock_card_id = sc.id
                LEFT JOIN categories c ON sc.category_id = c.id
                LEFT JOIN sellers sel ON s.seller_id = sel.id
                LEFT JOIN stock_card_movements scm_current ON scm_current.stock_card_id = sc.id 
                    AND scm_current.company_id = ? 
                    AND scm_current.type = 1
                WHERE s.company_id = ?
                    AND s.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    AND scm.type = 1
            ";
            
            $params = [$companyId, $companyId, $days];
            
            // Bayi filtresi
            if ($sellerId) {
                $query .= " AND s.seller_id = ?";
                $params[] = $sellerId;
            }
            
            $query .= "
                GROUP BY sc.id, sc.name, c.name, sel.name
                HAVING avg_days_to_sell IS NOT NULL
                ORDER BY avg_days_to_sell DESC, total_sold DESC
                LIMIT 100
            ";
            
            $stockTurnover = DB::select($query, $params);
            
            // Verileri formatla
            $formattedData = collect($stockTurnover)->map(function($item) {
                return [
                    'id' => $item->id,
                    'stock_name' => $item->stock_name,
                    'category' => $item->category ?? 'Belirtilmedi',
                    'seller_name' => $item->seller_name ?? 'Belirsiz',
                    'total_sold' => $item->total_sold,
                    'turnover_rate' => $item->avg_days_to_sell > 0 ? round($item->avg_days_to_sell, 1) : 0,
                    'current_stock' => $item->current_stock ?? 0
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stok devir hızı hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok devir hızı hesaplanamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * AI Analiz Raporu - PDF Export
     */
    public function exportAIAnalysisPDF(Request $request)
    {
        try {
            $companyId = Auth::user()->company_id;
            $sellerId = $request->get('seller_id');
            
            $aiService = app(\App\Services\StockTurnoverAIService::class);
            $analysis = $aiService->analyzeStockPerformance($companyId, $sellerId);
            
            $exportService = app(\App\Services\AIReportExportService::class);
            $companyName = Auth::user()->company->name ?? 'Şirket';
            
            return $exportService->exportToPDF($analysis, $companyName);
            
        } catch (\Exception $e) {
            Log::error('PDF export hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'PDF oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * AI Analiz Raporu - Excel Export
     */
    public function exportAIAnalysisExcel(Request $request)
    {
        try {
            $companyId = Auth::user()->company_id;
            $sellerId = $request->get('seller_id');
            
            $aiService = app(\App\Services\StockTurnoverAIService::class);
            $analysis = $aiService->analyzeStockPerformance($companyId, $sellerId);
            
            $exportService = app(\App\Services\AIReportExportService::class);
            return $exportService->exportToExcel($analysis);
            
        } catch (\Exception $e) {
            Log::error('Excel export hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Excel oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * AI Analiz Raporu - JSON Export
     */
    public function exportAIAnalysisJSON(Request $request)
    {
        try {
            $companyId = Auth::user()->company_id;
            $sellerId = $request->get('seller_id');
            
            $aiService = app(\App\Services\StockTurnoverAIService::class);
            $analysis = $aiService->analyzeStockPerformance($companyId, $sellerId);
            
            $exportService = app(\App\Services\AIReportExportService::class);
            return $exportService->exportToJSON($analysis);
            
        } catch (\Exception $e) {
            Log::error('JSON export hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'JSON oluşturulamadı: ' . $e->getMessage()
            ], 500);
        }
    }

}




