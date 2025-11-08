<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StockCardController extends Controller
{
    /**
     * Display a listing of stock cards
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockCard::with(['category', 'brand', 'version', 'color', 'warehouse', 'seller'])
            ->where('company_id', Auth::user()->company_id);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }

        $stockCards = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($stockCards);
    }

    /**
     * Store a newly created stock card
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:stock_cards,code',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'version_id' => 'nullable|exists:versions,id',
            'color_id' => 'nullable|exists:colors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'seller_id' => 'required|exists:sellers,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $stockCard = StockCard::create([
            'name' => $request->name,
            'code' => $request->code,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'version_id' => $request->version_id,
            'color_id' => $request->color_id,
            'warehouse_id' => $request->warehouse_id,
            'seller_id' => $request->seller_id,
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'description' => $request->description,
        ]);

        return response()->json($stockCard->load(['category', 'brand', 'version', 'color', 'warehouse', 'seller']), 201);
    }

    /**
     * Display the specified stock card
     */
    public function show(StockCard $stockCard): JsonResponse
    {
        $stockCard->load(['category', 'brand', 'version', 'color', 'warehouse', 'seller', 'movements']);
        
        return response()->json($stockCard);
    }

    /**
     * Update the specified stock card
     */
    public function update(Request $request, StockCard $stockCard): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:stock_cards,code,' . $stockCard->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'version_id' => 'nullable|exists:versions,id',
            'color_id' => 'nullable|exists:colors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'seller_id' => 'required|exists:sellers,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $stockCard->update($request->all());

        return response()->json($stockCard->load(['category', 'brand', 'version', 'color', 'warehouse', 'seller']));
    }

    /**
     * Remove the specified stock card
     */
    public function destroy(StockCard $stockCard): JsonResponse
    {
        $stockCard->delete();
        
        return response()->json(['message' => 'Stock card deleted successfully']);
    }
}
