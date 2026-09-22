<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StudentUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['idnumber' => env('STUDENT_IDNUMBER', '20250001')],
            [
                'name' => 'Demo Student',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'email' => env('STUDENT_EMAIL', 'student@example.com'),
                'password' => env('STUDENT_PASSWORD', 'Student123!'),
                'course' => 'BS Information Technology',
                'year_level' => '4',
                'status' => 'Active',
                'is_admin' => false,
                'admin_role' => null,
            ],
        );
    }
}
