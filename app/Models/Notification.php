<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends BaseModel
{
    use HasFactory;


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->company_id = auth()->user()->company_id;
            $model->seller_id = auth()->user()->seller_id;
            $model->user_id = auth()->user()->id;
        });
    }
}
