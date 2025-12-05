<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Color::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(["Siyah", "Beyaz", "Mavi", "Kırmızı", "Yeşil", "Sarı", "Turuncu", "Mor", "Pembe", "Gri"]),
            'is_status' => fake()->boolean(80),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
        ];
    }
}
