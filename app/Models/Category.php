<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name', 'parent_id', 'is_status', 'company_id','user_id'
    ];



    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parentName()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
