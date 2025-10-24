<?php

namespace App\Helper;

class SearchHelper
{
    /**
     * Gelen verinin barkod mu seri numarası mı olduğunu belirler
     * B- ile başlayanlar barkod, S- ile başlayanlar veya prefix olmayanlar seri numarası
     * 
     * @param string|null $input
     * @return array|null
     */
    public static function determineSearchType(?string $input): ?array
    {
        if (empty($input) || $input === 'undefined') {
            return null;
        }

        $input = trim($input);
        
        if (str_starts_with($input, 'B-')) {
            return [
                'type' => 'barcode',
                'value' => $input
            ];
        } elseif (str_starts_with($input, 'S-')) {
            return [
                'type' => 'serial',
                'value' => $input
            ];
        } else {
            // Prefix yoksa - önce barkod olarak dene, sonra seri numarası
            // Barkod genellikle daha uzun ve sayısal karakterler içerir
            if (strlen($input) >= 8 && is_numeric($input)) {
                return [
                    'type' => 'barcode',
                    'value' => $input
                ];
            } else {
                return [
                    'type' => 'serial',
                    'value' => $input
                ];
            }
        }
    }

    /**
     * Arama tipine göre uygun query builder'ı döndürür
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $searchInfo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applySearchFilter($query, array $searchInfo)
    {
        if ($searchInfo['type'] === 'barcode') {
            // Barkod araması - StockCard tablosunda ara
            return $query->whereHas('stock', function($q) use ($searchInfo) {
                $q->where('barcode', $searchInfo['value']);
            });
        } else {
            // Seri numarası araması
            return $query->where('serial_number', $searchInfo['value']);
        }
    }

    /**
     * Arama tipine göre uygun query builder'ı döndürür (LIKE ile arama)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $searchInfo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applySearchFilterLike($query, array $searchInfo)
    {
        if ($searchInfo['type'] === 'barcode') {
            // Barkod araması - StockCard tablosunda ara
            return $query->whereHas('stock', function($q) use ($searchInfo) {
                $q->where('barcode', 'like', '%' . $searchInfo['value'] . '%');
            });
        } else {
            // Seri numarası araması - LIKE ile arama
            return $query->where('serial_number', 'like', '%' . $searchInfo['value'] . '%');
        }
    }

    /**
     * Arama tipine göre uygun mesaj döndürür
     * 
     * @param array $searchInfo
     * @param bool $found
     * @return string
     */
    public static function getSearchMessage(array $searchInfo, bool $found = true): string
    {
        $type = $searchInfo['type'] === 'barcode' ? 'barkod' : 'seri numarası';
        
        if ($found) {
            return ucfirst($type) . ' ile stok bulundu';
        } else {
            return 'Bu ' . $type . ' sistemde bulunamadı';
        }
    }

    /**
     * Arama tipine göre uygun log mesajı döndürür
     * 
     * @param array $searchInfo
     * @return string
     */
    public static function getSearchLogMessage(array $searchInfo): string
    {
        $type = $searchInfo['type'] === 'barcode' ? 'barkod' : 'seri numarası';
        return ucfirst($type) . ' araması: ' . $searchInfo['value'];
    }
}
