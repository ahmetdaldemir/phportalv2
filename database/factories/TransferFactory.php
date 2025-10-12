<?php

namespace Database\Factories;

use App\Models\Transfer;
use App\Models\Company;
use App\Models\User;
use App\Models\Seller;
use App\Models\Reason;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transfer::class;

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
            'is_status' => fake()->numberBetween(1, 4),
            'main_seller_id' => Seller::factory(),
            'comfirm_id' => fake()->optional()->numberBetween(1, 10),
            'comfirm_date' => fake()->optional()->dateTime(),
            'delivery_id' => fake()->optional()->numberBetween(1, 10),
            'stocks' => fake()->optional()->randomElements([1, 2, 3, 4, 5], fake()->numberBetween(1, 3)),
            'number' => fake()->unique()->numerify("TRF-#####"),
            'delivery_seller_id' => Seller::factory(),
            'description' => fake()->optional()->sentence(),
            'serial_list' => fake()->optional()->randomElements(["SN001", "SN002", "SN003"], fake()->numberBetween(1, 3)),
            'type' => fake()->randomElement(["transfer", "return", "exchange"]),
            'detail' => fake()->optional()->randomElements(["detail1", "detail2"], fake()->numberBetween(1, 2)),
            'reason_id' => Reason::factory(),
        ];
    }
}
