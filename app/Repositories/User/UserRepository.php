<?php

namespace App\Repositories\User;

use LaravelEasyRepository\Repository;

interface UserRepository extends Repository{

    public function get();
    public function role($id,$data);
    public function lastInsertId();

}
