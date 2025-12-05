<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Company;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->optional()->numerify("CUST-#####"),
            'fullname' => fake()->name(),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'tc' => fake()->optional()->numerify("##########"),
            'iban' => fake()->optional()->iban("TR"),
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->optional()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->numberBetween(1, 81),
            'district' => fake()->numberBetween(1, 100),
            'email' => fake()->optional()->safeEmail(),
            'note' => fake()->optional()->sentence(),
            'type' => fake()->randomElement(["customer", "account", "siteCustomer"]),
            'company_id' => Company::factory(),
            'seller_id' => Seller::factory(),
            'user_id' => User::factory(),
            'is_status' => fake()->boolean(80),
            'is_danger' => fake()->boolean(10),
        ];
    }
}
