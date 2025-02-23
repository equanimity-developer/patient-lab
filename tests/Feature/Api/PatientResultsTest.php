<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Patient;
use App\Models\Result;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;

class PatientResultsTest extends TestCase
{
    use DatabaseTruncation;

    public function test_patient_can_get_their_results(): void
    {
        // Arrange
        $patient = Patient::factory()->create([
            'name' => 'John',
            'surname' => 'Smith',
            'sex' => 'male',
            'date_of_birth' => '2021-01-01',
        ]);

        $order1 = Order::factory()->create([
            'patient_id' => $patient->id,
        ]);

        $order2 = Order::factory()->create([
            'patient_id' => $patient->id,
        ]);

        // Create results for first order
        Result::factory()->create([
            'order_id' => $order1->id,
            'test_name' => 'foo',
            'test_value' => '1',
            'test_reference' => '1-2',
        ]);

        Result::factory()->create([
            'order_id' => $order1->id,
            'test_name' => 'bar',
            'test_value' => '2',
            'test_reference' => '1-2',
        ]);

        // Create results for second order
        Result::factory()->create([
            'order_id' => $order2->id,
            'test_name' => 'foo',
            'test_value' => '1',
            'test_reference' => '1-2',
        ]);

        // Act
        $response = $this->actingAs($patient, 'api')
            ->getJson('/api/results');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'patient' => [
                    'id' => $patient->id,
                    'name' => 'John',
                    'surname' => 'Smith',
                    'sex' => 'male',
                    'birthDate' => '2021-01-01',
                ],
                'orders' => [
                    [
                        'orderId' => $order1->id,
                        'results' => [
                            [
                                'name' => 'foo',
                                'value' => '1',
                                'reference' => '1-2',
                            ],
                            [
                                'name' => 'bar',
                                'value' => '2',
                                'reference' => '1-2',
                            ],
                        ],
                    ],
                    [
                        'orderId' => $order2->id,
                        'results' => [
                            [
                                'name' => 'foo',
                                'value' => '1',
                                'reference' => '1-2',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_patient_cannot_get_results(): void
    {
        // Act
        $response = $this->getJson('/api/results');

        // Assert
        $response->assertStatus(401);
    }

    public function test_patient_can_only_see_their_own_results(): void
    {
        // Arrange
        $patient1 = Patient::factory()->create();
        $patient2 = Patient::factory()->create();

        $order = Order::factory()->create([
            'patient_id' => $patient2->id,
        ]);

        Result::factory()->create([
            'order_id' => $order->id,
        ]);

        // Act
        $response = $this->actingAs($patient1, 'api')
            ->getJson('/api/results');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'patient' => [
                    'id' => $patient1->id,
                ],
                'orders' => [],
            ]);
    }
}
