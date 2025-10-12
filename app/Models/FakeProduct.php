<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakeProduct extends Model
{
    use HasFactory;

    protected $fillable = ['name','is_status','company_id','user_id'];

}
