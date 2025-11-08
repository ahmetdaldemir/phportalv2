<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\Sale;
use App\Models\StockCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(): JsonResponse
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'totalTransfers' => Transfer::where('company_id', $companyId)->count(),
            'pendingTransfers' => Transfer::where('company_id', $companyId)
                ->whereIn('is_status', [1, 2])
                ->count(),
            'totalSales' => Sale::where('company_id', $companyId)->count(),
            'totalStockCards' => StockCard::where('company_id', $companyId)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent transfers
     */
    public function recentTransfers(): JsonResponse
    {
        $transfers = Transfer::with(['user', 'mainSeller', 'deliverySeller'])
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($transfers);
    }
}
