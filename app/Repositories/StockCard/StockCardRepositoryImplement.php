<?php

namespace App\Repositories\StockCard;

use App\Models\StockCardMovement;
use App\Models\StockCardPrice;
use Illuminate\Support\Facades\Auth;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\StockCard;

class StockCardRepositoryImplement extends Eloquent implements StockCardRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(StockCard $model)
    {
        $this->model = $model;
    }

    public function get()
    {
        return $this->model->where('company_id', Auth::user()->company_id)->orderBy('id', 'desc')->get();
    }

    public function filter($arg)
    {
        $stock_card_movement = StockCardMovement::where('serial_number', $arg)->orderBy('id', 'desc')->first();
        $stock_card = $this->model->find($stock_card_movement->stock_card_id);
        return ['stock_card_movement' => $stock_card_movement, 'stock_card' => $stock_card];
    }

    public function getInvoiceForSerial($arg)
    {
        return StockCardMovement::where('invoice_id', $arg)->orderBy('id', 'desc')->get();
    }
    public function getStockData($arg)
    {
        $stock_card_movement = StockCardMovement::where('serial_number', $arg->serial)->orderBy('id', 'desc')->first();
        $stock_card_prices = StockCardPrice::where('stock_card_id', $arg->id)->orderBy('id', 'desc')->first();
        $stock_card = $this->model->find($arg->id);
        return ['stock_card_movement' => $stock_card_movement, 'stock_card' => $stock_card,'stock_card_price' => $stock_card_movement->sale_price];
    }
}
