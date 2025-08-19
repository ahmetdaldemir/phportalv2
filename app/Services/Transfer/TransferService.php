<?php

namespace App\Services\Transfer;

use Illuminate\Database\Eloquent\Collection;


interface TransferService {

    public function all(): ?Collection;
}
