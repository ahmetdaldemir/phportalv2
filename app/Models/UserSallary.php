<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSallary extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id','month',
        'year',
        'price',
    ];


}
