<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Result;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'test_name' => $this->faker->randomElement(['Glucose', 'Cholesterol', 'Hemoglobin', 'White Blood Cell Count', 'Platelets']),
            'test_value' => $this->faker->randomFloat(2, 1, 200),
            'test_reference' => $this->faker->randomElement(['4.0-5.4 mmol/L', '125-200 mg/dL', '13.5-17.5 g/dL', '4.5-11.0 x10^9/L', '150-450 x10^9/L']),
        ];
    }
} 