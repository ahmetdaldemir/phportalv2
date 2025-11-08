<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;



    const TYPE = [
        '1' => 'refund',//HASARLI İADE,
        '4' => 'delivered', //TESLİM EDİLDİ
        '6' => 'service_return', // SERVİSTEN DÖNDÜ
        '5' => 'service_send', // SERVİSE GİTTİ
    ];


    public function stock(): BelongsTo
    {
        return $this->belongsTo(StockCard::class,'stock_card_id','id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class,'color_id','id');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(Reason::class,'reason_id','id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    }
}
