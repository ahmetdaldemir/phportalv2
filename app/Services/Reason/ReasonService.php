<?php

namespace App\Services\Reason;

use Illuminate\Database\Eloquent\Collection;


interface ReasonService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
