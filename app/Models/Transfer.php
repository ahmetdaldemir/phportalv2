<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends BaseModel
{
    use SoftDeletes;

    const STATUS = [
        '1' => 'Beklemede',
        '2' => 'Onaylandı',
        '3' => 'Tamamlandı',
        '4' => 'Reddedildi',
    ];

    const STATUS_COLOR = [
        '1' => 'primary',
        '2' => 'success',
        '3' => 'warning',
        '4' => 'danger',
    ];


    protected $fillable = [
        'company_id',
        'user_id',
        'is_status',
        'main_seller_id',
        'comfirm_id',
        'comfirm_date',
        'delivery_id',
        'stocks',
        'number',
        'delivery_seller_id',
        'description',
        'serial_list',
        'type',
        'detail'
    ];

    protected $casts = ['stocks' => 'array','serial_list' => 'array','detail' => 'array'];


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope());
    }

    public function seller($id)
    {
        $seller =  Seller::find($id);
        if($seller){
            return $seller;
        }
        return null;
    }


    public function hasStaff($id): string
    {
        return $this->staff_id == $id ? 'true':'false';
    }

    public function hasColor($id): string
    {
        return $this->color_id == $id ? 'true':'false';
    }

    /**
     * Main seller relationship
     */
    public function main_seller()
    {
        return $this->belongsTo(Seller::class, 'main_seller_id');
    }

    /**
     * Delivery seller relationship
     */
    public function delivery_seller()
    {
        return $this->belongsTo(Seller::class, 'delivery_seller_id');
    }

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Confirm user relationship
     */
    public function confirm_user()
    {
        return $this->belongsTo(User::class, 'comfirm_id');
    }

    /**
     * Company relationship
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
