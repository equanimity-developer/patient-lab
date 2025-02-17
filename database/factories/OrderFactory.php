<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'ordered_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
} 