<?php

namespace Database\Factories;

use App\Models\TechnicalServiceCategory;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicalServiceCategory>
 */
class TechnicalServiceCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TechnicalServiceCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'parent_id' => fake()->optional()->numberBetween(1, 10),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'is_status' => fake()->boolean(80),
        ];
    }
}
