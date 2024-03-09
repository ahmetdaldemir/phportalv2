<?php

namespace App\Http\Controllers\ContentTypes;

use Illuminate\Support\Facades\DB;

class Coordinates extends BaseType
{
    /**
     * @return string|\Illuminate\Database\Query\Expression
     */
    public function handle()
    {
        $field = $this->row->category."_".$this->row->field;

        if (empty($coordinates = $this->request->input($field))) {
            return;
        }
        //DB::connection()->getPdo()->quote won't work as it quotes the
        // lat/lng, which leads to wrong Geometry type in POINT() MySQL constructor
        $lat = (float) $coordinates['lat'];
        $lng = (float) $coordinates['lng'];

        return DB::raw("ST_GeomFromText('POINT({$lng} {$lat})')");
    }
}
