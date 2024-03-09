<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalCustomService extends Model
{
    public function seller()
    {
        return $this->hasOne(Seller::class,'id','seller_id');
    }

    public function brand()
    {
        return $this->hasOne(Brand::class,'id','brand_id');
    }

    public function version()
    {
        return $this->hasOne(Version::class,'id','version_id');
    }

    public function delivery()
    {
        return $this->hasOne(User::class,'id','delivery_staff');
    }
    public function customer()
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }

    public function sumPrice()
    {
        return TechnicalCustomProducts::where('technical_custom_id',$this->id)->sum('sale_price');
    }
}
