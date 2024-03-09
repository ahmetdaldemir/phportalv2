<?php

namespace App\Services\StockCard;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface StockCardService extends BaseService{

    public function all(): ?Collection;
    public function get(): ?Collection;
    public function add_movement(array $request, $invoiceID, $type);
    public function add_movement_sales(array $request, $invoiceID, $type);
    public function filter($arg);
    public function getInvoiceForSerial($arg);
    public function getStockData($arg);
    public function stockSearch($arg);
}
