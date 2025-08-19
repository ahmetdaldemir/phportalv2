<?php

namespace App\Repositories\StockCard;

use App\Repositories\BaseRepository;

interface StockCardRepository extends BaseRepository{

    public function get();
    public function filter($arg);
    public function getStockData($arg);
    public function getInvoiceForSerial($arg);

}
