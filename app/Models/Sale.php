<?php

namespace App\Models;

use App\Observers\SaleObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;

    const STATUS = [
        "1" => "Telefon",
        "2" => "Aksesuar",
        "3" => "Teknik Ürün",
        "4" => "Kaplama",
        "5" => "Baskı",
        "6" => "Diğer",
    ];

    const STATUS_STRING = [
        "Telefon" => "1",
        "Aksesuar" => "2",
        "Teknik" => "3",
        "Kaplama" => "4",
        "Baski" => "5",
        "Diger" => "6",
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(SaleObserver::class);
        static::addGlobalScope(new CompanyScope);

    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function stock_card_movement(): HasOne
    {
        return $this->hasOne(StockCardMovement::class, 'id', 'stock_card_movement_id');
    }

    public function stockCardMovement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StockCardMovement::class, 'stock_card_movement_id');
    }

    public function stock_card(): HasOne
    {
        return $this->hasOne(StockCard::class, 'id', 'stock_card_id');
    }

    public function stockCard(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StockCard::class, 'stock_card_id');
    }

    public function statusName()
    {
        return self::STATUS[$this->type];
    }


    public function phone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Phone::class, 'stock_card_movement_id');
    }

    public function stock_card_list(): HasOne
    {
        return $this->hasOne(StockCard::class, 'id', 'stock_card_id');
    }

    public function technical(): HasOne
    {
        return $this->hasOne(TechnicalService::class, 'technical_person', 'technical_service_person_id');
    }

    public function sellerTable(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function seller(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function deliveryPersonnel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_personnel');
    }


    public static function totalMonthlySales($user, $type,$period)
    {
        $query = self::where('user_id', $user)
            ->where('company_id', Auth::user()->company_id)
            ->where('type', $type);

        if ($period === 'daily') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === 'monthly') {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $query->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear);
        } else {
            throw new \InvalidArgumentException('Invalid period provided. Use "daily" or "monthly".');
        }

        return $query->sum('sale_price') ?? 0;
    }


    public static function getTechnical($user, $request, $month)
    {
        $personData = [];
        if ($month == false) {
            $date1 = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
            $date2 = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $date1 = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $date2 = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        }

        $add_db = $request->filled('person') ? 'sellers' : 'users';
        $add_db_id = $request->filled('person') ? 'seller_id' : 'delivery_staff';

        $companyId = Auth::user()->company_id;



        $total = TechnicalService::where('payment_status', 1)
            ->whereBetween('updated_at', [$date1, $date2])
            ->where('company_id', $companyId)
            ->where($add_db_id, $user)
            ->sum('customer_price');

        return $total ?? 0;

    }



    public static function getCover($user, $request, $month)
    {
        $personData = [];
        if ($month == false) {
            $date1 = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
            $date2 = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $date1 = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $date2 = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        }

        $add_db = $request->filled('person') ? 'sellers' : 'users';
        $add_db_id = $request->filled('person') ? 'seller_id' : 'delivery_staff';

        $companyId = Auth::user()->company_id;



        $total = TechnicalCustomService::where('payment_status', 1)
            ->whereBetween('updated_at', [$date1, $date2])
            ->where('company_id', $companyId)
            ->where($add_db_id, $user)
            ->sum('customer_price');

        return $total ?? 0;

    }





}
