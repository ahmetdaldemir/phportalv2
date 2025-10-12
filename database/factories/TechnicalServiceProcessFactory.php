<?php

namespace Database\Factories;

use App\Models\TechnicalServiceProcess;
use App\Models\TechnicalService;
use App\Models\TechnicalProcess;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicalServiceProcess>
 */
class TechnicalServiceProcessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TechnicalServiceProcess::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'technical_service_id' => TechnicalService::factory(),
            'technical_process_id' => TechnicalProcess::factory(),
        ];
    }
}
