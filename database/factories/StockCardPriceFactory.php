<?php

namespace Database\Factories;

use App\Models\StockCardPrice;
use App\Models\Company;
use App\Models\StockCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockCardPrice>
 */
class StockCardPriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockCardPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'stock_card_id' => StockCard::factory(),
            'base_cost_price' => fake()->optional()->randomFloat(2, 50, 3000),
            'sale_price' => fake()->optional()->randomFloat(2, 100, 5000),
        ];
    }
}
