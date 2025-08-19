<?php

namespace App\Services\Color;

use Illuminate\Database\Eloquent\Collection;


interface ColorService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
