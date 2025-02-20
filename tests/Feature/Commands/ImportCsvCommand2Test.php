<?php

declare(strict_types=1);

namespace Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use TiMacDonald\Log\LogEntry;
use TiMacDonald\Log\LogFake;

class ImportCsvCommand2Test extends TestCase
{
    use DatabaseTruncation;

    private const string CORRECT_FILE = '/tests/Files/correct.csv';
    private const string EMPTY_FILE = '/tests/Files/empty.csv';
    private const string INVALID_FORMAT_FILE = '/tests/Files/invalid_format.csv';
    private const string MISSING_REQUIRED_FIELDS_FILE = '/tests/Files/missing_required_fields.csv';
    private const string INVALID_DATE_FILE = '/tests/Files/invalid_date.csv';
    private const string INVALID_SEX_FILE = '/tests/Files/invalid_sex.csv';

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

    public function test_handles_duplicate_import(): void
    {
        $this->artisan('import:csv', ['file' => base_path(self::CORRECT_FILE)])
            ->assertSuccessful()
            ->expectsOutput('Import completed. Successfully imported 9 records with 0 errors.');

        $this->assertDatabaseCount('patients', 4);
        $this->assertDatabaseCount('orders', 6);
        $this->assertDatabaseCount('results', 9);

        $this->artisan('import:csv', ['file' => base_path(self::CORRECT_FILE)])
            ->assertSuccessful()
            ->expectsOutput('Import completed. Successfully imported 9 records with 0 errors.');

        $this->assertDatabaseCount('patients', 4);
        $this->assertDatabaseCount('orders', 6);
        $this->assertDatabaseCount('results', 9);
    }

    public function test_cannot_import_nonexistent_file(): void
    {
        LogFake::bind();

        $this->artisan('import:csv', ['file' => base_path('/tests/Files/nonexistent.csv')])
            ->assertFailed();

        Log::assertLogged(fn(LogEntry $log) => $log->level === 'error'
            && $log->message === 'Failed to import CSV: File does not exist: /var/www/html/tests/Files/nonexistent.csv'
        );
    }

    public function test_cannot_import_empty_file(): void
    {
        LogFake::bind();

        $this->artisan('import:csv', ['file' => base_path(self::EMPTY_FILE)])
            ->assertFailed();

        Log::assertLogged(fn(LogEntry $log) => $log->level === 'error'
            && $log->message === 'Failed to import CSV: The header record does not exist or is empty at offset: `0`'
        );
    }

    public function test_cannot_import_file_with_invalid_format(): void
    {
        LogFake::bind();

        $this->artisan('import:csv', ['file' => base_path(self::INVALID_FORMAT_FILE)])
            ->assertFailed();

        Log::assertLogged(fn(LogEntry $log) => $log->level === 'error'
            && $log->message === 'Failed to import CSV: Missing required headers: patientId, patientName, patientSurname, patientSex, patientBirthDate, orderId, testName, testValue, testReference'
        );
    }

    public function test_handles_missing_required_fields(): void
    {
        $this->artisan('import:csv', ['file' => base_path(self::MISSING_REQUIRED_FIELDS_FILE)])
            ->assertSuccessful();

        $this->assertDatabaseCount('patients', 1);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('results', 2);

        //todo: check logs
    }

    public function test_handles_invalid_date_format(): void
    {
        $this->artisan('import:csv', ['file' => base_path(self::INVALID_DATE_FILE)])
            ->assertSuccessful();

        $this->assertDatabaseCount('patients', 1);
        $this->assertDatabaseHas('patients', [
            'name' => 'John',
            'surname' => 'Doe',
            'sex' => 'male',
            'date_of_birth' => '1990-01-01',
        ]);
    }

    public function test_handles_invalid_sex_value(): void
    {
        $this->artisan('import:csv', ['file' => base_path(self::INVALID_SEX_FILE)])
            ->assertSuccessful();

        $this->assertDatabaseCount('patients', 1);
        $this->assertDatabaseHas('patients', [
            'name' => 'John',
            'surname' => 'Doe',
            'sex' => 'male',
            'date_of_birth' => '1990-01-01',
        ]);
    }
}
