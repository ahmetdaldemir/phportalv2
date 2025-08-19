<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends BaseModel
{
    use HasFactory,SoftDeletes;


    const TYPE = [
        'customer' => 'Normal Müşteri',
        'account' => 'Cari Müşteri',
        'siteCustomer' => 'Site Müşteri',
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

    protected $fillable = [
        'code',
        'fullname',
        'tc',
        'iban',
        'phone1',
        'phone2',
        'address',
        'city',
        'district',
        'email',
        'note',
        'type',
        'company_id',
        'seller_id',
        'user_id',
        'is_status',
        'is_danger'
    ];
    protected function fileSettings()
    {
        // TODO: Implement fileSettings() method.
    }

    public function hasSeller($id): string
    {
        return $this->seller_id == $id ? 'true':'false';
    }

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function town(): HasOne
    {
        return $this->hasOne(Town::class, 'id', 'district');
    }


}
