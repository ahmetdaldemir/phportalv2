<?php

namespace App\Services\Safe;

use Illuminate\Database\Eloquent\Collection;


interface SafeService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
