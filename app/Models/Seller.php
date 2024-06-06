<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Scopes\TenantScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Seller extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'is_status', 'phone', 'company_id', 'user_id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope());
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function account()
    {
        return SellerAccountMonth::where('seller_id', $this->id)->where('mounth', date('m'))->first();
    }


    public function salesTable(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sale::class, 'seller_id', 'id');
    }

    public function totalCost()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalProfit = 0;
        $totalCost = 0;

        $sales = $this->salesTable()
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->get()->groupBy('invoice_id');
        if ($sales->count() > 1) {
            foreach ($sales as $key => $value) {
                $totalCost += $this->invoiceTotal($key);
                foreach ($value as $item) {
                    $profit =  $item->base_cost_price;
                    $totalProfit += $profit;
                }
            }
        }

        return $totalCost - $totalProfit;
    }

    public function invoiceTotal($id)
    {
        return Invoice::find($id)->total_price;
    }

}
