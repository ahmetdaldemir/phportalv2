<?php

namespace Database\Factories;

use App\Models\Phone;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Seller;
use App\Models\StockCard;
use App\Models\Brand;
use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phone>
 */
class PhoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Phone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'customer_id' => Customer::factory(),
            'altered_parts' => fake()->optional()->words(2, true),
            'physical_condition' => fake()->optional()->words(2, true),
            'memory' => fake()->optional()->randomElement(["64GB", "128GB", "256GB", "512GB"]),
            'batery' => fake()->optional()->randomElement(["Good", "Fair", "Poor"]),
            'warranty' => fake()->optional()->boolean(70),
            'status' => fake()->randomElement(["active", "inactive", "sold"]),
            'invoice_id' => Invoice::factory(),
            'is_confirm' => fake()->boolean(80),
            'sales_person' => Seller::factory(),
            'stock_card_id' => StockCard::factory(),
            'brand_id' => Brand::factory(),
            'version_id' => fake()->optional()->numberBetween(1, 10),
            'color_id' => Color::factory(),
            'cost_price' => fake()->randomFloat(2, 100, 5000),
            'sale_price' => fake()->randomFloat(2, 150, 6000),
        ];
    }
}
