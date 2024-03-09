<?php

namespace App\Services\Permission;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface PermissionService extends BaseService{

    public function get(): ?Collection;
}
