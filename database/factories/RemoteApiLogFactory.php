<?php

namespace Database\Factories;

use App\Models\RemoteApiLog;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RemoteApiLog>
 */
class RemoteApiLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RemoteApiLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'request' => fake()->optional()->json(),
            'response' => fake()->optional()->json(),
            'errors' => fake()->optional()->json(),
            'failed' => fake()->boolean(10),
        ];
    }
}
