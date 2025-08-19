<?php

namespace App\Services\Category;

use Illuminate\Database\Eloquent\Collection;


interface CategoryService {

    public function all(): ?Collection;
    public function get(): ?Collection;
    public function getList($category_id);
    public function getAllParentList($category_id);
}
