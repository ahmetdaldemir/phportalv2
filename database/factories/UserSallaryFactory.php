<?php

namespace Database\Factories;

use App\Models\UserSallary;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSallary>
 */
class UserSallaryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSallary::class;

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
            'price' => fake()->randomFloat(2, 5000, 50000),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2020, 2024),
        ];
    }
}
