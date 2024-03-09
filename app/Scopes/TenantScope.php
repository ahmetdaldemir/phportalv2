<?php

namespace App\Scopes;


use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if ((!Auth::user()->hasRole('super-admin') || !Auth::user()->hasRole('Depo Sorumlusu')) && Auth::user()->company_id != 1) // HAsarlı Sorgusu
        {
            $table = $model->getTable();
            $builder->where(''.$table.'.company_id', auth()->user()->company_id);
        }
    }
}
