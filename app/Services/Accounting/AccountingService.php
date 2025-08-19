<?php

namespace App\Services\Accounting;

use Illuminate\Database\Eloquent\Collection;


interface AccountingService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
