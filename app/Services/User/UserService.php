<?php

namespace App\Services\User;

use Illuminate\Database\Eloquent\Collection;


interface UserService {

    public function all(): ?Collection;

    public function get(): ?Collection;
    public function role($id,$data);
    public function lastInsertId();


}
