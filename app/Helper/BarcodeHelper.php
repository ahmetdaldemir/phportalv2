<?php

namespace App\Helper;

class BarcodeHelper
{
    /**
     * Ensure barcode starts with 'B-' prefix
     *
     * @param string|null $barcode
     * @return string|null
     */
    public static function formatBarcode(?string $barcode): ?string
    {
        if (empty($barcode)) {
            return null;
        }

        // If barcode doesn't start with 'B-', add the prefix
        if (!str_starts_with($barcode, 'B-')) {
            return 'B-' . $barcode;
        }

        return $barcode;
    }

    /**
     * Generate a random barcode with 'B-' prefix
     *
     * @param string $prefix
     * @param int $length
     * @return string
     */
    public static function generateBarcode(string $prefix = 'PH', int $length = 7): string
    {
        $randomPart = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPart .= rand(0, 9);
        }
        
        return 'B-' . $prefix . $randomPart;
    }

    /**
     * Ensure serial number starts with 'S-' prefix
     *
     * @param string|null $serialNumber
     * @return string|null
     */
    public static function formatSerialNumber(?string $serialNumber): ?string
    {
        if (empty($serialNumber)) {
            return null;
        }

        // If serial number doesn't start with 'S-', add the prefix
        if (!str_starts_with($serialNumber, 'S-')) {
            return 'S-' . $serialNumber;
        }

        return $serialNumber;
    }

    /**
     * Generate a random serial number with 'S-' prefix
     *
     * @param int $length
     * @return string
     */
    public static function generateSerialNumber(int $length = 7): string
    {
        $randomPart = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPart .= rand(0, 9);
        }
        
        return 'S-' . $randomPart;
    }
}
