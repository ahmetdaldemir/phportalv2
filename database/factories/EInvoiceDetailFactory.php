<?php

namespace Database\Factories;

use App\Models\EInvoiceDetail;
use App\Models\EInvoice;
use App\Models\StockCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EInvoiceDetail>
 */
class EInvoiceDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EInvoiceDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'e_invoice_id' => EInvoice::factory(),
            'stock_card_id' => StockCard::factory(),
            'name' => fake()->words(3, true),
            'quantity' => fake()->numberBetween(1, 100),
            'price' => fake()->randomFloat(2, 10, 1000),
            'taxPrice' => fake()->randomFloat(2, 0, 200),
            'tax' => fake()->randomFloat(2, 0, 20),
            'total_price' => fake()->randomFloat(2, 50, 5000),
        ];
    }
}
