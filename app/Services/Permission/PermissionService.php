<?php

namespace App\Services\Permission;

use Illuminate\Database\Eloquent\Collection;


interface PermissionService {

    public function get(): ?Collection;
}
