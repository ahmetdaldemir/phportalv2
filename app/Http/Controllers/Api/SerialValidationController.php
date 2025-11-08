<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\StockCardMovement;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SerialValidationController extends Controller
{
    /**
     * Validate serial number
     */
    public function validate(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
            'seller_id' => 'required|exists:sellers,id',
            'type' => 'required|in:phone,other'
        ]);

        $user = Auth::user();
        $serialNumber = $request->serial_number;
        $sellerId = $request->seller_id;
        $type = $request->type;

        // Check if serial number already exists in current transfers
        $existingTransfer = Transfer::where('company_id', $user->company_id)
            ->whereJsonContains('serial_list', $serialNumber)
            ->whereIn('is_status', [1, 2]) // Pending or pre-approved
            ->first();

        if ($existingTransfer) {
            return response()->json([
                'valid' => false,
                'message' => 'Bu seri numarası zaten sevk sürecinde'
            ]);
        }

        if ($type === 'phone') {
            $phone = Phone::where('barcode', $serialNumber)
                ->where('seller_id', $sellerId)
                ->first();

            if (!$phone) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Telefon bulunamadı veya farklı bayiye ait'
                ]);
            }

            if ($phone->status != 0) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Bu telefon zaten sevk edilmiş'
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Telefon seri numarası geçerli',
                'data' => [
                    'name' => $phone->brand->name . ' ' . $phone->version->name,
                    'brand' => $phone->brand->name,
                    'version' => $phone->version->name,
                    'color' => $phone->color->name
                ]
            ]);

        } else {
            $stock = StockCardMovement::where('serial_number', $serialNumber)
                ->where('seller_id', $sellerId)
                ->first();

            if (!$stock) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Ürün bulunamadı veya farklı bayiye ait'
                ]);
            }

            if ($stock->type != 1) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Bu ürün zaten sevk edilmiş'
                ]);
            }

            return response()->json([
                'valid' => true,
                'message' => 'Ürün seri numarası geçerli',
                'data' => [
                    'name' => $stock->stock->name,
                    'brand' => $stock->stock->brand->name,
                    'category' => $stock->stock->category->name,
                    'color' => $stock->color->name
                ]
            ]);
        }
    }
}
