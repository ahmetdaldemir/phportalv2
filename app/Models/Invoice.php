<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Invoice extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices';
    protected $casts = [
        'detail' => 'array',
    ];
    protected $fillable = [
        'type',
        'number',
        'create_date',
        'credit_card',
        'cash',
        'installment',
        'description',
        'is_status',
        'total_price',
        'tax_total',
        'discount_total',
        'staff_id',
        'customer_id',
        'user_id',
        'company_id',
        'safe_id',
        'exchange',
        'tax',
        'file',
        'paymentStatus',
        'paymentDate',
        'paymentStaff',
        'periodMounth',
        'periodYear',
        'accounting_category_id',
        'currency',
        'detail',
    ];

    public const INVOICE_TYPE = [
        '2' => 'Giden Fatura',
        '1' => 'Gelen Fatura'
    ];

    public const INVOICE_TYPE_COLOR = [
        '1' => 'success',
        '2' => 'danger'
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }
    public function invoice_type($type): string
    {
        return self::INVOICE_TYPE[$type];
    }

    public function invoice_type_color($type): string
    {
        return self::INVOICE_TYPE_COLOR[$type];
    }

    public function account(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function detail(): hasMany
    {
        return $this->hasMany(StockCardMovement::class, 'invoice_id', 'id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'invoice_id', 'id');
    }

    public function seller(): hasOne
    {
        return $this->hasOne(Seller::class, 'id', 'seller');
    }

    public function staff(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }

    public function hasSeller($id): string
    {
        return $this->seller_id == $id ? 'true' : 'false';
    }

    public function hasCategory($id): string
    {
        return $this->category_id == $id ? 'true' : 'false';
    }

    public function hasWarehouse($id): string
    {
        return $this->warehouse_id == $id ? 'true' : 'false';
    }

    public function hasBrand($id): string
    {
        return $this->brand_id == $id ? 'true' : 'false';
    }

    public function hasVersion($id): string
    {
        return $this->version_id == $id ? 'true' : 'false';
    }

    public function hasColor($id): string
    {
        return $this->color_id == $id ? 'true' : 'false';
    }

    public function hasStock($id): string
    {
        return $this->stock_card_id == $id ? 'true' : 'false';
    }

    public function hasReason($id): string
    {
        return $this->reason_id == $id ? 'true' : 'false';
    }

    public function hasStaff($id): string
    {
        return $this->staff_id == $id ? 'true' : 'false';
    }

    public function hasSafe($id): string
    {
        return $this->safe_id == $id ? 'true' : 'false';
    }

    public function totalCost()
    {
        return StockCardMovement::where('invoice_id', $this->id)->where('type', 1)->sum(DB::raw('quantity * cost_price'));
    }

    public function totalBaseCost()
    {
        return StockCardMovement::where('invoice_id', $this->id)->where('type', 1)->sum(DB::raw('quantity * base_cost_price'));
    }

    public function totalSale()
    {
        return StockCardMovement::where('invoice_id', $this->id)->where('type', 1)->sum(DB::raw('quantity * sale_price'));
    }

    public function totalSaleBaseCost()
    {
        $x = 0;
        $sale = Sale::where('invoice_id', $this->id)->get();
            foreach ($sale as $item) {
                if($item->type == 1)
                {
                    $x += Phone::where('id', $item->stock_card_movement_id)->first()->cost_price??0;
                }else{
                    $x += StockCardMovement::where('id', $item->stock_card_movement_id)->first()->base_cost_price??0;
                }
            }
        return $x;
    }


}
