<?php

namespace Database\Factories;

use App\Models\Reason;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reason>
 */
class ReasonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reason::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'is_status' => fake()->boolean(80),
            'type' => fake()->numberBetween(1, 5),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
        ];
    }
}
