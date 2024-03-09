<?php

namespace App\Http\Controllers;

use App\Events\QueueJobCompleted;
use App\Http\Controllers\Controller;
use App\Jobs\EnumerationJob;
use App\Models\Color;
use App\Models\Enumeration;
use App\Models\OlderEnumeration;
use App\Models\Seller;
use App\Models\StockCardMovement;
use App\Services\Brand\BrandService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use voku\helper\ASCII;

class EnumerationController extends Controller
{

    protected $insertId;

    public function __construct()
    {
        $this->insertId = '';
    }

    public function index()
    {
        $data['enumerations'] = Enumeration::all()->sortBy('finish_date');
        $data['sellers'] = Seller::all();
        return view('module.enumeration.index', $data);
    }

    public function stocktracking(Request $request)
    {
        $data['enumeration'] = Enumeration::find($request->id);
        $data['sellers'] = Seller::all();
        return view('module.enumeration.stocktracking', $data);
    }

    public function start(Request $request)
    {
        $serialnumbers = StockCardMovement::where('seller_id', $request->seller_id)->where('type', '!=', '2')->pluck('serial_number')->toArray();
        $record = Enumeration::where('seller_id', $request->seller_id)->whereNull('finish_date')->first();
        $message = 'Tamamlanmamis Sayim Mevcut';
        if (!$record) {
            $starts = new Enumeration();
            $starts->user_id = Auth::user()->id;
            $starts->company_id = Auth::user()->company_id;
            $starts->seller_id = $request->seller_id;
            $starts->start_date = Carbon::now()->format("Y-m-d H:i:s");
            $starts->stockCollection = json_encode($serialnumbers, JSON_FORCE_OBJECT);
            $starts->save();
            $data["id"] = $starts->id;
            $message = 'Sayim Basladi';
        }
        return redirect()->back()->withErrors(['enumerationmsg' => $message]);
    }

    public function update(Request $request)
    {
        EnumerationJob::dispatch($request->id, $request->serial)->onQueue('enumeration');
        event(new QueueJobCompleted($request->id, $request->serial));

        /*  $jsonData=[];
          $datas=[];
          $record = Enumeration::find($request->id);
          $stock_card_movement = StockCardMovement::where('serial_number',$request->serial);
          if($stock_card_movement->count() == 1)
          {
              if(!empty($record->dataCollection))
              {
                  $jsonData = json_decode($record->dataCollection, true);

                  $datas = array_values($jsonData);

              }
              $key = array_search($request->serial, $datas);
              if($key == "")
              {
                  $jsonData[rand(9,99999)] =  $request->serial;
                  $updatedJsonColumn = json_encode($jsonData);
                  $record->update(['dataCollection' => $updatedJsonColumn]);
                  return response()->json('0',200);
              }

          }else{
              return response()->json('Cift Seri Numarasi',200);
          }
        */
    }

    public function finish(Request $request)
    {
        $starts = Enumeration::find($request->id);
        if (is_null($starts->finish_date)) {
            $starts->finish_date = Carbon::now()->format("Y-m-d H:i:s");
            $starts->save();
            return redirect()->route('enumeration.print', ['id' => $request->id]);
        } else {
            return redirect()->route('enumeration.print', ['id' => $request->id]);
        }
    }

    public function print(Request $request)
    {
        $data['enumeration'] = Enumeration::find($request->id);
        $requestNew = new Request();
        $requestNew->replace(['id' => $request->id]);
        $data['table'] = $this->get($requestNew, 'finish');
        $data['sellers'] = Seller::all();
        $data['totalstock'] = StockCardMovement::where("seller_id", $data['enumeration']->seller_id)->where('type', '!=', '2')->count();
        return view('module.enumeration.print', $data);
    }

    public function get(Request $request, $status = "")
    {
        $dataCol = [];
        $dataCol1 = [];
        $starts = Enumeration::find($request->id);

        if ($starts && !empty($starts->dataCollection)) {

            $data = json_decode($starts->dataCollection, TRUE);
            $datas = array_values($data);
            $dataCol = StockCardMovement::with('stock', 'color', 'seller')
                ->whereIn('serial_number', $datas)
                ->get()
                ->map(function ($item) {
                    $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                    $item->class_string = Enumeration::CLASS_STRING[$item->type];
                    return $item;
                })
                ->toArray();
        }
        if ($status == 'finish') {
            $dataCol1 = StockCardMovement::with('stock', 'color', 'seller')
                ->whereNotIn('serial_number', $datas ?? [])
                ->where('type', '!=', '2')
                ->where('seller_id', $starts->seller_id ?? 0)
                ->get()
                ->map(function ($item) {
                    $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                    $item->class_string = Enumeration::CLASS_STRING[$item->type];
                    return $item;
                })
                ->toArray();
        }
        if (is_null($starts->finish_date)) {
            $this->olderEnumerationNotFound($request->id, $datas);
        }
        return response()->json(['dataCol' => $dataCol, 'dataCol1' => $dataCol1], 200);
    }


    public function newPrint(Request $request)
    {
        $enumeration = Enumeration::find($request->id);
        if (is_null($enumeration->finish_date)) {
            return redirect()->back();
        }

        $dataCollection = json_decode($enumeration->dataCollection, TRUE);
        $stockCollection = json_decode($enumeration->stockCollection, TRUE);
        $differenceArray = array_diff(array_values($stockCollection), array_values($dataCollection));

        $dataCollection =  array_values($dataCollection);



        $data['dataCol'] = StockCardMovement::with('stock', 'color', 'seller')
            ->whereNotIn('serial_number', $dataCollection)
            ->where('type', '!=', '2')
            ->get()
            ->map(function ($item) {
                $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                $item->class_string = Enumeration::CLASS_STRING[$item->type];
                return $item;
            })
            ->toArray();
//         DB::listen(function ($query) use($request) {
//                  foreach ($query->bindings as &$binding) {
//                      if ($binding instanceof \DateTime) {
//                          $binding = $binding->format('Y-m-d H:i:s');
//                      }
//                  }
//                  $rawQuery = sprintf(str_replace("?", "'%s'", $query->sql), ...$query->bindings);
//                  dd($rawQuery);
//
//              });

        $data['dataCol1'] = StockCardMovement::with('stock', 'color', 'seller')
            ->whereIn('serial_number', $differenceArray ?? [])
            ->where('type', '!=', '2')
            ->where('seller_id', $enumeration->seller_id ?? 0)
            ->get()
            ->map(function ($item) {
                $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                $item->class_string = Enumeration::CLASS_STRING[$item->type];
                return $item;
            })
            ->toArray();




       // $requestNew = new Request();
        //  $requestNew->replace(['id' => $request->id]);
      //  $data['table'] = $this->newPrintget($requestNew, 'finish');
        $data['table'] = [];
        $data['sellers'] = Seller::all();
        $data['totalstock'] = 0;
        $data['enumeration'] = $enumeration;
       // $data['totalstock'] = StockCardMovement::where("seller_id", $data['enumeration']->seller_id)->where('type', '!=', '2')->count();
        return view('module.enumeration.newPrint', $data);
    }

    public function newPrintget(Request $request, $status = "")
    {
        $olderenumeration = OlderEnumeration::select('serial')->where('enumeration_id', $request->id)->get()->toArray();
        $dataCol = [];
        $dataCol1 = [];
        $starts = Enumeration::find($request->id);
        if ($starts && !empty($starts->dataCollection)) {
            $data = json_decode($starts->dataCollection, TRUE);
            $datas = array_values($data);
            $dataCol = StockCardMovement::with('stock', 'color', 'seller')
                ->whereIn('serial_number', $datas)
                ->get()
                ->map(function ($item) {
                    $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                    $item->class_string = Enumeration::CLASS_STRING[$item->type];
                    return $item;
                })
                ->toArray();
        }
        if ($status == 'finish') {
            $dataCol1 = StockCardMovement::with('stock', 'color', 'seller')
                ->whereNotIn('serial_number', $olderenumeration ?? [])
                ->where('type', '!=', '2')
                ->where('seller_id', $starts->seller_id ?? 0)
                ->get()
                ->map(function ($item) {
                    $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                    $item->class_string = Enumeration::CLASS_STRING[$item->type];
                    return $item;
                })
                ->toArray();
        }
        return response()->json(['dataCol' => $dataCol, 'dataCol1' => $dataCol1], 200);
    }

    public function olderEnumerationNotFound($id, $datas)
    {
        StockCardMovement::whereNotIn('serial_number', $datas ?? [])
            ->where('type', '!=', '2')
            ->get()
            ->each(function ($item) use ($id) {
                OlderEnumeration::create([
                    'enumeration_id' => $id,
                    'stock_card_movement_id' => $item->id,
                    'serial' => $item->serial_number,
                ]);
            });
    }


    public function get_eski(Request $request, $status = "")
    {
        $dataCol = [];
        $dataCol1 = [];
        $starts = Enumeration::find($request->id);
        if ($starts) {
            if (!empty($starts->dataCollection)) {
                $data = json_decode($starts->dataCollection, TRUE);
                $datas = array_values($data);
                $i = 0;
                foreach ($datas as $item) {
                    $stockcardmovement = StockCardMovement::where('serial_number', $item)->first();
                    if ($stockcardmovement) {
                        $dataCol[$i]['name'] = $stockcardmovement->stock->name;
                        $dataCol[$i]['serial'] = $stockcardmovement->serial_number;
                        $dataCol[$i]['type'] = StockCardMovement::TYPE[$stockcardmovement->type];
                        $dataCol[$i]['typeId'] = $stockcardmovement->type;
                        $dataCol[$i]['color'] = Color::find($stockcardmovement->color_id)->name;
                        $dataCol[$i]['seller'] = Seller::find($stockcardmovement->seller_id)->name;
                        $dataCol[$i]['seller_id'] = $stockcardmovement->seller_id;
                        $dataCol[$i]['read'] = 1;
                    } else {
                        $dataCol[$i]['name'] = 'Hatali Urun';
                        $dataCol[$i]['serial'] = $item;
                        $dataCol[$i]['type'] = 'Hatali Urun';
                        $dataCol[$i]['typeId'] = 0;
                        $dataCol[$i]['color'] = 'Hatali Urun';
                        $dataCol[$i]['seller'] = 'Hatali Urun';
                        $dataCol[$i]['seller_id'] = 0;
                        $dataCol[$i]['read'] = 0;
                    }

                    $i++;
                }
            }

        }

        if ($status == 'finish') {
            $stockcardmovements = StockCardMovement::whereNotIn('serial_number', $datas)->where('type', '!=', '2')->where('seller_id', $starts->seller_id)->get();
            $a = 0;
            foreach ($stockcardmovements as $items) {
                $dataCol1[$a]['name'] = $items->stock->name;
                $dataCol1[$a]['serial'] = $items->serial_number;
                $dataCol1[$a]['type'] = StockCardMovement::TYPE[$items->type];
                $dataCol1[$a]['typeId'] = $items->type;
                $dataCol1[$a]['color'] = Color::find($items->color_id)->name;
                $dataCol1[$a]['seller'] = Seller::find($items->seller_id)->name;
                $dataCol1[$a]['seller_id'] = -1;
                $dataCol1[$a]['read'] = 2;
                $a++;
            }
        }

        $data['dataCol'] = $dataCol;
        $data['dataCol1'] = $dataCol1;
        return response()->json($data, 200);
    }

    public function getLastSerial(Request $request)
    {
        $dataCol = [];
        $aa = Enumeration::where('id', $request->serial)->whereJsonContains('dataCollection', $request->id)->first();
        if ($aa) {
            $a = json_decode($aa, TRUE);
            $b = array_values(end($a->dataCollection));
            $stockcardmovement = StockCardMovement::where('serial_number', $b)->first();
            if ($stockcardmovement) {
                $dataCol['name'] = $stockcardmovement->stock->name;
                $dataCol['serial'] = $stockcardmovement->serial_number;
                $dataCol['type'] = StockCardMovement::TYPE[$stockcardmovement->type];
                $dataCol['typeId'] = $stockcardmovement->type;
                $dataCol['color'] = Color::find($stockcardmovement->color_id)->name;
                $dataCol['seller'] = Seller::find($stockcardmovement->seller_id)->name;
                $dataCol['seller_id'] = $stockcardmovement->seller_id;
                $dataCol['read'] = 1;
            } else {
                $dataCol['name'] = 'Hatali Urun';
                $dataCol['serial'] = $request->id;
                $dataCol['type'] = 'Hatali Urun';
                $dataCol['typeId'] = 0;
                $dataCol['color'] = 'Hatali Urun';
                $dataCol['seller'] = 'Hatali Urun';
                $dataCol['seller_id'] = 0;
                $dataCol['read'] = 0;
            }
        }


        return response()->json($dataCol, 200);
    }

    public function newGet(Request $request, $status = "")
    {
        $dataCol = [];
        $datass = [];
        $dataCol1 = [];
        if (Cache::has('enumeration_' . $request->id)) {
            $datass = Cache::get('enumeration_' . $request->id);
        } else {
            $dataCol = [];
            $dataCol1 = [];
            $starts = Enumeration::find($request->id);
            if ($starts) {
                if (!empty($starts->dataCollection)) {
                    $data = json_decode($starts->dataCollection, TRUE);
                    ksort($data);
                    $datas = array_values($data);
                    $i = 0;
                    $dataCol = StockCardMovement::with('stock', 'color', 'seller')
                        ->get()
                        ->whereIn('serial_number', $datas)
                        ->map(function ($item) {
                            $item->type_name = StockCardMovement::TYPE[$item->type] ?? 'Bilinmiyor';
                            $item->class_string = Enumeration::CLASS_STRING[$item->type];
                            return $item;
                        })
                        ->toArray();

                }

            }
            Cache::set('enumeration_' . $request->id, $dataCol);
            $datass = Cache::get('enumeration_' . $request->id);
        }
        $data['dataCol'] = $datass;
        $data['dataCol1'] = $dataCol1;
        return response()->json($data, 200);
    }

    public function runquene()
    {
        Artisan::call('queue:work --daemon --queue=high,enumeration');
    }
}
