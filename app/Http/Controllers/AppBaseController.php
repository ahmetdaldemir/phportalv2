<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class AppBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Cache süreleri
     */
    protected const CACHE_TTL = [
        'categories' => 3600, // 1 saat
        'brands' => 1800,     // 30 dakika
        'colors' => 1800,     // 30 dakika
        'sellers' => 1800,    // 30 dakika
        'users' => 1800,      // 30 dakika
        'reasons' => 3600,    // 1 saat
        'warehouses' => 3600, // 1 saat
    ];

    /**
     * Ortak data array'i oluştur
     */
    protected function getCommonData(array $additionalData = []): array
    {
        $data = [];

        // Sadece gerekli olduğunda yükle
        if (request()->has('show_filters') || request()->filled('brand') || request()->filled('category')) {
            $data['brands'] = $this->getCachedBrands();
            $data['categories'] = $this->getCachedCategories();
            $data['colors'] = $this->getCachedColors();
            $data['sellers'] = $this->getCachedSellers();
            $data['users'] = $this->getCachedUsers();
            $data['reasons'] = $this->getCachedReasons();
            $data['warehouses'] = $this->getCachedWarehouses();
        }

        return array_merge($data, $additionalData);
    }

    /**
     * Cache'li categories sorgusu
     */
    protected function getCachedCategories()
    {
        return Cache::remember('categories_hierarchy', self::CACHE_TTL['categories'], function () {
            return DB::select("
                WITH RECURSIVE category_path (id, name, parent_id, path, level) AS (
                    SELECT id, name, parent_id, name as path, 0 as level
                    FROM categories
                    WHERE parent_id = 0 AND company_id = ? AND deleted_at IS NULL
                    
                    UNION ALL
                    
                    SELECT c.id, c.name, c.parent_id, 
                           CONCAT(cp.path, ' -> ', c.name),
                           cp.level + 1
                    FROM category_path cp 
                    JOIN categories c ON cp.id = c.parent_id 
                    WHERE c.deleted_at IS NULL AND cp.level < 10
                )
                SELECT * FROM category_path 
                ORDER BY path
                LIMIT 1000
            ", [auth()->user()->company_id]);
        });
    }

    /**
     * Cache'li brands sorgusu
     */
    protected function getCachedBrands()
    {
        return Cache::remember('brands_' . auth()->user()->company_id, self::CACHE_TTL['brands'], function () {
            return app(\App\Services\Brand\BrandService::class)->get();
        });
    }

    /**
     * Cache'li colors sorgusu
     */
    protected function getCachedColors()
    {
        return Cache::remember('colors_' . auth()->user()->company_id, self::CACHE_TTL['colors'], function () {
            return app(\App\Services\Color\ColorService::class)->get();
        });
    }

    /**
     * Cache'li sellers sorgusu
     */
    protected function getCachedSellers()
    {
        return Cache::remember('sellers_' . auth()->user()->company_id, self::CACHE_TTL['sellers'], function () {
            return app(\App\Services\Seller\SellerService::class)->all();
        });
    }

    /**
     * Cache'li users sorgusu
     */
    protected function getCachedUsers()
    {
        return Cache::remember('users_' . auth()->user()->company_id, self::CACHE_TTL['users'], function () {
            return app(\App\Services\User\UserService::class)->get();
        });
    }

    /**
     * Cache'li reasons sorgusu
     */
    protected function getCachedReasons()
    {
        return Cache::remember('reasons_' . auth()->user()->company_id, self::CACHE_TTL['reasons'], function () {
            return app(\App\Services\Reason\ReasonService::class)->get();
        });
    }

    /**
     * Cache'li warehouses sorgusu
     */
    protected function getCachedWarehouses()
    {
        return Cache::remember('warehouses_' . auth()->user()->company_id, self::CACHE_TTL['warehouses'], function () {
            return app(\App\Services\Warehouse\WarehouseService::class)->get();
        });
    }

    /**
     * Cache temizle
     */
    protected function clearCache(string $type = null): void
    {
        if ($type) {
            Cache::forget($type . '_' . auth()->user()->company_id);
        } else {
            // Tüm cache'i temizle
            foreach (array_keys(self::CACHE_TTL) as $cacheType) {
                Cache::forget($cacheType . '_' . auth()->user()->company_id);
            }
            Cache::forget('categories_hierarchy');
        }
    }

    /**
     * Pagination için optimize edilmiş sorgu
     */
    protected function getPaginatedResults($query, int $perPage = 20, array $with = [])
    {
        if (!empty($with)) {
            $query->with($with);
        }

        return $query->paginate($perPage);
    }

    /**
     * Response helper
     */
    protected function successResponse($data = [], string $message = 'İşlem başarılı', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse(string $message = 'Bir hata oluştu', int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}
