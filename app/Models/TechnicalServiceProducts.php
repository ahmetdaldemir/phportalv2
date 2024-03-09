<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalServiceProducts extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'company_id',
        'technical_service_id',
        'stock_card_movement_id',
        'serial_number',
        'quantity',
        'sale_price',
        'stock_card_id'
    ];

    public function stock_card()
    {
        return $this->hasOne(StockCard::class,"id","stock_card_id");
    }
}
