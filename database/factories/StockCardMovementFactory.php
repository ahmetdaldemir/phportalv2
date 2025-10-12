<?php

namespace Database\Factories;

use App\Models\StockCardMovement;
use App\Models\StockCard;
use App\Models\User;
use App\Models\Color;
use App\Models\Warehouse;
use App\Models\Seller;
use App\Models\Reason;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockCardMovement>
 */
class StockCardMovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockCardMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_card_id' => StockCard::factory(),
            'user_id' => User::factory(),
            'color_id' => Color::factory(),
            'warehouse_id' => Warehouse::factory(),
            'seller_id' => Seller::factory(),
            'reason_id' => Reason::factory(),
            'type' => fake()->numberBetween(1, 5),
            'quantity' => fake()->numberBetween(1, 100),
            'serial_number' => fake()->unique()->imei(),
            'invoice_id' => Invoice::factory(),
            'tax' => fake()->randomFloat(2, 0, 20),
            'cost_price' => fake()->randomFloat(2, 100, 5000),
            'base_cost_price' => fake()->randomFloat(2, 80, 4000),
            'sale_price' => fake()->randomFloat(2, 120, 6000),
            'description' => fake()->sentence(),
            'assigned_accessory' => fake()->optional()->words(2, true),
            'assigned_device' => fake()->optional()->words(2, true),
            'company_id' => Company::factory(),
            'prefix' => fake()->optional()->lexify("???"),
            'transfer_id' => fake()->optional()->numberBetween(1, 10),
            'brand_id' => Brand::factory(),
            'version_id' => fake()->optional()->numberBetween(1, 10),
            'category_id' => Category::factory(),
            'discount' => fake()->optional()->randomFloat(2, 0, 50),
            'imei' => fake()->optional()->imei(),
            'tracking_quantity' => fake()->optional()->numberBetween(0, 100),
        ];
    }
}
