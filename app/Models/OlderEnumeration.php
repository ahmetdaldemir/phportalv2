<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OlderEnumeration extends BaseModel
{
    use HasFactory;


    protected $fillable = [
        'stock_card_movement_id','enumeration_id',
        'serial'
    ];
}
