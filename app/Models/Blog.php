<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelEasyRepository\Traits\FileUpload;

class Blog extends BaseModel
{
    use HasFactory,SoftDeletes,FileUpload;


    protected function fileSettings()
    {
        // TODO: Implement fileSettings() method.
    }
}
