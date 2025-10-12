<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Traits\NotifiesOnDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class StockCard extends BaseModel
{

    protected $table = "stock_cards";
    use HasFactory, SoftDeletes, NotifiesOnDelete;

    protected string $message = '';

    protected $fillable = [
        'company_id',
        'user_id',
        'category_id',
        'warehouse_id',
        'seller_id',
        'brand_id',
        'version_id',
        'color_id',
        'sku',
        'barcode',
        'tracking',
        'unit',
        'tracking_quantity',
        'is_status',
        'name'
    ];
    protected $casts = ['version_id' => 'array'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

    public function addMessage($message)
    {
        $this->message = $message;
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
        return $this->id == $id ? 'true' : 'false';
    }


    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function version()
    {
        $array = $this->version_id;

        $names = collect($array)->map(function ($name, $key) {
             return Version::find($name)->name ?? "Belirtilmedi";
        });
        return $names->toJson();
        //return $this->hasOne(Version::class, 'id', 'version_id');
    }
    
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quantity()
    {
        $in = StockCardMovement::where('stock_card_id', $this->id)->where('company_id',Auth::user()->company_id)->where('type', 1)->sum('quantity');
         return $in;
    }

    public function quantityId($id)
    {
        $in = StockCardMovement::where('stock_card_id', $id)->where('company_id',Auth::user()->company_id)->where('type', 1)->sum('quantity');
         return $in;
    }


    public function stockCardPrice()
    {
        return $this->hasOne(StockCardPrice::class, 'id', 'stock_card_id');
    }

    public static function getStockCardPrice($id)
    {
       return StockCardPrice::where('stock_card_id',$id)->where('company_id',Auth::user()->company_id)->orderBy('id','desc')->first();
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

    public function movements() : BelongsTo
    {
        return $this->belongsTo(StockCardMovement::class,'stock_card_id','id');
    }

    public function versions()
    {
        return $this->belongsToMany(Version::class, 'versions', 'stock_id', 'version_id');
    }
}
