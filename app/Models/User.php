<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles,SoftDeletes;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'seller_id',
        'is_status',
        'position',
        'personel'

    ];

    const POSITION = [
        "1" => "Satış",
        "2" => "Teknik",
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class,'id','seller_id');
    }

    public function hasSeller($id): string
    {
        return $this->seller_id == $id ? 'true':'false';
    }

    public function account()
    {
        return PersonalAccountMonth::where('staff_id',$this->id)->where('mounth', date('m'))->first();
    }

    public function company()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }

    public function avans()
    {
       return  FinansTransaction::where('model_id',$this->id)->where('process_type','17')->where('model_class','App\Models\User')->sum('price');
    }

    public function amount()
    {

        return Safe::leftJoin('invoices', 'safes.invoice_id', '=', 'invoices.id')
            ->whereYear('safes.updated_at', '=', now()->year)
            ->whereMonth('safes.updated_at', '=', now()->month)
            ->where('safes.user_id',$this->staff_id)
            ->where('safes.invoice_id','!=',99999999)
            ->where('safes.type','in')
            ->sum('invoices.total_price');
     }
}
