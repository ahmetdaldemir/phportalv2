<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(["Türk Lirası", "US Dollar", "Euro", "British Pound"]),
            'code' => fake()->randomElement(["TRY", "USD", "EUR", "GBP"]),
            'symbol' => fake()->randomElement(["₺", "$", "€", "£"]),
            'format' => fake()->randomElement(["#,##0.00", "$#,##0.00", "€#,##0.00"]),
            'exchange_rate' => fake()->randomFloat(4, 0.5, 30),
            'active' => fake()->boolean(80),
        ];
    }
}
