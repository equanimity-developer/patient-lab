<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Patient;

class PatientAuthTest extends TestCase
{
    public function test_login_is_case_sensitive(): void
    {
        $patient = Patient::factory()->create([
            'name' => 'John',
            'surname' => 'Doe',
            'date_of_birth' => '1990-01-01',
        ]);

        // Correct case should work
        $response = $this->postJson('/api/login', [
            'login' => 'JohnDoe',
            'password' => '1990-01-01'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);

        // Different case should fail
        $response = $this->postJson('/api/login', [
            'login' => 'johndoe',
            'password' => '1990-01-01'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials'
            ]);
    }
} 