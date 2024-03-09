<?php

if (! function_exists('setting')) {
    function setting($key) {
        $newkey = explode(".",$key);
        return \App\Models\Setting::where('key',$newkey[1])->where('category',$newkey[0])->first()->value;
    }
}


if (! function_exists('sevkcount')) {
    function sevkcount() {
      return  \App\Models\Transfer::where('delivery_seller_id',auth()->user()->seller_id)->where('is_status',1)->get()->count();
    }
}


