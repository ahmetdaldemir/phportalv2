<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelEasyRepository\Traits\FileUpload;

class Customer extends BaseModel
{
    use FileUpload,HasFactory,SoftDeletes;


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
        'firstname',    // Exists in DB but not actively used
        'lastname',     // Exists in DB but not actively used
        'fullname',     // Primary name field (firstname + lastname combined)
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
