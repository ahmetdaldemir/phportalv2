<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreatePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:policy';

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
        return Command::SUCCESS;
    }
}

//public function handle()
//{
//    $this->path = app_path() . "/Models";
//    $models = $this->getModels($this->path);
//    foreach ($models as $key => $value) {
//        Artisan::call('make:policy ' . $value . 'Policy --model=' . $value . '');
//    }
//}


function getModels($path)
{
    $out = [];
    $results = scandir($path);
    foreach ($results as $result) {
        if ($result === '.' or $result === '..') continue;
        $filename = $result;
        if (is_dir($filename)) {
            $out = array_merge($out, getModels($filename));
        } else {
            $out[] = substr($filename, 0, -4);
        }
    }
    return $out;
}

