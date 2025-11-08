<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * Display a listing of sales
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['customer', 'stockCard', 'seller', 'user'])
            ->where('company_id', Auth::user()->company_id);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function ($customerQuery) use ($request) {
                      $customerQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($sales);
    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'stock_card_id' => 'required|exists:stock_cards,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'seller_id' => 'required|exists:sellers,id',
            'description' => 'nullable|string',
        ]);

        $sale = Sale::create([
            'number' => 'SAT-' . time(),
            'customer_id' => $request->customer_id,
            'stock_card_id' => $request->stock_card_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $request->total_price,
            'seller_id' => $request->seller_id,
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'description' => $request->description,
            'status' => 'completed',
        ]);

        return response()->json($sale->load(['customer', 'stockCard', 'seller', 'user']), 201);
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['customer', 'stockCard', 'seller', 'user', 'invoice']);
        
        return response()->json($sale);
    }

    /**
     * Update the specified sale
     */
    public function update(Request $request, Sale $sale): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'stock_card_id' => 'required|exists:stock_cards,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'seller_id' => 'required|exists:sellers,id',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $sale->update($request->all());

        return response()->json($sale->load(['customer', 'stockCard', 'seller', 'user']));
    }

    /**
     * Remove the specified sale
     */
    public function destroy(Sale $sale): JsonResponse
    {
        $sale->delete();
        
        return response()->json(['message' => 'Sale deleted successfully']);
    }
}
