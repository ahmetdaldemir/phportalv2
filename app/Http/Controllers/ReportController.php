<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\User;
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
use Illuminate\Support\Collection;
use App\Services\Modules\Report;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{

    private SellerService $sellerService;
    private BrandService $brandService;
    private CategoryService $categoryService;
    private ColorService $colorService;
    private VersionService $versionService;
    private UserService $userService;


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
        $filters = $this->prepareFilters($request);

        $data = [
            'sellers' => $this->sellerService->get(),
            'brands' => $this->brandService->get(),
            'colors' => $this->colorService->get(),
            'users' => $this->userService->get()->where('is_status', 1)->where('personel', 1),
            'categories' => $this->getCategoryPathList(),
            'types' => Sale::STATUS,
            'sendData' => $this->buildSendData($request, $filters),
        ];


        $data['report'] = $this
            ->buildReportQuery($filters)
            ->orderByDesc('id')
            ->get();

        return view('module.report.index', $data);
    }

    /**
     * JSON report data for Vue list
     */
    public function data(Request $request)
    {
        $filters = $this->prepareFilters($request);
        $collection = $this->buildReportQuery($filters)
            ->orderByDesc('id')
            ->get();

        $payload = $this->transformReportCollection($collection);

        return response()->json([
            'success' => true,
            'items' => $payload['items'],
            'totals' => $payload['totals'],
        ]);
    }

    /**
     * Export filtered report as CSV (Excel compatible)
     */
    public function export(Request $request)
    {
        $filters = $this->prepareFilters($request);
        $collection = $this->buildReportQuery($filters)
            ->orderByDesc('id')
            ->get();

        $payload = $this->transformReportCollection($collection);
        $filename = 'report-' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payload) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Personel',
                'Ürün Adı',
                'Kategori',
                'Seri',
                'Tip',
                'Marka / Model',
                'Alış Fiyatı',
                'Satış Fiyatı',
                'Kar',
                'Tarih',
            ], ';');

            foreach ($payload['items'] as $item) {
                fputcsv($handle, [
                    $item['person'],
                    $item['product_name'],
                    $item['category_path'],
                    $item['serial'],
                    $item['type_label'],
                    trim($item['brand'] . ' / ' . $item['model'], ' / '),
                    $item['cost_price'],
                    $item['sale_price'],
                    $item['profit'],
                    $item['created_at_formatted'],
                ], ';');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Hazır filtreleri hazırla ve normalize et
     */
    private function prepareFilters(Request $request): array
    {
        $hasCustomDate = $request->filled('date1') && $request->filled('date2');

        if ($hasCustomDate) {
            $dateFrom = $this->normalizeDate($request->input('date1'));
            $dateTo = $this->normalizeDate($request->input('date2'), true);

            if (!$dateFrom || !$dateTo) {
                $hasCustomDate = false;
            }
        }

        if (!$hasCustomDate) {
            $dateFrom = Carbon::now()->startOfMonth();
            $dateTo = Carbon::now()->endOfMonth();
        }

        return [
            'company_id' => Auth::user()->company_id ?? 1,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'has_custom_date' => $hasCustomDate,
            'seller_id' => $request->filled('seller') ? (int)$request->input('seller') : null,
            'brand_id' => $request->filled('brand') ? (int)$request->input('brand') : null,
            'category' => $request->filled('category') ? (int)$request->input('category') : null,
            'technical_person_id' => $request->filled('technical_person') ? (int)$request->input('technical_person') : null,
            'sales_person_id' => $request->filled('sales_person') ? (int)$request->input('sales_person') : null,
        ];
    }

    /**
     * Rapor sorgusunu hazırla
     */
    private function buildReportQuery(array $filters)
    {
        $query = Sale::query()
            ->with($this->reportRelations())
            ->where('company_id', $filters['company_id'])
            ->when(
                $filters['date_from'] && $filters['date_to'],
                fn($q) => $q->whereBetween('created_at', [$filters['date_from'], $filters['date_to']])
            )
            ->when(
                $filters['seller_id'],
                fn($q, $sellerId) => $q->where('seller_id', $sellerId)
            )
            ->when(
                $filters['brand_id'],
                fn($q, $brandId) => $q->whereHas('stock_card', function ($brandQuery) use ($brandId) {
                    $brandQuery->where('brand_id', $brandId);
                })
            )
            ->when(
                $filters['category'],
                fn($q, $category) => $q->where('type', $category)
            )
            ->when(
                $filters['technical_person_id'],
                fn($q, $technicalId) => $q->where('technical_service_person_id', $technicalId)
            )
            ->when(
                $filters['sales_person_id'],
                fn($q, $salesPersonId) => $q->where('user_id', $salesPersonId)
            );

        return $query;
    }

    /**
     * View'da kullanılacak filtre değerleri
     */
    private function buildSendData(Request $request, array $filters): object
    {
        return (object)[
            'date1' => $filters['has_custom_date']
                ? $request->input('date1')
                : $this->formatDateForInput($filters['date_from']),
            'date2' => $filters['has_custom_date']
                ? $request->input('date2')
                : $this->formatDateForInput($filters['date_to']),
            'brand' => $request->input('brand'),
            'seller' => $request->input('seller'),
            'category' => $request->input('category'),
            'sales_person' => $request->input('sales_person'),
            'technical_person' => $request->input('technical_person'),
        ];
    }

    /**
     * Tarih formatını normalize et
     */
    private function normalizeDate(?string $value, bool $endOfDay = false): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('d-m-Y', $value);
        } catch (\Exception $exception) {
            try {
                $date = Carbon::parse($value);
            } catch (\Exception $exception) {
                return null;
            }
        }

        return $endOfDay ? $date->endOfDay() : $date->startOfDay();
    }

    /**
     * Tarihi input formatında döndür
     */
    private function formatDateForInput(?Carbon $date): ?string
    {
        return $date ? $date->format('d-m-Y') : null;
    }

    /**
     * Rapor sayfasında ihtiyaç duyulan ilişkiler
     */
    private function reportRelations(): array
    {
        return [
            'user:id,name',
            'seller:id,name',
            'phone.brand',
            'phone.version',
            'stock_card.brand',
            'stock_card.category.parent',
            'stock_card_movement',
            'stock_card_movement.stock.category.parent',
        ];
    }

    /**
     * Convert sales collection into payload for JSON / export
     */
    private function transformReportCollection(Collection $sales): array
    {
        $totals = [
            'totalSale' => 0,
            'totalCostPrice' => 0,
            'totalBaseCostPrice' => 0,
            'totalProfit' => 0,
            'itemsCount' => $sales->count(),
        ];

        $items = $sales->map(function (Sale $sale) use (&$totals) {
            $isPhone = (int)$sale->type === 1;
            $salePrice = (float)($sale->sale_price ?? 0);
            $stockCard = $sale->stock_card;

            if ($isPhone) {
                $costPrice = (float)($sale->phone->cost_price ?? 0);
                $baseCostPrice = (float)($sale->phone->cost_price ?? 0);
                $brandLabel = $sale->phone->brand->name ?? 'Bulunamadı';
                $versionLabel = $sale->phone->version->name ?? '';
                $productName = $sale->phone->name ?? ($sale->phone->brand->name ?? 'Ürün');

                if (!$stockCard && $sale->stock_card_movement && $sale->stock_card_movement->stock) {
                    $stockCard = $sale->stock_card_movement->stock;
                }
            } else {
                $movement = $sale->stock_card_movement;
                $stockCard = $stockCard ?: $sale->stock_card;
                $costPrice = (float)($movement->cost_price ?? 0);
                $baseCostPrice = (float)($movement->base_cost_price ?? 0);
                $brandLabel = $stockCard->brand->name ?? 'Bulunamadı';
                $versionLabel = '';
                $productName = $stockCard->name ?? $brandLabel;

                if ($stockCard && method_exists($stockCard, 'versionNames')) {
                    $versionLabel = $stockCard->versionNames() ?? '';
                }
            }

            $profit = $salePrice - $costPrice;
            $categoryPath = $this->buildCategoryPath($stockCard);

            $totals['totalSale'] += $salePrice;
            $totals['totalCostPrice'] += $costPrice;
            $totals['totalBaseCostPrice'] += $baseCostPrice;
            $totals['totalProfit'] += $profit;

            return [
                'id' => $sale->id,
                'person' => $sale->user->name ?? 'Personel Yok',
                'serial' => $sale->serial ?? '—',
                'product_name' => $productName,
                'category_path' => $categoryPath,
                'type_label' => $sale->statusName() ?? 'Bilinmiyor',
                'brand' => $brandLabel,
                'model' => $versionLabel ?: 'Model bilgisi yok',
                'cost_price' => round($costPrice, 2),
                'base_cost_price' => round($baseCostPrice, 2),
                'sale_price' => round($salePrice, 2),
                'profit' => round($profit, 2),
                'created_at' => optional($sale->created_at)->toDateTimeString(),
                'created_at_date' => optional($sale->created_at)->format('d M Y'),
                'created_at_time' => optional($sale->created_at)->format('H:i'),
                'created_at_formatted' => optional($sale->created_at)->format('d M Y H:i'),
            ];
        })->values()->all();

        return [
            'items' => $items,
            'totals' => $totals,
        ];
    }

    private function buildCategoryPath(?StockCard $stockCard): string
    {
        if (!$stockCard || !$stockCard->category) {
            return '-';
        }

        $segments = [];
        $category = $stockCard->category;
        $safetyCounter = 0;

        while ($category && $safetyCounter < 10) {
            $segments[] = $category->name;

            if (!$category->relationLoaded('parent')) {
                $category->load('parent');
            }

            $category = $category->parent;
            $safetyCounter++;
        }

        return implode(' / ', array_reverse($segments));
    }

    protected function newReport(Request $request)
    {
        $reportdata = [];
        $data['sellers'] = $this->sellerService->get();
        $data['brands'] = $this->brandService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['categories'] = $this->getCategoryPathList();

        $query = Sale::where('company_id', Auth::user()->company_id)->whereNotNull('technical_service_person_id')->with(['phone', 'technical', 'stock_card_list']);
        if (isset($request->date1)) {
            if ($request->date1 == $request->date2) {
                $date1 = Carbon::parse($request->date1 . " 00:00:00")->format('Y-m-d H:i:s');
                $date2 = Carbon::parse($request->date2 . " 23:59:59")->format('Y-m-d H:i:s');
                $query->whereBetween('created_at', [$date1, $date2]);
            } else {
                $query->whereDate('created_at', '>=', Carbon::parse($request->date1)->format('Y-m-d') . ' 00:00:00')->whereDate('created_at', '<=', Carbon::parse($request->date2)->format('Y-m-d') . ' 23:59:59');
            }
        } else {
            $query->whereMonth('created_at', Carbon::now()->month);
        }


        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }


        if ($request->filled('brand')) {
            $query->whereHas('stock_card', function ($q) use ($request) {
                $q->where('brand_id', '=', $request->brand);
            });
        }


        if ($request->filled('category')) {
            $query->where('type', $request->category);
        }

        if ($request->filled('technical_person')) {
            $query->where('technical_service_person_id', $request->technical_person);
        }
        if ($request->filled('sales_person')) {
            $query->where('user_id', $request->sales_person);
        }

        $data['seachType'] = 'other';
        if ($request->filled('category')) {
            $data['seachType'] = 'other';
            $query->where('type', $request->category);
        }

        $data['report'] = $query->orderby('id', 'desc')->groupBy('technical_service_person_id')->sum('sale_price');
        $data['types'] = Sale::STATUS;
        if ($request) {
            $data['sendData'] = $request;
        }

        return view('module.report.newReport', $data);
    }

    public function personelsellerreport(Request $request)
    {
        (object)$personReport = "";
        (object)$deliveryReport = "";
        (object)$query = "";
        $added = '';
        $sql = 'and type NOT IN (3,4)';
        if ($request->filled('person')) {
            $added = ',user_id';
            $date1 = Carbon::parse(trim($request->date1) . " 00:00:00")->format('Y-m-d H:i:s');
            $date2 = Carbon::parse(trim($request->date2) . " 23:59:59")->format('Y-m-d H:i:s');
            $personReport = DB::select('SELECT sum(sale_price) as toplamTutar,type ' . $added . ',SUM(base_cost_price) as totalCost  from sales  WHERE  technical_service_person_id IS NULL and  created_at BETWEEN "' . $date1 . '" and   "' . $date2 . '" and user_id=' . $request->person . ' GROUP BY type');

            $deliveryReport = DB::select('SELECT sum(sale_price) as toplamTutar,type ' . $added . ',SUM(base_cost_price) as totalCost  from sales  WHERE  delivery_personnel=' . $request->person . ' and  created_at BETWEEN "' . $date1 . '" and   "' . $date2 . '" ' . $sql . ' GROUP BY type');

        }


        if ($request->filled('seller')) {
            $added = ',seller_id';
            $sql = 'and type NOT IN (3,4) and  seller_id=' . $request->seller . '';

            if ($request->filled('date1') && $request->filled('date2')) {
                $date1 = Carbon::parse($request->date1 . " 00:00:00")->format('Y-m-d H:i:s');
                $date2 = Carbon::parse($request->date2 . " 23:59:59")->format('Y-m-d H:i:s');
                $query = DB::select('SELECT sum(customer_price) as toplamTutar,type ' . $added . ',SUM(base_cost_price) as totalCost  from sales  WHERE (created_at BETWEEN "' . $date1 . '" and   "' . $date2 . '") ' . $sql . ' GROUP BY type');
            } else {
                $date1 = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
                $date2 = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
                $query = DB::select('SELECT sum(customer_price) as toplamTutar,type ' . $added . ',SUM(base_cost_price) as totalCost  from sales WHERE (created_at BETWEEN "' . $date1 . '" and "' . $date2 . '") ' . $sql . ' GROUP BY type');
            }
        }

        $kaplama = $this->coverReport($request);
        $data['sellers'] = $this->sellerService->get();
        $data['users'] = $this->userService->get();
        $data['personReport'] = $personReport;

        $data['deliveryReport'] = $deliveryReport;
        $data['query'] = $query;
        $data['kaplama'] = $kaplama;
        $data['sendData'] = "";
        if ($request) {
            $data['sendData'] = $request;
        }

        return view('module.report.personelsellerreport', $data);
    }

    public function personelsellernewreport(Request $request)
    {
        (object)$personReport = "";
        (object)$technicalReport = "";
        (object)$technicalCustomReport = "";
        (object)$deliveryReport = "";
        (object)$query = "";
        $added = '';
        $sql = '';
        $date1 = Carbon::parse(trim($request->date1) . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse(trim($request->date2) . " 23:59:59")->format('Y-m-d H:i:s');

        if ($request->types == 'personel') {
            $technicalReport = DB::select('	SELECT u.name as userName,technical_person,sum(salesproduct.base_cost_price) as bTotal from technical_services ts
		left join users u on u.id = ts.technical_person
	    left join (select tsp.technical_service_id,s.base_cost_price from sales s left join technical_service_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id) salesproduct on salesproduct.technical_service_id = ts.id
		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY technical_person

		');

            $a = [];
            $b = [];
            $personData = [];
            foreach ($technicalReport as $item) {
                $a[$item->technical_person]['name'] = $item->userName ?? NULL;
                $a[$item->technical_person]['price'] = $item->bTotal ?? NULL;
            }


            $technicalReport1 = DB::select('	SELECT u.name as Username,sum(ts.customer_price) as CTotal,technical_person from technical_services ts
		left join users u on u.id = ts.technical_person
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY technical_person

		');

            foreach ($technicalReport1 as $item1) {
                $b[$item1->technical_person]['name'] = $item1->Username ?? NULL;
                $b[$item1->technical_person]['price'] = $item1->CTotal ?? NULL;
            }

            $persons = User::where('is_status', 1)->get();
            foreach ($persons as $person) {
                if (isset($a[$person->id])) {
                    $personData[$person->id]['name'] = $a[$person->id]['name'] ?? 'Bulunamadı';
                    $personData[$person->id]['CTotal'] = $b[$person->id]['price'] ?? 0;
                    $personData[$person->id]['bTotal'] = $a[$person->id]['price'] ?? 0;
                }

            }
        } else {
            $technicalReport = DB::select('SELECT u.name as userName,technical_person,sum(salesproduct.base_cost_price) as bTotal,ts.seller_id from technical_services ts
		left join sellers u on u.id = ts.seller_id
	    left join (select tsp.technical_service_id,s.base_cost_price from sales s left join technical_service_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id) salesproduct on salesproduct.technical_service_id = ts.id
		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.seller_id');

            $a = [];
            $b = [];
            $personData = [];
            foreach ($technicalReport as $item) {
                $a[$item->seller_id]['name'] = $item->userName ?? NULL;
                $a[$item->seller_id]['price'] = $item->bTotal ?? NULL;
            }


            $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.seller_id from technical_services ts
		left join sellers u on u.id = ts.seller_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.seller_id');

            foreach ($technicalReport1 as $item1) {
                $b[$item1->seller_id]['name'] = $item1->Username ?? NULL;
                $b[$item1->seller_id]['price'] = $item1->CTotal ?? NULL;
            }

            $sellers = Seller::where('company_id', 1)->get();
            foreach ($sellers as $seller) {
                if (isset($a[$seller->id])) {
                    $personData[$seller->id]['name'] = $a[$seller->id]['name'] ?? 'Bulunamadı';
                    $personData[$seller->id]['CTotal'] = $b[$seller->id]['price'] ?? 0;
                    $personData[$seller->id]['bTotal'] = $a[$seller->id]['price'] ?? 0;
                }

            }
        }


        $data['sellers'] = $this->sellerService->get();
        $data['users'] = $this->userService->get();
        $data['personReport'] = $personReport;
        $data['technicalReport'] = $personData;
        $data['deliveryReport'] = $deliveryReport;
        $data['technicalCustomReport'] = $technicalCustomReport;

        $data['query'] = $query;
        $data['sendData'] = "";
        if ($request) {
            $data['sendData'] = $request;
        }


        return view('module.report.personelsellernewreport', $data);

    }

    public function technicalCustomReport(Request $request)
    {
        (object)$personReport = "";
        (object)$technicalReport = "";
        (object)$technicalCustomReport = "";
        (object)$deliveryReport = "";
        (object)$query = "";
        $added = '';
        $sql = '';
        $date1 = Carbon::parse(trim($request->date1) . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse(trim($request->date2) . " 23:59:59")->format('Y-m-d H:i:s');


        $technicalReport = DB::select('SELECT
                                            u.name AS userName,ts.delivery_staff,
                                            SUM(salesproduct.base_cost_price) AS totalCost
                                        FROM
                                            technical_custom_services ts
                                        LEFT JOIN
                                            (
                                                SELECT
                                                    tsp.technical_custom_id,
                                                    s.base_cost_price
                                                FROM
                                                    sales s
                                                LEFT JOIN
                                                    technical_custom_products tsp ON s.stock_card_movement_id = tsp.stock_card_movement_id
                                            ) AS salesproduct ON salesproduct.technical_custom_id = ts.id
                                        LEFT JOIN
                                            users u ON u.id = ts.delivery_staff
                                        WHERE
                                            ts.payment_status = 1
                                            AND ts.updated_at BETWEEN  "' . $date1 . '" and "' . $date2 . '"
                                        GROUP BY
                                           ts.delivery_staff');

        $a = [];
        $b = [];
        $personData = [];
        foreach ($technicalReport as $item) {
            $a[$item->delivery_staff]['name'] = $item->userName ?? NULL;
            $a[$item->delivery_staff]['price'] = $item->totalCost ?? NULL;
        }


        $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.delivery_staff from technical_custom_services ts
		left join users u on u.id = ts.user_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.delivery_staff');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->delivery_staff]['price'] = $item1->CTotal ?? NULL;
        }

        $users = User::where('company_id', 1)->get();

        foreach ($users as $user) {
            if (isset($a[$user->id])) {
                $personData[$user->id]['name'] = $a[$user->id]['name'] ?? 'Bulunamadı';
                $personData[$user->id]['CTotal'] = $b[$user->id]['price'] ?? 0;
                $personData[$user->id]['bTotal'] = $a[$user->id]['price'] ?? 0;
            }

        }


        $data['sellers'] = $this->sellerService->get();
        $data['users'] = $this->userService->get();
        $data['personReport'] = $personReport;
        $data['technicalReport'] = [];
        $data['technicalCustomReport'] = [];
        $data['technicalCustomReportADB'] = $personData;
        $data['deliveryReport'] = $deliveryReport;
        $data['query'] = $query;
        $data['sendData'] = "";
        if ($request) {
            $data['sendData'] = $request;
        }


        return view('module.report.personelsellerreport', $data);

    }

    public function coverReport(Request $request)
    {

        $personData = [];
        $date1 = Carbon::parse(trim($request->date1) . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse(trim($request->date2) . " 23:59:59")->format('Y-m-d H:i:s');

        if ($request->filled('person')) {
            $technicalReport = DB::select('SELECT u.name as userName,ts.user_id,sum(salesproduct.base_cost_price) as bTotal,ts.user_id from technical_custom_services ts
		left join users u on u.id = ts.user_id
	    left join (select tsp.technical_custom_id,s.base_cost_price from sales s left join technical_custom_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id) salesproduct on salesproduct.technical_custom_id = ts.id
		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.user_id');

            $a = [];
            $b = [];
            foreach ($technicalReport as $item) {
                $a[$item->user_id]['name'] = $item->userName ?? NULL;
                $a[$item->user_id]['price'] = $item->bTotal ?? NULL;
            }


            $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.user_id from technical_custom_services ts
		left join users u on u.id = ts.user_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.user_id');

            foreach ($technicalReport1 as $item1) {
                $b[$item1->user_id]['name'] = $item1->Username ?? NULL;
                $b[$item1->user_id]['price'] = $item1->CTotal ?? NULL;
            }

            $users = User::where('company_id', 1)->get();
            foreach ($users as $user) {
                if (isset($a[$user->id])) {
                    $personData[$user->id]['name'] = $a[$user->id]['name'] ?? 'Bulunamadı';
                    $personData[$user->id]['CTotal'] = $b[$user->id]['price'] ?? 0;
                    $personData[$user->id]['bTotal'] = $a[$user->id]['price'] ?? 0;
                }

            }
        }else{
            if(isset($request->seller))
            {
                $technicalReport = DB::select('SELECT u.name as userName,ts.seller_id,sum(salesproduct.base_cost_price) as bTotal,ts.seller_id from technical_custom_services ts
		left join sellers u on u.id = ts.seller_id
	    left join (select tsp.technical_custom_id,s.base_cost_price from sales s left join technical_custom_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id) salesproduct on salesproduct.technical_custom_id = ts.id
		where ts.payment_status = 1 and  ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.seller_id');

                $a = [];
                $b = [];
                foreach ($technicalReport as $item) {
                    $a[$item->seller_id]['name'] = $item->userName ?? NULL;
                    $a[$item->seller_id]['price'] = $item->bTotal ?? NULL;
                }


                $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.seller_id from technical_custom_services ts
		left join sellers u on u.id = ts.seller_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.seller_id');

                foreach ($technicalReport1 as $item1) {
                    $b[$item1->seller_id]['name'] = $item1->Username ?? NULL;
                    $b[$item1->seller_id]['price'] = $item1->CTotal ?? NULL;
                }

                $users = Seller::where('company_id', 1)->get();
                foreach ($users as $user) {
                    if (isset($a[$user->id])) {
                        $personData[$user->id]['name'] = $a[$user->id]['name'] ?? 'Bulunamadı';
                        $personData[$user->id]['CTotal'] = $b[$user->id]['price'] ?? 0;
                        $personData[$user->id]['bTotal'] = $a[$user->id]['price'] ?? 0;
                    }

                }
            }

        }
        return $personData;
    }


    public function excelReport()
    {
        return view("module.report.excelreport");
    }


    public function excelreportprint(Request $request)
    {

        $report = new Report();
        $data['accessory'] = $report->accessory($request->date1,$request->date2);
        $data['phones'] = $report->phones($request->date1,$request->date2);
        $data['cover'] = $report->cover($request->date1,$request->date2);
        $data['technical'] = $report->technicals($request->date1,$request->date2);
        $data['teslimalan'] = $report->teslimalan($request->date1,$request->date2);


        $data['accessorySeller'] = $report->accessorySeller($request->date1,$request->date2);
        $data['phonesSeller'] = $report->phonesSeller($request->date1,$request->date2);
        $data['coverSeller'] = $report->coverSeller($request->date1,$request->date2);
        $data['technicalSeller'] = $report->technicalsSeller($request->date1,$request->date2);
        $data['teslimalanSeller'] = $report->teslimalanSeller($request->date1,$request->date2);


        $data['users'] = User::where('company_id',Auth::user()->company_id)->where('is_status',1)->where('personel',1)->get();
        $data['sellers'] = Seller::where('company_id',Auth::user()->company_id)->get();
        $data['date1'] = $request->date1;
        $data['date2'] = $request->date2;
        return view("module.report.excelreportprint",$data);
    }


}
