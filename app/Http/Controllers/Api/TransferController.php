<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    /**
     * Display a listing of transfers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transfer::with(['user', 'mainSeller', 'deliverySeller', 'confirmUser', 'company'])
            ->where('company_id', Auth::user()->company_id);

        // Apply filters
        if ($request->filled('stockName')) {
            $query->whereHas('stockCardMovements.stockCard', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->stockName . '%');
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('stockCardMovements.stockCard', function ($q) use ($request) {
                $q->where('brand_id', $request->brand);
            });
        }

        if ($request->filled('version')) {
            $query->whereHas('stockCardMovements.stockCard', function ($q) use ($request) {
                $q->where('version_id', $request->version);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('stockCardMovements.stockCard', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        if ($request->filled('color')) {
            $query->whereHas('stockCardMovements.stockCard', function ($q) use ($request) {
                $q->where('color_id', $request->color);
            });
        }

        if ($request->filled('seller') && $request->seller !== 'all') {
            $query->where('main_seller_id', $request->seller);
        }

        if ($request->filled('serialNumber')) {
            $query->where('serial_list', 'like', '%' . $request->serialNumber . '%');
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($transfers);
    }

    /**
     * Store a newly created transfer
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:phone,other',
            'main_seller_id' => 'required|exists:sellers,id',
            'delivery_seller_id' => 'required|exists:sellers,id',
            'serial_list' => 'required|array|min:1',
            'serial_list.*' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $transfer = Transfer::create([
            'number' => 'TRF-' . time(),
            'type' => $request->type,
            'main_seller_id' => $request->main_seller_id,
            'delivery_seller_id' => $request->delivery_seller_id,
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'serial_list' => $request->serial_list,
            'description' => $request->description,
            'is_status' => 1,
        ]);

        return response()->json($transfer->load(['user', 'mainSeller', 'deliverySeller']), 201);
    }

    /**
     * Display the specified transfer
     */
    public function show(Transfer $transfer): JsonResponse
    {
        $transfer->load(['user', 'mainSeller', 'deliverySeller', 'confirmUser', 'company', 'stockCardMovements.stockCard']);
        
        return response()->json($transfer);
    }

    /**
     * Update the specified transfer
     */
    public function update(Request $request, Transfer $transfer): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:phone,other',
            'main_seller_id' => 'required|exists:sellers,id',
            'delivery_seller_id' => 'required|exists:sellers,id',
            'serial_list' => 'required|array|min:1',
            'serial_list.*' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $transfer->update([
            'type' => $request->type,
            'main_seller_id' => $request->main_seller_id,
            'delivery_seller_id' => $request->delivery_seller_id,
            'serial_list' => $request->serial_list,
            'description' => $request->description,
        ]);

        return response()->json($transfer->load(['user', 'mainSeller', 'deliverySeller']));
    }

    /**
     * Remove the specified transfer
     */
    public function destroy(Transfer $transfer): JsonResponse
    {
        $transfer->delete();
        
        return response()->json(['message' => 'Transfer deleted successfully']);
    }

    /**
     * Update transfer status
     */
    public function updateStatus(Request $request, Transfer $transfer): JsonResponse
    {
        $request->validate([
            'status' => 'required|integer|between:1,5',
        ]);

        $transfer->update([
            'is_status' => $request->status,
            'comfirm_id' => Auth::id(),
            'comfirm_date' => now(),
        ]);

        return response()->json($transfer->load(['user', 'mainSeller', 'deliverySeller', 'confirmUser']));
    }

    /**
     * Approve transfer
     */
    public function approve(Transfer $transfer): JsonResponse
    {
        $transfer->update([
            'is_status' => 3,
            'comfirm_id' => Auth::id(),
            'comfirm_date' => now(),
        ]);

        return response()->json($transfer->load(['user', 'mainSeller', 'deliverySeller', 'confirmUser']));
    }

    /**
     * Reject transfer
     */
    public function reject(Transfer $transfer): JsonResponse
    {
        $transfer->update([
            'is_status' => 4,
            'comfirm_id' => Auth::id(),
            'comfirm_date' => now(),
        ]);

        return response()->json($transfer->load(['user', 'mainSeller', 'deliverySeller', 'confirmUser']));
    }
}
