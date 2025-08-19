<?php

namespace App\Services\Technical;

use Illuminate\Database\Eloquent\Collection;


interface TechnicalService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
