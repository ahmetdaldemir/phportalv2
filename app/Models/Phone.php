<?php

namespace App\Models;

use App\Traits\NotifiesOnDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends BaseModel
{
    use HasFactory,SoftDeletes, NotifiesOnDelete;

    protected string $message = '';

    protected $fillable = [
        'user_id',
        'company_id',
        'brand_id',
        'version_id',
        'color_id',
        'seller_id',
        'quantity',
        'type',
        'imei',
        'barcode',
        'description',
        'cost_price',
        'sale_price',
        'customer_id',
        'physical_condition',
        'altered_parts',
        'memory',
        'batery',
        'warranty',
        'status'
    ];

    const TYPE = [
        'new' => 'SIFIR',
        'old' => 'İkinci El',
        'assigned_device' => 'Temlikli Telefon',
        'assigned_accessory' => 'Temlikli Aksesuar',
    ];

    const WARRANTY = [
        '2' => 'CİHAZ GARANTİSİZ',
        '1' => 'GARANTİLİ',
    ];


    const STATUS = [
        '1' => 'SATIŞTA',
        '0' => 'BEKLEMEDE',
        '2' => 'TRANSFER SÜRECİNDE',
     ];
    public function addMessage($message)
    {
        $this->message = $message;
    }

    public function seller(): hasOne
    {
        return $this->hasOne(Seller::class, 'id', 'seller_id');
    }

    public function brand(): hasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }
    public function version(): hasOne
    {
        return $this->hasOne(Version::class, 'id', 'version_id');
    }

    public function color(): hasOne
    {
        return $this->hasOne(Color::class, 'id', 'color_id');
    }
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
