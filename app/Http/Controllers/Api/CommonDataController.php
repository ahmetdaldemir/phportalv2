<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Seller\SellerService;
use App\Services\Category\CategoryService;
use App\Services\Warehouse\WarehouseService;
use App\Services\Color\ColorService;
use App\Services\Brand\BrandService;
use App\Services\Version\VersionService;
use App\Services\Reason\ReasonService;
use App\Services\Customer\CustomerService;
use App\Services\Safe\SafeService;
use App\Services\User\UserService;
use App\Models\City;
use App\Models\Town;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CommonDataController extends Controller
{
    private SellerService $sellerService;
    private CategoryService $categoryService;
    private WarehouseService $warehouseService;
    private ColorService $colorService;
    private BrandService $brandService;
    private VersionService $versionService;
    private ReasonService $reasonService;
    private CustomerService $customerService;
    private SafeService $safeService;
    private UserService $userService;

    public function __construct(
        SellerService $sellerService,
        CategoryService $categoryService,
        WarehouseService $warehouseService,
        ColorService $colorService,
        BrandService $brandService,
        VersionService $versionService,
        ReasonService $reasonService,
        CustomerService $customerService,
        SafeService $safeService,
        UserService $userService
    ) {
        $this->sellerService = $sellerService;
        $this->categoryService = $categoryService;
        $this->warehouseService = $warehouseService;
        $this->colorService = $colorService;
        $this->brandService = $brandService;
        $this->versionService = $versionService;
        $this->reasonService = $reasonService;
        $this->customerService = $customerService;
        $this->safeService = $safeService;
        $this->userService = $userService;
    }

    /**
     * Get Sellers with caching
     */
    public function getSellers()
    {
        return Cache::remember('sellers_' . auth()->user()->company_id, 300, function () {
            return $this->sellerService->get();
        });
    }

    /**
     * Get Categories with caching
     */
    public function getCategories()
    {
        return Cache::remember('categories_' . auth()->user()->company_id, 300, function () {
            return $this->categoryService->get();
        });
    }

    /**
     * Get Warehouses with caching
     */
    public function getWarehouses()
    {
        return Cache::remember('warehouses_' . auth()->user()->company_id, 300, function () {
            return $this->warehouseService->get();
        });
    }

    /**
     * Get Colors with caching
     */
    public function getColors()
    {
        return Cache::remember('colors_' . auth()->user()->company_id, 300, function () {
            return $this->colorService->get();
        });
    }

    /**
     * Get Brands with caching
     */
    public function getBrands()
    {
        return Cache::remember('brands_' . auth()->user()->company_id, 300, function () {
            return $this->brandService->get();
        });
    }

    /**
     * Get Versions with optional brand filtering
     */
    public function getVersions(Request $request)
    {
        $brandId = $request->get('brand_id');
        $cacheKey = $brandId ? 
            "versions_brand_{$brandId}_" . auth()->user()->company_id : 
            'versions_' . auth()->user()->company_id;

        return Cache::remember($cacheKey, 300, function () use ($brandId) {
            $versions = $this->versionService->get();
            
            if ($brandId) {
                return $versions->filter(function ($version) use ($brandId) {
                    return $version->brand_id == $brandId;
                })->values();
            }
            
            return $versions;
        });
    }

    /**
     * Get Reasons with caching
     */
    public function getReasons()
    {
        return Cache::remember('reasons_' . auth()->user()->company_id, 300, function () {
            return $this->reasonService->get();
        });
    }

    /**
     * Get Customers with optional type filtering
     */
    public function getCustomers(Request $request)
    {
        $type = $request->get('type');
        $cacheKey = $type ? 
            "customers_{$type}_" . auth()->user()->company_id : 
            'customers_' . auth()->user()->company_id;

        return Cache::remember($cacheKey, 300, function () use ($type) {
            $customers = $this->customerService->all();
            
            if ($type) {
                return $customers->filter(function ($customer) use ($type) {
                    return $customer->type === $type;
                })->values();
            }
            
            return $customers;
        });
    }

    /**
     * Get Cities with caching
     */
    public function getCities()
    {
        return Cache::remember('cities', 3600, function () { // 1 hour cache
            return City::all();
        });
    }

    /**
     * Get Towns by City with caching
     */
    public function getTowns(Request $request)
    {
        $cityId = $request->get('city_id');
        
        if (!$cityId) {
            return response()->json([]);
        }
        
        return Cache::remember("towns_city_{$cityId}", 3600, function () use ($cityId) {
            return Town::where('city_id', $cityId)->get();
        });
    }

    /**
     * Get Currencies with caching
     */
    public function getCurrencies()
    {
        return Cache::remember('currencies', 3600, function () {
            return Currency::all();
        });
    }

    /**
     * Get Safes with caching
     */
    public function getSafes()
    {
        return Cache::remember('safes_' . auth()->user()->company_id, 300, function () {
            return $this->safeService->all();
        });
    }

    /**
     * Get Users with caching
     */
    public function getUsers()
    {
        return Cache::remember('users_' . auth()->user()->company_id, 300, function () {
            return $this->userService->get();
        });
    }

    /**
     * Get all common data at once
     */
    public function getAllCommonData()
    {
        return [
            'sellers' => $this->getSellers(),
            'categories' => $this->getCategories(),
            'warehouses' => $this->getWarehouses(),
            'colors' => $this->getColors(),
            'brands' => $this->getBrands(),
            'versions' => $this->getVersions(request()),
            'reasons' => $this->getReasons(),
            'customers' => $this->getCustomers(request()),
            'cities' => $this->getCities(),
            'currencies' => $this->getCurrencies(),
            'safes' => $this->getSafes(),
            'users' => $this->getUsers()
        ];
    }

    /**
     * Clear cache for specific type
     */
    public function clearCache(Request $request)
    {
        $type = $request->get('type');
        $companyId = auth()->user()->company_id;
        
        switch($type) {
            case 'sellers':
                Cache::forget("sellers_{$companyId}");
                break;
            case 'categories':
                Cache::forget("categories_{$companyId}");
                break;
            case 'warehouses':
                Cache::forget("warehouses_{$companyId}");
                break;
            case 'colors':
                Cache::forget("colors_{$companyId}");
                break;
            case 'brands':
                Cache::forget("brands_{$companyId}");
                break;
            case 'versions':
                Cache::forget("versions_{$companyId}");
                // Clear brand-specific caches too
                $brands = $this->brandService->get();
                foreach ($brands as $brand) {
                    Cache::forget("versions_brand_{$brand->id}_{$companyId}");
                }
                break;
            case 'reasons':
                Cache::forget("reasons_{$companyId}");
                break;
            case 'customers':
                Cache::forget("customers_{$companyId}");
                Cache::forget("customers_account_{$companyId}");
                Cache::forget("customers_customer_{$companyId}");
                break;
            case 'cities':
                Cache::forget('cities');
                break;
            case 'currencies':
                Cache::forget('currencies');
                break;
            case 'safes':
                Cache::forget("safes_{$companyId}");
                break;
            case 'users':
                Cache::forget("users_{$companyId}");
                break;
            case 'all':
                // Clear all caches
                $patterns = [
                    "sellers_{$companyId}",
                    "categories_{$companyId}",
                    "warehouses_{$companyId}",
                    "colors_{$companyId}",
                    "brands_{$companyId}",
                    "versions_{$companyId}",
                    "reasons_{$companyId}",
                    "customers_{$companyId}",
                    "customers_account_{$companyId}",
                    "customers_customer_{$companyId}",
                    "safes_{$companyId}",
                    "users_{$companyId}",
                    'cities',
                    'currencies'
                ];
                
                foreach ($patterns as $pattern) {
                    Cache::forget($pattern);
                }
                break;
            default:
                return response()->json(['error' => 'Invalid cache type'], 400);
        }
        
        return response()->json(['message' => 'Cache cleared successfully']);
    }
}
