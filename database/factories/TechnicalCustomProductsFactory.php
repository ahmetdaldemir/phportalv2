<?php

namespace Database\Factories;

use App\Models\TechnicalCustomProducts;
use App\Models\Company;
use App\Models\User;
use App\Models\TechnicalCustomService;
use App\Models\StockCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicalCustomProducts>
 */
class TechnicalCustomProductsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TechnicalCustomProducts::class;

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
            'technical_custom_service_id' => TechnicalCustomService::factory(),
            'stock_card_id' => StockCard::factory(),
            'sale_price' => fake()->randomFloat(2, 50, 2000),
        ];
    }
}
