<?php

namespace Database\Factories;

use App\Models\Version;
use App\Models\Company;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Version>
 */
class VersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Version::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(["iPhone 15", "iPhone 14", "Galaxy S24", "Galaxy S23", "P40", "P30", "Mi 13", "Mi 12"]),
            'is_status' => fake()->boolean(80),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'image' => fake()->imageUrl(),
            'brand_id' => Brand::factory(),
            'technical' => fake()->boolean(30),
        ];
    }
}
