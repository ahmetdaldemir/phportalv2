<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DublicateRemoveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($podcast)
    {
        $this->podcast = $podcast;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::select("delete FROM sales
                 WHERE  id NOT IN
                 (
                     SELECT MAX(id)
                     FROM sales
                     GROUP BY serial,stock_card_movement_id
                 )");
 
    }
}
