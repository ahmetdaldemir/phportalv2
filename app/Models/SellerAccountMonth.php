<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerAccountMonth extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'seller_id','mounth',
        'rent',
        'invoice',
        'tax',
        'additional_expense',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model) {
            $model->company_id = auth()->user()->company_id;
        });
    }

}
