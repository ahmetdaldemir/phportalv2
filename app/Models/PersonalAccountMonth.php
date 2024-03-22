<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccountMonth extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'staff_id','mounth',
        'salary',
        'overtime',
        'way',
        'meal',
        'bounty',
        'insurance',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
        static::creating(function ($model) {
            $model->company_id = auth()->user()->company_id;
        });
    }


    public function userSallary()
    {
        dd($this->staff_id);
        return UserSallary::where('user_id',$this->staff_id)->where('month',$this->mounth)->where('year',$this->year)->orderBy('id','desc')->first();
    }


}
