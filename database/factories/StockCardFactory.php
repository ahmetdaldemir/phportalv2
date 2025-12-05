<?php

namespace Database\Factories;

use App\Models\StockCard;
use App\Models\Company;
use App\Models\User;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\Seller;
use App\Models\Brand;
use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockCard>
 */
class StockCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockCard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'warehouse_id' => Warehouse::factory(),
            'seller_id' => Seller::factory(),
            'brand_id' => Brand::factory(),
            'version_id' => fake()->randomElements([1, 2, 3, 4], fake()->numberBetween(1, 3)),
            'color_id' => Color::factory(),
            'sku' => fake()->unique()->ean13(),
            'barcode' => fake()->unique()->ean13(),
            'tracking' => fake()->boolean(70),
            'unit' => fake()->randomElement(["Adet", "Kg", "Litre", "Metre"]),
            'tracking_quantity' => fake()->numberBetween(0, 1000),
            'is_status' => fake()->boolean(80),
            'name' => fake()->words(3, true),
        ];
    }
}
