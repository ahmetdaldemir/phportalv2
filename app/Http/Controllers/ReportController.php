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
        $data['brands'] = $this->brandService->get();
        $data['colors'] = $this->colorService->get();
        $data['users'] = $this->userService->get();
        $data['categories'] = $this->getCategoryPathList();

        $query = Sale::where('company_id', Auth::user()->company_id)->with(['phone', 'stock_card_list']);
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

        /*  DB::listen(function ($query) use($request) {
              foreach ($query->bindings as &$binding) {
                  if ($binding instanceof \DateTime) {
                      $binding = $binding->format('Y-m-d H:i:s');
                  }
              }
              $rawQuery = sprintf(str_replace("?", "'%s'", $query->sql), ...$query->bindings);
              Log::debug("'{$rawQuery}' executed in {$query->time} ms");

          });
        */

        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }


        if ($request->filled('brand')) {
            $query->whereHas('stock_card', function ($q) use ($request) {
                $q->where('brand_id', '=', $request->brand);
            });
        }

        /*
        if ($request->filled('category')) {
            $query->where('type', $request->category);

            if ($request->filled('technical_person')) {
                if (in_array($request->category, [4, 5, 6])) {
                    $query->where('user_id', $request->technical_person);
                }
            }

            if ($request->filled('sales_person')) {
                if (in_array($request->category, [1, 2, 3])) {
                    $query->where('user_id', $request->sales_person);
                }
            }
        }

        if (!$request->filled('category')) {
            if ($request->filled('technical_person')) {
                $query->where('user_id', $request->technical_person);
            }

            if ($request->filled('sales_person')) {
                $query->where('user_id', $request->sales_person);
            }
        }
*/

        /*      if ($request->filled('category')) {
                  if($request->category == 1)
                  {
                      $query->whereHas('phone', function ($q) use ($request) {
                          $q->where('category_id', '=', $request->category);
                      });
                  }else{
                      $query->whereHas('stock_card_list', function ($q) use ($request) {
                          $q->where('category_id', '=', $request->category);
                      });
                  }

              }
        */

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

        $data['report'] = $query->orderby('id', 'desc')->get();
        $data['types'] = Sale::STATUS;
        if ($request) {
            $data['sendData'] = $request;
        }

        return view('module.report.index', $data);
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
        $data['users'] = User::where('company_id',Auth::user()->company_id)->where('is_status',1)->where('personel',1)->get();
        $data['date1'] = $request->date1;
        $data['date2'] = $request->date2;
        return view("module.report.excelreportprint",$data);
    }


}
