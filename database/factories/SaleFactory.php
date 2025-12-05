<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\StockCard;
use App\Models\StockCardMovement;
use App\Models\Invoice;
use App\Models\Seller;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_card_id' => StockCard::factory(),
            'type' => fake()->numberBetween(1, 6),
            'stock_card_movement_id' => StockCardMovement::factory(),
            'invoice_id' => Invoice::factory(),
            'sale_price' => fake()->randomFloat(2, 100, 10000),
            'customer_price' => fake()->randomFloat(2, 80, 8000),
            'cash_price' => fake()->randomFloat(2, 0, 5000),
            'credit_card_pricredit_card_price' => fake()->randomFloat(2, 0, 5000),
            'instalment_price' => fake()->randomFloat(2, 0, 5000),
            'name' => fake()->words(3, true),
            'seller_id' => Seller::factory(),
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'customer_id' => Customer::factory(),
            'serial' => fake()->optional()->imei(),
            'discount' => fake()->optional()->randomFloat(2, 0, 50),
            'creates_dates' => fake()->dateTime(),
            'technical_service_person_id' => fake()->optional()->numberBetween(1, 10),
            'base_cost_price' => fake()->randomFloat(2, 50, 3000),
            'delivery_personnel' => fake()->optional()->numberBetween(1, 10),
        ];
    }
}
