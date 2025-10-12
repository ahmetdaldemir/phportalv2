<?php

namespace Database\Factories;

use App\Models\VersionChild;
use App\Models\Version;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VersionChild>
 */
class VersionChildFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VersionChild::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version_id' => Version::factory(),
            'parent_version_id' => fake()->optional()->numberBetween(1, 10),
        ];
    }
}
