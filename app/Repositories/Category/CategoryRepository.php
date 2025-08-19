<?php

namespace App\Repositories\Category;

use App\Repositories\BaseRepository;

interface CategoryRepository extends BaseRepository{

    public function get();
    public function getList($category_id);

}
