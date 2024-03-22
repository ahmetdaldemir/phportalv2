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
}
