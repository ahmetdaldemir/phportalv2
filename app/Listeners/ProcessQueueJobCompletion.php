<?php

namespace App\Listeners;

use App\Events\QueueJobCompleted;
use App\Models\Color;
use App\Models\Enumeration;
use App\Models\Seller;
use App\Models\StockCardMovement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessQueueJobCompletion
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\QueueJobCompleted  $event
     * @return void
     */
    public function handle(QueueJobCompleted $event)
    {

         Log::channel('serial_log')->info("Queue job with ID  $event->serial completed successfully.");

        // Kuyruk işlemi tamamlandığında yapılacak işlemler
        $jobId = $event->jobId;
        $jobId = $event->serial;
        $jobId = $event->id;
        $dataCol = [];

             $stockcardmovement = StockCardMovement::where('serial_number', $event->serial)->first();
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
                 $dataCol['serial'] = $event->serial;
                 $dataCol['type'] = 'Hatali Urun';
                 $dataCol['typeId'] = 0;
                 $dataCol['color'] = 'Hatali Urun';
                 $dataCol['seller'] = 'Hatali Urun';
                 $dataCol['seller_id'] = 0;
                 $dataCol['read'] = 0;
             }
          echo json_encode($dataCol);
        // İşlemi loglama, veritabanına kaydetme gibi işlemler yapılabilir
    }
}
