<?php

namespace App\Models;

use App\Observers\SaleObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    const STATUS = [
        "1" => "Telefon",
        "2" => "Aksesuar",
        "3" => "Teknik Ürün",
        "4" => "Kaplama",
        "5" => "Baskı",
        "6" => "Diğer",
    ];

    const STATUS_STRING = [
        "Telefon" => "1",
        "Aksesuar" => "2",
        "Teknik" => "3",
        "Kaplama" => "4",
        "Baski" => "5",
        "Diger" => "6",
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(SaleObserver::class);
        static::addGlobalScope(new CompanyScope);

    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }

    public function stock_card_movement(): HasOne
    {
        return $this->hasOne(StockCardMovement::class,'id','stock_card_movement_id');
    }

    public function stock_card(): HasOne
    {
        return $this->hasOne(StockCard::class,'id','stock_card_id');
    }

    public function statusName()
    {
        return self::STATUS[$this->type];
    }


    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class,'id','stock_card_id');
    }

    public function stock_card_list(): HasOne
    {
        return $this->hasOne(StockCard::class,'id','stock_card_id');
    }

    public function technical(): HasOne
    {
        return $this->hasOne(TechnicalService::class,'technical_person','technical_service_person_id');
    }

}
