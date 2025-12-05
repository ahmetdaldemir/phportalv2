<?php

namespace Database\Factories;

use App\Models\SiteTechnicalServiceCategory;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SiteTechnicalServiceCategory>
 */
class SiteTechnicalServiceCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SiteTechnicalServiceCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'sort_description' => fake()->optional()->sentence(),
            'price' => fake()->optional()->randomFloat(2, 50, 2000),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
