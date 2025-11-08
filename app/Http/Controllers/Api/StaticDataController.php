<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Company;
use App\Models\Reason;
use App\Models\Seller;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaticDataController extends Controller
{
    public function companies()
    {
        try {
            $companies = Company::select('id', 'name')->get();
            return response()->json($companies);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Şirketler yüklenirken hata oluştu'], 500);
        }
    }

    public function sellers()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $sellers = Seller::where('company_id', $companyId)
                ->select('id', 'name')
                ->get();
            return response()->json($sellers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Şubeler yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function brands()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $brands = Brand::where('company_id', $companyId)
                ->select('id', 'name')
                ->get();
            return response()->json($brands);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Markalar yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function colors()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $colors = Color::where('company_id', $companyId)
                ->select('id', 'name')
                ->get();
            return response()->json($colors);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Renkler yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function categories()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $categories = DB::select("
                WITH RECURSIVE category_path (id, name, parent_id, path) AS (
                    SELECT id, name, parent_id, name as path
                    FROM categories
                    WHERE parent_id = 0 AND company_id = ? AND deleted_at IS NULL
                    UNION ALL
                    SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
                    FROM category_path cp 
                    JOIN categories k ON cp.id = k.parent_id 
                    WHERE k.deleted_at IS NULL
                )
                SELECT * FROM category_path ORDER BY path
            ", [$companyId]);

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function users()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $users = User::where('company_id', $companyId)
                ->select('id', 'name', 'email')
                ->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kullanıcılar yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function reasons()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $reasons = Reason::where('company_id', $companyId)
                ->select('id', 'name')
                ->get();
            return response()->json($reasons);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Nedenler yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    public function warehouses()
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $warehouses = Warehouse::where('company_id', $companyId)
                ->select('id', 'name')
                ->get();
            return response()->json($warehouses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Depolar yüklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }
}
