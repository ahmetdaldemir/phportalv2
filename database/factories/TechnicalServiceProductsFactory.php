<?php

namespace Database\Factories;

use App\Models\TechnicalServiceProducts;
use App\Models\User;
use App\Models\Company;
use App\Models\TechnicalService;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicalServiceProducts>
 */
class TechnicalServiceProductsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TechnicalServiceProducts::class;

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
            'technical_service_id' => TechnicalService::factory(),
            'stock_card_id' => StockCard::factory(),
            'stock_card_movement_id' => StockCardMovement::factory(),
            'serial_number' => fake()->unique()->imei(),
            'quantity' => fake()->numberBetween(1, 10),
            'sale_price' => fake()->randomFloat(2, 50, 2000),
        ];
    }
}
