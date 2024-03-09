<?php

namespace App\Jobs;

use App\Models\Enumeration;
use App\Models\StockCardMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EnumerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $serial;
    protected $jobId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$serial)
    {
        $this->id = $id;
        $this->serial = $serial;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('basladi'.time());
        $jsonData = [];
        $datas = [];
        $record = Enumeration::find($this->id);
        $stock_card_movement = StockCardMovement::where('serial_number', $this->serial);

        if ($stock_card_movement->count() == 1) {
            if (!empty($record->dataCollection)) {
                $jsonData = json_decode($record->dataCollection, true);
                $datas = array_values($jsonData);

            }
            $key = array_search($this->serial, $datas);

            if ($key == "") {
                $jsonData[rand(9, 99999)] = $this->serial;
                $updatedJsonColumn = json_encode($jsonData);
                $record->update(['dataCollection' => $updatedJsonColumn]);
              //  $this->dataUpdate($updatedJsonColumn);
             }
        }
        Log::info('bitti'.time());
    }

    public function dataUpdate($updatedJsonColumn)
    {

        $data  = json_decode($updatedJsonColumn,TRUE);
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

        Cache::set('enumeration_'.$this->id,$dataCol);
    }
}
