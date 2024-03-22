<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinansTransaction extends BaseModel
{
    use HasFactory;


     public const MODEL_STRING = [
         'App\Models\User'  => 'Personel',
         'App\Models\Seller'  => 'Şube',
    ];

    public const MODEL_COLOR = [
        'App\Models\User'  => 'background: aliceblue',
        'App\Models\Seller'  => 'background: #fff6f0',
    ];

    public const PAYMENT_TYPE = [
        'expense'  => 'Gider',
        'income'  => 'Gelir',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }


    public static function calculateCurrencyDifferences()
    {
        // currency_id'ye göre gruplayarak, her bir döviz kuru için farkı hesapla
        $currencyDifferences = self::select('currency_id')
            ->selectRaw('SUM(CASE WHEN payment_type = "income" THEN price ELSE -price END) AS difference')
            ->groupBy('currency_id')
            ->get();

        return $currencyDifferences;
    }


    public function finansModel()
    {
        if($this->model == 'App\Models\User')
        {
            return User::find($this->model_id)->name;
        }else{
            return Seller::find($this->model_id)->name;
        }
    }

    public function currency($field)
    {
        return Currency::find($this->currency_id)->{$field};
    }

    public function category()
    {
        return  $this->hasOne(AccountingCategory::class,'id','process_type');
    }



}
