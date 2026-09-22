<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersVotersImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading
{
    private array $failures = [];
    private int $successCount = 0;
    private int $duplicateCount = 0;

    public function model(array $row)
    {
        $name = isset($row['name']) ? trim((string) $row['name']) : null;
        $idNumber = isset($row['idnumber']) ? trim((string) $row['idnumber']) : (isset($row['id_number']) ? trim((string) $row['id_number']) : null);
        $email = isset($row['email']) ? trim((string) $row['email']) : null;
        $course = isset($row['course']) ? trim((string) $row['course']) : null;
        $yearLevel = isset($row['year_level']) ? trim((string) $row['year_level']) : (isset($row['year']) ? trim((string) $row['year']) : null);

        $status = isset($row['status']) ? trim((string) $row['status']) : 'Active';

        Log::debug('Users import row', [
            'name' => $name,
            'idnumber' => $idNumber,
            'email' => $email,
            'course' => $course,
            'year_level' => $yearLevel,
            'status' => $status,
        ]);

        if (!$name || !$idNumber || !$email || !$course || !$yearLevel) {
            $this->failures[] = 'Missing required fields in row: ' . json_encode($row);
            return null;
        }

        $existing = User::query()
            ->where('idnumber', $idNumber)
            ->orWhere('email', $email)
            ->first();

        if ($existing) {
            $this->duplicateCount++;
            $this->failures[] = 'Duplicate skipped: ' . $name . ' (' . $idNumber . ')';
            return null;
        }

        $this->successCount++;

        return new User([
            'name' => $name,
            'idnumber' => $idNumber,
            'email' => $email,
            'course' => $course,
            'year_level' => $yearLevel,
            'status' => $status ?: 'Active',
            'password' => bcrypt('password'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required'],
            'idnumber' => ['required'],
            'email' => ['required'],
            'course' => ['required'],
            'year_level' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Name is required',
            'idnumber.required' => 'ID Number is required',
            'email.required' => 'Email is required',
            'course.required' => 'Course is required',
            'year_level.required' => 'Year level is required',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getDuplicateCount(): int
    {
        return $this->duplicateCount;
    }
}
