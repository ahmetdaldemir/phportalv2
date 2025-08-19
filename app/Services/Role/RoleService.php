<?php

namespace App\Services\Role;

use Illuminate\Database\Eloquent\Collection;


interface RoleService {

    public function get(): ?Collection;

}
