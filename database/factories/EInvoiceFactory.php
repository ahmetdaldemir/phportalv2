<?php

namespace Database\Factories;

use App\Models\EInvoice;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EInvoice>
 */
class EInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'invoice_id' => Invoice::factory(),
            'pKAlias' => fake()->optional()->lexify("???"),
            'gBAlias' => fake()->optional()->lexify("???"),
            'currentDate' => fake()->date("Y-m-d"),
            'saveType' => fake()->randomElement(["draft", "final"]),
            'invoiceStatus' => fake()->randomElement(["pending", "approved", "rejected"]),
        ];
    }
}
