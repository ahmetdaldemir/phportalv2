<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTraking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'process_seller_id',
        'stock_seller_id',
        'serial_number',
        'stock_card_id',
    ];

}
