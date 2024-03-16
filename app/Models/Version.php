<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelEasyRepository\Traits\FileUpload;

class Version extends BaseModel
{
    use HasFactory,FileUpload,SoftDeletes;


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope());
    }


    protected $fillable = [
        'name', 'is_status', 'company_id','user_id','image','brand_id'
    ];

    protected function fileSettings()
    {
       //
    }

    public function brand()
    {
        return $this->hasOne(Brand::class,'id','brand_id');
    }
}
