<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    use HasFactory;

    public function stock()
    {
        return $this->hasOne(StockCard::class,'id','stock_card_id');
    }

    public function color()
    {
        return $this->hasOne(Color::class,'id','color_id');
    }
}
