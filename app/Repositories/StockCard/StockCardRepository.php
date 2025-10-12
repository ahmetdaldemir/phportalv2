<?php

namespace App\Repositories\StockCard;

use LaravelEasyRepository\Repository;

interface StockCardRepository extends Repository{

    public function get();
    public function filter($arg);
    public function getStockData($arg);
    public function getInvoiceForSerial($arg);

}
