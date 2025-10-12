<?php

namespace Database\Factories;

use App\Models\TechnicalCustomService;
use App\Models\Company;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicalCustomService>
 */
class TechnicalCustomServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TechnicalCustomService::class;

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
            'seller_id' => Seller::factory(),
            'customer_id' => Customer::factory(),
            'customer_price' => fake()->randomFloat(2, 50, 2000),
            'payment_status' => fake()->numberBetween(0, 1),
        ];
    }
}
