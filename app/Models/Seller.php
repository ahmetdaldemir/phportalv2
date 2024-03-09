<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seller extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name','is_status','phone','company_id','user_id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function account()
    {
        return SellerAccountMonth::where('seller_id',$this->id)->where('mounth', date('m'))->first();
    }

    public function amount()
    {
        return Safe::leftJoin('invoices', 'safes.invoice_id', '=', 'invoices.id')
            ->whereYear('safes.updated_at', '=', now()->year)
            ->whereMonth('safes.updated_at', '=', now()->month)
            ->where('safes.seller_id',$this->seller_id)
            ->where('safes.invoice_id','!=',99999999)
            ->where('safes.type','in')
            ->sum('invoices.total_price');
    }
}
