<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Patient;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_login_with_valid_credentials(): void
    {
        $patient = Patient::factory()->create([
            'name' => 'John',
            'surname' => 'Smith',
            'sex' => 'male',
            'date_of_birth' => '2021-01-01',
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'JohnSmith',
            'password' => '2021-01-01'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
co                'token',
            ]);
    }

    public function test_patient_cannot_login_with_invalid_login(): void
    {
        $patient = Patient::factory()->create([
            'name' => 'John',
            'surname' => 'Smith',
            'sex' => 'male',
            'date_of_birth' => '2021-01-01',
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'JohnDoe',
            'password' => '2021-01-01'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    public function test_patient_cannot_login_with_invalid_password(): void
    {
        $patient = Patient::factory()->create([
            'name' => 'John',
            'surname' => 'Smith',
            'sex' => 'male',
            'date_of_birth' => '2021-01-01',
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'JohnSmith',
            'password' => '2021-01-02'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }

    public function test_login_validation_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_login_validation_fails_with_invalid_date_format(): void
    {
        $response = $this->postJson('/api/login', [
            'login' => 'JohnSmith',
            'password' => 'not-a-date'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_is_case_sensitive(): void
    {
        $patient = Patient::factory()->create([
            'name' => 'John',
            'surname' => 'Smith',
            'sex' => 'male',
            'date_of_birth' => '2021-01-01',
        ]);

        // Correct case should work
        $response = $this->postJson('/api/login', [
            'login' => 'JohnSmith',
            'password' => '2021-01-01'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);

        // Different case should fail
        $response = $this->postJson('/api/login', [
            'login' => 'johnsmith',
            'password' => '2021-01-01'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }
}
