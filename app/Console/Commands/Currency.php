<?php

namespace App\Console\Commands;

use http\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Currency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currecies = \App\Models\Currency::all();
        foreach ($currecies as $item){
           $price =  $this->call($item->code);
           $item->exchange_rate = $price;
           $item->save();
        }
    }

    public function call($command, array $arguments = [])
    {
        $response = Http::get('https://api.getgeoapi.com/v2/currency/convert?api_key=ac87d69c87c68fe72f16d73a1d6d48234977140a&from='.$command.'&to=TRY&amount=1&format=json');
        $response = json_decode($response->body(),TRUE);
        return $response['rates']['TRY']['rate'];
    }

}
