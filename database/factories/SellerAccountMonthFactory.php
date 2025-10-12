<?php

namespace Database\Factories;

use App\Models\SellerAccountMonth;
use App\Models\Company;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SellerAccountMonth>
 */
class SellerAccountMonthFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerAccountMonth::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'seller_id' => Seller::factory(),
            'salary' => fake()->randomFloat(2, 5000, 50000),
            'mounth' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2020, 2024),
        ];
    }
}
