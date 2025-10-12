<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seller::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'is_status' => fake()->boolean(80),
            'phone' => fake()->phoneNumber(),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'can_see_stock' => fake()->boolean(80),
            'can_see_cost_price' => fake()->boolean(60),
            'can_see_base_cost_price' => fake()->boolean(60),
            'can_see_sale_price' => fake()->boolean(80),
        ];
    }
}
