<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinansTransaction extends BaseModel
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

}
