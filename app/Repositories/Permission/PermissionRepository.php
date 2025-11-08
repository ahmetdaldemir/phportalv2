<?php

namespace App\Repositories\Permission;

use LaravelEasyRepository\Repository;

interface PermissionRepository extends Repository{

    public function get();
}
