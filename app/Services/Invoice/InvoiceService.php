<?php

namespace App\Services\Invoice;

use Illuminate\Database\Eloquent\Collection;


interface InvoiceService {

    public function all(): ?Collection;
    public function get(): ?Collection;
}
