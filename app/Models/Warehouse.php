<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'company_id', 'user_id','seller_id','is_status'];
}
