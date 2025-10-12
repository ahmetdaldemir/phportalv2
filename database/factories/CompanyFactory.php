<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'authorized' => fake()->name(),
            'is_status' => fake()->boolean(80),
            'web_sites' => fake()->url(),
            'commercial_registration_number' => fake()->numerify("##########"),
            'tax_number' => fake()->numerify("##########"),
            'tax_office' => fake()->city(),
            'mersis_number' => fake()->numerify("##########"),
            'company_name' => fake()->company(),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'district' => fake()->city(),
            'country' => fake()->country(),
            'country_code' => fake()->countryCode(),
        ];
    }
}
