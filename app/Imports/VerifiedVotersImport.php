<?php 

namespace App\Imports;

use App\Models\VerifiedVoter;
use App\Models\User;
use App\Models\Semesters;
use App\Models\Acadyr;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;

class VerifiedVotersImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    WithBatchInserts, 
    WithChunkReading
{
    private $failures = [];
    private $successCount = 0;
    private $duplicateCount = 0;

    public function model(array $row)
    {
        Log::debug('Raw Row:', $row);

        // Convert ID number to string and clean it
        $idNumber = isset($row['id_number']) ? (string)$row['id_number'] : null;
        $academicYearName = $row['academic_year'] ?? null;
        $semesterName = $row['semester'] ?? null;
        $name = $row['name'] ?? null;
        
        // Add more detailed logging
        Log::info('Processing import row:', [
            'name' => $name,
            'id_number' => $idNumber,
            'academic_year' => $academicYearName,
            'semester' => $semesterName
        ]);

        // Strict validation
        if (!$idNumber || strlen(trim($idNumber)) === 0) {
            $this->failures[] = "Invalid ID Number in row: " . json_encode($row);
            return null;
        }
        
        if (empty($academicYearName) || empty($semesterName)) {
            $this->failures[] = "Missing academic year or semester in row: " . json_encode($row);
            return null;
        }
        
        // First try to find user by ID number
        $user = User::where('idnumber', $idNumber)->first();
        
        if (!$user) {
            // If not found by ID, try by name
            $user = User::where('name', $name)->first();
            
            if ($user) {
                // Update existing user with ID number
                $user->idnumber = $idNumber;
                $user->save();
                
                Log::info("Updated existing user with ID number:", [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'idnumber' => $user->idnumber
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $name,
                    'idnumber' => $idNumber,
                    'email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
                    'password' => bcrypt('password')
                ]);
                
                Log::info("Created new user:", [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'idnumber' => $user->idnumber
                ]);
            }
        }
        
        // Find or create Academic Year
        $academicYear = Acadyr::firstOrCreate(['name' => $academicYearName]);
        
        // Find Semester
        $semester = Semesters::where('name', $semesterName)->first();
        if (!$semester) {
            $this->failures[] = "Semester not found: " . $semesterName;
            return null;
        }
        
        // Check if voter already exists
        $existingVoter = VerifiedVoter::where('users_id', $user->id)
            ->where('acadyr_id', $academicYear->id)
            ->where('sem_id', $semester->id)
            ->first();
            
        if ($existingVoter) {
            $this->duplicateCount++;
            $this->failures[] = "Duplicate entry skipped: " . $user->name . " (" . $idNumber . ")";
            return null;
        }

        $this->successCount++;
        return new VerifiedVoter([
            'users_id' => $user->id,
            'acadyr_id' => $academicYear->id,
            'sem_id' => $semester->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'id_number' => 'required',
            'academic_year' => 'required',
            'semester' => 'required|in:1st Semester,2nd Semester',
            'name' => 'required'
        ];
    }      

    public function customValidationMessages()
    {
        return [
            'id_number.required' => 'ID Number is required',
            'academic_year.required' => 'Academic Year is required',
            'semester.required' => 'Semester is required',
            'semester.in' => 'Semester must be either "1st Semester" or "2nd Semester"',
            'name.required' => 'Name is required'
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

    public function getFailures()
    {
        return $this->failures;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getDuplicateCount()
    {
        return $this->duplicateCount;
    }
}
