<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reason extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'is_status',
        'type',
        'company_id',
    ];


    const ReasonList = [
        1 => 'İPTAL',
        2 => 'İADE',
        3 => 'SATIŞ',
        4 => 'TEKNİK SERVİS',
        5 => 'ALIŞ'
    ];

}
