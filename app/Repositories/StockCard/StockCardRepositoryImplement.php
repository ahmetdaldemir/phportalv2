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
        return $this->model->with(['brand', 'category']) // Eager loading - N+1 sorununu çöz
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('id', 'desc')
            ->limit(50) // Performans optimizasyonu - maksimum 50 kayıt
            ->get();
    }

    public function filter($arg)
    {
        // Validate input parameter
        if (!$arg || empty($arg)) {
            return [
                'stock_card_movement' => null, 
                'stock_card' => null
            ];
        }

        $stock_card_movement = StockCardMovement::where('serial_number', $arg)
            ->orderBy('id', 'desc')
            ->first();
        
        // Check if movement found
        if (!$stock_card_movement || !$stock_card_movement->stock_card_id) {
            return [
                'stock_card_movement' => $stock_card_movement, 
                'stock_card' => null
            ];
        }

        $stock_card = $this->model->find($stock_card_movement->stock_card_id);
        
        return [
            'stock_card_movement' => $stock_card_movement, 
            'stock_card' => $stock_card
        ];
    }

    public function getInvoiceForSerial($arg)
    {
        return StockCardMovement::with(['stock.brand', 'stock.category', 'color'])
            ->where('invoice_id', $arg)
            ->orderBy('id', 'desc')
            ->get();
    }
    public function getStockData($arg)
    {
        // Validate input parameters
        if (!$arg || (!isset($arg->serial) && !isset($arg->id))) {
            return [
                'stock_card_movement' => null, 
                'stock_card' => null,
                'stock_card_price' => null
            ];
        }

        $stock_card_movement = null;
        $stock_card = null;
        $stock_card_price = null;

        // Get stock card movement if serial number provided
        if (isset($arg->serial) && !empty($arg->serial)) {
            $stock_card_movement = StockCardMovement::where('serial_number', $arg->serial)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Get stock card if id provided
        if (isset($arg->id) && !empty($arg->id)) {
            $stock_card = $this->model->find($arg->id);
            
            // Get stock card prices
            $stock_card_prices = StockCardPrice::where('stock_card_id', $arg->id)
                ->orderBy('id', 'desc')
                ->first();
        }

        // Determine stock card price
        if ($stock_card_movement) {
            $stock_card_price = $stock_card_movement->sale_price;
        } elseif ($stock_card_prices) {
            $stock_card_price = $stock_card_prices->sale_price ?? null;
        }

        return [
            'stock_card_movement' => $stock_card_movement, 
            'stock_card' => $stock_card,
            'stock_card_price' => $stock_card_price
        ];
    }
}
