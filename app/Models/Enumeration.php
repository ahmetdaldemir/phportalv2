<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enumeration extends Model
{
    use HasFactory;

    protected $fillable = ['dataCollection'];


    const CLASS_STRING = [
        '2' => 'badge rounded-pill bg-danger' ,
        '1' => 'badge rounded-pill bg-success',
        '3' => 'badge rounded-pill bg-warning',
        '4' => 'badge rounded-pill bg-info',
        '5' => 'badge rounded-pill bg-primary'
     ];

}
