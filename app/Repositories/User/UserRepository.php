<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;

interface UserRepository extends BaseRepository{

    public function get();
    public function role($id,$data);
    public function lastInsertId();

}
