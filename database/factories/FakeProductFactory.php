<?php

namespace Database\Factories;

use App\Models\FakeProduct;
use App\Models\Company;
use App\Models\User;
use App\Models\StockCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FakeProduct>
 */
class FakeProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FakeProduct::class;

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
            'stock_card_id' => StockCard::factory(),
        ];
    }
}
