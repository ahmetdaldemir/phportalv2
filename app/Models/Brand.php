<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name', 'is_status', 'company_id','user_id'
    ];

}
