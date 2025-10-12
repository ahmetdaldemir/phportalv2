<?php

namespace Database\Factories;

use App\Models\FinansTransaction;
use App\Models\User;
use App\Models\Company;
use App\Models\Safe;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinansTransaction>
 */
class FinansTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinansTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'safe_id' => Safe::factory(),
            'model_class' => fake()->randomElement(["App\Models\User", "App\Models\Sale", "App\Models\Invoice"]),
            'model_id' => fake()->numberBetween(1, 100),
            'price' => fake()->randomFloat(2, 100, 10000),
            'process_type' => fake()->optional()->numberBetween(1, 20),
            'payment_type' => fake()->optional()->randomElement(["cash", "credit_card", "bank_transfer"]),
            'currency_id' => Currency::factory(),
            'rate' => fake()->optional()->randomFloat(4, 0.5, 30),
        ];
    }
}
