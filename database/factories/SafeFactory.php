<?php

namespace Database\Factories;

use App\Models\Safe;
use App\Models\Company;
use App\Models\User;
use App\Models\Seller;
use App\Models\Invoice;
use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Safe>
 */
class SafeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Safe::class;

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
            'type' => fake()->randomElement(["in", "out"]),
            'incash' => fake()->randomFloat(2, 0, 10000),
            'outcash' => fake()->randomFloat(2, 0, 10000),
            'amount' => fake()->randomFloat(2, 0, 10000),
            'invoice_id' => Invoice::factory(),
            'description' => fake()->optional()->sentence(),
            'credit_card' => fake()->optional()->randomFloat(2, 0, 5000),
            'installment' => fake()->optional()->randomFloat(2, 0, 5000),
            'bank_id' => Bank::factory(),
        ];
    }
}
