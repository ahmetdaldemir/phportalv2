<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeletedAtSerialNumber extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'serial_number',
        'deleted_at'
    ];
}
