<?php

namespace App\Services\AccountingCategory;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface AccountingCategoryService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
