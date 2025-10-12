<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Safe;
use App\Models\AccountingCategory;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->numberBetween(1, 2),
            'number' => fake()->unique()->numerify("INV-#####"),
            'create_date' => fake()->dateTime(),
            'credit_card' => fake()->randomFloat(2, 0, 10000),
            'cash' => fake()->randomFloat(2, 0, 10000),
            'installment' => fake()->randomFloat(2, 0, 10000),
            'description' => fake()->optional()->sentence(),
            'is_status' => fake()->boolean(80),
            'total_price' => fake()->randomFloat(2, 100, 50000),
            'tax_total' => fake()->randomFloat(2, 0, 5000),
            'discount_total' => fake()->randomFloat(2, 0, 2000),
            'staff_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'safe_id' => Safe::factory(),
            'exchange' => fake()->randomFloat(2, 1, 30),
            'tax' => fake()->randomFloat(2, 0, 20),
            'file' => fake()->optional()->filePath(),
            'paymentStatus' => fake()->randomElement(["paid", "pending", "cancelled"]),
            'paymentDate' => fake()->optional()->dateTime(),
            'paymentStaff' => fake()->optional()->numberBetween(1, 10),
            'periodMounth' => fake()->numberBetween(1, 12),
            'periodYear' => fake()->numberBetween(2020, 2024),
            'accounting_category_id' => AccountingCategory::factory(),
            'currency' => Currency::factory(),
            'detail' => fake()->optional()->randomElements(["detail1", "detail2"], fake()->numberBetween(1, 2)),
        ];
    }
}
