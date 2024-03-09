<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Version;
use App\Models\VersionChild;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

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

        /*
        $version = Version::all();
        foreach ($version as $item)
        {
            $versionChild = new VersionChild();
            $versionChild->version_id = $item->id;
            $versionChild->name = $item->name;
            $versionChild->save();
        }
*/
        /*
        $brands = Brand::all();
        foreach ($brands as $brand)
        {
            $brandall = \DB::table('brands_test')->where('brand',$brand->name)->get();
            foreach ($brandall as $key)
            {
                $version = new Version();
                $version->name = $key->model;
                $version->image = "";
                $version->brand_id = $brand->id;
                $version->user_id = 1;
                $version->company_id = 1;
                $version->is_status = 1;
                $version->save();
            }
        }
*/

        /*
               $brands = Brand::where('company_id',1)->get();
               foreach ($brands as $brand)
               {
                   $x = new Brand();
                   $x->name = $brand->name;
                   $x->company_id = 4;
                   $x->user_id = 1;
                   $x->save();
                   $lastID = $x->id;

                   $versions = Version::where('brand_id',$brand->id)->get();
                   foreach ($versions as $key)
                   {
                       $version = new Version();
                       $version->name = $key->name;
                       $version->image = "";
                       $version->brand_id = $lastID;
                       $version->user_id = 1;
                       $version->company_id = 4;
                       $version->is_status = 1;
                       $version->save();
                   }
               }
        */
        
    }
}
