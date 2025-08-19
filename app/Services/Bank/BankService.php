<?php

namespace App\Services\Bank;

use Illuminate\Database\Eloquent\Collection;


interface BankService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
