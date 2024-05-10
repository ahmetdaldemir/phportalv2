<?php

namespace App\Services\Modules;

use App\Models\Invoice;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Report
{

    public function accessory(...$calc)
    {
        $ar = [];
        $arbc = [];
        $date1 = Carbon::parse($calc[0] . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse($calc[1] . " 23:59:59")->format('Y-m-d H:i:s');
        $invoceGroupId = $this->saleGroupInvoces($date1, $date2, 2);
        $accesoryReport = $this->staffAccessory($invoceGroupId);
        $accesoryReportBaseCost = $this->staffAccessoryBaseCost($date1, $date2, $invoceGroupId, 2);
        $i = 0;
        foreach ($accesoryReport as $item) {
            $ar[$item->staff_id] = $item->ciro;
            $i++;
        }

        $a = 0;
        foreach ($accesoryReportBaseCost as $items) {
            $arbc[$items->user_id] = $items->ciro;
            $a++;
        }
        return ['ar' => $ar,'arbc' => $arbc];
    }


    public function saleGroupInvoces($date1, $date2, $type = '')
    {
        return Sale::select('invoice_id')->where('type', $type)->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2)->groupBy('invoice_id')->pluck('invoice_id');
    }

    public function staffAccessory($invoiceIds)
    {
        $result = Invoice::select(DB::raw('SUM(total_price) as ciro'), 'staff_id')
            ->whereIn('id', $invoiceIds)
            ->where('type', 2)
            ->groupBy('staff_id')
            ->get();
        return $result;
    }

    public function staffAccessoryBaseCost($date1, $date2, $invoiceIds, $type)
    {
        $result = Sale::select(DB::raw('SUM(base_cost_price) as ciro'), 'user_id')
            ->where('type', $type)
            ->whereIn('invoice_id', $invoiceIds)
            ->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2)
            ->groupBy('user_id')
            ->get();
        return $result;
    }

    public function phones(...$calc)
    {
        $ar = [];
        $arbc = [];
        $date1 = Carbon::parse($calc[0] . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse($calc[1] . " 23:59:59")->format('Y-m-d H:i:s');

       $result = Sale::select(DB::raw('SUM(sale_price) as ciro'),DB::raw('SUM(base_cost_price) as maliyet'), 'user_id')
                     ->where('type', 1)
                     ->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2)
                     ->groupBy('user_id')
                     ->get();
          foreach ($result as $item) {
              $ar[$item->user_id] = $item->ciro;
           }
           foreach ($result as $item) {
               $arbc[$item->user_id] = $item->maliyet;
           }

        return ['ar' => $ar,'arbc' => $arbc];
    }


    public function cover(...$calc)
    {
        $ar = [];
        $arbc = [];
        $date1 = Carbon::parse($calc[0] . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse($calc[1] . " 23:59:59")->format('Y-m-d H:i:s');

        $technicalReport = DB::select('SELECT
                                            u.name AS userName,u.id,ts.delivery_staff,
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
                                                where  s.company_id = "' . Auth::user()->company_id . '"
                                            ) AS salesproduct ON salesproduct.technical_custom_id = ts.id
                                        LEFT JOIN
                                            users u ON u.id = ts.delivery_staff
                                        WHERE
                                            ts.payment_status = 1 and
                                             ts.company_id = "' . Auth::user()->company_id . '"
                                            AND ts.updated_at BETWEEN  "' . $date1 . '" and "' . $date2 . '"
                                        GROUP BY
                                           ts.delivery_staff');

        $a = [];
        $b = [];
        $personData = [];
        foreach ($technicalReport as $item) {
            $a[$item->delivery_staff] = $item->totalCost;
        }


        $technicalReport1 = DB::select('SELECT u.name as Username,sum(ts.customer_price) as CTotal,ts.delivery_staff from technical_custom_services ts
		left join users u on u.id = ts.user_id
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '"  GROUP BY ts.delivery_staff');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->delivery_staff] = $item1->CTotal;
        }
        return ['ar' => $a,'arbc' => $b];
    }

    public function technicals(...$calc)
    {

        $ar = [];
        $arbc = [];
        $date1 = Carbon::parse($calc[0] . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse($calc[1] . " 23:59:59")->format('Y-m-d H:i:s');

        $technicalReport = DB::select('	SELECT u.name as userName,technical_person,sum(salesproduct.base_cost_price) as bTotal from technical_services ts
		left join users u on u.id = ts.technical_person
	    left join (select tsp.technical_service_id,s.base_cost_price from sales s left join technical_service_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id where s.company_id = "' . Auth::user()->company_id . '" ) salesproduct on salesproduct.technical_service_id = ts.id
		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '" and  ts.company_id = "' . Auth::user()->company_id . '"  GROUP BY technical_person

		');

        $a = [];
        $b = [];
        $personData = [];
        foreach ($technicalReport as $item) {
            $a[$item->technical_person] = $item->bTotal;
        }


        $technicalReport1 = DB::select('	SELECT u.name as Username,sum(ts.customer_price) as CTotal,technical_person from technical_services ts
		left join users u on u.id = ts.technical_person
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '" and  ts.company_id = "' . Auth::user()->company_id . '"  GROUP BY technical_person

		');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->technical_person] = $item1->CTotal;
        }


        return ['ar' => $a,'arbc' => $b];

    }


    public function teslimalan(...$calc)
    {
        $ar = [];
        $arbc = [];
        $date1 = Carbon::parse($calc[0] . " 00:00:00")->format('Y-m-d H:i:s');
        $date2 = Carbon::parse($calc[1] . " 23:59:59")->format('Y-m-d H:i:s');

        $technicalReport = DB::select('	SELECT u.name as userName,technical_person,sum(salesproduct.base_cost_price) as bTotal from technical_services ts
		left join users u on u.id = ts.technical_person
	    left join (select tsp.technical_service_id,s.base_cost_price from sales s left join technical_service_products tsp on s.stock_card_movement_id = tsp.stock_card_movement_id where s.company_id = "' . Auth::user()->company_id . '" ) salesproduct on salesproduct.technical_service_id = ts.id
		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '" and  ts.company_id = "' . Auth::user()->company_id . '"  GROUP BY delivery_staff

		');

        $a = [];
        $b = [];
        $personData = [];
        foreach ($technicalReport as $item) {
            $a[$item->delivery_staff] = $item->bTotal;
        }


        $technicalReport1 = DB::select('	SELECT u.name as Username,sum(ts.customer_price) as CTotal,technical_person from technical_services ts
		left join users u on u.id = ts.technical_person
 		where ts.payment_status = 1 and ts.updated_at BETWEEN "' . $date1 . '" and "' . $date2 . '" and  ts.company_id = "' . Auth::user()->company_id . '"  GROUP BY delivery_staff

		');

        foreach ($technicalReport1 as $item1) {
            $b[$item1->delivery_staff] = $item1->CTotal;
        }


        return ['ar' => $a,'arbc' => $b];
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => '₺'.' '.number_format($this->price, 2, ',', '.')
        );
    }
}
