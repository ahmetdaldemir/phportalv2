<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountingCategory extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name','is_status','category','company_id','user_id'];
}
