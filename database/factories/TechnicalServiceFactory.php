<?php

namespace Database\Factories;

use App\Models\TechnicalService;
use App\Models\Company;
use App\Models\User;
use App\Models\Customer;
use App\Models\Brand;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicalService>
 */
class TechnicalServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TechnicalService::class;

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
            'customer_id' => Customer::factory(),
            'brand_id' => Brand::factory(),
            'version_id' => fake()->optional()->numberBetween(1, 10),
            'customer_price' => fake()->randomFloat(2, 50, 2000),
            'products' => fake()->optional()->randomElements(["product1", "product2"], fake()->numberBetween(1, 2)),
            'seller_id' => Seller::factory(),
            'accessory_category' => fake()->optional()->words(2, true),
            'physically_category' => fake()->optional()->words(2, true),
            'fault_category' => fake()->optional()->words(2, true),
            'payment_status' => fake()->numberBetween(0, 1),
            'payment_person' => fake()->optional()->numberBetween(1, 10),
            'technical_person' => fake()->optional()->numberBetween(1, 10),
        ];
    }
}
