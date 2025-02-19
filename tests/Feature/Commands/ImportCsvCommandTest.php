<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportCsvCommandTest extends TestCase
{
    use RefreshDatabase;

    private const string CORRECT_FILE = '/tests/Files/correct.csv';

    public function test_can_import_csv_file(): void
    {
        $this->artisan('import:csv', ['file' => base_path(self::CORRECT_FILE)])
            ->assertSuccessful()
            ->expectsOutput('Import completed. Successfully imported 9 records with 0 errors.');

        $this->assertDatabaseCount('patients', 4);
        $this->assertDatabaseHas('patients', [
            'id' => 1,
            'name' => 'Piotr',
            'surname' => 'Kowalski',
            'sex' => 'male',
            'date_of_birth' => '1983-04-12',
        ]);
        $this->assertDatabaseHas('patients', [
            'id' => 2,
            'name' => 'Anna',
            'surname' => 'Jabłońska',
            'sex' => 'female',
            'date_of_birth' => '2002-12-12',
        ]);
        $this->assertDatabaseHas('patients', [
            'id' => 3,
            'name' => 'Andrzej',
            'surname' => 'Kowalski',
            'sex' => 'male',
            'date_of_birth' => '2020-01-31',
        ]);
        $this->assertDatabaseHas('patients', [
            'id' => 4,
            'name' => 'Bożena',
            'surname' => 'Wiśniewska',
            'sex' => 'female',
            'date_of_birth' => '2021-11-21',
        ]);

        $this->assertDatabaseCount('orders', 6);
        $this->assertDatabaseHas('orders', ['id' => 1, 'patient_id' => 1]);
        $this->assertDatabaseHas('orders', ['id' => 2, 'patient_id' => 1]);
        $this->assertDatabaseHas('orders', ['id' => 3, 'patient_id' => 2]);
        $this->assertDatabaseHas('orders', ['id' => 4, 'patient_id' => 3]);
        $this->assertDatabaseHas('orders', ['id' => 5, 'patient_id' => 3]);
        $this->assertDatabaseHas('orders', ['id' => 6, 'patient_id' => 4]);

        $this->assertDatabaseCount('results', 9);
        $this->assertDatabaseHas('results', [
            'order_id' => 1,
            'test_name' => 'Protoporfiryna cynkowa',
            'test_value' => '4.0',
            'test_reference' => '< 40,0',
        ]);
        $this->assertDatabaseHas('results', [
            'order_id' => 1,
            'test_name' => 'Wolna trijodotyronina (FT3) (O55)',
            'test_value' => '4.00',
            'test_reference' => '2,30 - 4,20',
        ]);
        $this->assertDatabaseHas('results', [
            'order_id' => 3,
            'test_name' => 'Azotyny',
            'test_value' => 'nieobecne',
            'test_reference' => 'nieobecne',
        ]);
    }
}
