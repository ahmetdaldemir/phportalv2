<?php

namespace Database\Factories;

use App\Models\Warehouse;
use App\Models\Company;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Warehouse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'seller_id' => Seller::factory(),
            'is_status' => fake()->boolean(80),
        ];
    }
}
