<?php

namespace Database\Factories;

use App\Models\AccountingCategory;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountingCategory>
 */
class AccountingCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountingCategory::class;

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
            'name' => fake()->words(2, true),
            'slug' => fake()->optional()->slug(),
            'is_status' => fake()->boolean(80),
            'category' => fake()->randomElement(["income", "expense", "asset", "liability"]),
        ];
    }
}
