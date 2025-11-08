<?php

namespace App\Services\Role;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface RoleService extends BaseService{

    public function get(): ?Collection;

}
