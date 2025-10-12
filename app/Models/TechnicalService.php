<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalService extends Model
{
    use HasFactory;
    protected $casts = ['products' => 'array','accessory_category'=> 'array','physically_category'=> 'array','fault_category'=> 'array'];

    protected $fillable = [
        'customer_id',
        'physical_condition',
        'accessories',
        'fault_information',
        'device_password',
         'products',
        'seller_id',
        'brand_id',
        'version_id',
        'total_price',
        'customer_price',
        'status',
        'delivery_staff',
        'company_id',
        'user_id',
        'accessory_category',
        'physically_category',
        'fault_category',
        'imei',
    ];


    const STATUS = [
        "1" => "İşleme Alındı",
        "3" => "Fiyat Onayı Bekleniyor",
        "7" => "Fiyat Onayı Alınamadı",
        "8" => "Onarım Sağlanamıyor",
        "2" => "Parça Bekleniyor",
        "9" => "Parça Tedarik Edilemedi",
        "4" => "Hazır",
        "5" => "Ödeme Alındı",
        "6" => "Tamamlandı",
        "10" => "İptal",
    ];

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

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TechnicalServiceProducts::class,'id','technical_service_id');
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
       return TechnicalServiceProducts::where('technical_service_id',$this->id)->sum('sale_price');
    }
}
