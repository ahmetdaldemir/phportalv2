<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Phone;
use App\Models\Seller;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PhoneController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get company ID from authenticated user or use fallback
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
        
            // Build query with eager loading
            $query = Phone::with(['brand:id,name', 'color:id,name', 'seller:id,name', 'version:id,name'])
                ->where('company_id', $companyId);

            // Apply filters
            if ($request->filled('brand')) {
                $query->where('brand_id', $request->brand);
            }

            if ($request->filled('version')) {
                $query->where('version_id', $request->version);
            }

            if ($request->filled('color')) {
                $query->where('color_id', $request->color);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('seller')) {
                if ($request->seller !== 'all') {
                    $query->where('seller_id', $request->seller);
                }
            } else {
                // Apply role-based filtering
                if (Auth::user()) {
                    $hasAdminRole = Auth::user()->roles()
                        ->whereIn('name', ['super-admin', 'Depo Sorumlusu'])
                        ->exists();
                    
                    if (!$hasAdminRole) {
                        $query->where('seller_id', Auth::user()->seller_id);
                    }
                }
            }

            if ($request->filled('barcode')) {
                $query->where('barcode', 'like', '%' . $request->barcode . '%');
            }

            if ($request->filled('imei')) {
                $query->where('imei', 'like', '%' . $request->imei . '%');
            }

            // Get paginated results
            $perPage = $request->get('per_page', 20);
            $phones = $query->orderBy('status', 'asc')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            // Transform data for frontend
            $phones->getCollection()->transform(function ($phone) {
                return [
                    'id' => $phone->id,
                    'imei' => $phone->imei,
                    'barcode' => $phone->barcode,
                    'brand_name' => $phone->brand->name ?? '-',
                    'version_name' => $phone->version->name ?? 'Bulunamadı',
                    'type' => $phone->type,
                    'type_text' => $this->getTypeText($phone->type),
                    'memory' => $phone->memory,
                    'color_name' => $phone->color->name ?? '-',
                    'battery' => $phone->batery,
                    'warranty' => $this->getWarrantyText($phone->warranty),
                    'seller_name' => $phone->seller->name ?? '-',
                    'cost_price' => $phone->cost_price,
                    'sale_price' => $phone->sale_price,
                    'status' => $phone->status,
                    'is_confirm' => $phone->is_confirm,
                    'created_at' => $phone->created_at->format('d.m.Y H:i'),
                    'updated_at' => $phone->updated_at->format('d.m.Y H:i')
                ];
            });

            return response()->json($phones);
        } catch (\Exception $e) {
            Log::error('Phone API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            
            $phone = Phone::with(['brand:id,name', 'color:id,name', 'seller:id,name', 'version:id,name'])
                ->where('company_id', $companyId)
                ->findOrFail($id);

            return response()->json($phone);
        } catch (\Exception $e) {
            Log::error('Phone show error: ' . $e->getMessage());
            return response()->json(['error' => 'Telefon bulunamadı'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'imei' => 'required|string|max:255',
                'brand_id' => 'required|exists:brands,id',
                'version_id' => 'required|exists:versions,id',
                'color_id' => 'required|exists:colors,id',
                'type' => 'required|in:new,old,assigned_device',
                'memory' => 'nullable|string',
                'battery' => 'nullable|integer|min:0|max:100',
                'warranty' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);

            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            $userId = Auth::id() ?: 1;

            $phone = Phone::create([
                'imei' => $request->imei,
                'barcode' => 'PH' . rand(1111111, 9999999),
                'brand_id' => $request->brand_id,
                'version_id' => $request->version_id,
                'color_id' => $request->color_id,
                'type' => $request->type,
                'memory' => $request->memory,
                'batery' => $request->battery,
                'warranty' => $request->warranty,
                'cost_price' => $request->cost_price,
                'sale_price' => $request->sale_price,
                'description' => $request->description,
                'seller_id' => Auth::user() ? Auth::user()->seller_id : 1,
                'user_id' => $userId,
                'company_id' => $companyId,
                'status' => 0,
                'is_confirm' => 0
            ]);

            return response()->json([
                'message' => 'Telefon başarıyla oluşturuldu',
                'data' => $phone
            ], 201);

        } catch (\Exception $e) {
            Log::error('Phone store error: ' . $e->getMessage());
            return response()->json(['error' => 'Telefon oluşturulurken hata oluştu'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'imei' => 'required|string|max:255',
                'brand_id' => 'required|exists:brands,id',
                'version_id' => 'required|exists:versions,id',
                'color_id' => 'required|exists:colors,id',
                'type' => 'required|in:new,old,assigned_device',
                'memory' => 'nullable|string',
                'battery' => 'nullable|integer|min:0|max:100',
                'warranty' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0',
                'sale_price' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);

            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            
            $phone = Phone::where('company_id', $companyId)->findOrFail($id);
            
            $phone->update([
                'imei' => $request->imei,
                'brand_id' => $request->brand_id,
                'version_id' => $request->version_id,
                'color_id' => $request->color_id,
                'type' => $request->type,
                'memory' => $request->memory,
                'batery' => $request->battery,
                'warranty' => $request->warranty,
                'cost_price' => $request->cost_price,
                'sale_price' => $request->sale_price,
                'description' => $request->description
            ]);

            return response()->json([
                'message' => 'Telefon başarıyla güncellendi',
                'data' => $phone
            ]);

        } catch (\Exception $e) {
            Log::error('Phone update error: ' . $e->getMessage());
            return response()->json(['error' => 'Telefon güncellenirken hata oluştu'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            
            $phone = Phone::where('company_id', $companyId)->findOrFail($id);
            $phone->delete();

            return response()->json(['message' => 'Telefon başarıyla silindi']);

        } catch (\Exception $e) {
            Log::error('Phone delete error: ' . $e->getMessage());
            return response()->json(['error' => 'Telefon silinirken hata oluştu'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_confirm' => 'required|boolean'
            ]);

            $companyId = Auth::user() ? Auth::user()->company_id : 1;
            
            $phone = Phone::where('company_id', $companyId)->findOrFail($id);
            $phone->is_confirm = $request->is_confirm;
            $phone->save();

            return response()->json(['message' => 'Durum güncellendi']);
        } catch (\Exception $e) {
            Log::error('Phone status update error: ' . $e->getMessage());
            return response()->json(['error' => 'Durum güncellenirken hata oluştu'], 500);
        }
    }

    public function getVersions(Request $request)
    {
        try {
            $request->validate([
                'brand_id' => 'required|integer'
            ]);

            $versions = Version::where('brand_id', $request->brand_id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($versions);
        } catch (\Exception $e) {
            Log::error('Versions fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Modeller yüklenirken hata oluştu'], 500);
        }
    }

    private function getTypeText($type)
    {
        $types = [
            'new' => 'Sıfır',
            'old' => 'İkinci El',
            'assigned_device' => 'Temnikli'
        ];

        return $types[$type] ?? $type;
    }

    private function getWarrantyText($warranty)
    {
        if ($warranty === null) {
            return 'Garantisiz';
        } elseif ($warranty === '1') {
            return 'Garantili';
        } elseif ($warranty === '2') {
            return 'Garantili';
        } else {
            try {
                return \Carbon\Carbon::parse($warranty)->format('d-m-Y');
            } catch (\Exception $e) {
                return 'Garantisiz';
            }
        }
    }
}
