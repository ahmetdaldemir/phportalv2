<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name','is_status','company_id'];

}
