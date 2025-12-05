<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\Company;
use App\Models\User;
use App\Models\Seller;
use App\Models\Reason;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Refund>
 */
class RefundFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Refund::class;

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
            'seller_id' => Seller::factory(),
            'reason_id' => Reason::factory(),
            'serial_number' => fake()->unique()->imei(),
            'status' => fake()->randomElement(["pending", "approved", "rejected"]),
            'service_send_date' => fake()->optional()->dateTime(),
            'service_return_date' => fake()->optional()->dateTime(),
        ];
    }
}
