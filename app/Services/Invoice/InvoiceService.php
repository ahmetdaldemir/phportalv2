<?php

namespace App\Services\Invoice;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface InvoiceService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
}
