<?php

namespace App\Services\Refund;

use Illuminate\Database\Eloquent\Collection;


interface RefundService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
