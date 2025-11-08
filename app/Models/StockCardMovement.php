<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Traits\NotifiesOnDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Activity;

class StockCardMovement extends BaseModel
{


    protected $table = "stock_card_movements";
    use HasFactory, SoftDeletes, NotifiesOnDelete;

    public $incrementing = true; // bu olmalı
    protected $keyType = 'int';
    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

    protected string $message = '';

    protected $fillable = [
        'stock_card_id',
        'user_id',
        'color_id',
        'warehouse_id',
        'seller_id',
        'reason_id',
        'type',
        'quantity',
        'serial_number',
        'invoice_id',
        'tax',
        'cost_price',
        'base_cost_price',
        'sale_price',
        'description',
        'assigned_accessory',
        'assigned_device',
        'company_id',
        'prefix'
    ];


    const TYPE = [
        '1' => 'Satışta',
        '2' => 'Satıldı', //Kullanılmayacak
        '3' => 'Hasarlı',
        '4' => 'Transfer Sürecinde',
        '5' => 'Teknik Servis Sürecinde',
     ];

    public function getTypeNameAttribute()
    {
        return self::TYPE[$this->type] ?? 'Bilinmiyor';
    }

    public function addMessage($message)
    {
        $this->message = $message;
    }


    public function stock(): BelongsTo
    {
        return $this->belongsTo(StockCard::class, 'stock_card_id', 'id');
    }

    public function quantityCheck($serial_number)
    {
        $in = StockCardMovement::where('serial_number', $serial_number)->where('type', 1)->sum('quantity');
        $out = StockCardMovement::where('serial_number', $serial_number)->where('type', 2)->sum('quantity');
        return $in - $out;
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


    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');      
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'id');      
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id', 'id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class, 'version_id', 'id');
    }

    public function transfer()
    {
        return $this->hasOne(Transfer::class, 'stock_card_movement_id', 'id');
    }


    public function sale()
    {
        return $this->hasOne(Sale::class, 'stock_card_movement_id', 'id');
    }


    public function stockCard(): BelongsTo
    {
        return $this->belongsTo(StockCard::class, 'stock_card_id', 'id');
    }

    public function quantityCheckData()
    {
        $in = StockCardMovement::where('serial_number', $this->serial_number)->where('type', 1)->sum('quantity');
        $out = StockCardMovement::where('serial_number', $this->serial_number)->where('type', 2)->sum('quantity');
        return (int)$in - (int)$out;
    }

    public function quantityCheckDataNew()
    {
        $in = Sale::where('stock_card_movement_id', $this->id)->first();
        if ($in) {
            return 0;
        }else{
            return 1;
        }
    }

    public  function testParent($category_id=0)
    {
        $x = Category::find($category_id);
        $data=null;
        $categories = Category::where('id', $x->parent_id)->get();
//dd($categories);
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'list' => $this->testParent($category->id),
                'name' => $category->name
            ];
        }
return $data;
    }
    public function categorySeperator($data)
    {
        if(!empty($data))
        {
            return implode("/",$this->array_column_recursive($data, 'name'))." /";
        }
        return "";
    }

    function array_column_recursive(array $haystack, $needle)
    {
        $found = [];
        array_walk_recursive($haystack, function ($value, $key) use (&$found, $needle) {
            if ($key == $needle)
                $found[] = $value;
        });
        return $found;
    }
    public function deletedUser(): string
    {
       $log =  Activity::where('subject_id',$this->id)->where('event','deleted')->first();
       if($log)
       {
           return User::find($log->causer_id)->name;
       }
       return "Bulunamadı";

    }

}
