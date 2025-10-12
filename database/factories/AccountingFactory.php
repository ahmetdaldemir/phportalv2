<?php

namespace Database\Factories;

use App\Models\Accounting;
use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Bank;
use App\Models\AccountingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting>
 */
class AccountingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Accounting::class;

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
            'invoice_id' => Invoice::factory(),
            'bank_id' => Bank::factory(),
            'exchange' => fake()->randomFloat(4, 0.5, 30),
            'tax' => fake()->randomFloat(2, 0, 20),
            'price' => fake()->randomFloat(2, 100, 10000),
            'file' => fake()->optional()->filePath(),
            'paymentDate' => fake()->dateTime(),
            'accounting_category_id' => AccountingCategory::factory(),
        ];
    }
}
