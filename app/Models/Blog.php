<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Blog extends BaseModel
{
    use HasFactory,SoftDeletes;


    protected function fileSettings()
    {
        // TODO: Implement fileSettings() method.
    }
}
