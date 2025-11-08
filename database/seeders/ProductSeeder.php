<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Version;
use App\Models\Color;
use App\Models\Warehouse;
use App\Models\StockCard;
use App\Models\Reason;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Kategoriler
        $phoneCategory = Category::create([
            'name' => 'Telefonlar',
            'parent_id' => null,
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
        ]);

        $accessoryCategory = Category::create([
            'name' => 'Aksesuarlar',
            'parent_id' => null,
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
        ]);

        $caseCategory = Category::create([
            'name' => 'Kılıflar',
            'parent_id' => $accessoryCategory->id,
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
        ]);

        // Markalar
        $apple = Brand::create([
            'name' => 'Apple',
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'technical' => 0,
        ]);

        $samsung = Brand::create([
            'name' => 'Samsung',
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'technical' => 0,
        ]);

        $xiaomi = Brand::create([
            'name' => 'Xiaomi',
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'technical' => 0,
        ]);

        // Versiyonlar
        $iphone15 = Version::create([
            'name' => 'iPhone 15',
            'brand_id' => $apple->id,
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'technical' => 0,
        ]);

        $iphone15Pro = Version::create([
            'name' => 'iPhone 15 Pro',
            'brand_id' => $apple->id,
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'technical' => 0,
        ]);

        $galaxyS24 = Version::create([
            'name' => 'Galaxy S24',
            'brand_id' => $samsung->id,
            'company_id' => 1,
            'user_id' => 1,
            'is_status' => 1,
            'technical' => 0,
        ]);

        // Renkler
        $black = Color::create([
            'company_id' => 1,
            'name' => 'Siyah',
            'is_status' => 1,
        ]);

        $white = Color::create([
            'company_id' => 1,
            'name' => 'Beyaz',
            'is_status' => 1,
        ]);

        $blue = Color::create([
            'company_id' => 1,
            'name' => 'Mavi',
            'is_status' => 1,
        ]);

        // Depolar
        $mainWarehouse = Warehouse::create([
            'company_id' => 1,
            'user_id' => 1,
            'name' => 'Ana Depo',
            'is_status' => 1,
            'seller_id' => 1,
        ]);

        $branchWarehouse = Warehouse::create([
            'company_id' => 1,
            'user_id' => 1,
            'name' => 'Şube Depo',
            'is_status' => 1,
            'seller_id' => 2,
        ]);

        // Sebepler
        $saleReason = Reason::create([
            'company_id' => 1,
            'type' => 'sale',
            'name' => 'Satış',
            'is_status' => 1,
        ]);

        $purchaseReason = Reason::create([
            'company_id' => 1,
            'type' => 'buy',
            'name' => 'Alış',
            'is_status' => 1,
        ]);

        $transferReason = Reason::create([
            'company_id' => 1,
            'type' => 'move',
            'name' => 'Transfer',
            'is_status' => 1,
        ]);

        // Stok Kartları
        StockCard::create([
            'name' => 'iPhone 15 128GB',
            'category_id' => $phoneCategory->id,
            'brand_id' => $apple->id,
            'version_id' => $iphone15->id,
            'sku' => 'IPH15-128',
            'barcode' => '1234567890123',
            'tracking' => 1,
            'unit' => 'adet',
            'tracking_quantity' => 1,
            'is_status' => 1,
            'user_id' => 1,
            'company_id' => 1,
        ]);

        StockCard::create([
            'name' => 'iPhone 15 Pro 256GB',
            'category_id' => $phoneCategory->id,
            'brand_id' => $apple->id,
            'version_id' => $iphone15Pro->id,
            'sku' => 'IPH15P-256',
            'barcode' => '1234567890124',
            'tracking' => 1,
            'unit' => 'adet',
            'tracking_quantity' => 1,
            'is_status' => 1,
            'user_id' => 1,
            'company_id' => 1,
        ]);

        StockCard::create([
            'name' => 'Galaxy S24 128GB',
            'category_id' => $phoneCategory->id,
            'brand_id' => $samsung->id,
            'version_id' => $galaxyS24->id,
            'sku' => 'GALS24-128',
            'barcode' => '1234567890125',
            'tracking' => 1,
            'unit' => 'adet',
            'tracking_quantity' => 1,
            'is_status' => 1,
            'user_id' => 1,
            'company_id' => 1,
        ]);

        StockCard::create([
            'name' => 'iPhone Şarj Kablosu',
            'category_id' => $accessoryCategory->id,
            'brand_id' => $apple->id,
            'version_id' => null,
            'sku' => 'ACC-CABLE-01',
            'barcode' => '1234567890126',
            'tracking' => 0,
            'unit' => 'adet',
            'tracking_quantity' => 0,
            'is_status' => 1,
            'user_id' => 1,
            'company_id' => 1,
        ]);
    }
}