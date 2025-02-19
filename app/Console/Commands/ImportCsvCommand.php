<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Patient;
use App\Models\Result;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Throwable;

class ImportCsvCommand extends Command
{
    protected $signature = 'import:csv {file : Path to the CSV file}';
    protected $description = 'Import patients and their results from a CSV file';

    private const REQUIRED_HEADERS = [
        'patientId',
        'patientName',
        'patientSurname',
        'patientSex',
        'patientBirthDate',
        'orderId',
        'testName',
        'testValue',
        'testReference',
    ];

    public function handle(): int
    {
        try {
            $filePath = $this->argument('file');
            $this->validateFile($filePath);

            $csv = $this->initializeCsvReader($filePath);
            $this->validateHeaders($csv);

            $successCount = 0;
            $errorCount = 0;

            DB::beginTransaction();

            foreach ($csv as $index => $record) {
                try {
                    $this->processRecord($record);
                    $successCount++;

                    if ($successCount % 100 === 0) {
                        $this->info(__('csv.progress_update', ['count' => $successCount]));
                    }
                } catch (Throwable $e) {
                    $errorCount++;
                    $this->logImportError($index + 2, $e);

                    if ($errorCount > 100) {
                        throw new Exception(__('csv.too_many_errors'));
                    }
                }
            }

            DB::commit();
            $this->displaySummary($successCount, $errorCount);

            return 0;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->handleFatalError($e);

            return 1;
        }
    }

    private function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new Exception(__('csv.file_not_found', ['path' => $filePath]));
        }

        if (!is_readable($filePath)) {
            throw new Exception(__('csv.file_not_readable', ['path' => $filePath]));
        }
    }

    private function initializeCsvReader(string $filePath): Reader
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);
        return $csv;
    }

    private function validateHeaders(Reader $csv): void
    {
        $headers = $csv->getHeader();
        $missingHeaders = array_diff(self::REQUIRED_HEADERS, $headers);

        if (!empty($missingHeaders)) {
            throw new Exception(__('csv.missing_headers', [
                'headers' => implode(', ', $missingHeaders)
            ]));
        }
    }

    private function validateRecord(array $record): void
    {
        $validator = Validator::make($record, [
            'patientId' => 'required|numeric',
            'patientName' => 'required|string|max:255',
            'patientSurname' => 'required|string|max:255',
            'patientSex' => 'required|in:male,female,other',
            'patientBirthDate' => 'required|date',
            'orderId' => 'required|numeric',
            'testName' => 'required|string|max:255',
            'testValue' => 'required|string|max:255',
            'testReference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new Exception(__('csv.validation_failed').': '.
                implode(', ', $validator->errors()->all()));
        }
    }

    private function processRecord(array $record): void
    {
        $this->validateRecord($record);

        $patient = Patient::firstOrCreate(
            ['id' => $record['patientId']],
            [
                'name' => $record['patientName'],
                'surname' => $record['patientSurname'],
                'sex' => $record['patientSex'],
                'date_of_birth' => $record['patientBirthDate'],
            ]
        );

        $order = Order::firstOrCreate(
            ['id' => $record['orderId']],
            [
                'patient_id' => $patient->id,
            ]
        );

        Result::create([
            'order_id' => $order->id,
            'test_name' => $record['testName'],
            'test_value' => $record['testValue'],
            'test_reference' => $record['testReference'],
        ]);
    }

    private function logImportError(int $line, Throwable $e): void
    {
        $message = __('csv.error_at_line', [
            'line' => $line,
            'message' => $e->getMessage()
        ]);

        $this->error($message);
        Log::error($message, [
            'exception' => $e,
            'line' => $line,
        ]);
    }

    private function handleFatalError(Throwable $e): void
    {
        $message = __('csv.import_failed', ['message' => $e->getMessage()]);
        $this->error($message);
        Log::error($message, [
            'exception' => $e,
        ]);
    }

    private function displaySummary(int $successCount, int $errorCount): void
    {
        $this->info(__('csv.import_summary', [
            'success' => $successCount,
            'errors' => $errorCount,
        ]));
    }
}
