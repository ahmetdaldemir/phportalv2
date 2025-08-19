<?php

namespace App\Services\AccountingCategory;

use Illuminate\Database\Eloquent\Collection;


interface AccountingCategoryService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
